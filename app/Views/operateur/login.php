<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mobile Money - Accès opérateur</title>
<link rel="stylesheet" href="<?= App\Core\View::baseUrl('assets/css/app.css') ?>">
</head>
<body>
<div class="center-wrap">
  <div class="auth-card">
    <h1>Espace <span>Opérateur</span></h1>
    <p class="subtitle">Accès réservé à l'administration</p>

    <div class="card">
      <?php if ($erreur = App\Core\Session::getFlash('error')) : ?>
        <div class="alert alert-error"><?= App\Core\View::esc($erreur) ?></div>
      <?php endif; ?>
      <?php if ($succes = App\Core\Session::getFlash('success')) : ?>
        <div class="alert alert-success"><?= App\Core\View::esc($succes) ?></div>
      <?php endif; ?>

      <form action="<?= App\Core\View::baseUrl('operateur/login') ?>" method="post">
        <input type="hidden" name="csrf_token" value="<?= App\Core\Session::csrfToken() ?>">
        <div class="field">
          <label for="mot_de_passe">Mot de passe opérateur</label>
          <input type="password" id="mot_de_passe" name="mot_de_passe" required autofocus>
        </div>
        <button type="submit" class="btn btn-primary">Se connecter</button>
      </form>
    </div>

    <p class="text-center" style="margin-top:20px;">
      <a href="<?= App\Core\View::baseUrl('login') ?>" class="link-muted">Retour espace client</a>
    </p>
  </div>
</div>
</body>
</html>
