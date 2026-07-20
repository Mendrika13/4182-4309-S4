<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Money - Historique</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>

<?= view('client/partials/navbar', ['client' => $client, 'activePage' => 'historique']) ?>

<div class="dashboard-container">

    <?= view('client/partials/alerts') ?>

    <?= view('client/partials/historique_table', ['historique' => $historique, 'client' => $client]) ?>

</div>

<?= view('client/partials/footer') ?>

<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
