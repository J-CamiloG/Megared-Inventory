<?php 
$title = "Gestión de Compras - " . APP_NAME;
ob_start();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Gestión de Compras</h1>
        <a href="/purchases/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Compra
        </a>
    </div>
    
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show">
            <?= $_SESSION['flash_message'] ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Referencia</th>
                            <th>Proveedor</th>
                            <th>Fecha</th>
                            <th>Subtotal</th>
                            <th>Impuesto</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($purchases)): ?>
                            <tr>
                                <td colspan="8" class="text-center">No hay compras registradas</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($purchases as $purchase): ?>
                                <tr>
                                    <td><?= $purchase['id'] ?></td>
                                    <td><?= htmlspecialchars($purchase['reference_number']) ?></td>
                                    <td><?= htmlspecialchars($purchase['supplier_name'] ?? 'N/A') ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($purchase['purchase_date'])) ?></td>
                                    <td>$<?= number_format($purchase['subtotal'], 2) ?></td>
                                    <td>$<?= number_format($purchase['tax_amount'], 2) ?></td>
                                    <td>$<?= number_format($purchase['total_amount'], 2) ?></td>
                                    <td>
                                        <a href="/purchases/view/<?= $purchase['id'] ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . "/views/layout/main.php";
?>