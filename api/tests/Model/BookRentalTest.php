<?php

namespace App\Tests\Controller;

use App\Model\BookRental;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookRentalTest extends WebTestCase
{
    /**
     * @throws \Exception
     */
    public function testBookRentalService(): void
    {
        $client = static::createClient();
        $userId = Uuid::uuid4()->toString();
        $isbn = '978-0345453747';
        $bookRental = new BookRental($userId, $isbn);

        /** @var EntityManagerInterface $em */
        $em = $client->getContainer()->get('doctrine')->getManager();
        $em->persist($bookRental);
        $em->flush();
        $em->clear();

        /** @var BookRental $bookRental */
        $bookRental = $em->getRepository(BookRental::class)->findOneBy(['userId' => $userId]);
        $this->assertInstanceOf(BookRental::class, $bookRental);
        $this->assertEquals($isbn, $bookRental->getIsbn());
        $this->assertEquals($userId, $bookRental->getUserId());
        $this->assertInstanceOf(\DateTimeImmutable::class, $bookRental->getIssued());
        $this->assertNull($bookRental->getReturned());

        $bookRental->returnBook();
        $this->assertInstanceOf(\DateTimeImmutable::class, $bookRental->getReturned());
    }
}
