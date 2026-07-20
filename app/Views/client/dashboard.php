<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Money - Mon compte</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>

<?= view('client/partials/navbar', ['client' => $client, 'activePage' => 'accueil']) ?>

<div class="dashboard-container">

    <?= view('client/partials/alerts') ?>

    <div class="balance-wrapper">
        <div class="balance-card balance-card-compact fade-up visible">
            <div class="balance-label">Solde actuel</div>
            <div class="balance-value gradient-text"><?= number_format($solde, 0, ',', ' ') ?> Ar</div>
            <div class="balance-sub">Titulaire : <span class="font-mono"><?= esc($client['telephone']) ?></span></div>
        </div>
    </div>

    <div class="grid-actions">

        <div class="card-custom card-compact fade-up visible">
            <div class="card-title-custom text-depot">Dépôt</div>
            <div class="card-desc card-desc-compact">Gratuit et instantané.</div>
            <form action="<?= base_url('client/depot') ?>" method="post" style="margin-top: auto;">
                <?= csrf_field() ?>
                <div class="form-group-custom form-group-compact">
                    <label class="form-label-custom">Montant (Ar)</label>
                    <input type="number" name="montant" class="custom-input font-mono" min="1" step="1" required placeholder="Ex: 50000">
                </div>
                <button type="submit" class="btn btn-depot w-100 btn-compact">Déposer</button>
            </form>
        </div>

        <div class="card-custom card-compact fade-up visible">
            <div class="card-title-custom text-danger">Retrait</div>
            <div class="card-desc card-desc-compact">Frais selon le barème.</div>
            <form action="<?= base_url('client/retrait') ?>" method="post" style="margin-top: auto;">
                <?= csrf_field() ?>
                <div class="form-group-custom form-group-compact">
                    <label class="form-label-custom">Montant (Ar)</label>
                    <input type="number" name="montant" class="custom-input font-mono" min="1" step="1" required placeholder="Ex: 10000">
                </div>
                <button type="submit" class="btn btn-danger-custom w-100 btn-compact">Retirer</button>
            </form>
        </div>

        <a href="<?= base_url('client/transfert-unique') ?>" class="card-custom card-compact card-link fade-up visible">
            <div class="card-title-custom text-primary">Transfert unique</div>
        </a>

        <a href="<?= base_url('client/envoi-multiple') ?>" class="card-custom card-compact card-link fade-up visible">
            <div class="card-title-custom text-dark">Envoi multiple</div>
        </a>

    </div>

</div>

<?= view('client/partials/footer') ?>

<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
