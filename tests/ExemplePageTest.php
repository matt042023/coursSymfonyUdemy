<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ExemplePageTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/exemple');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'ExempleController!');
    }
}
