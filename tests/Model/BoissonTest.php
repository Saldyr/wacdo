<?php

namespace Tests\Model;

class BoissonTest extends TestCase
{
    public function testAddAndGetBoisson(): void
    {
        $model = new \Boisson($this->db);

        // Ajout d'une boisson
        $ok = $model->add(
            'Cola',            // nom
            2.50,              // prix
            1,                 // disponible (1 = oui, 0 = non)
            'Boisson gazeuse', // description
            'url/cola.jpg'     // image_url
        );
        $this->assertTrue($ok, 'add() doit renvoyer true');

        // Récupération
        $id      = (int)$this->db->lastInsertId();
        $boisson = $model->get($id);

        $this->assertIsArray($boisson, 'get() doit renvoyer un tableau');
        $this->assertSame('Cola',                  $boisson['boisson_nom']);
        $this->assertSame('Boisson gazeuse',       $boisson['boisson_description']);
        $this->assertEquals(2.50,                   (float)$boisson['boisson_prix']);
        $this->assertSame('url/cola.jpg',         $boisson['boisson_image_url']);
        $this->assertSame(1,                        (int)$boisson['boisson_disponibilite']);
    }

    public function testUpdateAndDeleteBoisson(): void
    {
        $model = new \Boisson($this->db);

        // Création initiale
        $model->add('Sprite', 1.80, 1, 'Soda citron', 'url/sprite.jpg');
        $id = (int)$this->db->lastInsertId();

        // Mise à jour
        $updated = $model->update(
            $id,
            'Fanta',           // nouveau nom
            2.00,              // nouveau prix
            0,                 // non disponible
            'Soda orange',     // nouvelle description
            'url/fanta.jpg'    // nouvelle image_url
        );
        $this->assertTrue($updated, 'update() doit renvoyer true');

        $b = $model->get($id);
        $this->assertSame('Fanta',             $b['boisson_nom']);
        $this->assertEquals(2.00,               (float)$b['boisson_prix']);
        $this->assertSame(0,                    (int)$b['boisson_disponibilite']);

        // Suppression
        $deleted = $model->delete($id);
        $this->assertTrue($deleted, 'delete() doit renvoyer true');

        // Après suppression, get() renvoie false
        $this->assertFalse(
            $model->get($id),
            'Après delete(), get() doit renvoyer false'
        );
    }

    public function testGetAllBoissons(): void
    {
        $model = new \Boisson($this->db);

        // Préparation de plusieurs boissons
        $model->add('Eau', 1.00, 1, 'Eau plate', 'url/eau.jpg');
        $model->add('Jus', 2.20, 1, 'Jus de fruit', 'url/jus.jpg');

        $list = $model->getAll();
        $this->assertIsArray($list, 'getAll() doit renvoyer un tableau');
        $this->assertCount(2, $list, 'Il doit y avoir 2 boissons');

        $noms = array_column($list, 'boisson_nom');
        $this->assertEqualsCanonicalizing(
            ['Eau', 'Jus'],
            $noms,
            'Les noms des boissons doivent correspondre'
        );
    }
}
