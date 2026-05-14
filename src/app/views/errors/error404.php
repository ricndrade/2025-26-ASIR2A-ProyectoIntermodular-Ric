<?php
$pageTitle = 'Página no encontrada';
$galleryHref = '/';
$headerActions = [];

require dirname(__DIR__) . '/partials/header.php';
?>

<main class="page-main error-main">
    <div class="surface-card surface-card-narrow error-container">
        <div class="surface-head">
            <h1 class="surface-title">404</h1>
            <p class="surface-copy">Página no encontrada</p>
        </div>

        <p class="error-message">
            Lo sentimos, la página que estás buscando no existe o ha sido movida.
        </p>

        <div class="form-stack">
            <a href="/" class="button button-primary" style="text-align: center;">
                Volver al inicio
            </a>
        </div>
    </div>
</main>

<?php
require dirname(__DIR__) . '/partials/footer.php';
?>


