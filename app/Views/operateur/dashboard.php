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

<!-- NAVBAR -->
<nav class="navbar scrolled" id="navbar">
    <div class="container">
        <a href="#" class="nav-brand">
            <span class="brand-icon" style="background: var(--rose); box-shadow: 0 0 24px rgba(244, 63, 94, 0.4);">O</span>
            Espace Opérateur
        </a>
        <div class="nav-actions">
            <a href="<?= base_url('operateur/logout') ?>" class="btn btn-secondary btn-sm" style="padding: 8px 18px;">Déconnexion</a>
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

    <!-- Gains Grid -->
    <div class="grid-2">
        
        <!-- Gains Internes -->
        <div class="card-custom text-center py-4 fade-up visible" style="text-align: center;">
            <div class="balance-label" style="color: var(--text-secondary);">Gain Opérateur (Interne)</div>
            <div class="balance-value gradient-text" style="margin: 12px 0;"><?= number_format($gainInterne, 0, ',', ' ') ?> Ar</div>
            <div class="balance-sub" style="color: var(--text-muted);">Frais sur les transactions internes (retraits + transferts)</div>
        </div>

        <!-- Gains Externes -->
        <div class="card-custom text-center py-4 fade-up visible" style="text-align: center;">
            <div class="balance-label" style="color: var(--text-secondary);">Gain Opérateur (Externe)</div>
            <div class="balance-value font-mono" style="font-size: clamp(36px, 6vw, 48px); font-weight: 800; color: var(--accent-light); margin: 12px 0;"><?= number_format($gainExterne, 0, ',', ' ') ?> Ar</div>
            <div class="balance-sub" style="color: var(--text-muted);">Frais + Commissions sur transferts externes</div>
        </div>

    </div>

    <!-- Configuration de Commission -->
    <div class="card-custom fade-up visible" style="margin-bottom: 32px; padding: 20px 24px;">
        <form action="<?= base_url('operateur/commission/modifier') ?>" method="post" style="display: flex; flex-wrap: wrap; align-items: center; gap: 16px;">
            <?= csrf_field() ?>
            <div style="font-weight: 700; font-size: 14.5px; display: flex; align-items: center; gap: 8px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--accent);"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                Commission sur transferts concurrents (externe) :
            </div>
            <div style="display: flex; align-items: center; gap: 8px; width: 140px;">
                <input type="number" step="0.01" min="0" max="100" name="pourcentage" class="custom-input font-mono" value="<?= esc($commission) ?>" required style="padding: 8px 12px; text-align: center;">
                <span style="font-weight: 700; color: var(--text-secondary);">%</span>
            </div>
            <button type="submit" class="btn btn-primary" style="padding: 8px 20px; font-size: 13px;">Modifier</button>
        </form>
    </div>

    <!-- Main Section Grid (Comptes clients + Prefixes) -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 32px;" class="grid-3-custom">
        
        <!-- Comptes Clients -->
        <div class="table-container-custom fade-up visible" style="margin-top: 0;">
            <div class="table-header-custom">
                <h3 class="table-header-title">Comptes clients</h3>
                <span class="badge-custom badge-secondary" style="font-size: 12px; font-family: inherit;"><?= count($clients) ?> compte(s)</span>
            </div>
            <div style="overflow-x: auto;">
                <table class="custom-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Téléphone</th>
                        <th>Date de création</th>
                        <th class="text-end">Solde</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($clients)) : ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 40px 0;">Aucun client enregistré.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($clients as $c) : ?>
                        <tr>
                            <td class="font-mono" style="color: var(--text-muted);"><?= (int) $c['id'] ?></td>
                            <td><strong><?= esc($c['telephone']) ?></strong></td>
                            <td style="color: var(--text-secondary);"><?= date('d/m/Y H:i', strtotime($c['date_creation'])) ?></td>
                            <td class="text-end font-mono text-primary" style="font-weight: 700;">
                                <?= number_format($c['solde'], 0, ',', ' ') ?> Ar
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Prefixes Internes -->
        <div class="card-custom fade-up visible" style="display: flex; flex-direction: column;">
            <div class="card-title-custom text-dark" style="margin-bottom: 6px;">
                Préfixes Internes (Autorisés)
            </div>
            <div class="card-desc" style="margin-bottom: 16px;">Gérer les indicatifs de numéros gérés par votre réseau.</div>
            
            <form action="<?= base_url('operateur/prefixe/ajouter') ?>" method="post" style="display: flex; gap: 10px; margin-bottom: 20px;">
                <?= csrf_field() ?>
                <input type="text" name="prefixe" class="custom-input font-mono" placeholder="Ex: 033" maxlength="3" required style="padding: 10px;">
                <button type="submit" class="btn btn-success" style="padding: 10px 18px; border-radius: var(--radius-sm); font-size: 13px;">Ajouter</button>
            </form>

            <div style="flex-grow: 1; overflow-y: auto; max-height: 250px; border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); background: rgba(0,0,0,0.15);">
                <?php if (empty($prefixes)) : ?>
                    <div style="text-align: center; color: var(--text-muted); padding: 24px; font-size: 13px;">Aucun préfixe configuré.</div>
                <?php endif; ?>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <?php foreach ($prefixes as $p) : ?>
                        <li style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-bottom: 1px solid var(--border-subtle);">
                            <span class="badge-custom badge-secondary" style="font-size: 13px;"><?= esc($p['prefixe']) ?></span>
                            <a href="<?= base_url('operateur/prefixe/supprimer/' . $p['id']) ?>" 
                               class="btn btn-ghost" 
                               style="color: var(--rose); font-size: 11px; padding: 4px 10px;"
                               onclick="return confirm('Supprimer ce préfixe ?');">Supprimer</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

    </div>

    <!-- Secondary section Grid (Concurrents & Concurrent prefixes) -->
    <div class="grid-2">
        
        <!-- Concurrents -->
        <div class="card-custom fade-up visible">
            <div class="card-title-custom text-dark" style="margin-bottom: 6px;">
                Autres Opérateurs (Concurrents)
            </div>
            <div class="card-desc" style="margin-bottom: 16px;">Définir les opérateurs externes avec lesquels vous échangez des transferts.</div>
            
            <form action="<?= base_url('operateur/autre-operateur/ajouter') ?>" method="post" style="display: flex; gap: 10px; margin-bottom: 20px;">
                <?= csrf_field() ?>
                <input type="text" name="nom" class="custom-input" placeholder="Nom de l'opérateur (ex: Orange)" required style="padding: 10px;">
                <button type="submit" class="btn btn-success" style="padding: 10px 18px; border-radius: var(--radius-sm); font-size: 13px;">Ajouter</button>
            </form>

            <div style="overflow-y: auto; max-height: 200px; border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); background: rgba(0,0,0,0.15);">
                <table class="custom-table" style="font-size: 13px;">
                    <tbody>
                    <?php if (empty($autresOperateurs)) : ?>
                        <tr>
                            <td colspan="2" style="text-align: center; color: var(--text-muted); padding: 24px;">Aucun autre opérateur configuré.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($autresOperateurs as $ao) : ?>
                        <tr>
                            <td><strong><?= esc($ao['nom']) ?></strong></td>
                            <td class="text-end" style="padding: 8px 16px;">
                                <a href="<?= base_url('operateur/autre-operateur/supprimer/' . $ao['id']) ?>" 
                                   class="btn btn-ghost" 
                                   style="color: var(--rose); font-size: 11px; padding: 4px 10px;"
                                   onclick="return confirm('Supprimer cet opérateur externe et tous ses préfixes liés ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Prefixes concurrents -->
        <div class="card-custom fade-up visible">
            <div class="card-title-custom text-dark" style="margin-bottom: 6px;">
                Préfixes Concurrents (Externes)
            </div>
            <div class="card-desc" style="margin-bottom: 16px;">Associer des préfixes téléphoniques spécifiques à des opérateurs externes.</div>
            
            <form action="<?= base_url('operateur/prefixe-externe/ajouter') ?>" method="post" style="display: flex; gap: 8px; margin-bottom: 20px;">
                <?= csrf_field() ?>
                <input type="text" name="prefixe" class="custom-input font-mono" placeholder="032" maxlength="3" required style="padding: 10px; max-width: 80px;">
                
                <select name="autre_operateur_id" class="custom-input select-custom" required style="padding: 10px 40px 10px 16px; flex-grow: 1;">
                    <option value="">-- Opérateur --</option>
                    <?php foreach ($autresOperateurs as $ao) : ?>
                        <option value="<?= $ao['id'] ?>"><?= esc($ao['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
                
                <button type="submit" class="btn btn-success" style="padding: 10px 18px; border-radius: var(--radius-sm); font-size: 13px; flex-shrink: 0;">Ajouter</button>
            </form>

            <div style="overflow-y: auto; max-height: 200px; border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); background: rgba(0,0,0,0.15);">
                <table class="custom-table" style="font-size: 13px;">
                    <thead>
                    <tr>
                        <th style="padding: 8px 16px;">Préfixe</th>
                        <th style="padding: 8px 16px;">Opérateur</th>
                        <th class="text-end" style="padding: 8px 16px;">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($prefixesExternes)) : ?>
                        <tr>
                            <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 24px;">Aucun préfixe externe configuré.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($prefixesExternes as $pe) : ?>
                        <tr>
                            <td style="padding: 8px 16px;"><span class="badge-custom badge-secondary"><?= esc($pe['prefixe']) ?></span></td>
                            <td style="padding: 8px 16px;"><?= esc($pe['operateur_nom']) ?></td>
                            <td class="text-end" style="padding: 8px 16px;">
                                <a href="<?= base_url('operateur/prefixe-externe/supprimer/' . $pe['id']) ?>" 
                                   class="btn btn-ghost" 
                                   style="color: var(--rose); font-size: 11px; padding: 4px 10px;"
                                   onclick="return confirm('Supprimer ce préfixe externe ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Situation de Remboursement Opérateurs (Concurrents) -->
    <div class="table-container-custom fade-up visible">
        <div class="table-header-custom">
            <h3 class="table-header-title">Situation des montants à reverser à chaque opérateur</h3>
            <span class="badge-custom badge-primary" style="font-size: 12px; font-family: inherit;"><?= count($montantsAEnvoyer) ?> opérateur(s) concerné(s)</span>
        </div>
        <div style="overflow-x: auto;">
            <table class="custom-table">
                <thead>
                <tr>
                    <th>Opérateur</th>
                    <th class="text-end">Montant Total à Reverser</th>
                    <th class="text-end">Nombre de Transferts</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($montantsAEnvoyer)) : ?>
                    <tr>
                        <td colspan="3" style="text-align: center; color: var(--text-muted); padding: 40px 0;">Aucun montant à reverser pour le moment.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($montantsAEnvoyer as $m) : ?>
                    <tr>
                        <td><strong><?= esc($m['operateur']) ?></strong></td>
                        <td class="text-end font-mono text-danger" style="font-weight: 700;">
                            <?= number_format($m['montant_a_envoyer'], 0, ',', ' ') ?> Ar
                        </td>
                        <td class="text-end style-muted" style="color: var(--text-secondary);"><?= (int) $m['nb_transferts'] ?> transfert(s)</td>
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
                    <span style="color: var(--text-muted); font-size: 12.5px;">Espace Opérateur (Administration)</span>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="<?= base_url('assets/js/app.js') ?>"></script>
<style>
/* Custom style to enforce grid rendering on large viewports */
@media (min-width: 992px) {
    .grid-3-custom {
        grid-template-columns: 2fr 1fr !important;
    }
}
@media (max-width: 991px) {
    .grid-3-custom {
        grid-template-columns: 1fr !important;
    }
}
</style>
</body>
</html>
