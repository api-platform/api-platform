<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190819120152 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $platformName = $this->connection->getDatabasePlatform()->getName();
        $this->skipIf('postgresql' !== $platformName && 'mysql' !== $platformName, 'Migration can only be executed safely on \'postgresql\' or \'mysql\'.');

        $this->addSql('CREATE TABLE greeting (id SERIAL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        $platformName = $this->connection->getDatabasePlatform()->getName();
        $this->skipIf('postgresql' !== $platformName && 'mysql' !== $platformName, 'Migration can only be executed safely on \'postgresql\' or \'mysql\'.');

        $this->addSql('DROP TABLE greeting');
    }
}
