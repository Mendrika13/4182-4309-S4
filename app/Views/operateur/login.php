<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Money - Accès opérateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center" style="min-height: 100vh;">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="text-center mb-4">
                <h1 class="fw-bold text-white">🏦 Espace Opérateur</h1>
                <p class="text-white-50">Accès réservé à l'administration</p>
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

                    <form action="<?= base_url('operateur/login') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="mot_de_passe" class="form-label">Mot de passe opérateur</label>
                            <input type="password" class="form-control form-control-lg" id="mot_de_passe"
                                   name="mot_de_passe" required autofocus>
                        </div>
                        <button type="submit" class="btn btn-dark btn-lg w-100">Se connecter</button>
                    </form>

                </div>
            </div>

            <div class="text-center mt-3">
                <a href="<?= base_url('login') ?>" class="text-white-50 small">Retour espace client</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
