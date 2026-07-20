-- =====================================================================
-- base.sql
-- Mobile Money - Version 1
-- Script unique de création de la base SQLite : tables, vues, données.
-- À exécuter avec : sqlite3 writable/mobile_money.db < base.sql
-- =====================================================================

PRAGMA foreign_keys = ON;

-- ---------------------------------------------------------------------
-- 1. TABLES
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS baremes_frais;
DROP TABLE IF EXISTS clients;
DROP TABLE IF EXISTS prefixes;

CREATE TABLE prefixes (
    id      INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe VARCHAR(3) NOT NULL UNIQUE
);

CREATE TABLE clients (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    telephone     VARCHAR(15) NOT NULL UNIQUE,
    date_creation DATETIME NOT NULL
);

CREATE TABLE baremes_frais (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation  VARCHAR(20) NOT NULL,      -- 'retrait' ou 'transfert'
    montant_min     DECIMAL(15,2) NOT NULL,
    montant_max     DECIMAL(15,2) NOT NULL,
    frais           DECIMAL(15,2) NOT NULL
);

CREATE TABLE transactions (
    id                INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation    VARCHAR(20) NOT NULL,    -- 'depot', 'retrait', 'transfert'
    expediteur_id     INTEGER NULL,            -- NULL si depot
    destinataire_id   INTEGER NULL,            -- NULL si retrait
    montant           DECIMAL(15,2) NOT NULL,
    frais             DECIMAL(15,2) NOT NULL DEFAULT 0,
    date_transaction  DATETIME NOT NULL,
    FOREIGN KEY (expediteur_id)   REFERENCES clients(id),
    FOREIGN KEY (destinataire_id) REFERENCES clients(id)
);

CREATE INDEX idx_transactions_expediteur   ON transactions(expediteur_id);
CREATE INDEX idx_transactions_destinataire ON transactions(destinataire_id);

-- ---------------------------------------------------------------------
-- 2. VUES
-- ---------------------------------------------------------------------

-- Solde dynamique de chaque client :
-- Solde = (depots + transferts reçus) - (retraits + frais retrait + transferts envoyés + frais transfert)
DROP VIEW IF EXISTS v_soldes_clients;
CREATE VIEW v_soldes_clients AS
SELECT
    c.id             AS client_id,
    c.telephone      AS telephone,
    c.date_creation  AS date_creation,
    (
        COALESCE((SELECT SUM(t.montant) FROM transactions t WHERE t.type_operation = 'depot'     AND t.destinataire_id = c.id), 0)
      + COALESCE((SELECT SUM(t.montant) FROM transactions t WHERE t.type_operation = 'transfert' AND t.destinataire_id = c.id), 0)
      - COALESCE((SELECT SUM(t.montant) FROM transactions t WHERE t.type_operation = 'retrait'   AND t.expediteur_id   = c.id), 0)
      - COALESCE((SELECT SUM(t.frais)   FROM transactions t WHERE t.type_operation = 'retrait'   AND t.expediteur_id   = c.id), 0)
      - COALESCE((SELECT SUM(t.montant) FROM transactions t WHERE t.type_operation = 'transfert' AND t.expediteur_id   = c.id), 0)
      - COALESCE((SELECT SUM(t.frais)   FROM transactions t WHERE t.type_operation = 'transfert' AND t.expediteur_id   = c.id), 0)
    ) AS solde
FROM clients c;

-- Historique des transactions avec le numéro de téléphone des deux parties
DROP VIEW IF EXISTS v_historique_transactions;
CREATE VIEW v_historique_transactions AS
SELECT
    t.id,
    t.type_operation,
    t.montant,
    t.frais,
    t.date_transaction,
    t.expediteur_id,
    ce.telephone AS telephone_expediteur,
    t.destinataire_id,
    cd.telephone AS telephone_destinataire
FROM transactions t
LEFT JOIN clients ce ON ce.id = t.expediteur_id
LEFT JOIN clients cd ON cd.id = t.destinataire_id;

-- Gain global cumulé de l'opérateur (somme de tous les frais perçus)
DROP VIEW IF EXISTS v_gain_operateur;
CREATE VIEW v_gain_operateur AS
SELECT COALESCE(SUM(frais), 0) AS gain_total
FROM transactions;

-- ---------------------------------------------------------------------
-- 3. DONNÉES INITIALES
-- ---------------------------------------------------------------------

-- Préfixes autorisés
INSERT INTO prefixes (prefixe) VALUES
    ('032'),
    ('033'),
    ('034'),
    ('037'),
    ('038');

-- Barèmes de frais - RETRAITS
INSERT INTO baremes_frais (type_operation, montant_min, montant_max, frais) VALUES
    ('retrait', 0,      5000,      100),
    ('retrait', 5001,   20000,     300),
    ('retrait', 20001,  50000,     600),
    ('retrait', 50001,  100000,    1000),
    ('retrait', 100001, 999999999, 1500);

-- Barèmes de frais - TRANSFERTS
INSERT INTO baremes_frais (type_operation, montant_min, montant_max, frais) VALUES
    ('transfert', 0,      5000,      50),
    ('transfert', 5001,   20000,     150),
    ('transfert', 20001,  50000,     300),
    ('transfert', 50001,  100000,    500),
    ('transfert', 100001, 999999999, 800);
