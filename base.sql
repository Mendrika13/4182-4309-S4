
PRAGMA foreign_keys = ON;


DROP TABLE IF EXISTS credits_frais_retrait;
DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS baremes_frais;
DROP TABLE IF EXISTS clients;
DROP TABLE IF EXISTS prefixes_externes;
DROP TABLE IF EXISTS commission_externe;
DROP TABLE IF EXISTS autres_operateurs;
DROP TABLE IF EXISTS prefixes;

CREATE TABLE prefixes (
    id      INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe VARCHAR(3) NOT NULL UNIQUE
);

CREATE TABLE autres_operateurs (
    id   INTEGER PRIMARY KEY AUTOINCREMENT,
    nom  VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE prefixes_externes (
    id                 INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe            VARCHAR(3) NOT NULL UNIQUE,
    autre_operateur_id INTEGER NOT NULL,
    FOREIGN KEY (autre_operateur_id) REFERENCES autres_operateurs(id)
);

CREATE TABLE commission_externe (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    pourcentage DECIMAL(5,2) NOT NULL
);

INSERT INTO commission_externe (pourcentage) VALUES (2.00);

CREATE TABLE clients (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    telephone     VARCHAR(15) NOT NULL UNIQUE,
    date_creation DATETIME NOT NULL
);

CREATE TABLE baremes_frais (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation  VARCHAR(20) NOT NULL,      
    montant_min     DECIMAL(15,2) NOT NULL,
    montant_max     DECIMAL(15,2) NOT NULL,
    frais           DECIMAL(15,2) NOT NULL
);

CREATE TABLE transactions (
    id                INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation    VARCHAR(20) NOT NULL, 
    expediteur_id     INTEGER NULL,          
    destinataire_id   INTEGER NULL,           
    montant           DECIMAL(15,2) NOT NULL,
    frais             DECIMAL(15,2) NOT NULL DEFAULT 0,
    date_transaction  DATETIME NOT NULL,
    est_externe       BOOLEAN NOT NULL DEFAULT 0,
    autre_operateur_id INTEGER NULL REFERENCES autres_operateurs(id),
    commission        DECIMAL(15,2) NOT NULL DEFAULT 0,
    lot_id            VARCHAR(36) NULL,
    FOREIGN KEY (expediteur_id)   REFERENCES clients(id),
    FOREIGN KEY (destinataire_id) REFERENCES clients(id)
);

CREATE TABLE credits_frais_retrait (
    id                     INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id              INTEGER NOT NULL,
    transaction_origine_id INTEGER NOT NULL,
    montant_credit         DECIMAL(15,2) NOT NULL,
    utilise                BOOLEAN NOT NULL DEFAULT 0,
    transaction_retrait_id INTEGER NULL,
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (transaction_origine_id) REFERENCES transactions(id),
    FOREIGN KEY (transaction_retrait_id) REFERENCES transactions(id)
);

CREATE INDEX idx_transactions_expediteur   ON transactions(expediteur_id);
CREATE INDEX idx_transactions_destinataire ON transactions(destinataire_id);



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
      - COALESCE((SELECT SUM(t.frais + t.commission) FROM transactions t WHERE t.type_operation = 'transfert' AND t.expediteur_id   = c.id), 0)
    ) AS solde
FROM clients c;

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
    cd.telephone AS telephone_destinataire,
    t.est_externe,
    t.autre_operateur_id,
    t.commission,
    t.lot_id
FROM transactions t
LEFT JOIN clients ce ON ce.id = t.expediteur_id
LEFT JOIN clients cd ON cd.id = t.destinataire_id;

DROP VIEW IF EXISTS v_gain_operateur;
CREATE VIEW v_gain_operateur AS
SELECT
    COALESCE(SUM(CASE WHEN est_externe = 0 THEN frais ELSE 0 END), 0) AS gain_interne,
    COALESCE(SUM(CASE WHEN est_externe = 1 THEN frais + commission ELSE 0 END), 0) AS gain_externe
FROM transactions;

DROP VIEW IF EXISTS v_montants_a_envoyer;
CREATE VIEW v_montants_a_envoyer AS
SELECT
    ao.id  AS autre_operateur_id,
    ao.nom AS operateur,
    COALESCE(SUM(t.montant), 0) AS montant_a_envoyer,
    COUNT(t.id) AS nb_transferts
FROM autres_operateurs ao
LEFT JOIN transactions t ON t.autre_operateur_id = ao.id AND t.type_operation = 'transfert'
GROUP BY ao.id, ao.nom;



INSERT INTO prefixes (prefixe) VALUES
    ('032'),
    ('033'),
    ('034'),
    ('037'),
    ('038');

INSERT INTO baremes_frais (type_operation, montant_min, montant_max, frais) VALUES
    ('retrait', 0,      5000,      100),
    ('retrait', 5001,   20000,     300),
    ('retrait', 20001,  50000,     600),
    ('retrait', 50001,  100000,    1000),
    ('retrait', 100001, 999999999, 1500);

INSERT INTO baremes_frais (type_operation, montant_min, montant_max, frais) VALUES
    ('transfert', 0,      5000,      50),
    ('transfert', 5001,   20000,     150),
    ('transfert', 20001,  50000,     300),
    ('transfert', 50001,  100000,    500),
    ('transfert', 100001, 999999999, 800);
