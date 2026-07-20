<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Money - Espace Opérateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand fw-bold"> Espace Opérateur</span>
        <a href="<?= base_url('operateur/logout') ?>" class="btn btn-outline-light btn-sm">Déconnexion</a>
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

    
    <div class="row mb-4 g-3">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 bg-dark text-white h-100">
                <div class="card-body text-center py-4">
                    <p class="mb-1 text-muted">Gain Opérateur (Interne)</p>
                    <h2 class="fw-bold mb-0"><?= number_format($gainInterne, 0, ',', ' ') ?> Ar</h2>
                    <small class="text-muted">Frais sur les transactions internes (retraits + transferts)</small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0 bg-secondary text-white h-100">
                <div class="card-body text-center py-4">
                    <p class="mb-1 text-light">Gain Opérateur (Externe)</p>
                    <h2 class="fw-bold mb-0"><?= number_format($gainExterne, 0, ',', ' ') ?> Ar</h2>
                    <small class="text-light">Frais + Commissions sur transferts externes</small>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body py-3">
            <form action="<?= base_url('operateur/commission/modifier') ?>" method="post" class="row align-items-center g-3">
                <?= csrf_field() ?>
                <div class="col-auto">
                    <label class="form-label mb-0 fw-bold">️ Commission sur transfert externe :</label>
                </div>
                <div class="col-auto">
                    <div class="input-group input-group-sm" style="max-width: 150px;">
                        <input type="number" step="0.01" min="0" max="100" name="pourcentage" class="form-control" value="<?= esc($commission) ?>" required>
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4 mb-4">

        
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Comptes clients</h5>
                    <span class="badge bg-secondary"><?= count($clients) ?> compte(s)</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Téléphone</th>
                                <th>Date de création</th>
                                <th class="text-end">Solde</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($clients)) : ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Aucun client enregistré.</td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($clients as $c) : ?>
                                <tr>
                                    <td><?= (int) $c['id'] ?></td>
                                    <td><?= esc($c['telephone']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($c['date_creation'])) ?></td>
                                    <td class="text-end fw-bold text-primary">
                                        <?= number_format($c['solde'], 0, ',', ' ') ?> Ar
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Préfixes autorisés (Interne)</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('operateur/prefixe/ajouter') ?>" method="post" class="d-flex gap-2 mb-3">
                        <?= csrf_field() ?>
                        <input type="text" name="prefixe" class="form-control" placeholder="Ex: 033" maxlength="3" required>
                        <button type="submit" class="btn btn-success">Ajouter</button>
                    </form>

                    <ul class="list-group">
                        <?php if (empty($prefixes)) : ?>
                            <li class="list-group-item text-muted text-center">Aucun préfixe configuré.</li>
                        <?php endif; ?>

                        <?php foreach ($prefixes as $p) : ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= esc($p['prefixe']) ?>
                                <a href="<?= base_url('operateur/prefixe/supprimer/' . $p['id']) ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Supprimer ce préfixe ?');">Supprimer</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

    </div>

    
    <div class="row g-4 mb-4">
        
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"> Autres Opérateurs (Concurrents)</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('operateur/autre-operateur/ajouter') ?>" method="post" class="d-flex gap-2 mb-3">
                        <?= csrf_field() ?>
                        <input type="text" name="nom" class="form-control" placeholder="Nom de l'opérateur (ex: Orange)" required>
                        <button type="submit" class="btn btn-success">Ajouter</button>
                    </form>

                    <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                        <table class="table table-hover align-middle table-sm mb-0">
                            <thead>
                            <tr>
                                <th>Nom</th>
                                <th class="text-end">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($autresOperateurs)) : ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-3">Aucun autre opérateur configuré.</td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($autresOperateurs as $ao) : ?>
                                <tr>
                                    <td><strong><?= esc($ao['nom']) ?></strong></td>
                                    <td class="text-end">
                                        <a href="<?= base_url('operateur/autre-operateur/supprimer/' . $ao['id']) ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Supprimer cet opérateur externe et tous ses préfixes liés ?');">Supprimer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"> Préfixes des Concurrents</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('operateur/prefixe-externe/ajouter') ?>" method="post" class="d-flex gap-2 mb-3">
                        <?= csrf_field() ?>
                        <input type="text" name="prefixe" class="form-control" placeholder="Ex: 032" maxlength="3" required style="max-width: 100px;">
                        <select name="autre_operateur_id" class="form-select" required>
                            <option value="">-- Choisir Opérateur --</option>
                            <?php foreach ($autresOperateurs as $ao) : ?>
                                <option value="<?= $ao['id'] ?>"><?= esc($ao['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-success">Ajouter</button>
                    </form>

                    <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                        <table class="table table-hover align-middle table-sm mb-0">
                            <thead>
                            <tr>
                                <th>Préfixe</th>
                                <th>Opérateur</th>
                                <th class="text-end">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($prefixesExternes)) : ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">Aucun préfixe externe configuré.</td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($prefixesExternes as $pe) : ?>
                                <tr>
                                    <td><span class="badge bg-secondary font-monospace"><?= esc($pe['prefixe']) ?></span></td>
                                    <td><?= esc($pe['operateur_nom']) ?></td>
                                    <td class="text-end">
                                        <a href="<?= base_url('operateur/prefixe-externe/supprimer/' . $pe['id']) ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Supprimer ce préfixe externe ?');">Supprimer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-header bg-white">
            <h5 class="mb-0">️ Situation des montants à envoyer à chaque opérateur</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>Opérateur</th>
                        <th class="text-end">Montant Total à Reverser</th>
                        <th class="text-end">Nombre de Transferts</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($montantsAEnvoyer)) : ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">Aucun montant à envoyer pour le moment.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($montantsAEnvoyer as $m) : ?>
                        <tr>
                            <td class="fw-bold"><?= esc($m['operateur']) ?></td>
                            <td class="text-end fw-bold text-danger">
                                <?= number_format($m['montant_a_envoyer'], 0, ',', ' ') ?> Ar
                            </td>
                            <td class="text-end text-muted"><?= (int) $m['nb_transferts'] ?> transferts</td>
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
