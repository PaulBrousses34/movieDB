<?php

namespace App\Tests\Services;

use App\Services\Slugger;
use PHPUnit\Framework\TestCase;

class SluggerTest extends TestCase
{
    // Toutes les méthodes de test doivent commencer par «test»
    // On peut avoir des méthodes avec d'autres noms du moment
    // qu'on en a au moins une qui commence par «test»
    public function testSlugify()
    {
        // On doit instancier nous-mème notre Slugger
        $slugger = new Slugger();

        // On exécute slugify avec une valeur écrite en dur car on va tester le slug exact
        $slug = $slugger->slugify('The King of Staten Island');
        // le slug de ça est the-king-of-staten-island

        // On teste que la valeur calculée par slugify est bien un string et
        // qu'elle correspond exactement au slug attendu
        $this->assertIsString($slug);
        $this->assertEquals('the-king-of-staten-island', $slug);

        // On refait un nouveau «test», sur slugify, toujours, mais avec une nouvelle valeur
        $slug = $slugger->slugify('La vie d\'adèle');
        // le slug attendu : la-vie-d-adele

        $this->assertIsString($slug);
        $this->assertEquals('la-vie-d-ad-le', $slug);
    }
}
