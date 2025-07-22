<?php
// public/api.php
require_once __DIR__ . '/../lib/Auth.php';
require_once __DIR__ . '/../model/Utilisateur.php';
require_once __DIR__ . '/../model/Menu.php';
require_once __DIR__ . '/../model/Produit.php';
require_once __DIR__ . '/../model/Commande.php';
require_once __DIR__ . '/../model/CommandeMenu.php';
require_once __DIR__ . '/../model/CommandeProduit.php';
require_once __DIR__ . '/../model/Boisson.php';

header('Content-Type: application/json; charset=utf-8');

$method   = $_SERVER['REQUEST_METHOD'];
$resource = $_GET['r'] ?? '';

try {
    switch ("$method $resource") {
        // --------------------
        // 1) LOGIN → retourne JSON {success, user, csrf}
        // --------------------
        case 'POST login':
            $body = json_decode(file_get_contents('php://input'), true);
            $uM   = new Utilisateur();
            if (
                !empty($body['email']) &&
                !empty($body['password']) &&
                ($user = $uM->findByEmail(trim($body['email']))) &&
                password_verify($body['password'], $user['user_password'])
            ) {
                session_start();
                $_SESSION['user'] = [
                    'id'      => $user['user_id'],
                    'role_id' => $user['role_id'],
                    'name'    => $user['user_prenom'].' '.$user['user_nom'],
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

        // --------------------
        // 2) LISTE DES MENUS
        // --------------------
        case 'GET menus':
            Auth::check([1,2,3]);
            $mM = new Menu();
            echo json_encode($mM->getAll());
            break;

        // --------------------
        // 3) LISTE DES PRODUITS (optionnel filter)
        // --------------------
        case 'GET produits':
            Auth::check([1,2,3]);
            $pM   = new Produit();
            $cat  = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
            $list = $pM->getAll($cat);
            echo json_encode($list);
            break;

        // --------------------
        // 4) LISTE DES COMMANDES
        // --------------------
        case 'GET commandes':
            Auth::check([1,2,3]);
            $cM  = new Commande();
            $cmM = new CommandeMenu();
            $cpM = new CommandeProduit();
            $mM  = new Menu();
            $pM  = new Produit();
            $bM  = new Boisson();

            $orders = $cM->getAll();
            $result = [];
            foreach ($orders as $o) {
                $id = $o['order_id'];
                // Récupère les menus liés
                $menus = $cmM->getMenusByCommande($id);
                $o['menus'] = [];
                foreach ($menus as $r) {
                    $menu = $mM->get($r['menu_id']);
                    $o['menus'][] = [
                        'menu' => $menu,
                        'qty'  => $r['order_menu_quantite'],
                    ];
                }
                // Récupère les produits liés
                $prods = $cpM->getProduitsByCommande($id);
                $o['produits'] = [];
                foreach ($prods as $r) {
                    $prod = $pM->get($r['product_id']);
                    $o['produits'][] = [
                        'product' => $prod,
                        'qty'     => $r['order_product_quantite'],
                    ];
                }
                // Récupère la boisson
                $o['boisson'] = $o['boisson_id']
                    ? $bM->get($o['boisson_id'])
                    : null;

                $result[] = $o;
            }

            echo json_encode($result);
            break;

        // --------------------
        // 5) CRÉER UNE COMMANDE
        // --------------------
        case 'POST commandes':
            Auth::check([1,3]);
            $in = json_decode(file_get_contents('php://input'), true);

            // Validation minimale
            if (
                empty($in['date']) ||
                empty($in['statut']) ||
                empty($in['ticket']) ||
                empty($in['user_id'])
            ) {
                http_response_code(422);
                echo json_encode(['error' => 'Champs obligatoires manquants']);
                break;
            }

            // Insertion
            $cM = new Commande();
            $ok = $cM->add(
                $in['date'],
                $in['heure']       ?? null,
                $in['statut'],
                $in['ticket'],
                (int)$in['user_id'],
                isset($in['boisson_id']) ? (int)$in['boisson_id'] : null
            );
            $id = $ok ? $cM->getLastInsertId() : null;

            if (! $id) {
                http_response_code(500);
                echo json_encode(['error'=>'Impossible de créer la commande']);
                break;
            }

            // Liaisons menus
            $cm = new CommandeMenu();
            foreach ($in['menus'] ?? [] as $mid => $q) {
                if ((int)$q > 0) {
                    $cm->add($id, (int)$mid, (int)$q);
                }
            }
            // Liaisons produits
            $cp = new CommandeProduit();
            foreach ($in['produits'] ?? [] as $pid => $q) {
                if ((int)$q > 0) {
                    $cp->add($id, (int)$pid, (int)$q);
                }
            }

            http_response_code(201);
            echo json_encode(['success'=>true,'id'=>$id]);
            break;

        default:
            http_response_code(404);
            echo json_encode(['error'=>'Ressource non trouvée']);
    }
}
catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['error'=>$e->getMessage()]);
}
