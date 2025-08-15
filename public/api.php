<?php
session_start();

// Lecture centralisée des données JSON
$input = json_decode(file_get_contents('php://input'), true) ?: [];

require_once __DIR__ . '/../lib/Auth.php';
require_once __DIR__ . '/../lib/Database.php';
require_once __DIR__ . '/../model/Utilisateur.php';
require_once __DIR__ . '/../model/Menu.php';
require_once __DIR__ . '/../model/Produit.php';
require_once __DIR__ . '/../model/Commande.php';
require_once __DIR__ . '/../model/CommandeMenu.php';
require_once __DIR__ . '/../model/CommandeProduit.php';
require_once __DIR__ . '/../model/Boisson.php';
require_once __DIR__ . '/../model/CommandeBoisson.php';

header('Content-Type: application/json; charset=utf-8');

$method   = $_SERVER['REQUEST_METHOD'];
$resource = $_GET['r'] ?? '';
$action   = $_GET['action'] ?? '';     // “prepared” ou “delivered”
$id       = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    switch ("$method $resource") {
        // 1) LOGIN
        case 'POST login':
            $email    = trim($input['email']    ?? '');
            $password = $input['password']      ?? '';
            $uM       = new Utilisateur();

            if (
                $email && $password && ($user = $uM->findByEmail($email))
                && password_verify($password, $user['user_password'])
            ) {
                $_SESSION['user'] = [
                    'id'      => $user['user_id'],
                    'role_id' => $user['role_id'],
                    'name'    => $user['user_prenom'] . ' ' . $user['user_nom'],
                ];
                $_SESSION['csrf'] = bin2hex(random_bytes(16));

                echo json_encode([
                    'success' => true,
                    'user'    => $_SESSION['user'],
                    'csrf'    => $_SESSION['csrf'],
                ]);
            } else {
                http_response_code(401);
                echo json_encode(['error' => 'Identifiants invalides']);
            }
            break;

        // 2) GET menus (public ↔ roles 1,2,3)
        case 'GET menus':
            Auth::check([1, 2, 3]);
            echo json_encode((new Menu())->getAll());
            break;

        // 3) GET produits (public ↔ roles 1,2,3)
        case 'GET produits':
            Auth::check([1, 2, 3]);
            $cat = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
            echo json_encode((new Produit())->getAll($cat));
            break;

        // 4) GET commandes (roles 1,2,3)
        case 'GET commandes':
            Auth::check([1, 2, 3]);
            $cM  = new Commande();
            $cmM = new CommandeMenu();
            $cpM = new CommandeProduit();
            $mM  = new Menu();
            $pM  = new Produit();
            $bM  = new Boisson();

            $result = [];
            foreach ($cM->getAll() as $o) {
                $orderId = $o['order_id'];

                // Menus
                $o['menus'] = array_map(fn($r) => [
                    'menu' => $mM->get($r['menu_id']),
                    'qty'  => $r['order_menu_quantite'],
                ], $cmM->getMenusByCommande($orderId));

                // Produits
                $o['produits'] = array_map(fn($r) => [
                    'product' => $pM->get($r['product_id']),
                    'qty'     => $r['order_product_quantite'],
                ], $cpM->getProduitsByCommande($orderId));

                // Boisson
                $o['boisson'] = $o['boisson_id']
                    ? $bM->get($o['boisson_id'])
                    : null;

                // Boissons additionnelles
                $cbM = new CommandeBoisson();
                $liste = $cbM->getBoissonsByCommande($orderId);
                $o['boissons_additionnelles'] = array_map(fn($r) => [
                    'boisson'  => $bM->get($r['boisson_id']),
                    'quantity' => (int)$r['order_boisson_quantite'],
                ], $liste);

                $result[] = $o;
            }

            // Récupère les menus liés
            $menusRaw = $cmM->getMenusByCommande($orderId);
            $o['menus'] = [];
            foreach ($menusRaw as $r) {
                $menuId = $r['menu_id'];
                $menu   = $mM->get($menuId);

                // On enrichit le menu avec ses produits
                $produitsMenu = $mM->getProduitsByMenu($menuId);
                $menu['produits'] = array_map(fn($pm) => [
                    'product' => [
                        'id'          => $pm['product_id'],
                        'nom'         => $pm['product_nom'],
                        'description' => $pm['product_description'],
                        'prix'        => $pm['product_prix'],
                    ],
                ], $produitsMenu);

                $o['menus'][] = [
                    'menu' => $menu,
                    'qty'  => $r['order_menu_quantite'],
                ];
            }

            echo json_encode($result);
            break;

        // 5) POST commandes (roles 1,3)
        case 'POST commandes':
            Auth::check([1, 3]);

            // Champs obligatoires
            foreach (['date', 'statut', 'ticket', 'user_id'] as $field) {
                if (empty($input[$field])) {
                    http_response_code(422);
                    echo json_encode(['error' => 'Champ manquant : ' . $field]);
                    exit;
                }
            }

            $cM = new Commande();
            $ok = $cM->add(
                $input['date'],
                $input['heure']    ?? null,
                $input['statut'],
                $input['ticket'],
                (int)$input['user_id'],
                isset($input['boisson_id']) ? (int)$input['boisson_id'] : null
            );
            $orderId = $ok ? $cM->getLastInsertId() : null;

            if (! $orderId) {
                http_response_code(500);
                echo json_encode(['error' => 'Impossible de créer la commande']);
                exit;
            }

            // Liaisons menus
            foreach ($input['menus'] ?? [] as $mid => $qty) {
                if ((int)$qty > 0) {
                    (new CommandeMenu())->add($orderId, (int)$mid, (int)$qty);
                }
            }

            // Liaisons produits
            foreach ($input['produits'] ?? [] as $pid => $qty) {
                if ((int)$qty > 0) {
                    (new CommandeProduit())->add($orderId, (int)$pid, (int)$qty);
                }
            }

            // Liaisons boissons additionnelles
            $cbM = new CommandeBoisson();
            foreach ($input['boissons'] ?? [] as $boissonId => $qty) {
                if ((int)$qty > 0) {
                    $cbM->add($orderId, (int)$boissonId, (int)$qty);
                }
            }

            http_response_code(201);
            echo json_encode(['success' => true, 'id' => $orderId]);
            break;

        // 6) PUT commandes (roles 2, then 2|3)
        case 'PUT commandes':
            if (! $id || ! in_array($action, ['prepared', 'delivered'], true)) {
                http_response_code(400);
                echo json_encode(['error' => 'Paramètres id ou action invalides']);
                break;
            }

            if ($action === 'prepared') {
                Auth::check([2]);
                $newStatus = 'prepared';
            } else {
                Auth::check([2, 3]);
                $newStatus = 'delivered';
            }

            $cM = new Commande();
            $updated = $cM->updateStatus($id, $newStatus);

            if ($updated) {
                echo json_encode(['success' => true, 'id' => $id, 'status' => $newStatus]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Commande introuvable']);
            }
            break;

        // Default – ressource non trouvée
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Ressource non trouvée']);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
