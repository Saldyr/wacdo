<?php

namespace Tests\Model;

class MenuTest extends TestCase
{
    public function testAddAndGetMenu(): void
    {
        // 1) Instanciation du modèle
        $model = new \Menu($this->db);

        // 2) Création du menu
        $ok = $model->add(
            'Formule Midi',       // nom
            'Entrée+Plat+Dessert', // description
            14.90,                // prix
            'https://img/menu.jpg', // image_url
            1                     // disponible (1 = oui, 0 = non)
        );
        $this->assertTrue($ok, 'add() doit renvoyer true');

        // 3) Récupération en base
        $id   = (int)$this->db->lastInsertId();
        $menu = $model->get($id);
        $this->assertIsArray($menu, 'get() doit renvoyer un tableau');
        $this->assertSame('Formule Midi',          $menu['menu_nom']);
        $this->assertSame('Entrée+Plat+Dessert',   $menu['menu_description']);
        $this->assertEquals(14.90,                  (float)$menu['menu_prix']);
        $this->assertSame('https://img/menu.jpg',   $menu['menu_image_url']);
        $this->assertSame(1,                        (int)$menu['menu_disponibilite']);
    }

    public function testUpdateMenu(): void
    {
        $model = new \Menu($this->db);

        // Création initiale
        $model->add('Ancien Menu', 'Desc A', 10.00, 'urlA', 1);
        $id = (int)$this->db->lastInsertId();

        // Mise à jour
        $updated = $model->update(
            $id,
            'Nouveau Menu',
            'Desc B',
            12.50,
            'urlB',
            0
        );
        $this->assertTrue($updated, 'update() doit renvoyer true');

        $m = $model->get($id);
        $this->assertSame('Nouveau Menu', $m['menu_nom']);
        $this->assertEquals(12.50,         (float)$m['menu_prix']);
        $this->assertSame(0,               (int)$m['menu_disponibilite']);
    }

    public function testDeleteMenu(): void
    {
        $model = new \Menu($this->db);

        // Création initiale
        $model->add('Menu à supprimer', 'Desc', 5.00, '', 1);
        $id = (int)$this->db->lastInsertId();

        // Suppression
        $deleted = $model->delete($id);
        $this->assertTrue($deleted, 'delete() doit renvoyer true');

        // get() renvoie false après suppression
        $this->assertFalse(
            $model->get($id),
            'Après delete(), get() doit renvoyer false'
        );
    }

    public function testGetProduitsByMenu(): void
    {
        // 1) Préparer une catégorie et un produit
        $this->db->exec("
            INSERT INTO categorie (category_nom) VALUES ('Cat1');
        ");
        $this->db->exec("
            INSERT INTO produit
                (product_nom,product_description,product_prix,product_image_url,product_disponibilite,category_id)
            VALUES
                ('P1','D1',1.23,'url',1,1);
        ");
        $pid = (int)$this->db->lastInsertId();

        // 2) Créer 2 menus
        $model = new \Menu($this->db);
        $model->add('Menu 1', 'Desc1', 10.0, '', 1);
        $mid1 = (int)$this->db->lastInsertId();
        $model->add('Menu 2', 'Desc2', 20.0, '', 1);
        $mid2 = (int)$this->db->lastInsertId();

        // 3) Lier le produit au premier menu
        $ok2 = $model->addProduit($mid1, $pid);
        $this->assertTrue($ok2, 'addProduit() doit renvoyer true');

        // 4) Vérifier que getProduitsByMenu($mid1) contient P1
        $list1 = $model->getProduitsByMenu($mid1);
        $this->assertCount(1, $list1, 'doit y avoir 1 produit lié');
        $this->assertSame('P1', $list1[0]['product_nom']);

        // 5) Vérifier que getProduitsByMenu($mid2) est vide
        $list2 = $model->getProduitsByMenu($mid2);
        $this->assertIsArray($list2);
        $this->assertCount(0, $list2, 'aucun produit lié au menu 2');
    }
}
