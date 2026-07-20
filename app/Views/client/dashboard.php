<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mobile Money - Mon compte</title>
<link rel="stylesheet" href="<?= App\Core\View::baseUrl('assets/css/app.css') ?>">
</head>
<body>

<header class="topbar">
  <div class="topbar-inner">
    <div class="topbar-brand">Mobile <span>Money</span></div>
    <div class="topbar-user">
      <span><?= App\Core\View::esc($client['telephone']) ?></span>
      <a href="<?= App\Core\View::baseUrl('logout') ?>" class="btn btn-outline btn-sm">Déconnexion</a>
    </div>
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
      <p>Solde actuel</p>
      <h1><?= App\Core\View::argent($solde) ?> Ar</h1>
      <small>Numéro : <?= App\Core\View::esc($client['telephone']) ?></small>
    </div>

    <div class="grid-3">

      <div class="card op-card">
        <h3>Dépôt</h3>
        <p class="desc">Gratuit, sans frais.</p>
        <form action="<?= App\Core\View::baseUrl('client/depot') ?>" method="post">
          <input type="hidden" name="csrf_token" value="<?= App\Core\Session::csrfToken() ?>">
          <div class="field">
            <label>Montant (Ar)</label>
            <input type="number" name="montant" min="1" step="1" required>
          </div>
          <button type="submit" class="btn btn-primary">Déposer</button>
        </form>
      </div>

      <div class="card op-card">
        <h3>Retrait</h3>
        <p class="desc">Frais selon barème en vigueur.</p>
        <form action="<?= App\Core\View::baseUrl('client/retrait') ?>" method="post">
          <input type="hidden" name="csrf_token" value="<?= App\Core\Session::csrfToken() ?>">
          <div class="field">
            <label>Montant (Ar)</label>
            <input type="number" name="montant" min="1" step="1" required>
          </div>
          <button type="submit" class="btn btn-danger">Retirer</button>
        </form>
      </div>

      <div class="card op-card">
        <h3>Transfert</h3>
        <p class="desc">Vers un autre numéro Mobile Money.</p>
        <form action="<?= App\Core\View::baseUrl('client/transfert') ?>" method="post">
          <input type="hidden" name="csrf_token" value="<?= App\Core\Session::csrfToken() ?>">
          <div class="field">
            <label>Numéro destinataire</label>
            <input type="text" name="telephone_destinataire" placeholder="0331234567" maxlength="10" required>
          </div>
          <div class="field">
            <label>Montant (Ar)</label>
            <input type="number" name="montant" min="1" step="1" required>
          </div>
          <button type="submit" class="btn btn-primary">Transférer</button>
        </form>
      </div>

    </div>

    <div class="card">
      <div class="card-header-row">
        <h2>Historique des transactions</h2>
        <span class="badge"><?= count($historique) ?> opération(s)</span>
      </div>

      <table>
        <thead>
          <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Détail</th>
            <th class="text-end">Montant</th>
            <th class="text-end">Frais</th>
            <th class="text-end">Impact</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($historique)) : ?>
            <tr><td colspan="6" class="text-center text-muted">Aucune transaction pour le moment.</td></tr>
          <?php endif; ?>

          <?php foreach ($historique as $t) : ?>
            <?php
              $estCredit = false;
              $detail = '';
              $pillClass = 'pill-neutral';

              if ($t['type_operation'] === 'depot') {
                  $estCredit = true;
                  $detail = 'Dépôt sur votre compte';
                  $pillClass = 'pill-success';
              } elseif ($t['type_operation'] === 'retrait') {
                  $estCredit = false;
                  $detail = 'Retrait sur votre compte';
                  $pillClass = 'pill-danger';
              } elseif ($t['type_operation'] === 'transfert') {
                  $pillClass = 'pill-neutral';
                  if ((int) $t['expediteur_id'] === (int) $client['id']) {
                      $estCredit = false;
                      $detail = 'Transfert envoyé vers ' . $t['telephone_destinataire'];
                  } else {
                      $estCredit = true;
                      $detail = 'Transfert reçu de ' . $t['telephone_expediteur'];
                  }
              }

              $impact = $estCredit
                  ? '+' . App\Core\View::argent($t['montant'])
                  : '-' . App\Core\View::argent($t['montant'] + $t['frais']);
            ?>
            <tr>
              <td><?= date('d/m/Y H:i', strtotime($t['date_transaction'])) ?></td>
              <td><span class="pill <?= $pillClass ?>"><?= ucfirst($t['type_operation']) ?></span></td>
              <td><?= App\Core\View::esc($detail) ?></td>
              <td class="text-end"><?= App\Core\View::argent($t['montant']) ?> Ar</td>
              <td class="text-end"><?= App\Core\View::argent($t['frais']) ?> Ar</td>
              <td class="text-end <?= $estCredit ? 'amount-credit' : 'amount-debit' ?>"><?= $impact ?> Ar</td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </div>
</main>

<script src="<?= App\Core\View::baseUrl('assets/js/app.js') ?>"></script>
</body>
</html>
