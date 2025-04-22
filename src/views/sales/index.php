<?php 
$title = "Gestión de Ventas - " . APP_NAME;

ob_start();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Gestión de Ventas</h1>
        <a href="/sales/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Venta
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
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Filtros</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="/sales" class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="start_date">Fecha Inicio</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $startDate ?? '' ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="end_date">Fecha Fin</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $endDate ?? '' ?>">
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">Filtrar</button>
                    <a href="/sales" class="btn btn-secondary">Limpiar</a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Factura</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Vendedor</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sales)): ?>
                            <tr>
                                <td colspan="9" class="text-center">No hay ventas registradas</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sales as $sale): ?>
                                <tr>
                                    <td><?= $sale['id'] ?></td>
                                    <td><?= htmlspecialchars($sale['invoice_number']) ?></td>
                                    <td><?= htmlspecialchars($sale['customer_name'] ?? 'N/A') ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($sale['sale_date'])) ?></td>
                                    <td><?= htmlspecialchars($sale['username']) ?></td>
                                    <td>$<?= number_format($sale['total_amount'], 2) ?></td>
                                    <td>
                                        <a href="/sales/details/<?= $sale['id'] ?>" class="btn btn-sm btn-info" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/sales/pdf/<?= $sale['id'] ?>" class="btn btn-sm btn-danger" title="Generar PDF" target="_blank">
                                            <i class="fas fa-file-pdf"></i>
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