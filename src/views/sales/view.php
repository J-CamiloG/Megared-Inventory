<?php 
$title = "Detalle de Venta - " . APP_NAME;
ob_start();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Detalle de Venta</h1>
        <div>
            <a href="/sales" class="btn btn-secondary mr-2">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <a href="/sales/pdf/<?= $sale['id'] ?>" class="btn btn-danger" target="_blank">
                <i class="fas fa-file-pdf"></i> Generar PDF
            </a>
        </div>
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
    
    <?php if (isset($sale) && $sale): ?>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Información de la Venta</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Factura:</th>
                                <td><?= htmlspecialchars($sale['invoice_number']) ?></td>
                            </tr>
                            <tr>
                                <th>Fecha:</th>
                                <td><?= date('d/m/Y H:i', strtotime($sale['sale_date'])) ?></td>
                            </tr>
                            <tr>
                                <th>Total:</th>
                                <td><strong>$<?= number_format($sale['total_amount'], 2) ?></strong></td>
                            </tr>
                            <tr>
                                <th>Notas:</th>
                                <td><?= nl2br(htmlspecialchars($sale['notes'] ?? '')) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Información del Cliente y Vendedor</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Cliente:</th>
                                <td><?= htmlspecialchars($sale['customer_name'] ?? 'N/A') ?></td>
                            </tr>
                            <tr>
                                <th>Vendedor:</th>
                                <td><?= htmlspecialchars($sale['first_name'] . ' ' . $sale['last_name']) ?></td>
                            </tr>
                            <tr>
                                <th>Usuario:</th>
                                <td><?= htmlspecialchars($sale['username']) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Productos Vendidos</h5>
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
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-right"><strong>Total:</strong></td>
                                <td><strong>$<?= number_format($sale['total_amount'], 2) ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            Venta no encontrada
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . "/views/layout/main.php";
?>