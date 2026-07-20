<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Money - Mon compte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <span class="navbar-brand fw-bold"> Mobile Money</span>
        <div class="d-flex align-items-center">
            <span class="text-white me-3"><?= esc($client['telephone']) ?></span>
            <a href="<?= base_url('logout') ?>" class="btn btn-outline-light btn-sm">Déconnexion</a>
        </div>
    </div>
</nav>

<div class="container my-4">

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success" role="alert">
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    
    <div class="card shadow-sm border-0 mb-4 bg-primary text-white">
        <div class="card-body text-center py-4">
            <p class="mb-1">Solde actuel</p>
            <h1 class="display-4 fw-bold mb-0"><?= number_format($solde, 0, ',', ' ') ?> Ar</h1>
            <small>Numéro : <?= esc($client['telephone']) ?></small>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-success">️ Dépôt</h5>
                    <p class="text-muted small">Gratuit, sans frais.</p>
                    <form action="<?= base_url('client/depot') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">Montant (Ar)</label>
                            <input type="number" name="montant" class="form-control" min="1" step="1" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Déposer</button>
                    </form>
                </div>
            </div>
        </div>

        
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-danger">️ Retrait</h5>
                    <p class="text-muted small">Frais selon barème en vigueur (offerts si crédit prépayé disponible).</p>
                    <form action="<?= base_url('client/retrait') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">Montant (Ar)</label>
                            <input type="number" name="montant" class="form-control" min="1" step="1" required>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Retirer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-primary">️ Transfert Unique</h5>
                    <p class="text-muted small">Vers un autre numéro Mobile Money (interne ou externe).</p>
                    <form action="<?= base_url('client/transfert') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-2">
                            <label class="form-label">Numéro destinataire</label>
                            <input type="text" name="telephone_destinataire" class="form-control" placeholder="0331234567" maxlength="10" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Montant (Ar)</label>
                            <input type="number" name="montant" class="form-control" min="1" step="1" required>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="inclure_frais_retrait" id="inclureFraisRetrait" value="1">
                            <label class="form-check-label small text-muted" for="inclureFraisRetrait">
                                Inclure les frais de retrait (Interne uniquement)
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Transférer</button>
                    </form>
                </div>
            </div>
        </div>

        
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-dark"> Envoi Multiple</h5>
                    <p class="text-muted small">Transférer un montant divisé équitablement entre plusieurs numéros internes.</p>
                    <form action="<?= base_url('client/transfert-multiple') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-2">
                            <label class="form-label">Numéros destinataires (séparés par espace, virgule ou point-virgule)</label>
                            <textarea name="telephones" class="form-control" rows="2" placeholder="0331234567, 0337654321" required></textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Montant total global (Ar)</label>
                            <input type="number" name="montant_total" class="form-control" min="1" step="1" required>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="inclure_frais_retrait" id="inclureFraisRetraitMultiple" value="1">
                            <label class="form-check-label small text-muted" for="inclureFraisRetraitMultiple">
                                Inclure les frais de retrait pour chaque numéro
                            </label>
                        </div>
                        <button type="submit" class="btn btn-dark w-100">Envoyer à tous</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h5 class="mb-0">Historique des transactions</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Détail</th>
                        <th class="text-end">Montant</th>
                        <th class="text-end">Frais</th>
                        <th class="text-end">Impact sur le solde</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($historique)) : ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Aucune transaction pour le moment.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($historique as $t) : ?>
                        <?php
                            $estCredit = false;
                            $detail    = '';
                            $badge     = 'secondary';

                            if ($t['type_operation'] === 'depot') {
                                $estCredit = true;
                                $detail    = 'Dépôt sur votre compte';
                                $badge     = 'success';
                            } elseif ($t['type_operation'] === 'retrait') {
                                $estCredit = false;
                                $detail    = 'Retrait sur votre compte';
                                $badge     = 'danger';
                            } elseif ($t['type_operation'] === 'transfert') {
                                $badge = 'primary';
                                if ((int) $t['expediteur_id'] === (int) $client['id']) {
                                    $estCredit = false;
                                    $detail    = 'Transfert envoyé vers ' . esc($t['telephone_destinataire']);
                                    if ($t['est_externe'] == 1) {
                                        $detail .= ' (' . esc($t['autre_operateur_nom'] ?? 'Externe') . ')';
                                    }
                                    if ($t['lot_id']) {
                                        $detail .= ' <span class="badge bg-secondary font-monospace" style="font-size: 0.7rem;">Envoi Multiple</span>';
                                    }
                                } else {
                                    $estCredit = true;
                                    $detail    = 'Transfert reçu de ' . esc($t['telephone_expediteur']);
                                    if ($t['lot_id']) {
                                        $detail .= ' <span class="badge bg-secondary font-monospace" style="font-size: 0.7rem;">Envoi Multiple</span>';
                                    }
                                }
                            }

                            $impact = $estCredit
                                ? '+' . number_format($t['montant'], 0, ',', ' ')
                                : '-' . number_format($t['montant'] + $t['frais'] + $t['commission'], 0, ',', ' ');
                        ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($t['date_transaction'])) ?></td>
                            <td><span class="badge bg-<?= $badge ?>"><?= ucfirst($t['type_operation']) ?></span></td>
                            <td><?= $detail ?></td>
                            <td class="text-end"><?= number_format($t['montant'], 0, ',', ' ') ?> Ar</td>
                            <td class="text-end">
                                <?= number_format($t['frais'], 0, ',', ' ') ?> Ar
                                <?php if ($t['commission'] > 0) : ?>
                                    <br><small class="text-muted text-nowrap">+ comm. : <?= number_format($t['commission'], 0, ',', ' ') ?> Ar</small>
                                <?php endif; ?>
                            </td>
                            <td class="text-end fw-bold <?= $estCredit ? 'text-success' : 'text-danger' ?>">
                                <?= $impact ?> Ar
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
