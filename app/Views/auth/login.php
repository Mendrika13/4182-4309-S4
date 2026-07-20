<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mobile Money - Connexion</title>
<link rel="stylesheet" href="<?= App\Core\View::baseUrl('assets/css/app.css') ?>">
</head>
<body>
<div class="center-wrap">
  <div class="auth-card">
    <h1>Mobile <span>Money</span></h1>
    <p class="subtitle">Connectez-vous avec votre numéro de téléphone</p>

    <div class="card">
      <?php if ($erreur = App\Core\Session::getFlash('error')) : ?>
        <div class="alert alert-error"><?= App\Core\View::esc($erreur) ?></div>
      <?php endif; ?>
      <?php if ($succes = App\Core\Session::getFlash('success')) : ?>
        <div class="alert alert-success"><?= App\Core\View::esc($succes) ?></div>
      <?php endif; ?>

      <form action="<?= App\Core\View::baseUrl('login') ?>" method="post">
        <input type="hidden" name="csrf_token" value="<?= App\Core\Session::csrfToken() ?>">
        <div class="field">
          <label for="telephone">Numéro de téléphone</label>
          <input type="text" id="telephone" name="telephone" placeholder="0331234567" maxlength="10" required autofocus>
          <p class="hint">Format : 0XXXXXXXXX (10 chiffres). Si le numéro n'existe pas encore, un compte est créé automatiquement.</p>
        </div>
        <button type="submit" class="btn btn-primary">Se connecter</button>
      </form>
    </div>

    <p class="text-center" style="margin-top:20px;">
      <a href="<?= App\Core\View::baseUrl('operateur/login') ?>" class="link-muted">Accès opérateur</a>
    </p>
  </div>
</div>
</body>
</html>
