<?php
namespace Tests\Model;

class UtilisateurTest extends TestCase
{
    public function testAddAndGetUser(): void
    {
        // Instanciation du modèle
        $model = new \Utilisateur($this->db);

        // Ajout d’un utilisateur
        $ok = $model->add(
            'Jean', 
            'Dupont', 
            'j.dupont@example.com',
            password_hash('secret', PASSWORD_DEFAULT),
            5,            // rôle client
            true,         // consentement
            new \DateTime(),
            true          // is_active
        );
        $this->assertTrue($ok, 'L’insert doit renvoyer true');

        // Récupération par ID
        $id   = (int)$this->db->lastInsertId();
        $user = $model->get($id);

        $this->assertSame('Jean',  $user['user_prenom']);
        $this->assertSame('Dupont',$user['user_nom']);
        $this->assertEquals(1,     $user['is_active']);
    }

    public function testUpdateAndDelete(): void
    {
        $model = new \Utilisateur($this->db);
        // On crée un user temporaire
        $model->add('A','B','a.b@example.com',password_hash('x',PASSWORD_DEFAULT),5,false,null,true);
        $id = (int)$this->db->lastInsertId();

        // On met à jour ses infos
        $this->assertTrue(
            $model->update($id, 'Alice', 'Bob', 'alice.bob@example.com', 5),
            'update() doit renvoyer true'
        );

        $user = $model->get($id);
        $this->assertSame('Alice', $user['user_prenom']);

        // On supprime
        $this->assertTrue(
            $model->delete($id),
            'delete() doit renvoyer true'
        );
        $this->assertNull(
            $model->get($id),
            'Après delete(), get() renvoie null'
        );
    }
}
