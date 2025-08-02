<?php
// controller/CommandeController.php

// -----------------------------------------------------------------------------
// A) BOOTSTRAP & MODELS
// -----------------------------------------------------------------------------
require_once __DIR__ . '/../lib/Auth.php';
// Autorise Admin (1), Manager (2), Prépa/Accueil (3), Livreurs (4) et Clients (5)
Auth::check([1, 2, 3, 4, 5]);

require_once __DIR__ . '/../model/Commande.php';
require_once __DIR__ . '/../model/CommandeMenu.php';
require_once __DIR__ . '/../model/CommandeProduit.php';
require_once __DIR__ . '/../model/CommandeBoisson.php';
require_once __DIR__ . '/../model/Menu.php';
require_once __DIR__ . '/../model/Produit.php';
require_once __DIR__ . '/../model/Boisson.php';
require_once __DIR__ . '/../model/Categorie.php';

// -----------------------------------------------------------------------------
// B) SESSION & PANIER INITIALIZATION
// -----------------------------------------------------------------------------
// B-1: Démarrage de la session déjà géré dans public/index.php
// B-2: Initialisation du panier
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = ['menus' => [], 'produits' => [], 'boissons' => []];
}

// -----------------------------------------------------------------------------
// C) INSTANTIATION & CONTEXTE
// -----------------------------------------------------------------------------
// C-1: Instanciation des modèles
$cmdM = new Commande();
$mM   = new Menu();
$pM   = new Produit();
$bM   = new Boisson();
$cM   = new Categorie();
$cmM  = new CommandeMenu();
$cpM  = new CommandeProduit();
$cbM  = new CommandeBoisson();

// C-2: Contexte utilisateur
$role   = $_SESSION['user']['role_id'] ?? null;
$action = $_GET['action']            ?? '';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// -----------------------------------------------------------------------------
// D) UTILITAIRES
// -----------------------------------------------------------------------------
/**
 * D-1: Renvoie le panier en JSON pour requêtes AJAX
 */
function sendCartJson(array $models)
{
    $detail = ['items' => [], 'total' => 0];
    foreach (['menus', 'produits', 'boissons'] as $type) {
        $model = $models[$type];
        foreach ($_SESSION['cart'][$type] as $id => $data) {
            if ($type === 'menus') {
                $qty     = (int)$data['qty'];
                $freeBId = $data['boisson_id'];
            } else {
                $qty     = (int)$data;
                $freeBId = null;
            }
            $item = $model->get($id);
            if (!$item) continue;
            $priceKey = $type === 'menus' ? 'menu_prix' : ($type === 'produits' ? 'product_prix' : 'boisson_prix');
            $nameKey  = $type === 'menus' ? 'menu_nom'  : ($type === 'produits' ? 'product_nom' : 'boisson_nom');
            $price    = (float)$item[$priceKey];
            $subtotal = $price * $qty;
            $entry = [
                'type'     => $type,
                'id'       => $id,
                'name'     => $item[$nameKey],
                'qty'      => $qty,
                'price'    => $price,
                'subtotal' => $subtotal,
            ];
            if ($type === 'menus' && $freeBId) {
                $b = $models['boissons']->get($freeBId);
                $entry['boisson_id']   = $freeBId;
                $entry['boisson_name'] = $b['boisson_nom'] ?? '—';
            }
            $detail['items'][] = $entry;
            $detail['total']  += $subtotal;
        }
    }
    header('Content-Type: application/json');
    echo json_encode($detail);
    exit;
}

// -----------------------------------------------------------------------------
// E) AJAX: GESTION DU PANIER CLIENT
// -----------------------------------------------------------------------------
// E-1: Ajout au panier
if ($role === 5 && $action === 'addCart') {
    $type = $_REQUEST['type'] ?? '';
    $id   = (int)($_REQUEST['id'] ?? 0);
    if (in_array($type, ['menus', 'produits', 'boissons'], true) && $id > 0) {
        if ($type === 'menus') {
            $bid = isset($_REQUEST['boisson_id']) ? (int)$_REQUEST['boisson_id'] : null;
            if (!isset($_SESSION['cart']['menus'][$id])) {
                $_SESSION['cart']['menus'][$id] = ['qty' => 1, 'boisson_id' => $bid];
            } else {
                $_SESSION['cart']['menus'][$id]['qty']++;
            }
        } else {
            $_SESSION['cart'][$type][$id] = ($_SESSION['cart'][$type][$id] ?? 0) + 1;
        }
    }
    if (isset($_REQUEST['ajax'])) sendCartJson(['menus' => $mM, 'produits' => $pM, 'boissons' => $bM]);
    header('Location:index.php?section=commande');
    exit;
}
// E-2: Retrait du panier
if ($role === 5 && $action === 'removeCart') {
    $type = $_REQUEST['type'] ?? '';
    $id   = (int)($_REQUEST['id'] ?? 0);
    if ($type === 'menus' && !empty($_SESSION['cart']['menus'][$id])) {
        $_SESSION['cart']['menus'][$id]['qty']--;
        if ($_SESSION['cart']['menus'][$id]['qty'] <= 0) unset($_SESSION['cart']['menus'][$id]);
    } elseif (!empty($_SESSION['cart'][$type][$id])) {
        $_SESSION['cart'][$type][$id]--;
        if ($_SESSION['cart'][$type][$id] <= 0) unset($_SESSION['cart'][$type][$id]);
    }
    if (isset($_REQUEST['ajax'])) sendCartJson(['menus' => $mM, 'produits' => $pM, 'boissons' => $bM]);
    header('Location:index.php?section=commande');
    exit;
}

// -----------------------------------------------------------------------------
// F) CLIENT: PASSE ET AFFICHAGE DES COMMANDES
// -----------------------------------------------------------------------------
// F-1: Checkout
if ($role === 5 && $action === 'checkout' && $method === 'POST') {
    if (!isset($_POST['csrf'], $_SESSION['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) exit('CSRF détecté');
    $orderType = $_POST['order_type'] ?? 'sur_place';
    if (!in_array($orderType, ['sur_place', 'a_emporter', 'livraison'], true)) $orderType = 'sur_place';
    $date   = date('Y-m-d');
    $ticket = $cmdM->generateNextTicket($date);
    $cmdM->add($date, null, 'en_preparation', $ticket, $_SESSION['user']['user_id'], null, $orderType);
    $orderId = $cmdM->getLastInsertId();
    foreach ($_SESSION['cart']['menus'] as $mid => $d) for ($i = 0; $i < (int)$d['qty']; $i++) $cmM->add($orderId, $mid, 1, (int)$d['boisson_id']);
    foreach ($_SESSION['cart']['produits'] as $pid => $q) if (($q = (int)$q) > 0) $cpM->add($orderId, $pid, $q);
    foreach ($_SESSION['cart']['boissons'] as $bid => $q)  if (($q = (int)$q) > 0) $cbM->add($orderId, $bid, $q);
    $_SESSION['cart'] = ['menus' => [], 'produits' => [], 'boissons' => []];
    header('Location:index.php?section=commande&action=listClient');
    exit;
}
// F-2: Catalogue + panier
if ($role === 5 && $action === '') {
    $menus    = $mM->getAll();
    $produits = $pM->getAll();
    $boissons = $bM->getAll();
    $catMap   = [];
    foreach ($cM->getAll() as $c) $catMap[$c['category_id']] = $c['category_nom'];
    $produitsParCategorie = [];
    foreach ($produits as $p) {
        $cat = $catMap[$p['category_id']] ?? 'Autres';
        $produitsParCategorie[$cat][] = $p;
    }
    $cartDetail = ['items' => [], 'total' => 0];
    foreach (['menus', 'produits', 'boissons'] as $type) {
        $model = $type === 'menus' ? $mM : ($type === 'produits' ? $pM : $bM);
        foreach ($_SESSION['cart'][$type] ?? [] as $id => $d) {
            if ($type === 'menus') {
                $q = (int)$d['qty'];
                $bid = $d['boisson_id'];
            } else {
                $q = (int)$d;
                $bid = null;
            }
            $item = $model->get($id);
            if (!$item) continue;
            $pk = $type === 'menus' ? 'menu_prix' : ($type === 'produits' ? 'product_prix' : 'boisson_prix');
            $nk = $type === 'menus' ? 'menu_nom' : ($type === 'produits' ? 'product_nom' : 'boisson_nom');
            $price = (float)$item[$pk];
            $st = $price * $q;
            $entry = ['type' => $type, 'id' => $id, 'name' => $item[$nk], 'qty' => $q, 'price' => $price, 'subtotal' => $st];
            if ($bid) {
                $b = $bM->get($bid);
                $entry['boisson_id'] = $bid;
                $entry['boisson_name'] = $b['boisson_nom'] ?? '—';
            }
            $cartDetail['items'][] = $entry;
            $cartDetail['total'] += $st;
        }
    }
    require __DIR__ . '/../view/client_order.php';
    exit;
}
// F-3: Détail
if ($role === 5 && $action === 'view' && isset($_GET['id'])) {
    $oid = (int)$_GET['id'];
    if ($oid <= 0) {
        http_response_code(400);
        exit('ID invalide');
    }
    $cmd = $cmdM->get($oid);
    if (!$cmd || $cmd['user_id'] !== $_SESSION['user']['user_id']) {
        http_response_code(403);
        exit('Pas autorisé');
    }
    $menusCmd = $cmM->getMenusByCommande($oid);
    $prodsCmd = $cpM->getProduitsByCommande($oid);
    $bevCmd = $cbM->getByCommande($oid);
    require __DIR__ . '/../view/client_order_view.php';
    exit;
}
// F-4: Liste client
if ($role === 5 && $action === 'listClient') {
    // Récupère toutes les commandes du client
    $commandes = $cmdM->getAllByUser($_SESSION['user']['user_id']);

    // Exclut celles déjà « Servie » ou « Livrée »
    $commandes = array_filter(
        $commandes,
        fn(array $c) => !in_array($c['order_statut_commande'], ['servie', 'livree'], true)
    );

    // On passe $commandes à la vue
    require __DIR__ . '/../view/client_order_list.php';
    exit;
}

// -----------------------------------------------------------------------------
// G) LIVREUR – prise en charge
// -----------------------------------------------------------------------------
if ($role === 4 && $action === 'prendre' && $method === 'POST') {
    // CSRF
    if (!isset($_POST['csrf'], $_SESSION['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        exit('CSRF détecté');
    }
    // Récupère l’ID de la commande
    $orderId       = (int)($_POST['id'] ?? 0);
    $currentUserId = $_SESSION['user']['user_id'];

    // Assigne et passe en "en_livraison"
    $cmdM->assignToLivreur($orderId, $currentUserId);

    // Redirection vers la liste
    header('Location: index.php?section=commande');
    exit;
}

// -----------------------------------------------------------------------------
// H) BACK-OFFICE – CRÉATION, MODIFICATION, SUPPRESSION & MARQUAGE
// -----------------------------------------------------------------------------

// H-0: Détail d'une commande (GET)
if ($method === 'GET' && $action === 'view' && in_array($role, [1, 2, 3, 4], true)) {
    $oid = (int)($_GET['id'] ?? 0);
    if ($oid <= 0) {
        http_response_code(400);
        exit('ID invalide');
    }
    // Récupère la commande et ses lignes
    $cmd       = $cmdM->get($oid);
    $menusParCommande    = $cmM->getMenusByCommande($oid);
    $produitsParCommande = $cpM->getProduitsByCommande($oid);
    $boissonsUniteParCommande = $cbM->getByCommande($oid);

    // Charge la vue back-office (à créer ci-dessous)
    require __DIR__ . '/../view/commande_view.php';
    exit;
}

// H-1: Historique des commandes
// Récupération des dates de filtre
if ($method === 'GET' && $action === 'history') {
    // Récupération des dates de filtre
    $from = $_GET['from'] ?? '';
    $to   = $_GET['to']   ?? '';

    // Validation rapide (YYYY-MM-DD)
    $validFrom = preg_match('/^\d{4}-\d{2}-\d{2}$/', $from) ? $from : null;
    $validTo   = preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)   ? $to   : null;

    // Chargement brut des commandes
    if (in_array($role, [1, 2, 3], true)) {
        $all = $cmdM->getAll();
    }
    // Historique personnel pour le livreur
    elseif ($role === 4) {
        $all = $cmdM->getAll();
        $uid = $_SESSION['user']['user_id'];
        $all = array_filter($all, fn($c) => (int)$c['livreur_id'] === $uid);
    }
    // Autres rôles non autorisés
    else {
        http_response_code(403);
        exit('Pas autorisé');
    }

    // Filtrer par order_date_commande
    $commandes = array_filter($all, function ($c) use ($validFrom, $validTo) {
        $d = $c['order_date_commande'];
        if ($validFrom && $d < $validFrom) return false;
        if ($validTo   && $d > $validTo)   return false;
        return true;
    });

    require __DIR__ . '/../view/commande_history.php';
    exit;
}

// H-2: Affichage du formulaire de création (GET)
if ($method === 'GET' && $action === 'add' && in_array($role, [1, 3], true)) {
    // Charger les données pour le formulaire
    $menus    = $mM->getAll();
    // Regrouper produits par catégorie
    $catMap = [];
    foreach ($cM->getAll() as $c) {
        $catMap[$c['category_id']] = $c['category_nom'];
    }
    $produitsParCategorie = [];
    foreach ($pM->getAll() as $prod) {
        $nom = $catMap[$prod['category_id']] ?? 'Autres';
        $produitsParCategorie[$nom][] = $prod;
    }
    $boissons = $bM->getAll();

    require __DIR__ . '/../view/commande_add.php';
    exit;
}

// H-3: Création de la commande (POST)
if ($method === 'POST' && $action === 'add') {
    // CSRF + droits
    if (!isset($_POST['csrf'], $_SESSION['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        exit('CSRF détecté');
    }
    if (!in_array($role, [1, 3], true)) {
        http_response_code(403);
        exit('Pas autorisé');
    }

    // Lecture des champs
    $d   = $_POST['order_date_commande']        ?? '';
    $h   = $_POST['order_heure_livraison']      ?: null;
    $s   = $_POST['order_statut_commande']      ?? '';
    $u   = (int)($_POST['user_id']              ?? 0);
    $t   = $_POST['order_type']                 ?? 'sur_place';
    $tic = $cmdM->generateNextTicket($d);

    // Insertion
    $cmdM->add($d, $h, $s, $tic, $u, null, $t);

    header('Location: index.php?section=commande');
    exit;
}

// H-4: Affichage du formulaire de modification (GET)
if ($method === 'GET' && $action === 'edit' && in_array($role, [1, 3], true)) {
    $oid       = (int)($_GET['id'] ?? 0);
    $commande  = $cmdM->get($oid);

    // a) Lignes existantes
    $menusParCommande    = $cmM->getMenusByCommande($oid);
    $produitsParCommande = $cpM->getProduitsByCommande($oid);
    $boissonsUnite       = $cbM->getByCommande($oid);

    // b) Préparer quantités menus + boissons offertes
    $menuQty = $menuFreeChoice = [];
    foreach ($menusParCommande as $row) {
        $mid = (int)$row['menu_id'];
        $menuQty[$mid] = ($menuQty[$mid] ?? 0) + 1;
        if (!empty($row['menu_boisson_id'])) {
            $bid = (int)$row['menu_boisson_id'];
            $menuFreeChoice[$mid][$bid] = ($menuFreeChoice[$mid][$bid] ?? 0) + 1;
        }
    }

    // c) Préparer quantités produits
    $prodQty = [];
    foreach ($produitsParCommande as $row) {
        $pid = (int)$row['product_id'];
        $prodQty[$pid] = (int)$row['order_product_quantite'];
    }

    // d) Préparer quantités boissons à l’unité
    $boissonsUniteQty = [];
    foreach ($boissonsUnite as $row) {
        $bid = (int)$row['boisson_id'];
        $boissonsUniteQty[$bid] = (int)$row['order_boisson_quantite'];
    }

    // e) Charger les listes pour le formulaire
    $menus    = $mM->getAll();
    $boissons = $bM->getAll();
    $catMap   = [];
    foreach ($cM->getAll() as $c) {
        $catMap[$c['category_id']] = $c['category_nom'];
    }
    $produitsParCategorie = [];
    foreach ($pM->getAll() as $prod) {
        $nom = $catMap[$prod['category_id']] ?? 'Autres';
        $produitsParCategorie[$nom][] = $prod;
    }

    // Affichage de la vue (avec toutes les variables calculées)
    require __DIR__ . '/../view/commande_edit.php';
    exit;
}

// H-5: Modification de la commande (POST)
if ($method === 'POST' && $action === 'edit') {
    // CSRF + droits
    if (!isset($_POST['csrf'], $_SESSION['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        exit('CSRF détecté');
    }
    if (!in_array($role, [1, 3], true)) {
        http_response_code(403);
        exit('Pas autorisé');
    }

    $oid  = (int)($_GET['id'] ?? 0);
    $d    = $_POST['order_date_commande']        ?? '';
    $h    = $_POST['order_heure_livraison']      ?: null;
    $s    = $_POST['order_statut_commande']      ?? '';
    $u    = (int)($_POST['user_id']              ?? 0);
    $t    = $_POST['order_type']                 ?? 'sur_place';
    $tic  = $cmdM->get($oid)['order_numero_ticket'] ?? '000';

    // Mise à jour de l’entête
    $cmdM->update($oid, $d, $h, $s, $tic, $u, null, $t);

    // Réinsertion des lignes
    $cmM->deleteAllByCommande($oid);
    $cpM->deleteAllByCommande($oid);
    $cbM->deleteAllByCommande($oid);

    foreach ($_POST['menus'] ?? [] as $m => $q) {
        $q = (int)$q;
        if ($q < 1) {
            continue;
        }

        // map boisson_id => quantité de boissons offertes
        $freeMap = $_POST['menu_boissons'][$m] ?? [];

        // 1) Insérer d’abord toutes les boissons offertes
        $totalFree = 0;
        foreach ($freeMap as $b => $bq) {
            $bq = (int)$bq;
            $totalFree += $bq;
            for ($j = 0; $j < $bq; $j++) {
                $cmM->add($oid, $m, 1, $b);
            }
        }

        // 2) Insérer enfin les menus "nus" pour atteindre la quantité demandée
        $remaining = $q - $totalFree;
        for ($i = 0; $i < $remaining; $i++) {
            $cmM->add($oid, $m, 1, null);
        }
    }

    foreach ($_POST['produits'] ?? [] as $p => $q) {
        if (($n = (int)$q) > 0) {
            $cpM->add($oid, $p, $n);
        }
    }

    foreach ($_POST['boissons_unite'] ?? [] as $b => $q) {
        if (($n = (int)$q) > 0) {
            $cbM->add($oid, $b, $n);
        }
    }

    header('Location: index.php?section=commande');
    exit;
}

// H-6: Suppression de la commande (POST)
if ($method === 'POST' && $action === 'delete') {
    // CSRF + droits
    if (!isset($_POST['csrf'], $_SESSION['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        exit('CSRF détecté');
    }
    if (!in_array($role, [1, 3], true)) {
        http_response_code(403);
        exit('Pas autorisé');
    }

    $oid = (int)($_POST['id'] ?? 0);
    $cmM->deleteAllByCommande($oid);
    $cpM->deleteAllByCommande($oid);
    $cbM->deleteAllByCommande($oid);
    $cmdM->delete($oid);

    header('Location: index.php?section=commande');
    exit;
}

// H-7: Marquer un nouveau statut (POST)
if ($method === 'POST' && $action === 'markReady') {
    // CSRF
    if (!isset($_POST['csrf'], $_SESSION['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) {
        exit('CSRF détecté');
    }

    $oid = (int)($_POST['id'] ?? 0);
    $c   = $cmdM->get($oid);
    if (!$c) {
        http_response_code(404);
        exit('Commande introuvable');
    }

    // Calcul du nouveau statut selon le rôle
    $cur = $c['order_statut_commande'];
    $ot  = $c['order_type'];
    $rid = $_SESSION['user']['role_id'];
    $nx  = null;

    if ($rid === 2 && $cur === 'en_preparation')                            $nx = 'pret';
    elseif ($rid === 3 && $cur === 'pret' && in_array($ot, ['sur_place', 'a_emporter'], true)) $nx = 'servie';
    elseif ($rid === 3 && $cur === 'pret' && $ot === 'livraison')          $nx = 'en_livraison';
    elseif ($rid === 4 && $cur === 'en_livraison')                          $nx = 'livree';

    if ($nx) {
        $cmdM->updateStatus($oid, $nx);
    }

    header('Location: index.php?section=commande');
    exit;
}


// -----------------------------------------------------------------------------
// I) BACK-OFFICE: LISTE DES COMMANDES PAR RÔLE
// -----------------------------------------------------------------------------
$all = $cmdM->getAll();
if ($role === 2)      $commandes = array_filter($all, fn($c) => $c['order_statut_commande'] === 'en_preparation');
elseif ($role === 3)  $commandes = array_filter(
    $all,
    fn($c) => in_array(
        $c['order_statut_commande'],
        ['en_preparation', 'pret'],
        true
    )
);
elseif ($role === 4) {
    $uid = $_SESSION['user']['user_id'];
    $commandes = array_filter($all, function ($c) use ($uid) {
        return $c['order_statut_commande'] === 'en_livraison'
            && (
                $c['livreur_id'] === null
                || $c['livreur_id'] === $uid
            );
    });
} else               $commandes = array_filter($all, fn($c) => !in_array($c['order_statut_commande'], ['servie', 'livree'], true));

$menusParCommande = [];
$produitsParCommande = [];
$boissonsUniteParCommande = [];
$boissonMap = [];
$boissonsParCommande = [];
foreach ($bM->getAll() as $b) $boissonMap[$b['boisson_id']] = $b;
foreach ($commandes as $c) {
    $oid = $c['order_id'];
    $mrows = $cmM->getMenusByCommande($oid);
    foreach ($mrows as &$r) $r['menu_nom'] = $mM->get($r['menu_id'])['menu_nom'];
    $menusParCommande[$oid] = $mrows;
    $prows = $cpM->getProduitsByCommande($oid);
    foreach ($prows as &$r) $r['product_nom'] = $pM->get($r['product_id'])['product_nom'];
    $produitsParCommande[$oid] = $prows;
    $boissonsUniteParCommande[$oid] = $cbM->getByCommande($oid);
    $boissonsParCommande[$oid] = $c['boisson_id'] ? $bM->get($c['boisson_id']) : null;
}
require __DIR__ . '/../view/commande_list.php';
