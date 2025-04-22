<?php 
$title = "Detalle de Compra - " . APP_NAME;
ob_start();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Detalle de Compra</h1>
        <a href="/purchases" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
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
    
    <?php if (isset($purchase) && $purchase): ?>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Información de la Compra</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Referencia:</th>
                                <td><?= htmlspecialchars($purchase['reference_number']) ?></td>
                            </tr>
                            <tr>
                                <th>Fecha:</th>
                                <td><?= date('d/m/Y H:i', strtotime($purchase['purchase_date'])) ?></td>
                            </tr>
                            <tr>
                                <th>Subtotal:</th>
                                <td>$<?= number_format($purchase['subtotal'], 2) ?></td>
                            </tr>
                            <tr>
                                <th>Impuesto:</th>
                                <td>$<?= number_format($purchase['tax_amount'], 2) ?></td>
                            </tr>
                            <tr>
                                <th>Total:</th>
                                <td><strong>$<?= number_format($purchase['total_amount'], 2) ?></strong></td>
                            </tr>
                            <tr>
                                <th>Notas:</th>
                                <td><?= nl2br(htmlspecialchars($purchase['notes'] ?? '')) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Información del Proveedor</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Nombre:</th>
                                <td><?= htmlspecialchars($purchase['supplier_name'] ?? 'N/A') ?></td>
                            </tr>
                            <tr>
                                <th>Contacto:</th>
                                <td><?= htmlspecialchars($purchase['contact_name'] ?? 'N/A') ?></td>
                            </tr>
                            <tr>
                                <th>Teléfono:</th>
                                <td><?= htmlspecialchars($purchase['phone'] ?? 'N/A') ?></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td><?= htmlspecialchars($purchase['email'] ?? 'N/A') ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Productos Comprados</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($details)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No hay detalles disponibles</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($details as $detail): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($detail['product_code']) ?></td>
                                        <td><?= htmlspecialchars($detail['product_name']) ?></td>
                                        <td><?= $detail['quantity'] ?></td>
                                        <td>$<?= number_format($detail['unit_price'], 2) ?></td>
                                        <td>$<?= number_format($detail['subtotal'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                                <td>$<?= number_format($purchase['subtotal'], 2) ?></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-right"><strong>Impuesto (18%):</strong></td>
                                <td>$<?= number_format($purchase['tax_amount'], 2) ?></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-right"><strong>Total:</strong></td>
                                <td><strong>$<?= number_format($purchase['total_amount'], 2) ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            Compra no encontrada
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . "/views/layout/main.php";
?>