<section class="auth-layout">
    <div class="panel">
        <p class="eyebrow">Nouveau mot de passe</p>
        <h1>Réinitialiser mon mot de passe</h1>
        <form method="post" action="<?= e(url('/reinitialiser-mot-de-passe')) ?>" class="stack-form">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token('reset-password')) ?>">
            <input type="hidden" name="email" value="<?= e($email) ?>">
            <input type="hidden" name="token" value="<?= e($token) ?>">

            <label for="password">Nouveau mot de passe</label>
            <input id="password" name="password" type="password" required minlength="10">

            <label for="password_confirmation">Confirmer le mot de passe</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required minlength="10">

            <button class="button button-primary" type="submit">Mettre à jour</button>
        </form>
    </div>
</section>
