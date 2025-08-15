<?php
namespace Tests\Model;

class ProduitTest extends TestCase
{
    public function testAddAndGetProduit(): void
    {
        // 1) Insérez d'abord une catégorie
        $this->db->exec("
            INSERT INTO categorie (category_nom)
            VALUES ('Catégorie Test');
        ");

        // 2) Instanciation du modèle avec le PDO de test
        $model = new \Produit($this->db);

        // 3) Appel de add()
        $ok = $model->add(
            'Produit Test',         // $nom
            'Une description',      // $description
            9.99,                   // $prix
            'https://img.url/test', // $image_url
            1,                      // $dispo (1 = dispo, 0 = non dispo)
            1                       // $categorie (category_id = 1)
        );
        $this->assertTrue($ok, 'add() doit renvoyer true');

        // 4) Récupération et vérifications
        $id      = (int)$this->db->lastInsertId();
        $produit = $model->get($id);

        $this->assertSame('Produit Test',       $produit['product_nom']);
        $this->assertSame('Une description',    $produit['product_description']);
        $this->assertEquals(9.99,               (float)$produit['product_prix']);
        $this->assertSame('https://img.url/test', $produit['product_image_url']);
        $this->assertSame(1,                    (int)$produit['product_disponibilite']);
        $this->assertSame(1,                    (int)$produit['category_id']);
    }

    public function testUpdateAndDeleteProduit(): void
    {
        // 1) Catégorie
        $this->db->exec("
            INSERT INTO categorie (category_nom)
            VALUES ('Cat2');
        ");

        $model = new \Produit($this->db);

        // 2) Création initiale
        $model->add('A', 'Dossier A', 1.23, 'urlA', 1, 1);
        $id = (int)$this->db->lastInsertId();

        // 3) Mise à jour
        $this->assertTrue(
            $model->update(
                $id,
                'B', 'Dossier B', 4.56, 'urlB', 0, 1
            ),
            'update() doit renvoyer true'
        );

        $p = $model->get($id);
        $this->assertSame('B',    $p['product_nom']);
        $this->assertEquals(4.56, (float)$p['product_prix']);
        $this->assertSame(0,      (int)$p['product_disponibilite']);

        // 4) Suppression
        $this->assertTrue($model->delete($id), 'delete() doit renvoyer true');
        $this->assertNull($model->get($id),   'Après delete(), get() renvoie null');
    }
}
