<?php

namespace App\Tests\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccessTest extends WebTestCase
{
    /**
     * @dataProvider getRoutes
     */
    public function testRoutesAsAnonymous($route)
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $route);

        $this->assertResponseRedirects();
    }

    /**
     * @dataProvider getRoutes
     */
    public function testRoutesAsAdmin($route)
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'hmorvan@free.fr', // Dans la BDD de Djyp aujourd'hui ce user est un ROLE_ADMIN
            'PHP_AUTH_PW'   => 'Derrick',
        ]);
        $client->request('GET', $route);

        $this->assertResponseIsSuccessful();
    }

    /**
     * @dataProvider getRoutes
     */
    public function testRoutesAsUser($route)
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'ledoux.helene@lelievre.fr', // Dans la BDD de Djyp aujourd'hui ce user est un ROLE_USER
            'PHP_AUTH_PW'   => 'Derrick',
        ]);
        $client->request('GET', $route);

        // Toutes les routes qui servent à ajouter ou modifier des données sont interdites aux ROLE_USER
        if (preg_match('/\/(add|edit)/', $route)) {
            $this->assertResponseStatusCodeSame(403);
        } else {
            $this->assertResponseIsSuccessful();
        }
    }

    public function getRoutes()
    {
        return [
            ['/admin/?action=list&entity=Movie'],
            ['/admin/movie/'],
            ['/admin/movie/edit/1'],
            ['/admin/movie/add'],
            ['/admin/genre/'],
            ['/admin/genre/edit/1'],
            ['/admin/genre/add'],
        ];
    }
}
