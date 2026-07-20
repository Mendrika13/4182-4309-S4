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
        <span class="navbar-brand fw-bold">🏦 Espace Opérateur</span>
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

    <div class="card shadow-sm border-0 mb-4 bg-dark text-white">
        <div class="card-body text-center py-4">
            <p class="mb-1">Gain global cumulé de l'opérateur</p>
            <h1 class="display-4 fw-bold mb-0"><?= number_format($gainGlobal, 0, ',', ' ') ?> Ar</h1>
            <small>Somme de tous les frais perçus (retraits + transferts)</small>
        </div>
    </div>

    <div class="row g-4">

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
                    <h5 class="mb-0">Préfixes autorisés</h5>
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

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
