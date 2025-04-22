<?php
// configuracion bd
define('DB_HOST', getenv('DB_HOST') ?: 'b1fwlu040o0pjurosjp8-mysql.services.clever-cloud.com');
define('DB_NAME', getenv('DB_NAME') ?: 'b1fwlu040o0pjurosjp8');
define('DB_USER', getenv('DB_USER') ?: 'ulhihvey1k4zyr7c');
define('DB_PASS', getenv('DB_PASS') ?: 'EGTPk59A6nOPISub1yC4');
define('DB_PORT', getenv('DB_PORT') ?: '3306');

// configuracion app 
define('APP_NAME', 'MEGARED Inventory System');
define('APP_URL', 'http://localhost:8080');

// configuracion  seccion 
define('MAX_LOGIN_ATTEMPTS', 3);
define('SESSION_TIMEOUT', 3600); 

// configuracion paginacion e iva
// define('ITEMS_PER_PAGE', 10);
// define('TAX_RATE', 0.19); 