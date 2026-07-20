# Guide d'intégration - Mobile Money V1

Ce paquet contient uniquement les fichiers **nouveaux** ou **à ajouter**.
Voici les 3 modifications à faire dans des fichiers **existants** de votre
projet CodeIgniter 4, avant de copier les dossiers `app/`.

---

## 1. Copier les fichiers

Copiez tout le contenu de ce paquet dans la racine de votre projet CI4 en
respectant l'arborescence (le dossier `app/` fusionne avec le vôtre) :

```
app/Database/Migrations/*.php
app/Database/Seeds/*.php
app/Models/*.php
app/Controllers/*.php
app/Filters/*.php
app/Views/auth/login.php
app/Views/client/dashboard.php
app/Views/operateur/login.php
app/Views/operateur/dashboard.php
app/Config/Routes.php   <-- remplace le fichier existant (ou fusionnez si vous avez déjà des routes)
```

---

## 2. Configurer SQLite (.env)

Dans votre fichier `.env` (copiez `env` en `.env` si ce n'est pas déjà fait),
décommentez/modifiez ces lignes :

```ini
database.default.DBDriver = SQLite3
database.default.database = mobile_money.db
database.default.DBPrefix =
```

Le fichier `writable/mobile_money.db` sera créé automatiquement lors de la
première migration (assurez-vous que le dossier `writable/` est accessible
en écriture).

Ajoutez également le mot de passe opérateur (Version 1, mécanisme simple) :

```ini
OPERATEUR_PASSWORD = admin123
```

---

## 3. Enregistrer les filtres (app/Config/Filters.php)

Ouvrez `app/Config/Filters.php` et ajoutez les deux alias dans la propriété
`$aliases` :

```php
public array $aliases = [
    // ... alias existants ...
    'clientAuth'    => \App\Filters\ClientAuthFilter::class,
    'operateurAuth' => \App\Filters\OperateurAuthFilter::class,
];
```

(Les filtres sont ensuite appliqués automatiquement via les groupes de
routes définis dans `Routes.php` — aucune autre configuration n'est
nécessaire.)

---

## 4. Créer la base de données

**Un fichier unique `base.sql` est fourni à la racine du projet.** Il
contient l'intégralité des scripts de création des 4 tables, des 3 vues
SQL (`v_soldes_clients`, `v_historique_transactions`, `v_gain_operateur`)
et des données initiales (préfixes + barèmes de frais). C'est la source de
vérité du schéma pour la soutenance/le rendu.

Pour l'exécuter directement sur le fichier SQLite du projet :

```bash
sqlite3 writable/mobile_money.db < base.sql
```

(Adaptez le chemin `writable/mobile_money.db` à celui déclaré dans votre
`.env`, cf. section 2. Le script fait des `DROP TABLE IF EXISTS` /
`DROP VIEW IF EXISTS` avant chaque création : il peut être rejoué sans
risque pour repartir d'une base propre.)

**Alternative (facultative) via les migrations CI4 :** les migrations et
seeders fournis dans `app/Database/` reproduisent exactement le même
schéma et les mêmes données, si vous préférez la commande `php spark` :

```bash
php spark migrate
php spark db:seed DatabaseSeeder
```

N'utilisez qu'une seule des deux méthodes pour éviter les conflits.

---

## 5. Tester

- `http://localhost:8080/login` → connexion client (ex: `0331234567`)
- `http://localhost:8080/operateur/login` → connexion opérateur
  (mot de passe : `admin123` ou celui défini dans `.env`)

---

## Résumé de la logique métier implémentée

- **Login client automatique** : préfixe vérifié dans `prefixes`, création
  auto du compte si le numéro est inconnu (`ClientModel::trouverOuCreer`).
- **Solde dynamique** (jamais stocké en base, toujours recalculé) :
  `ClientModel::getSolde()`.
- **Frais** : dépôt gratuit ; retrait/transfert selon tranche de
  `baremes_frais` (`BaremeFraisModel::getFraisApplicable`).
- **Validation** : contrôle `solde >= montant + frais` avant tout retrait
  ou transfert (`ClientController::retrait` / `transfert`).
- **Vue opérateur** : gain global = somme de tous les `frais` de la table
  `transactions` (`TransactionModel::getGainTotalOperateur`), liste des
  clients avec soldes (`ClientModel::getTousAvecSolde`), gestion CRUD des
  préfixes.
