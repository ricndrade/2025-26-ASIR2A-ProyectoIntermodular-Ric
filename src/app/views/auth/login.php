<?php
$errors = $_SESSION['errors'] ?? [];
$old    = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

$pageTitle = 'Iniciar sesion';
$galleryHref = '/login';
$headerActions = [
    ['href' => '/register', 'label' => 'Crear cuenta', 'icon' => 'profile', 'visible' => true],
];

require dirname(__DIR__) . '/partials/header.php';
?>
<main class="page-main page-main-auth">
    <section class="surface-card surface-card-narrow">
        <div class="surface-head">
            <h1 class="surface-title">Iniciar sesion</h1>
            <p class="surface-copy">Accede para gestionar tu perfil y tus fotos.</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert-stack">
                <?php foreach ($errors as $error): ?>
                    <p class="alert-message alert-message-error"><?= htmlspecialchars($error) ?></p>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <form class="form-stack" method="POST" action="/login">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <label class="field-group">
                <span class="field-label">Email</span>
                <input
                    class="text-input"
                    type="email"
                    name="email"
                    value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                    required
                >
            </label>

            <label class="field-group">
                <span class="field-label">Contrasena</span>
                <input class="text-input" type="password" name="password" required>
            </label>

            <button class="button button-primary" type="submit">Entrar</button>
        </form>

        <p class="inline-note">No tienes cuenta? <a class="inline-link" href="/register">Registrate</a></p>
    </section>
</main>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
