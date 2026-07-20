<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mobile Money - Espace Opérateur</title>
<link rel="stylesheet" href="<?= App\Core\View::baseUrl('assets/css/app.css') ?>">
</head>
<body>

<header class="topbar">
  <div class="topbar-inner">
    <div class="topbar-brand">Espace <span>Opérateur</span></div>
    <a href="<?= App\Core\View::baseUrl('operateur/logout') ?>" class="btn btn-outline btn-sm">Déconnexion</a>
  </div>
</header>

<main class="page">
  <div class="container">

    <?php if ($erreur = App\Core\Session::getFlash('error')) : ?>
      <div class="alert alert-error"><?= App\Core\View::esc($erreur) ?></div>
    <?php endif; ?>
    <?php if ($succes = App\Core\Session::getFlash('success')) : ?>
      <div class="alert alert-success"><?= App\Core\View::esc($succes) ?></div>
    <?php endif; ?>

    <div class="hero-balance">
      <p>Gain global cumulé de l'opérateur</p>
      <h1><?= App\Core\View::argent($gainGlobal) ?> Ar</h1>
      <small>Somme de tous les frais perçus (retraits + transferts)</small>
    </div>

    <div class="grid-2">

      <div class="card">
        <div class="card-header-row">
          <h2>Comptes clients</h2>
          <span class="badge"><?= count($clients) ?> compte(s)</span>
        </div>
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Téléphone</th>
              <th>Date de création</th>
              <th class="text-end">Solde</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($clients)) : ?>
              <tr><td colspan="4" class="text-center text-muted">Aucun client enregistré.</td></tr>
            <?php endif; ?>

            <?php foreach ($clients as $c) : ?>
              <tr>
                <td><?= (int) $c['client_id'] ?></td>
                <td><?= App\Core\View::esc($c['telephone']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($c['date_creation'])) ?></td>
                <td class="text-end amount-credit"><?= App\Core\View::argent($c['solde']) ?> Ar</td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="card">
        <div class="card-header-row">
          <h2>Préfixes autorisés</h2>
        </div>

        <form action="<?= App\Core\View::baseUrl('operateur/prefixe/ajouter') ?>" method="post" class="inline-form">
          <input type="hidden" name="csrf_token" value="<?= App\Core\Session::csrfToken() ?>">
          <input type="text" name="prefixe" placeholder="Ex: 033" maxlength="3" required>
          <button type="submit" class="btn btn-primary btn-sm">Ajouter</button>
        </form>

        <?php if (empty($prefixes)) : ?>
          <p class="text-muted text-center">Aucun préfixe configuré.</p>
        <?php endif; ?>

        <?php foreach ($prefixes as $p) : ?>
          <div class="list-item">
            <span><?= App\Core\View::esc($p['prefixe']) ?></span>
            <a href="<?= App\Core\View::baseUrl('operateur/prefixe/supprimer/' . $p['id']) ?>"
               class="btn btn-outline btn-sm"
               data-confirm="Supprimer ce préfixe ?">Supprimer</a>
          </div>
        <?php endforeach; ?>
      </div>

    </div>

  </div>
</main>

<script src="<?= App\Core\View::baseUrl('assets/js/app.js') ?>"></script>
</body>
</html>
