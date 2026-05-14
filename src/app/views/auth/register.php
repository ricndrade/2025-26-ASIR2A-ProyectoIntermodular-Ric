<?php
require dirname(__DIR__) . '/partials/header.php';
?>
<main class="page-main page-main-auth">
    <section class="surface-card surface-card-narrow">
        <div class="surface-head">
            <h1 class="surface-title">Crear cuenta</h1>
            <p class="surface-copy">Registra tu usuario y entra directo a tu galeria.</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert-stack">
                <?php foreach ($errors as $error): ?>
                    <p class="alert-message alert-message-error"><?= htmlspecialchars($error) ?></p>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <form class="form-stack" method="POST" action="/register">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <label class="field-group">
                <span class="field-label">Usuario</span>
                <input
                    class="text-input"
                    type="text"
                    name="username"
                    value="<?= htmlspecialchars((string) ($old['username'] ?? '')) ?>"
                    required
                >
            </label>

            <label class="field-group">
                <span class="field-label">Email</span>
                <input
                    class="text-input"
                    type="email"
                    name="email"
                    value="<?= htmlspecialchars((string) ($old['email'] ?? '')) ?>"
                    required
                >
            </label>

            <label class="field-group">
                <span class="field-label">Contrasena</span>
                <input class="text-input" type="password" name="password" required>
            </label>

            <button class="button button-primary" type="submit">Registrarse</button>
        </form>

        <p class="inline-note">Ya tienes cuenta? <a class="inline-link" href="/login">Inicia sesion</a></p>
    </section>
</main>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
