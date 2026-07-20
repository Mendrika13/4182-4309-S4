<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Money - Mon compte</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar scrolled" id="navbar">
    <div class="container">
        <a href="#" class="nav-brand">
            <span class="brand-icon">M</span>
            Mobile Money
        </a>
        <div class="nav-actions" style="display: flex; align-items: center; gap: 20px;">
            <span style="font-family: 'JetBrains Mono', monospace; font-size: 14px; color: var(--text-secondary);"><?= esc($client['telephone']) ?></span>
            <a href="<?= base_url('logout') ?>" class="btn btn-secondary btn-sm" style="padding: 8px 18px;">Déconnexion</a>
        </div>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div class="dashboard-container">

    <!-- Flash Alerts -->
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

    <!-- Solde actuel -->
    <div class="balance-card fade-up visible">
        <div class="balance-label">Solde actuel</div>
        <div class="balance-value gradient-text"><?= number_format($solde, 0, ',', ' ') ?> Ar</div>
        <div class="balance-sub">Titulaire : <span class="font-mono"><?= esc($client['telephone']) ?></span></div>
    </div>

    <!-- Operations Grid -->
    <div class="grid-2">
        
        <!-- Depot -->
        <div class="card-custom fade-up visible">
            <div class="card-title-custom text-success">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
                Dépôt
            </div>
            <div class="card-desc">Gratuit et instantané, aucun frais appliqué.</div>
            <form action="<?= base_url('client/depot') ?>" method="post" style="margin-top: auto;">
                <?= csrf_field() ?>
                <div class="form-group-custom">
                    <label class="form-label-custom">Montant (Ar)</label>
                    <input type="number" name="montant" class="custom-input font-mono" min="1" step="1" required placeholder="Ex: 50000">
                </div>
                <button type="submit" class="btn btn-success w-100" style="padding: 12px;">Déposer</button>
            </form>
        </div>

        <!-- Retrait -->
        <div class="card-custom fade-up visible">
            <div class="card-title-custom text-danger">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
                Retrait
            </div>
            <div class="card-desc">Frais selon le barème (offerts si vous avez reçu un transfert avec frais de retrait prépayés).</div>
            <form action="<?= base_url('client/retrait') ?>" method="post" style="margin-top: auto;">
                <?= csrf_field() ?>
                <div class="form-group-custom">
                    <label class="form-label-custom">Montant (Ar)</label>
                    <input type="number" name="montant" class="custom-input font-mono" min="1" step="1" required placeholder="Ex: 10000">
                </div>
                <button type="submit" class="btn btn-danger-custom w-100" style="padding: 12px;">Retirer</button>
            </form>
        </div>

    </div>

    <div class="grid-2">

        <!-- Transfert Unique -->
        <div class="card-custom fade-up visible">
            <div class="card-title-custom text-primary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                Transfert Unique
            </div>
            <div class="card-desc">Envoyer de l'argent vers un numéro interne (Mobile Money) ou externe (concurrent).</div>
            <form action="<?= base_url('client/transfert') ?>" method="post" style="margin-top: auto;">
                <?= csrf_field() ?>
                <div class="form-group-custom">
                    <label class="form-label-custom">Numéro destinataire</label>
                    <input type="text" name="telephone_destinataire" class="custom-input font-mono" placeholder="0331234567" maxlength="10" required>
                </div>
                <div class="form-group-custom">
                    <label class="form-label-custom">Montant (Ar)</label>
                    <input type="number" name="montant" class="custom-input font-mono" min="1" step="1" required placeholder="Ex: 25000">
                </div>
                <div class="form-checkbox-custom">
                    <input type="checkbox" name="inclure_frais_retrait" id="inclureFraisRetrait" value="1">
                    <label for="inclureFraisRetrait" class="form-checkbox-label">Inclure les frais de retrait (Interne uniquement)</label>
                </div>
                <button type="submit" class="btn btn-primary w-100" style="padding: 12px;">Transférer</button>
            </form>
        </div>

        <!-- Envoi Multiple -->
        <div class="card-custom fade-up visible">
            <div class="card-title-custom text-dark">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Envoi Multiple
            </div>
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

    <!-- Historique des Transactions -->
    <div class="table-container-custom fade-up visible">
        <div class="table-header-custom">
            <h3 class="table-header-title">Historique des transactions</h3>
            <span class="badge-custom badge-secondary" style="font-size: 12px; font-family: inherit;"><?= count($historique) ?> transaction(s)</span>
        </div>
        <div style="overflow-x: auto;">
            <table class="custom-table">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Détail</th>
                    <th class="text-end">Montant</th>
                    <th class="text-end">Frais</th>
                    <th class="text-end">Impact sur le solde</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($historique)) : ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px 0;">Aucune transaction pour le moment.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($historique as $t) : ?>
                    <?php
                        $estCredit = false;
                        $detail    = '';
                        $badge     = 'secondary';

                        if ($t['type_operation'] === 'depot') {
                            $estCredit = true;
                            $detail    = 'Dépôt sur votre compte';
                            $badge     = 'success';
                        } elseif ($t['type_operation'] === 'retrait') {
                            $estCredit = false;
                            $detail    = 'Retrait sur votre compte';
                            $badge     = 'danger';
                        } elseif ($t['type_operation'] === 'transfert') {
                            $badge = 'primary';
                            if ((int) $t['expediteur_id'] === (int) $client['id']) {
                                $estCredit = false;
                                $detail    = 'Transfert envoyé vers ' . esc($t['telephone_destinataire']);
                                if ($t['est_externe'] == 1) {
                                    $detail .= ' (' . esc($t['autre_operateur_nom'] ?? 'Externe') . ')';
                                }
                                if ($t['lot_id']) {
                                    $detail .= ' <span class="badge-custom badge-secondary" style="font-size: 9px; padding: 2px 6px; margin-left: 6px;">Envoi Multiple</span>';
                                }
                            } else {
                                $estCredit = true;
                                $detail    = 'Transfert reçu de ' . esc($t['telephone_expediteur']);
                                if ($t['lot_id']) {
                                    $detail .= ' <span class="badge-custom badge-secondary" style="font-size: 9px; padding: 2px 6px; margin-left: 6px;">Envoi Multiple</span>';
                                }
                            }
                        }

                        $impact = $estCredit
                            ? '+' . number_format($t['montant'], 0, ',', ' ')
                            : '-' . number_format($t['montant'] + $t['frais'] + $t['commission'], 0, ',', ' ');
                    ?>
                    <tr>
                        <td style="color: var(--text-secondary);"><?= date('d/m/Y H:i', strtotime($t['date_transaction'])) ?></td>
                        <td>
                            <span class="badge-custom badge-<?= $badge ?>">
                                <span class="badge-<?= $estCredit ? 'success' : 'danger' ?>-dot"></span>
                                <?= ucfirst($t['type_operation']) ?>
                            </span>
                        </td>
                        <td><?= $detail ?></td>
                        <td class="text-end font-mono"><?= number_format($t['montant'], 0, ',', ' ') ?> Ar</td>
                        <td class="text-end font-mono" style="color: var(--text-secondary);">
                            <?= number_format($t['frais'], 0, ',', ' ') ?> Ar
                            <?php if ($t['commission'] > 0) : ?>
                                <br><small style="color: var(--text-muted); font-size: 11px;">+ comm. : <?= number_format($t['commission'], 0, ',', ' ') ?> Ar</small>
                            <?php endif; ?>
                        </td>
                        <td class="text-end font-mono <?= $estCredit ? 'text-success' : 'text-danger' ?>" style="font-weight: 700;">
                            <?= $impact ?> Ar
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- FOOTER -->
<footer class="footer" style="margin-top: 80px;">
    <div class="container">
        <div class="footer-row-bottom" style="border-top: none;">
            <p class="footer-copy">Copyright &copy; 2026 Mobile Money. Design inspiré de <a href="https://templatemo.com" target="_blank" rel="nofollow">TemplateMo</a></p>
            <div class="footer-right">
                <div class="footer-legal">
                    <span style="color: var(--text-muted); font-size: 12.5px;">Espace Client</span>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
