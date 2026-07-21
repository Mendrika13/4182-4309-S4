<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VolaPay - Transfert unique</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>

<?= view('client/partials/navbar', ['client' => $client, 'activePage' => 'transfert']) ?>

<div class="dashboard-container">

    <?= view('client/partials/alerts') ?>

    <div class="page-form-wrapper">
        <div class="card-custom fade-up visible">
            <div class="card-title-custom text-primary">Transfert unique</div>
            <div class="card-desc">Envoyer de l'argent vers un numéro interne (Mobile Money) ou externe (concurrent).</div>
            <form action="<?= base_url('client/transfert') ?>" method="post" style="margin-top: auto;">
                <?= csrf_field() ?>
                <div class="form-group-custom">
                    <label class="form-label-custom">Numéro destinataire</label>
                    <input type="text" name="telephone_destinataire" class="custom-input font-mono" placeholder="0331234567" maxlength="10" required>
                </div>
                <div class="form-group-custom">
                    <label class="form-label-custom">Montant (Ar)</label>
                    <input type="number" name="montant" class="custom-input font-mono" min="1" step="1" required placeholder="Ex: 25000">
                </div>
                <div class="form-checkbox-custom">
                    <input type="checkbox" name="inclure_frais_retrait" id="inclureFraisRetrait" value="1">
                    <label for="inclureFraisRetrait" class="form-checkbox-label">Inclure les frais de retrait (Interne uniquement)</label>
                </div>
                <button type="submit" class="btn btn-primary w-100" style="padding: 12px;">Transférer</button>
            </form>
        </div>
    </div>

</div>

<?= view('client/partials/footer') ?>

<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
