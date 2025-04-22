<?php 
$title = "Dashboard - " . APP_NAME;
ob_start();
?>


<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-danger text-white">
            <h5>Confirmar eliminación</h5>
        </div>
        <div class="card-body">
            <p>¿Estás seguro de que deseas eliminar el producto <strong><?= htmlspecialchars($product['name']) ?></strong>?</p>
            
            <?php if ($relatedCount > 0): ?>
                <div class="alert alert-warning">
                    <strong>¡Advertencia!</strong> Este producto tiene <?= $relatedCount ?> registros relacionados en compras.
                    Estos registros también serán eliminados.
                </div>
            <?php endif; ?>
            
            <form action="/products/delete/<?= $product['id'] ?>" method="post">
                <button type="submit" class="btn btn-danger">Sí, eliminar</button>
                <a href="/products" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>


<?php
$content = ob_get_clean();
require BASE_PATH . "/views/layout/main.php";
?>