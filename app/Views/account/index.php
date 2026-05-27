<section class="section-header">
    <div>
        <p class="eyebrow">Tableau de bord</p>
        <h1>Bonjour <?= e($user->email) ?></h1>
    </div>
</section>

<section class="dashboard-grid dashboard-grid-single">
    <article class="panel">
        <h2>Mon compte</h2>
        <p>Courriel : <?= e($user->email) ?></p>
        <div class="stack-actions">
            <a class="button button-secondary" href="<?= e(url('/compte/mot-de-passe')) ?>">Modifier mon mot de passe</a>
            <a class="button button-secondary" href="<?= e(url('/compte/achats')) ?>">Voir mes achats</a>
            <a class="button button-secondary" href="<?= e(url('/compte/ventes')) ?>">Voir mes ventes</a>
            <a class="button button-primary" href="<?= e(url('/vendre')) ?>">Gérer mes articles</a>
        </div>
    </article>
</section>
