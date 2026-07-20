<!-- NAVBAR -->
<nav class="navbar scrolled navbar-client" id="navbar">
    <div class="container">
        <a href="<?= base_url('client/dashboard') ?>" class="nav-brand">Mobile Money</a>
        <ul class="nav-links" id="navLinks">
            <li><a href="<?= base_url('client/dashboard') ?>" class="<?= ($activePage ?? '') === 'accueil' ? 'active' : '' ?>">Accueil</a></li>
            <li><a href="<?= base_url('client/historique') ?>" class="<?= ($activePage ?? '') === 'historique' ? 'active' : '' ?>">Historique</a></li>
            <li><a href="<?= base_url('client/transfert-unique') ?>" class="<?= ($activePage ?? '') === 'transfert' ? 'active' : '' ?>">Transfert unique</a></li>
            <li><a href="<?= base_url('client/envoi-multiple') ?>" class="<?= ($activePage ?? '') === 'envoi-multiple' ? 'active' : '' ?>">Envoi multiple</a></li>
            <li class="nav-mobile-actions">
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 14px; color: var(--text-secondary);"><?= esc($client['telephone']) ?></span>
                <a href="<?= base_url('logout') ?>" class="btn btn-secondary btn-sm">Déconnexion</a>
            </li>
        </ul>
        <div class="nav-actions">
            <span style="font-family: 'JetBrains Mono', monospace; font-size: 14px; color: var(--text-secondary);"><?= esc($client['telephone']) ?></span>
            <a href="<?= base_url('logout') ?>" class="btn btn-secondary btn-sm" style="padding: 8px 18px;">Déconnexion</a>
        </div>
        <button class="nav-toggle" id="navToggle" aria-label="Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</nav>
