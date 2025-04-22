<?php 
$title = "Dashboard - " . APP_NAME;
ob_start();
?>


<div class="container-fluid">
    <h1 class="mt-4">Búsqueda de Productos</h1>
    <p class="lead">Resultados para: "<?= htmlspecialchars($term) ?>"</p>
    
    <!-- Barra de acciones -->
    <div class="row mb-3">
        <div class="col-md-6">
            <a href="/products" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a la Lista
            </a>
        </div>
        <div class="col-md-6">
            <form action="/products/search" method="GET" class="d-flex">
                <input type="text" name="term" class="form-control me-2" placeholder="Buscar productos..." value="<?= htmlspecialchars($term) ?>" required>
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>
    
    <!-- Tabla de productos -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-search me-1"></i>
            Resultados de la Búsqueda
        </div>
        <div class="card-body">
            <?php if (empty($products)): ?>
                <p class="text-center">No se encontraron productos que coincidan con la búsqueda.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Precio Compra</th>
                                <th>Precio Venta</th>
                                <th>Stock</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?= htmlspecialchars($product['code']) ?></td>
                                    <td><?= htmlspecialchars($product['name']) ?></td>
                                    <td>$<?= number_format($product['purchase_price'], 2) ?></td>
                                    <td>$<?= number_format($product['sale_price'], 2) ?></td>
                                    <td>
                                        <?php if ($product['stock'] <= 5): ?>
                                            <span class="badge bg-danger"><?= $product['stock'] ?></span>
                                        <?php elseif ($product['stock'] <= 10): ?>
                                            <span class="badge bg-warning"><?= $product['stock'] ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><?= $product['stock'] ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="/products/view/<?= $product['id'] ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="/products/edit/<?= $product['id'] ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="/products/delete/<?= $product['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar este producto?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

require BASE_PATH . "/views/layout/main.php";
?>