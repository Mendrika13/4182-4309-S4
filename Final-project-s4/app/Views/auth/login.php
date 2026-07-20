<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Money - Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="text-center mb-4">
                <h1 class="fw-bold text-primary">📱 Mobile Money</h1>
                <p class="text-muted">Connectez-vous avec votre numéro de téléphone</p>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

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

                    <form action="<?= base_url('login') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="telephone" class="form-label">Numéro de téléphone</label>
                            <input type="text" class="form-control form-control-lg" id="telephone" name="telephone"
                                   placeholder="0331234567" maxlength="10" required autofocus>
                            <div class="form-text">Format : 0XXXXXXXXX (10 chiffres). Si le numéro n'existe pas
                                encore, un compte sera créé automatiquement.</div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">Se connecter</button>
                    </form>

                </div>
            </div>

            <div class="text-center mt-3">
                <a href="<?= base_url('operateur/login') ?>" class="text-muted small">Accès opérateur</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
