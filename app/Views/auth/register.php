<section class="auth-layout">
    <div class="panel">
        <p class="eyebrow">Nouveau membre</p>
        <h1>Créer un compte</h1>
        <p>Un courriel d’activation sera envoyé après l’inscription.</p>
        <form method="post" action="<?= e(url('/inscription')) ?>" class="stack-form">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token('register')) ?>">

            <label for="email">Courriel</label>
            <input id="email" name="email" type="email" required value="<?= e((string) old('email')) ?>">

            <label for="password">Mot de passe</label>
            <input id="password" name="password" type="password" required minlength="10">

            <label for="password_confirmation">Confirmer le mot de passe</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required minlength="10">

            <button class="button button-primary" type="submit">Créer mon compte</button>
        </form>
    </div>
</section>
