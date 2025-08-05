# Conformité RGPD

## 1. Consentement
- Champ `user_consentement` (datetime) enregistre la date/heure du premier login.
- Affichage d’une mention de collecte des données sur la page d’inscription/connexion.

## 2. Droit d’accès et de suppression
- **URL** : `POST /index.php?section=utilisateur&action=delete&id=<user_id>`
- **Rôles** :  
  - Administrateur peut supprimer n’importe quel compte.  
  - Utilisateur peut supprimer son propre compte.
- **Comportement** :  
  - Suppression physique ou anonymisation (remplace email et nom par `anonyme_<id>`).

## 3. Conservation des données
- Les données anonymisées sont conservées pour l’audit, aucune information personnelle identifiable n’est gardée.

