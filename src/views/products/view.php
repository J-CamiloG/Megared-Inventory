<?php 
$title = "Dashboard - " . APP_NAME;
ob_start();
?>


<div class="container-fluid">
    <h1 class="mt-4">Detalles del Producto</h1>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-info-circle me-1"></i>
            Producto: <?= htmlspecialchars($product['name']) ?>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th>ID:</th>
                            <td><?= $product['id'] ?></td>
                        </tr>
                        <tr>
                            <th>Código:</th>
                            <td><?= htmlspecialchars($product['code']) ?></td>
                        </tr>
                        <tr>
                            <th>Nombre:</th>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                        </tr>
                        <tr>
                            <th>Descripción:</th>
                            <td><?= htmlspecialchars($product['description'] ?? 'N/A') ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th>Precio de Compra:</th>
                            <td>$<?= number_format($product['purchase_price'], 2) ?></td>
                        </tr>
                        <tr>
                            <th>Precio de Venta:</th>
                            <td>$<?= number_format($product['sale_price'], 2) ?></td>
                        </tr>
                        <tr>
                            <th>Margen de Ganancia:</th>
                            <td>
                                <?php 
                                $margin = $product['sale_price'] - $product['purchase_price'];
                                $marginPercent = ($margin / $product['purchase_price']) * 100;
                                echo number_format($marginPercent, 2) . '%';
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Stock:</th>
                            <td>
                                <?php if ($product['stock'] <= 5): ?>
                                    <span class="badge bg-danger"><?= $product['stock'] ?></span>
                                <?php elseif ($product['stock'] <= 10): ?>
                                    <span class="badge bg-warning"><?= $product['stock'] ?></span>
                                <?php else: ?>
                                    <span class="badge bg-success"><?= $product['stock'] ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Fecha de Creación:</th>
                            <td><?= date('d/m/Y H:i', strtotime($product['created_at'])) ?></td>
                        </tr>
                        <tr>
                            <th>Última Actualización:</th>
                            <td><?= $product['updated_at'] ? date('d/m/Y H:i', strtotime($product['updated_at'])) : 'N/A' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="mt-3">
                <a href="/products" class="btn btn-secondary">Volver a la Lista</a>
                <a href="/products/edit/<?= $product['id'] ?>" class="btn btn-warning">Editar</a>
                <a href="/products/delete/<?= $product['id'] ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de eliminar este producto?')">Eliminar</a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . "/views/layout/main.php";
?>