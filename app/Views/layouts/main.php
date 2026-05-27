<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(config('app.name', 'Best Sell')) ?></title>
    <link rel="stylesheet" href="<?= e(asset_url('assets/css/app.css')) ?>">
</head>
<body>
<div class="page-shell">
    <header class="site-header">
        <a class="brand" href="<?= e(url('/')) ?>">Best Sell</a>
        <nav class="site-nav" aria-label="Navigation principale">
            <a class="<?= $currentPath === '/' ? 'is-active' : '' ?>" href="<?= e(url('/')) ?>">Accueil</a>
            <?php if ($auth !== null): ?>
                <a class="<?= $currentPath === '/vendre' || str_starts_with($currentPath, '/produits/ajouter') ? 'is-active' : '' ?>" href="<?= e(url('/vendre')) ?>">Vendre</a>
                <a class="<?= str_starts_with($currentPath, '/compte') ? 'is-active' : '' ?>" href="<?= e(url('/compte')) ?>">Mon compte</a>
                <a class="<?= $currentPath === '/panier' ? 'is-active' : '' ?>" href="<?= e(url('/panier')) ?>">Panier</a>
                <form method="post" action="<?= e(url('/deconnexion')) ?>" class="inline-form">
                    <input type="hidden" name="_csrf" value="<?= e(csrf_token('logout')) ?>">
                    <button class="link-button" type="submit">Déconnexion</button>
                </form>
            <?php else: ?>
                <a class="<?= $currentPath === '/connexion' ? 'is-active' : '' ?>" href="<?= e(url('/connexion')) ?>">Connexion</a>
                <a class="<?= $currentPath === '/inscription' ? 'is-active' : '' ?>" href="<?= e(url('/inscription')) ?>">Inscription</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="site-main">
        <?php if ($successMessage !== null): ?>
            <div class="alert alert-success" role="status"><?= e($successMessage) ?></div>
        <?php endif; ?>

        <?php if ($errorMessage !== null): ?>
            <div class="alert alert-error" role="alert"><?= e($errorMessage) ?></div>
        <?php endif; ?>

        <?= $content ?>
    </main>
</div>
<script src="<?= e(asset_url('assets/js/app.js')) ?>" defer></script>
</body>
</html>
