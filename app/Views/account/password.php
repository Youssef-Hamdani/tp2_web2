<section class="auth-layout">
    <div class="panel">
        <p class="eyebrow">Sécurité</p>
        <h1>Modifier mon mot de passe</h1>
        <form method="post" action="<?= e(url('/compte/mot-de-passe')) ?>" class="stack-form">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token('change-password')) ?>">

            <label for="current_password">Mot de passe actuel</label>
            <input id="current_password" name="current_password" type="password" required>

            <label for="password">Nouveau mot de passe</label>
            <input id="password" name="password" type="password" required minlength="10">

            <label for="password_confirmation">Confirmer le nouveau mot de passe</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required minlength="10">

            <button class="button button-primary" type="submit">Enregistrer</button>
        </form>
    </div>
</section>
