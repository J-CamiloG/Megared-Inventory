<?php $title = "403 - Acceso denegado"; ?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">Error 403 - Acceso denegado</h4>
                </div>
                <div class="card-body">
                    <p>Lo sentimos, no tienes permiso para acceder a esta página.</p>
                    <p><?= $message ?? "No tienes los permisos necesarios para acceder a este recurso." ?></p>
                    <a href="/dashboard" class="btn btn-primary">Volver al panel</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . "/views/layout/main.php";
?>