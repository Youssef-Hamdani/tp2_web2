<section class="auth-layout">
    <div class="panel">
        <p class="eyebrow">Réinitialisation</p>
        <h1>Mot de passe oublié</h1>
        <p>Entrez votre courriel. Si le compte existe, vous recevrez un lien de réinitialisation.</p>
        <form method="post" action="<?= e(url('/mot-de-passe-oublie')) ?>" class="stack-form">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token('forgot-password')) ?>">

            <label for="email">Courriel</label>
            <input id="email" name="email" type="email" required value="<?= e((string) old('email')) ?>">

            <button class="button button-primary" type="submit">Envoyer le lien</button>
        </form>
    </div>
</section>
