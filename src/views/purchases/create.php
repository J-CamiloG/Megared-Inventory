<?php 
$title = "Nueva Compra - " . APP_NAME;
ob_start();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Nueva Compra</h1>
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
    
    <div class="card">
        <div class="card-body">
            <form action="/purchases/store" method="POST" id="purchaseForm">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="reference_number">Número de Referencia</label>
                            <input type="text" class="form-control" id="reference_number" name="reference_number" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="supplier_id">Proveedor</label>
                            <select class="form-control" id="supplier_id" name="supplier_id" required>
                                <option value="">Seleccione un proveedor</option>
                                <?php if (isset($suppliers) && !empty($suppliers)): ?>
                                    <?php foreach ($suppliers as $supplier): ?>
                                        <option value="<?= $supplier['id'] ?>"><?= htmlspecialchars($supplier['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="purchase_date">Fecha de Compra</label>
                            <input type="datetime-local" class="form-control" id="purchase_date" name="purchase_date" value="<?= date('Y-m-d\TH:i') ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="notes">Notas</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                
                <h4 class="mt-4">Productos</h4>
                <div class="table-responsive">
                    <table class="table table-bordered" id="products-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th width="150">Cantidad</th>
                                <th width="200">Precio Unitario</th>
                                <th width="200">Subtotal</th>
                                <th width="50">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="row-template">
                                <td>
                                    <select class="form-control product-select" name="product_id[]" required>
                                        <option value="">Seleccione un producto</option>
                                        <?php if (isset($products) && !empty($products)): ?>
                                            <?php foreach ($products as $product): ?>
                                                <option value="<?= $product['id'] ?>" data-price="<?= $product['purchase_price'] ?>"><?= htmlspecialchars($product['name']) ?> (<?= htmlspecialchars($product['code']) ?>)</option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control quantity" name="quantity[]" min="1" value="1" required>
                                </td>
                                <td>
                                    <input type="number" class="form-control unit-price" name="unit_price[]" min="0.01" step="0.01" required>
                                </td>
                                <td>
                                    <input type="text" class="form-control subtotal" readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-row">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5">
                                    <button type="button" class="btn btn-success btn-sm" id="add-row">
                                        <i class="fas fa-plus"></i> Agregar Producto
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                                <td colspan="2"><span id="total-subtotal">$0.00</span></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Impuesto (18%):</strong></td>
                                <td colspan="2"><span id="total-tax">$0.00</span></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                <td colspan="2"><span id="total-amount">$0.00</span></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">Guardar Compra</button>
                    <a href="/purchases" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    addRow();

    document.getElementById('add-row').addEventListener('click', function() {
        addRow();
    });
    
    document.getElementById('products-table').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row') || e.target.parentElement.classList.contains('remove-row')) {
            const row = e.target.closest('tr');
            if (document.querySelectorAll('#products-table tbody tr').length > 1) {
                row.remove();
                calculateTotals();
            } else {
                alert('Debe haber al menos un producto');
            }
        }
    });
    
    document.getElementById('products-table').addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            const row = e.target.closest('tr');
            const option = e.target.options[e.target.selectedIndex];
            const price = option.dataset.price || 0;
            row.querySelector('.unit-price').value = price;
            updateRowSubtotal(row);
        }
        
        if (e.target.classList.contains('quantity') || e.target.classList.contains('unit-price')) {
            const row = e.target.closest('tr');
            updateRowSubtotal(row);
        }
    });
    
    document.getElementById('products-table').addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity') || e.target.classList.contains('unit-price')) {
            const row = e.target.closest('tr');
            updateRowSubtotal(row);
        }
    });
    
    function addRow() {
        const template = document.getElementById('row-template');
        const newRow = template.cloneNode(true);
        newRow.id = '';
        document.querySelector('#products-table tbody').appendChild(newRow);
    }
    
    function updateRowSubtotal(row) {
        const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
        const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
        const subtotal = quantity * unitPrice;
        row.querySelector('.subtotal').value = subtotal.toFixed(2);
        calculateTotals();
    }
    
    function calculateTotals() {
        let subtotal = 0;
        document.querySelectorAll('#products-table tbody tr').forEach(function(row) {
            const rowSubtotal = parseFloat(row.querySelector('.subtotal').value) || 0;
            subtotal += rowSubtotal;
        });
        
        const taxRate = 0.18; 
        const tax = subtotal * taxRate;
        const total = subtotal + tax;
        
        document.getElementById('total-subtotal').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('total-tax').textContent = '$' + tax.toFixed(2);
        document.getElementById('total-amount').textContent = '$' + total.toFixed(2);
    }
    
    document.getElementById('purchaseForm').addEventListener('submit', function(e) {
        const rows = document.querySelectorAll('#products-table tbody tr');
        if (rows.length === 0) {
            e.preventDefault();
            alert('Debe agregar al menos un producto');
            return false;
        }
        
        let valid = true;
        rows.forEach(function(row) {
            const product = row.querySelector('.product-select').value;
            const quantity = row.querySelector('.quantity').value;
            const unitPrice = row.querySelector('.unit-price').value;
            
            if (!product || !quantity || !unitPrice) {
                valid = false;
            }
        });
        
        if (!valid) {
            e.preventDefault();
            alert('Por favor complete todos los campos de productos');
            return false;
        }
    });
});
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . "/views/layout/main.php";
?>