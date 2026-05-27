<section class="auth-layout">
    <div class="panel">
        <p class="eyebrow">Connexion</p>
        <h1>Accéder à mon compte</h1>
        <form method="post" action="<?= e(url('/connexion')) ?>" class="stack-form">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token('login')) ?>">

            <label for="email">Courriel</label>
            <input id="email" name="email" type="email" required value="<?= e((string) old('email')) ?>">

            <label for="password">Mot de passe</label>
            <input id="password" name="password" type="password" required>

            <label class="checkbox-row">
                <input name="remember_me" type="checkbox" value="1">
                <span>Se souvenir de moi</span>
            </label>

            <button class="button button-primary" type="submit">Me connecter</button>
        </form>
        <p class="panel-link"><a href="<?= e(url('/mot-de-passe-oublie')) ?>">J’ai oublié mon mot de passe</a></p>
    </div>
</section>
