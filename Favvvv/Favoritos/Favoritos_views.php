

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tus Favoritos</title>
    <link rel="stylesheet" href="<?= media(); ?>/css/Favoritos.css">
</head>
<body>
<?php if (empty($data['favoritos'])): ?>
    <section class="favoritos-empty">
        <h2>No tenés destinos favoritos todavía.</h2>
        <p>Agregá destinos a favoritos desde la lista de productos.</p>
    </section>
<?php else: ?>
    <section class="favoritos-container">
        <h2>Tus Destinos Favoritos</h2>
        <div class="favoritos-grid">
            <?php foreach ($data['favoritos'] as $fila): ?>
                <article class="card">
                    <img class="card-img" src="<?= media(); ?>/images/temp/<?= htmlspecialchars($fila['imagen'] ?? 'default.png'); ?>" alt="Imagen destino">
                    <div class="card-content">
                        <h3><?= htmlspecialchars($fila['DESTINO_TURISTICO_nombre']); ?></h3>
                        <p class="destino"><?= htmlspecialchars($fila['DESTINO_TURISTICO_tipo_destino']); ?></p>
                        <p class="comentario"><?= htmlspecialchars($fila['DESTINO_TURISTICO_descripcion']); ?></p>
                        <div class="popularidad">Popularidad: <?= htmlspecialchars($fila['DESTINO_TURISTICO_popularidad']); ?> ⭐</div>
                        <div class="card-buttons">
                            <button class="btn-fav is-fav" data-id="<?= htmlspecialchars($fila['Id_destino']); ?>">Quitar</button>
                            <a class="btn" href="<?= BASE_URL; ?>/productos/detalle/<?= htmlspecialchars($fila['Id_destino']); ?>">Ver</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<script>const BASE_URL_JS = "<?= BASE_URL ?>";</script>
<script src="<?= media(); ?>/js/favoritos.js"></script>
</body>
</html>