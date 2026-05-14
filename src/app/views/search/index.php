<?php
$isLoggedIn = isLoggedIn();
$pageTitle = 'Buscar';
$galleryHref = $isLoggedIn ? '/u/' . $_SESSION['username'] : '/login';
$headerActions = $isLoggedIn
    ? [
        ['href' => '/upload', 'label' => 'Subir foto', 'icon' => 'upload', 'visible' => true],
        ['href' => '/settings', 'label' => 'Editar perfil', 'icon' => 'settings', 'visible' => true],
        ['href' => '/search', 'label' => 'Buscar', 'icon' => 'search', 'visible' => true],
        ['href' => '/logout', 'label' => 'Cerrar sesion', 'icon' => 'logout', 'visible' => true],
    ]
    : [
        ['href' => '/search', 'label' => 'Buscar', 'icon' => 'search', 'visible' => true],
        ['href' => '/login', 'label' => 'Perfil o iniciar sesion', 'icon' => 'profile', 'visible' => true],
    ];

$query = trim((string) ($_GET['q'] ?? ''));

require dirname(__DIR__) . '/partials/header.php';
?>
<main class="page-main">
    <section class="surface-card">
        <div class="surface-head">
            <h1 class="surface-title">Buscar usuarios</h1>
            <p class="surface-copy">Busca por usuario o nombre publico.</p>
        </div>

        <form class="search-form" method="GET" action="/search">
            <input
                class="text-input text-input-search"
                type="text"
                name="q"
                value="<?= htmlspecialchars($query) ?>"
                placeholder="Buscar por usuario o nombre..."
                autofocus
            >
            <button class="button button-primary" type="submit">Buscar</button>
        </form>
    </section>

    <section class="surface-card">
        <?php if (!empty($results)): ?>
            <ul class="result-list">
                <?php foreach ($results as $result): ?>
                    <?php
                    $name = trim((string) ($result['display_name'] ?? '')) ?: (string) $result['username'];
                    $avatarUrl = !empty($result['profile_image']) ? '/uploads/' . rawurlencode((string) $result['profile_image']) : null;
                    ?>
                    <li class="result-item">
                        <?php if ($avatarUrl): ?>
                            <img
                                class="result-avatar"
                                src="<?= htmlspecialchars($avatarUrl) ?>"
                                alt="Foto de perfil de <?= htmlspecialchars($name) ?>"
                            >
                        <?php else: ?>
                            <div class="result-avatar result-avatar-fallback" aria-hidden="true">
                                <?= htmlspecialchars(strtoupper(substr((string) $result['username'], 0, 1) ?: '?')) ?>
                            </div>
                        <?php endif ?>

                        <div class="result-copy">
                            <a class="result-link" href="/u/<?= htmlspecialchars((string) $result['username']) ?>">
                                <?= htmlspecialchars($name) ?>
                            </a>
                            <p class="result-meta">@<?= htmlspecialchars((string) $result['username']) ?></p>
                            <?php if ((int) $result['total_fotos'] > 0): ?>
                                <p class="result-count">
                                    <?= (int) $result['total_fotos'] ?> foto<?= (int) $result['total_fotos'] === 1 ? '' : 's' ?>
                                </p>
                            <?php endif ?>
                        </div>
                    </li>
                <?php endforeach ?>
            </ul>
        <?php elseif ($query !== ''): ?>
            <p class="empty-state">No se encontraron usuarios.</p>
        <?php else: ?>
            <p class="empty-state">Escribe una busqueda para empezar.</p>
        <?php endif ?>
    </section>
</main>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
