

ETU-4309
Rôle principal : Architecture Base de données, Modèles & Logique Métier (Back-End)

- [ ] **Configuration de l'environnement :** Initialisation du framework CodeIgniter 4 et configuration du fichier `.env` pour l'intégration de la base SQLite embarquée.
- [ ] **Modélisation & Script SQL :** Création du fichier unique `base.sql` à la racine contenant les schémas des tables (`clients`, `transactions`, `prefixes`, `baremes_frais`).
- [ ] **Moteur d'Authentification :** Développement de la logique du contrôleur pour le login automatique (vérification du numéro de téléphone via les préfixes valides de l'opérateur et création automatique du compte à la volée s'il s'agit d'un nouveau client).
- [ ] **Logique des Opérations (Algorithmes) :** 
  - [ ] Écriture du script de calcul du solde dynamique d'un client (Somme des dépôts - Somme des retraits - Somme des transferts envoyés - Somme des frais).
  - [ ] Développement du calcul automatique des frais par tranche (recherche dans la table des barèmes selon le montant et le type d'opération).
- [ ] **Logique Opérateur :** Développement des requêtes de calcul des gains de l'opérateur (somme de tous les frais retenus) et de l'extraction de la situation globale des comptes clients.

---

ETU-4182
Rôle principal : Design d'Interface, Intégration UX/UI & Vues (Front-End)

- [ ] **Structure Front-End Globale :** Intégration de Bootstrap (via CDN ou local) et création du Layout de base (Header, Pied de page, gestion des messages Flash d'erreur ou de succès).
- [ ] **Interfaces Espace Client :**
  - [ ] Création de la page de connexion épurée (simple input pour le numéro de téléphone).
  - [ ] Design du Tableau de Bord Client affichant de manière claire le solde actuel et l'historique des transactions (Date, Type, Montant, Frais, Destinataire/Expéditeur).
  - [ ] Intégration des formulaires d'action rapides (Dépôt automatique, Retrait automatique, Transfert de fonds).
- [ ] **Interfaces Espace Opérateur :**
  - [ ] Design de la page de configuration des préfixes valides (ex: 033, 037) et du tableau modifiable des barèmes de frais par tranche.
  - [ ] Création du tableau de bord de situation : Affichage du gain total de l'opérateur et liste complète des comptes clients avec leurs soldes respectifs.
- [ ] **Récette & Validation :** Tests d'intégration bout en bout avec l'Étudiant A pour valider le comportement visuel lors d'un transfert (validation des soldes mis à jour en temps réel).

