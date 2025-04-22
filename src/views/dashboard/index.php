<?php 
$title = "Dashboard - " . APP_NAME;
ob_start();
?>

<div class="container-fluid">
    
    <div class="row mt-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <h5>Productos</h5>
                    <h2><?= $productCount ?? 0 ?></h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="/products">Ver detalles</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-shopping-cart me-1"></i>
                    Compras recientes
                </div>
                <div class="card-body">
                    <?php if (empty($recentPurchases)): ?>
                        <p>No hay compras recientes.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Referencia</th>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentPurchases as $purchase): ?>
                                        <tr>
                                            <td><?= $purchase["reference_number"] ?></td>
                                            <td><?= date("d/m/Y", strtotime($purchase["purchase_date"])) ?></td>
                                            <td>$<?= number_format($purchase["total_amount"], 2) ?></td>
                                            <td>
                                                <a href="/purchases/view/<?= $purchase["id"] ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
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
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cash-register me-1"></i>
                    Ventas recientes
                </div>
                <div class="card-body">
                    <?php if (empty($recentSales)): ?>
                        <p>No hay ventas recientes.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Factura</th>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentSales as $sale): ?>
                                        <tr>
                                            <td><?= $sale["invoice_number"] ?></td>
                                            <td><?= date("d/m/Y", strtotime($sale["sale_date"])) ?></td>
                                            <td>$<?= number_format($sale["total_amount"], 2) ?></td>
                                            <td>
                                                <a href="/sales/details/<?= $sale['id'] ?>" class="btn btn-sm btn-info" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="/sales/pdf/<?= $sale["id"] ?>" class="btn btn-sm btn-danger" target="_blank">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
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
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . "/views/layout/main.php";
?>