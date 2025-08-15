<?php

namespace Tests\Model;

class CommandeTest extends TestCase
{
    public function testAddGetUpdateDeleteCommande(): void
    {
        // 1) Préparer un utilisateur
        $this->db->exec("
            INSERT INTO utilisateur (
                user_prenom,
                user_nom,
                user_mail,
                user_password,
                user_date_creation,
                role_id,
                consentement,
                is_active
            ) VALUES (
                'Alice','Dupont','a@d.com','h','2025-01-01',5,1,1
            );
        ");
        $uid = (int)$this->db->lastInsertId();

        // 2) Instancier le modèle
        $model = new \Commande($this->db);

        // 3) add()
        $date    = '2025-08-07';
        $heure   = '12:30:00';
        $statut  = 'en_cours';
        $ticket  = '001';
        $type    = 'a_emporter';
        $ok = $model->add($date, $heure, $statut, $ticket, $uid, null, $type);
        $this->assertTrue($ok, 'add() doit renvoyer true');

        // 4) getLastInsertId & get()
        $id    = $model->getLastInsertId();
        $order = $model->get($id);
        $this->assertSame($id,       (int)$order['order_id']);
        $this->assertSame($date,     $order['order_date_commande']);
        $this->assertSame($heure,    $order['order_heure_livraison']);
        $this->assertSame($statut,   $order['order_statut_commande']);
        $this->assertSame($ticket,   $order['order_numero_ticket']);
        $this->assertSame($uid,      (int)$order['user_id']);
        $this->assertSame($type,     $order['order_type']);

        // 5) updateStatus()
        $this->assertTrue($model->updateStatus($id, 'validee'));
        $this->assertCount(1, $model->getByStatus('validee'), 'getByStatus()');

        // 6) delete()
        $this->assertTrue($model->delete($id), 'delete() doit renvoyer true');
        $this->assertNull($model->get($id), 'après delete(), get() renvoie null');
    }

    public function testGetAllAndGetAllByUser(): void
    {
        // Préparer deux utilisateurs
        $this->db->exec("
            INSERT INTO utilisateur (
                user_prenom,user_nom,user_mail,user_password,user_date_creation,role_id,consentement,is_active
            ) VALUES
                ('U1','Test','u1@test.com','h','2025-01-01',5,1,1),
                ('U2','Test','u2@test.com','h','2025-01-01',5,1,1);
        ");

        $model = new \Commande($this->db);

        // Créer 3 commandes : deux pour U1, une pour U2
        $model->add('2025-08-07', null, 's', '001', 1);
        $model->add('2025-08-08', null, 's', '002', 1);
        $model->add('2025-08-09', null, 's', '003', 2);

        $all = $model->getAll();
        $this->assertCount(3, $all, 'getAll() doit renvoyer toutes les commandes');

        $byUser1 = $model->getAllByUser(1);
        $this->assertCount(2, $byUser1, 'getAllByUser() pour U1');

        $byUser2 = $model->getAllByUser(2);
        $this->assertCount(1, $byUser2, 'getAllByUser() pour U2');
    }

    public function testTicketGeneration(): void
    {
        $model = new \Commande($this->db);
        $date  = '2025-08-07';

        $this->db->exec("
        INSERT INTO utilisateur (
            user_prenom, user_nom, user_mail, user_password,
            user_date_creation, role_id, consentement, is_active
        ) VALUES ('Tmp','User','tmp@test.invalid','h','2025-01-01',5,1,1)
    ");
        $uid = (int)$this->db->lastInsertId();

        $this->db->exec("
        INSERT INTO commande (
            order_date_commande, order_heure_livraison, order_statut_commande,
            order_numero_ticket, user_id, order_type
        ) VALUES
            ('$date', NULL, 's', '001', $uid, 'sur_place'),
            ('$date', NULL, 's', '002', $uid, 'sur_place')
    ");

        $this->assertSame('003', $model->generateNextTicket($date), 'generateNextTicket()');

        // 4) Ajoute un ticket > 100 pour tester le wrap
        $this->db->exec("
        INSERT INTO commande (
            order_date_commande, order_heure_livraison, order_statut_commande,
            order_numero_ticket, user_id, order_type
        ) VALUES
            ('$date', NULL, 's', '101', $uid, 'sur_place')
    ");

        // 5) Après 101, next=102 -> wrap '000'
        $this->assertSame('000', $model->generateNextTicket($date));
    }

    public function testAssignAndGetByLivreur(): void
    {
        // Préparer un livreur
        $this->db->exec("
            INSERT INTO utilisateur (
                user_prenom,user_nom,user_mail,user_password,user_date_creation,role_id,consentement,is_active
            ) VALUES
                ('L','Livreur','l@livreur.com','h','2025-01-01',4,1,1);
        ");
        $livreurId = (int)$this->db->lastInsertId();

        $model = new \Commande($this->db);
        $model->add('2025-08-07', null, 'en_cours', '001', 1);
        $cid = (int)$model->getLastInsertId();

        $this->assertTrue(
            $model->assignToLivreur($cid, $livreurId),
            'assignToLivreur() doit renvoyer true'
        );

        $result = $model->getByLivreur($livreurId);
        $this->assertCount(1, $result, 'getByLivreur()');
        $this->assertSame($cid, (int)$result[0]['order_id']);
    }
}
