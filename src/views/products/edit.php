<?php 
$title = "Dashboard - " . APP_NAME;
ob_start();
?>


<div class="container-fluid">
    <h1 class="mt-4">Editar Producto</h1>
    <p class="lead">Modificar información del producto.</p>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i>
            Formulario de Producto
        </div>
        <div class="card-body">
            <?php if (isset($errors['general'])): ?>
                <div class="alert alert-danger"><?= $errors['general'] ?></div>
            <?php endif; ?>
            
            <form action="/products/update/<?= $product['id'] ?>" method="POST">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="code" class="form-label">Código *</label>
                            <input type="text" class="form-control <?= isset($errors['code']) ? 'is-invalid' : '' ?>" id="code" name="code" value="<?= htmlspecialchars($product['code'] ?? '') ?>" required>
                            <?php if (isset($errors['code'])): ?>
                                <div class="invalid-feedback"><?= $errors['code'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre *</label>
                            <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= htmlspecialchars($product['name'] ?? '') ?>" required>
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback"><?= $errors['name'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Descripción</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="purchase_price" class="form-label">Precio de Compra *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" class="form-control <?= isset($errors['purchase_price']) ? 'is-invalid' : '' ?>" id="purchase_price" name="purchase_price" value="<?= htmlspecialchars($product['purchase_price'] ?? '') ?>" required>
                                <?php if (isset($errors['purchase_price'])): ?>
                                    <div class="invalid-feedback"><?= $errors['purchase_price'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="sale_price" class="form-label">Precio de Venta *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" class="form-control <?= isset($errors['sale_price']) ? 'is-invalid' : '' ?>" id="sale_price" name="sale_price" value="<?= htmlspecialchars($product['sale_price'] ?? '') ?>" required>
                                <?php if (isset($errors['sale_price'])): ?>
                                    <div class="invalid-feedback"><?= $errors['sale_price'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock *</label>
                            <input type="number" min="0" class="form-control <?= isset($errors['stock']) ? 'is-invalid' : '' ?>" id="stock" name="stock" value="<?= htmlspecialchars($product['stock'] ?? '0') ?>" required>
                            <?php if (isset($errors['stock'])): ?>
                                <div class="invalid-feedback"><?= $errors['stock'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Actualizar Producto</button>
                    <a href="/products" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . "/views/layout/main.php";
?>