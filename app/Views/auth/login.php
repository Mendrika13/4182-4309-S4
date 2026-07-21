<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VolaPay - Connexion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>

<div class="login-container">
    <div class="login-card fade-up visible">
        <div class="text-center mb-4" style="text-align: center; margin-bottom: 32px;">
            <a href="#" class="nav-brand" style="justify-content: center; margin-bottom: 12px; font-size: 24px;">
                <span class="brand-icon">M</span>
                VolaPay
            </a>
            <p style="color: var(--text-secondary); font-size: 14px;">Connectez-vous avec votre numéro de téléphone</p>
        </div>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert-custom alert-danger">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                <span><?= esc(session()->getFlashdata('error')) ?></span>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert-custom alert-success">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span><?= esc(session()->getFlashdata('success')) ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('login') ?>" method="post">
            <?= csrf_field() ?>

            <div class="form-group-custom">
                <label for="telephone" class="form-label-custom">Numéro de téléphone</label>
                <input type="text" class="custom-input font-mono" id="telephone" name="telephone"
                       placeholder="0331234567" maxlength="10" required autofocus>
                <div class="form-text-custom">
                    Format : 0XXXXXXXXX (10 chiffres). Si le numéro n'existe pas encore, un compte sera créé automatiquement.
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100" style="margin-top: 16px;">Se connecter</button>
        </form>

        <div style="text-align: center; margin-top: 24px;">
            <a href="<?= base_url('operateur/login') ?>" class="btn btn-ghost" style="font-size: 13px;">Accès opérateur &rarr;</a>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
