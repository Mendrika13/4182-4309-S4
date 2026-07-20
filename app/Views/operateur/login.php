<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Money - Espace Opérateur</title>
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
                <span class="brand-icon" style="background: var(--rose); box-shadow: 0 0 24px rgba(244, 63, 94, 0.4);">O</span>
                Espace Opérateur
            </a>
            <p style="color: var(--text-secondary); font-size: 14px;">Accès réservé à l'administration</p>
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

        <form action="<?= base_url('operateur/login') ?>" method="post">
            <?= csrf_field() ?>

            <div class="form-group-custom">
                <label for="mot_de_passe" class="form-label-custom">Mot de passe opérateur</label>
                <input type="password" class="custom-input" id="mot_de_passe" name="mot_de_passe" required autofocus>
            </div>

            <button type="submit" class="btn btn-primary w-100" style="margin-top: 16px; background: var(--rose); box-shadow: 0 0 24px rgba(244, 63, 94, 0.35);">Se connecter</button>
        </form>

        <div style="text-align: center; margin-top: 24px;">
            <a href="<?= base_url('login') ?>" class="btn btn-ghost" style="font-size: 13px;">&larr; Retour espace client</a>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
