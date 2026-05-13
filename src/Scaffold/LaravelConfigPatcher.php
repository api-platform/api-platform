<?php

declare(strict_types=1);

namespace ApiPlatform\Installer\Scaffold;

use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\CloningVisitor;
use PhpParser\ParserFactory;
use PhpParser\PhpVersion;
use PhpParser\PrettyPrinter;

/**
 * Updates `config/api-platform.php` by manipulating its AST. Surrounding
 * source and comments stay verbatim thanks to the format-preserving printer.
 */
final class LaravelConfigPatcher
{
    private const FORMAT_MAP = [
        'jsonld' => ['key' => 'jsonld', 'mime' => 'application/ld+json'],
        'jsonapi' => ['key' => 'jsonapi', 'mime' => 'application/vnd.api+json'],
        'hal' => ['key' => 'jsonhal', 'mime' => 'application/hal+json'],
    ];

    /**
     * @param array<string> $formats
     * @param array<string> $docs
     */
    public function patch(string $source, array $formats, array $docs): string
    {
        $parser = (new ParserFactory())->createForVersion(PhpVersion::fromString('8.3'));
        $oldStmts = $parser->parse($source) ?? [];
        $oldTokens = $parser->getTokens();

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new CloningVisitor());
        $newStmts = $traverser->traverse($oldStmts);

        $root = $this->findReturnedArray($newStmts);
        if (null === $root) {
            throw new \RuntimeException('Expected a top-level "return [...]".');
        }

        $this->replaceFormats($root, $formats);

        if (!\in_array('swagger_ui', $docs, true)) {
            $this->disableSwaggerUi($root);
        }

        return (new PrettyPrinter\Standard())->printFormatPreserving($newStmts, $oldStmts, $oldTokens);
    }

    /**
     * @param array<Node> $stmts
     */
    private function findReturnedArray(array $stmts): ?Node\Expr\Array_
    {
        $return = (new NodeFinder())->findFirstInstanceOf($stmts, Node\Stmt\Return_::class);
        if ($return instanceof Node\Stmt\Return_ && $return->expr instanceof Node\Expr\Array_) {
            return $return->expr;
        }

        return null;
    }

    /**
     * @param array<string> $formats
     */
    private function replaceFormats(Node\Expr\Array_ $root, array $formats): void
    {
        $items = [];
        foreach ($formats as $f) {
            if (!isset(self::FORMAT_MAP[$f])) {
                continue;
            }
            $entry = self::FORMAT_MAP[$f];
            $items[] = new Node\ArrayItem(
                new Node\Expr\Array_([
                    new Node\ArrayItem(new Node\Scalar\String_($entry['mime'])),
                ]),
                new Node\Scalar\String_($entry['key']),
            );
        }

        $node = $this->findKey($root, 'formats');
        if (null === $node) {
            throw new \RuntimeException('Could not find "formats" key in config.');
        }
        $node->value = new Node\Expr\Array_($items);
    }

    private function disableSwaggerUi(Node\Expr\Array_ $root): void
    {
        $section = $this->findKey($root, 'swagger_ui');
        if (null === $section || !$section->value instanceof Node\Expr\Array_) {
            return;
        }
        $enabled = $this->findKey($section->value, 'enabled');
        if (null !== $enabled) {
            $enabled->value = new Node\Expr\ConstFetch(new Node\Name('false'));
        }
    }

    private function findKey(Node\Expr\Array_ $array, string $key): ?Node\ArrayItem
    {
        foreach ($array->items as $item) {
            if ($item instanceof Node\ArrayItem
                && $item->key instanceof Node\Scalar\String_
                && $item->key->value === $key) {
                return $item;
            }
        }

        return null;
    }
}
