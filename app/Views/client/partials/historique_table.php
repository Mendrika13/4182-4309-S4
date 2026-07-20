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
