<?php
// controller/CommandeController.php

require_once __DIR__ . '/../lib/Auth.php';
// Autorise Admin (1), Manager (2), Prépa/Accueil (3) et Clients (5)
Auth::check([1, 2, 3, 5]);

require_once __DIR__ . '/../model/Commande.php';
require_once __DIR__ . '/../model/CommandeMenu.php';
require_once __DIR__ . '/../model/CommandeProduit.php';
require_once __DIR__ . '/../model/CommandeBoisson.php';
require_once __DIR__ . '/../model/Menu.php';
require_once __DIR__ . '/../model/Produit.php';
require_once __DIR__ . '/../model/Boisson.php';
require_once __DIR__ . '/../model/Categorie.php';

// La session est démarrée dans public/index.php

// REPÈRE A-1 : Initialisation du panier en session
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = ['menus' => [], 'produits' => [], 'boissons' => []];
}

// Instanciation des modèles
$cmdM = new Commande();
$mM   = new Menu();
$pM   = new Produit();
$bM   = new Boisson();
$cM   = new Categorie();

// ← AJOUTS ICI
$cmM = new CommandeMenu();
$cpM = new CommandeProduit();
$cbM = new CommandeBoisson();

$role   = $_SESSION['user']['role_id'] ?? null;
$action = $_GET['action']            ?? '';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

/**
 * REPÈRE B-1 : Fonction utilitaire pour renvoyer le panier en JSON (AJAX)
 */
function sendCartJson(array $models)
{
    $detail = ['items' => [], 'total' => 0];
    foreach (['menus', 'produits', 'boissons'] as $type) {
        $model = $models[$type];
        foreach ($_SESSION['cart'][$type] as $id => $qty) {
            $item = $model->get($id);
            if (!$item) continue;
            $priceKey = $type === 'menus'
                ? 'menu_prix'
                : ($type === 'produits' ? 'product_prix' : 'boisson_prix');
            $nameKey  = $type === 'menus'
                ? 'menu_nom'
                : ($type === 'produits' ? 'product_nom' : 'boisson_nom');
            $price    = (float)$item[$priceKey];
            $subtotal = $price * $qty;
            $detail['items'][] = [
                'type'     => $type,
                'id'       => $id,
                'name'     => $item[$nameKey],
                'qty'      => $qty,
                'price'    => $price,
                'subtotal' => $subtotal,
            ];
            $detail['total'] += $subtotal;
        }
    }
    header('Content-Type: application/json');
    echo json_encode($detail);
    exit;
}

/**
 * REPÈRE C-1 : Ajout au panier (clients seulement)
 */
if ($role === 5 && $action === 'addCart') {
    // C-1-a : modifier la session
    $type = $_GET['type'] ?? '';
    $id   = (int)($_GET['id'] ?? 0);
    if (in_array($type, ['menus', 'produits', 'boissons'], true) && $id > 0) {
        $_SESSION['cart'][$type][$id] = ($_SESSION['cart'][$type][$id] ?? 0) + 1;
    }
    // C-1-b : réponse AJAX vs redirection full-page
    if (isset($_GET['ajax'])) {
        sendCartJson(['menus' => $mM, 'produits' => $pM, 'boissons' => $bM]);
    }
    header('Location: index.php?section=commande');
    exit;
}

/**
 * REPÈRE C-2 : Retrait du panier (clients seulement)
 */
if ($role === 5 && $action === 'removeCart') {
    // C-2-a : modifier la session
    $type = $_GET['type'] ?? '';
    $id   = (int)($_GET['id'] ?? 0);
    if (!empty($_SESSION['cart'][$type][$id])) {
        $_SESSION['cart'][$type][$id]--;
        if ($_SESSION['cart'][$type][$id] <= 0) {
            unset($_SESSION['cart'][$type][$id]);
        }
    }
    // C-2-b : AJAX ou full
    if (isset($_GET['ajax'])) {
        sendCartJson(['menus' => $mM, 'produits' => $pM, 'boissons' => $bM]);
    }
    header('Location: index.php?section=commande');
    exit;
}

/**
 * REPÈRE D-1 : Finalisation de la commande par le client
 */
if ($role === 5 && $action === 'checkout' && $method === 'POST') {
    // D-1-a : CSRF
    if (!isset($_POST['csrf'], $_SESSION['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        exit('CSRF détecté');
    }
    // D-1-b : créer la commande
    $dateCommande   = date('Y-m-d');
    $ticket         = $cmdM->generateNextTicket($dateCommande);
    $cmdM->add(
        $dateCommande,
        null,
        'En attente',
        $ticket,
        $_SESSION['user']['user_id'],
        null,
        'sur_place'
    );
    $orderId = $cmdM->getLastInsertId();
    // D-1-c : insérer les détails
    $cmM = new CommandeMenu();
    $cpM = new CommandeProduit();
    $cbM = new CommandeBoisson();
    foreach ($_SESSION['cart']['menus'] as $mid => $q) {
        for ($i = 0; $i < $q; $i++) {
            $cmM->add($orderId, $mid, 1, null);
        }
    }
    foreach ($_SESSION['cart']['produits'] as $pid => $q) {
        $cpM->add($orderId, $pid, $q);
    }
    foreach ($_SESSION['cart']['boissons'] as $bid => $q) {
        $cbM->add($orderId, $bid, $q);
    }
    // D-1-d : vider le panier
    $_SESSION['cart'] = ['menus' => [], 'produits' => [], 'boissons' => []];
    // D-1-e : rediriger vers liste client
    header('Location: index.php?section=commande&action=listClient');
    exit;
}

/**
 * REPÈRE D-2 : Affichage catalogue + panier initial (clients)
 */
if ($role === 5 && !$action) {  // on affiche le catalogue seulement si aucune action particulière
    // D-2-a : récupérer catalogue
    $menus    = $mM->getAll();
    $produits = $pM->getAll();
    $boissons = $bM->getAll();

    // D-2-b : regrouper produits par catégorie
    $catMap = [];
    foreach ($cM->getAll() as $c) {
        $catMap[$c['category_id']] = $c['category_nom'];
    }
    $produitsParCategorie = [];
    foreach ($produits as $prod) {
        $nom = $catMap[$prod['category_id']] ?? 'Autres';
        $produitsParCategorie[$nom][] = $prod;
    }

    // D-2-c : préparer les détails du panier
    $cartDetail = ['items' => [], 'total' => 0];
    foreach (['menus', 'produits', 'boissons'] as $type) {
        $model = $type === 'menus' ? $mM : ($type === 'produits' ? $pM : $bM);
        foreach ($_SESSION['cart'][$type] ?? [] as $id => $q) {
            $item = $model->get($id);
            if (!$item) {
                continue;
            }
            $pk = $type === 'menus'
                ? 'menu_prix'
                : ($type === 'produits' ? 'product_prix' : 'boisson_prix');
            $nk = $type === 'menus'
                ? 'menu_nom'
                : ($type === 'produits' ? 'product_nom' : 'boisson_nom');
            $pr = (float) $item[$pk];
            $st = $pr * $q;
            $cartDetail['items'][] = [
                'type'     => $type,
                'id'       => $id,
                'name'     => $item[$nk],
                'qty'      => $q,
                'price'    => $pr,
                'subtotal' => $st,
            ];
            $cartDetail['total'] += $st;
        }
    }

    // D-2-d : afficher la vue catalogue + panier
    require __DIR__ . '/../view/client_order.php';
    exit;
}

/**
 * REPÈRE H-0 : Détail d'une commande pour le client
 */
if ($role === 5 && $action === 'view' && isset($_GET['id'])) {
    // H-0-a : sécuriser l’ID
    $orderId = (int) $_GET['id'];
    if ($orderId <= 0) {
        http_response_code(400);
        exit('ID invalide');
    }

    // H-0-b : Vérifier que la commande appartient bien à l’utilisateur
    $cmd = $cmdM->get($orderId);
    if (!$cmd || $cmd['user_id'] !== $_SESSION['user']['user_id']) {
        http_response_code(403);
        exit('Pas autorisé');
    }

    // H-0-c : Récupérer les détails (menus, produits, boissons)
    $menusCommandes    = $cmM->getMenusByCommande($orderId);
    $produitsCommandes = $cpM->getProduitsByCommande($orderId);
    $boissonsCommandes = $cbM->getByCommande($orderId);

    // H-0-d : Charger la vue de détail (client_order_view.php)
    require __DIR__ . '/../view/client_order_view.php';
    exit;
}

/**
 * REPÈRE H-1 : Liste des commandes du client
 */
if ($role === 5 && $action === 'listClient') {
    // H-1-a : Récupérer les commandes de l'utilisateur
    $all = $cmdM->getAllByUser($_SESSION['user']['user_id']);
    // H-1-b : Charger la vue de liste (client_order_list.php)
    require __DIR__ . '/../view/client_order_list.php';
    exit;
}




/**
 * REPÈRE F-1 : Back‑office – création de commande (admin/accueil)
 */
if ($action === 'add' && $method === 'POST') {
    // F-1-a : CSRF + droits
    if (!isset($_POST['csrf'], $_SESSION['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        exit('CSRF détecté');
    }
    if (!in_array($role, [1, 3], true)) {
        http_response_code(403);
        exit('Pas autorisé');
    }
    // F-1-b : insertion
    $dateCommande   = $_POST['order_date_commande'] ?? '';
    $heureLivraison = $_POST['order_heure_livraison'] ?: null;
    $statut         = $_POST['order_statut_commande'] ?? '';
    $userId         = (int)($_POST['user_id'] ?? 0);
    $orderType      = $_POST['order_type'] ?? 'sur_place';
    $ticket         = $cmdM->generateNextTicket($dateCommande);
    $cmdM->add($dateCommande, $heureLivraison, $statut, $ticket, $userId, null, $orderType);
    header('Location: index.php?section=commande');
    exit;
}

//
// C) MODIFIER UNE COMMANDE
//
if ($action === 'edit') {
    if ($method === 'POST') {
        // CSRF + droits
        if (!isset($_POST['csrf'], $_SESSION['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
            exit('CSRF détecté');
        }
        if (!in_array($role, [1, 3], true)) {
            http_response_code(403);
            exit('Pas autorisé');
        }

        // Lecture
        $orderId        = (int)($_GET['id']               ?? 0);
        $dateCommande   = $_POST['order_date_commande']  ?? '';
        $heureLivraison = $_POST['order_heure_livraison'] ?: null;
        $statut         = $_POST['order_statut_commande'] ?? '';
        $userId         = (int)($_POST['user_id']         ?? 0);
        $orderType      = $_POST['order_type']            ?? 'sur_place';

        // Conserver ticket
        $ticket = $cmdM->get($orderId)['order_numero_ticket'] ?? '000';

        // Mise à jour
        $cmdM->update(
            $orderId,
            $dateCommande,
            $heureLivraison,
            $statut,
            $ticket,
            $userId,
            null,
            $orderType
        );

        // Supprimer anciennes liaisons
        $cmM->deleteAllByCommande($orderId);
        $cpM->deleteAllByCommande($orderId);
        $cbM->deleteAllByCommande($orderId);

        // Réinsertion (mêmes boucles qu'en add)
        foreach ($_POST['menus'] ?? [] as $menuId => $qty) {
            $qty = (int)$qty;
            if ($qty < 1) continue;
            $freeMap  = $_POST['menu_boissons'][$menuId] ?? [];
            $inserted = 0;
            foreach ($freeMap as $bId => $bQty) {
                for ($i = 0; $i < (int)$bQty; $i++, $inserted++) {
                    $cmM->add($orderId, $menuId, 1, $bId);
                }
            }
            for (; $inserted < $qty; $inserted++) {
                $cmM->add($orderId, $menuId, 1, null);
            }
        }
        foreach ($_POST['produits'] ?? [] as $prodId => $pQty) {
            $pQty = (int)$pQty;
            if ($pQty > 0) {
                $cpM->add($orderId, $prodId, $pQty);
            }
        }
        foreach ($_POST['boissons_unite'] ?? [] as $bId => $bQty) {
            $bQty = (int)$bQty;
            if ($bQty > 0) {
                $cbM->add($orderId, $bId, $bQty);
            }
        }

        header('Location: index.php?section=commande');
        exit;
    }

    // GET → formulaire d'édition
    $orderId             = (int)($_GET['id'] ?? 0);
    $commande            = $cmdM->get($orderId);
    $menusParCommande    = $cmM->getMenusByCommande($orderId);
    $produitsParCommande = $cpM->getProduitsByCommande($orderId);
    $boissonsUnite       = $cbM->getByCommande($orderId);

    // Listes complètes pour la vue
    $menus               = $mM->getAll();
    $produits            = $pM->getAll();    // ← indispensable !
    $boissons            = $bM->getAll();    // ← indispensable !

    // Quantités menus + boissons gratuites
    $menuQty = $menuFreeChoice = [];
    foreach ($menusParCommande as $row) {
        $mid = $row['menu_id'];
        $menuQty[$mid] = ($menuQty[$mid] ?? 0) + 1;
        if (!empty($row['menu_boisson_id'])) {
            $bid = $row['menu_boisson_id'];
            $menuFreeChoice[$mid][$bid] = ($menuFreeChoice[$mid][$bid] ?? 0) + 1;
        }
    }

    // Quantités produits
    $prodQty = [];
    foreach ($produitsParCommande as $row) {
        $prodQty[$row['product_id']] = $row['order_product_quantite'];
    }

    // **Quantités boissons à l’unité** → clé correcte `order_boisson_quantite`
    $boissonsUniteQty = [];
    foreach ($boissonsUnite as $row) {
        $boissonsUniteQty[$row['boisson_id']] = $row['order_boisson_quantite'];
    }

    // Produits regroupés par catégorie
    $rawProduits = $pM->getAll();
    $produitsParCategorie = [];
    foreach ($rawProduits as $prod) {
        $cat = $prod['product_category'] ?? 'Autres';
        $produitsParCategorie[$cat][] = $prod;
    }


    require __DIR__ . '/../view/commande_edit.php';
    exit;
}


//
// D) SUPPRESSION D’UNE COMMANDE
//
if ($action === 'delete' && $method === 'POST') {
    if (!isset($_POST['csrf'], $_SESSION['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        exit('CSRF détecté');
    }
    if (!in_array($role, [1, 3], true)) {
        http_response_code(403);
        exit('Pas autorisé');
    }
    $orderId = (int)($_POST['id'] ?? 0);
    $cmM->deleteAllByCommande($orderId);
    $cpM->deleteAllByCommande($orderId);
    $cbM->deleteAllByCommande($orderId);
    $cmdM->delete($orderId);
    header('Location: index.php?section=commande');
    exit;
}


//
// E) MARQUER COMME PRÊTE / EN LIVRAISON
//
if ($action === 'markReady' && $method === 'POST') {
    if (!isset($_POST['csrf'], $_SESSION['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        exit('CSRF détecté');
    }
    if ($role !== 2) {
        http_response_code(403);
        exit('Pas autorisé');
    }
    $orderId = (int)($_POST['id'] ?? 0);
    $cmd     = $cmdM->get($orderId);
    if (!$cmd) {
        http_response_code(404);
        exit('Commande introuvable');
    }
    $newStatus = $cmd['order_type'] === 'a_emporter' ? 'En livraison' : 'Prête';
    $cmdM->updateStatus($orderId, $newStatus);
    header('Location: index.php?section=commande');
    exit;
}


//
// F) LISTE DES COMMANDES (par défaut)
//
$commandes                = $cmdM->getAll();
$menusParCommande         = [];
$produitsParCommande      = [];
$boissonsUniteParCommande = [];
$boissonMap               = [];
$boissonsParCommande      = [];

// Préparer la map boisson pour la vue liste
foreach ($bM->getAll() as $b) {
    $boissonMap[$b['boisson_id']] = $b;
}

foreach ($commandes as $c) {
    $oid = $c['order_id'];

    // Menus + boissons offertes
    $mrows = $cmM->getMenusByCommande($oid);
    foreach ($mrows as &$r) {
        $r['menu_nom'] = $mM->get($r['menu_id'])['menu_nom'];
    }
    $menusParCommande[$oid] = $mrows;

    // Produits supplémentaires
    $prows = $cpM->getProduitsByCommande($oid);
    foreach ($prows as &$r) {
        $r['product_nom'] = $pM->get($r['product_id'])['product_nom'];
    }
    $produitsParCommande[$oid] = $prows;

    // Boissons à l’unité
    $boissonsUniteParCommande[$oid] = $cbM->getByCommande($oid);

    // Boisson « au choix »
    $boissonsParCommande[$oid] = $c['boisson_id']
        ? $bM->get($c['boisson_id'])
        : null;
}

require __DIR__ . '/../view/commande_list.php';
