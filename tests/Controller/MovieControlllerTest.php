<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MovieControlllerTest extends WebTestCase
{
    public function testAnonymous()
    {
        // On teste la route /
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'MovieDB');
        $this->assertSelectorExists('nav.navbar');

        // Avec le même objet $client, on teste la route /admin/movie
        // On veut vérifier, que, sans connexion, on obtient bien uen redirection
        $client->request('GET', '/admin/movie');

        $this->assertResponseRedirects();
    }

    public function testUser()
    {
        // On teste la route /
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'ledoux.helene@lelievre.fr',
            'PHP_AUTH_PW'   => 'Derrick',
        ]);
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/movie/');
        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/movie/edit/1');
        $this->assertResponseStatusCodeSame(403);
    }

    public function testAdmin()
    {
        // On teste la route /
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'hmorvan@free.fr',
            'PHP_AUTH_PW'   => 'Derrick',
        ]);
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/movie/');
        $this->assertResponseIsSuccessful();

        $client->request('GET', '/admin/movie/edit/1');
        $this->assertResponseIsSuccessful();
    }
}
