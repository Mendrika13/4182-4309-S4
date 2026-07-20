# Suivi des travaux - Projet Mobile Money

Ce fichier liste les travaux effectués par chaque étudiant, pour chaque livraison du projet.

## Livraison 1

Étudiant 4182 : tout le front.
- Intégration du design du template TemplateMo 622 Clearwave (tokens de couleurs, typographie, boutons, cartes) adapté à l'application.
- Réalisation des 4 pages : connexion client, connexion opérateur, tableau de bord client, tableau de bord opérateur.
- Formulaires : dépôt, retrait, transfert, ajout de préfixe, suppression de préfixe.
- Feuille de style `public/assets/css/app.css` et script `public/assets/js/app.js`.

Étudiant 4309 : tout le back.
- Structure du projet en PHP pur, sans framework, connectée uniquement à SQLite via PDO.
- Point d'entrée unique `public/index.php` et routeur (`app/Core/Router.php`).
- Gestion de session et des messages flash (`app/Core/Session.php`), jetons CSRF.
- Connexion et création automatique de la base SQLite à partir de `base.sql` (`app/Core/Database.php`).
- Modèles : `ClientModel`, `TransactionModel`, `PrefixeModel`, `BaremeFraisModel`.
- Contrôleurs : `AuthController`, `ClientController`, `OperateurController`.
- Script unique `base.sql` à la racine du projet : tables (`clients`, `prefixes`, `baremes_frais`, `transactions`), vues (`v_soldes_clients`, `v_historique_transactions`, `v_gain_operateur`) et données initiales (préfixes autorisés, barèmes de frais).
- Logique métier : login automatique par numéro de téléphone, calcul dynamique du solde, application des frais selon barème, vérification du solde avant retrait/transfert, gestion des préfixes autorisés côté opérateur.
