<?php 
$title = "Recuperación de Contraseña - " . APP_NAME;
ob_start();
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm mt-5">
                <div class="card-body p-4">
                    <h1 class="h4 text-center mb-4">Recuperación de Contraseña</h1>
                    
                    <div class="alert alert-success" role="alert">
                        <?= $message ?? 'Se ha enviado un enlace de recuperación a tu correo electrónico.' ?>
                    </div>
                    
                    <?php if (isset($resetLink)): ?>
                        <div class="alert alert-info" role="alert">
                            <p><strong>Enlace de recuperación (solo para desarrollo):</strong></p>
                            <a href="<?= $resetLink ?>" class="d-block text-break"><?= $resetLink ?></a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="text-center mt-4">
                        <a href="/login" class="btn btn-primary">Volver al inicio de sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . "/views/layout/main.php";
?>