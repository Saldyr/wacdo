<?php

namespace Tests\Model;

use PHPUnit\Framework\TestCase as BaseTestCase;
use PDO;

abstract class TestCase extends BaseTestCase
{
    protected PDO $db;

    protected function setUp(): void
    {
        // 1) Base en mémoire
        $this->db = new PDO('sqlite::memory:');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->exec('PRAGMA foreign_keys = ON;');
        $fk = (int) $this->db->query('PRAGMA foreign_keys')->fetchColumn();
        if ($fk !== 1) {
            throw new \RuntimeException('SQLite PRAGMA foreign_keys est OFF');
        }

        // 2) Tables nécessaires

        // role
        $this->db->exec("
            CREATE TABLE role (
                role_id INTEGER PRIMARY KEY AUTOINCREMENT,
                role_nom TEXT NOT NULL
            );
        ");

$this->db->exec("
    INSERT INTO role (role_id, role_nom) VALUES
        (1,'Administrateur'),
        (2,'Préparateur'),
        (3,'Caissier(e)'),
        (4,'Livreur(se)'),
        (5,'Client(e)');
");

        // utilisateur
        $this->db->exec("
            CREATE TABLE utilisateur (
                user_id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_prenom TEXT NOT NULL,
                user_nom TEXT NOT NULL,
                user_mail TEXT NOT NULL UNIQUE,
                user_password TEXT NOT NULL,
                user_date_creation TEXT,
                role_id INTEGER,
                consentement INTEGER,
                date_consentement TEXT,
                is_active INTEGER,
                FOREIGN KEY (role_id) REFERENCES role(role_id)
            );
        ");

        // catégorie (votre table réelle s’appelle probablement 'categorie')
        $this->db->exec("
        CREATE TABLE categorie (
            category_id INTEGER PRIMARY KEY AUTOINCREMENT,
            category_nom TEXT NOT NULL
        );
    ");

        // produit (nom exact des colonnes selon votre base MySQL)
        $this->db->exec("
        CREATE TABLE produit (
            product_id            INTEGER PRIMARY KEY AUTOINCREMENT,
            product_nom           TEXT NOT NULL,
            product_description   TEXT,
            product_prix          REAL NOT NULL,
            product_image_url     TEXT,
            product_disponibilite INTEGER,
            category_id           INTEGER,
            FOREIGN KEY (category_id)
            REFERENCES categorie(category_id)
            ON DELETE RESTRICT ON UPDATE CASCADE
        );
    ");

        $this->db->exec("
        CREATE TABLE menu (
            menu_id             INTEGER PRIMARY KEY AUTOINCREMENT,
            menu_nom            TEXT    NOT NULL,
            menu_description    TEXT,
            menu_prix           REAL    NOT NULL,
            menu_image_url      TEXT,
            menu_disponibilite  INTEGER
        );
    ");

        // table de liaison menu ⇆ produit
        $this->db->exec("
        CREATE TABLE menu_produit (
            menu_id    INTEGER NOT NULL,
            product_id INTEGER NOT NULL,
            PRIMARY KEY (menu_id, product_id),
        FOREIGN KEY (menu_id)
            REFERENCES menu(menu_id)
            ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (product_id)
            REFERENCES produit(product_id)
            ON DELETE RESTRICT ON UPDATE CASCADE
        );
    ");

        $this->db->exec("
        CREATE TABLE boisson (
            boisson_id           INTEGER PRIMARY KEY AUTOINCREMENT,
            boisson_nom          TEXT    NOT NULL,
            boisson_description  TEXT,
            boisson_prix         REAL    NOT NULL,
            boisson_image_url    TEXT,
            boisson_disponibilite INTEGER
        );
    ");

        $this->db->exec("
        CREATE TABLE commande (
            order_id              INTEGER PRIMARY KEY AUTOINCREMENT,
            order_date_commande   DATE      NOT NULL,
            order_created_at      DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP,
            order_heure_livraison TIME,
            order_statut_commande VARCHAR(30) NOT NULL,
            order_numero_ticket   VARCHAR(20) NOT NULL,
            order_type            VARCHAR(12) NOT NULL,
            user_id               INTEGER NULL,
            livreur_id            INTEGER NULL,
            FOREIGN KEY(user_id)    REFERENCES utilisateur(user_id) ON DELETE SET NULL ON UPDATE CASCADE,
            FOREIGN KEY(livreur_id) REFERENCES utilisateur(user_id) ON DELETE SET NULL ON UPDATE CASCADE
        );
    ");

        // 2) liaison commande ⇆ produit
        $this->db->exec("
        CREATE TABLE commande_produit (
            order_id               INTEGER NOT NULL,
            product_id             INTEGER NOT NULL,
            order_product_quantite INTEGER DEFAULT 1,
            PRIMARY KEY(order_id, product_id),
            FOREIGN KEY(order_id)
            REFERENCES commande(order_id)
            ON DELETE CASCADE ON UPDATE CASCADE,
            FOREIGN KEY(product_id)
            REFERENCES produit(product_id)
            ON DELETE RESTRICT ON UPDATE CASCADE
        );
    ");

        // 3) liaison commande ⇆ boisson
        $this->db->exec("
        CREATE TABLE IF NOT EXISTS commande_boisson (
            commande_boisson_id    INTEGER PRIMARY KEY AUTOINCREMENT,
            order_id               INTEGER NOT NULL,
            boisson_id             INTEGER NOT NULL,
            quantity               INTEGER DEFAULT 1,
            FOREIGN KEY(order_id)    REFERENCES commande(order_id),
            FOREIGN KEY(boisson_id)  REFERENCES boisson(boisson_id)
        );
    ");

        // 4) liaison commande ⇆ menu
        $this->db->exec("
            CREATE TABLE commande_menu (
                id                     INTEGER PRIMARY KEY AUTOINCREMENT,
                order_id               INTEGER NOT NULL,
                menu_id                INTEGER NOT NULL,
                order_menu_quantite    INTEGER DEFAULT 1,
                menu_boisson_id        INTEGER NULL,
                FOREIGN KEY(order_id)      REFERENCES commande(order_id),
                FOREIGN KEY(menu_id)       REFERENCES menu(menu_id),
                FOREIGN KEY(menu_boisson_id) REFERENCES boisson(boisson_id)
            );
        ");
    }
}
