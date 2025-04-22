<?php
// Configuración BD - Usa solo variables de entorno (Docker)
define('DB_HOST', getenv('DB_HOST'));
define('DB_NAME', getenv('DB_NAME'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASS', getenv('DB_PASS'));
define('DB_PORT', getenv('DB_PORT'));

// Configuración app 
define('APP_NAME', 'MEGARED Inventory System');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost:8080');

// Configuración sesión 
define('MAX_LOGIN_ATTEMPTS', 3);
define('SESSION_TIMEOUT', 3600); 

// Configuración paginación e IVA
// define('ITEMS_PER_PAGE', 10);
// define('TAX_RATE', 0.19);