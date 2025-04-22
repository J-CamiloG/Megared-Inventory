<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">Error 404 - Página no encontrada</h4>
                </div>
                <div class="card-body">
                    <p>Lo sentimos, la página que estás buscando no existe.</p>
                    <p><?= $message ?? "La URL solicitada no se encontró en este servidor." ?></p>
                    <a href="/" class="btn btn-primary">Volver al inicio</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . "/views/layout/main.php";
?>