<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VolaPay - Envoi multiple</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>

<?= view('client/partials/navbar', ['client' => $client, 'activePage' => 'envoi-multiple']) ?>

<div class="dashboard-container">

    <?= view('client/partials/alerts') ?>

    <div class="page-form-wrapper">
        <div class="card-custom fade-up visible">
            <div class="card-title-custom text-dark">Envoi multiple</div>
            <div class="card-desc">Diviser équitablement un montant total entre plusieurs destinataires (interne uniquement).</div>
            <form action="<?= base_url('client/transfert-multiple') ?>" method="post" style="margin-top: auto;">
                <?= csrf_field() ?>
                <div class="form-group-custom">
                    <label class="form-label-custom">Numéros destinataires (séparés par espaces, virgules ou point-virgule)</label>
                    <textarea name="telephones" class="custom-input font-mono" rows="2" placeholder="0331234567, 0337654321" required style="resize: vertical; min-height: 58px; max-height: 120px;"></textarea>
                </div>
                <div class="form-group-custom">
                    <label class="form-label-custom">Montant total global (Ar)</label>
                    <input type="number" name="montant_total" class="custom-input font-mono" min="1" step="1" required placeholder="Ex: 60000">
                </div>
                <div class="form-checkbox-custom">
                    <input type="checkbox" name="inclure_frais_retrait" id="inclureFraisRetraitMultiple" value="1">
                    <label for="inclureFraisRetraitMultiple" class="form-checkbox-label">Inclure les frais de retrait pour chaque numéro</label>
                </div>
                <button type="submit" class="btn btn-secondary w-100" style="padding: 12px; border: 1px solid var(--border-subtle);">Envoyer à tous</button>
            </form>
        </div>
    </div>

</div>

<?= view('client/partials/footer') ?>

<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
