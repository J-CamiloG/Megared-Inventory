<?php
class Router {
    private $routes = [
        'GET' => [],
        'POST' => []
    ];
    
    public function __construct() {
        // Define routes
        $this->defineRoutes();
    }
    
    private function defineRoutes() {
        // Auth rutas
        $this->get('/', 'AuthController@loginForm');
        $this->get('/login', 'AuthController@loginForm');
        $this->post('/login', 'AuthController@login');
        $this->get('/logout', 'AuthController@logout');
        $this->get('/forgot-password', 'AuthController@forgotPasswordForm');
        $this->post('/forgot-password', 'AuthController@forgotPassword');
        $this->get('/reset-password', 'AuthController@resetPasswordForm');
        $this->post('/reset-password', 'AuthController@resetPassword');
        
        // tablredo administracion
        $this->get('/dashboard', 'DashboardController@index');
        
        // Usuarios rutas
        $this->get('/users', 'UserController@index');
        $this->get('/users/create', 'UserController@create');
        $this->post('/users/store', 'UserController@store');
        $this->get('/users/edit/{id}', 'UserController@edit');
        $this->post('/users/update/{id}', 'UserController@update');
        $this->get('/users/toggleStatus/{id}', 'UserController@toggleStatus');
        $this->get('/users/block/{id}', 'UserController@block');
        $this->get('/users/unblock/{id}', 'UserController@unblock');
        
        // rutas prodcutos 
        $this->get('/products', 'ProductController@index');
        $this->get('/products/create', 'ProductController@create');
        $this->post('/products/store', 'ProductController@store');
        $this->get('/products/edit/{id}', 'ProductController@edit');
        $this->post('/products/update/{id}', 'ProductController@update');
        $this->get('/products/delete/{id}', 'ProductController@delete');
        $this->post('/products/delete/{id}', 'ProductController@delete');
        $this->get('/products/search', 'ProductController@search');
        $this->get('/products/view/{id}', 'ProductController@details');
        
        // compras 
        $this->get('/purchases', 'PurchaseController@index');
        $this->get('/purchases/create', 'PurchaseController@create');
        $this->post('/purchases/store', 'PurchaseController@store');
        $this->get('/purchases/view/{id}', 'PurchaseController@details'); 

        // ventas 
        $this->get('/sales', 'SaleController@index');
        $this->get('/sales/create', 'SaleController@create');
        $this->post('/sales/store', 'SaleController@store');
        $this->get('/sales/details/{id}', 'SaleController@details'); 
        $this->get('/sales/pdf/{id}', 'SaleController@generatePdf');
        
        // API routes
        // $this->get('/api/products', 'ApiController@getProducts');
        // $this->get('/api/products/{id}', 'ApiController@getProduct');
        // $this->post('/api/sales', 'ApiController@createSale');
        // $this->get('/api/sales', 'ApiController@getSales');
    }
    
    public function get($uri, $controller) {
        $this->routes['GET'][$uri] = $controller;
    }
    
    public function post($uri, $controller) {
        $this->routes['POST'][$uri] = $controller;
    }
    
    public function dispatch() {
        $uri = $this->getUri();
        $method = $_SERVER['REQUEST_METHOD'];

        $route = $this->matchRoute($uri, $method);
        
        if ($route) {
            list($controller, $action, $params) = $route;
            $this->callAction($controller, $action, $params);
        } else {
            $this->notFound();
        }
    }
    
    private function getUri() {
        $uri = $_SERVER['REQUEST_URI'];
        $uri = parse_url($uri, PHP_URL_PATH);
        
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        
        return $uri;
    }
    
    private function matchRoute($uri, $method) {
        if (isset($this->routes[$method][$uri])) {
            $controllerAction = $this->routes[$method][$uri];
            list($controller, $action) = explode('@', $controllerAction);
            return [$controller, $action, []];
        }
        
        foreach ($this->routes[$method] as $route => $controllerAction) {
            $pattern = preg_replace('/{[^}]+}/', '([^/]+)', $route);
            $pattern = "#^$pattern$#";
            
            if (preg_match($pattern, $uri, $matches)) {
                // Extract parameters
                $params = [];
                preg_match_all('/{([^}]+)}/', $route, $paramNames);
                array_shift($matches); 
                
                if (isset($paramNames[1])) {
                    foreach ($paramNames[1] as $index => $name) {
                        if (isset($matches[$index])) {
                            $params[$name] = $matches[$index];
                        }
                    }
                }
                
                list($controller, $action) = explode('@', $controllerAction);
                return [$controller, $action, $params];
            }
        }
        
        return false;
    }
    
    private function callAction($controller, $action, $params) {
        $controller = new $controller();
        
        if (method_exists($controller, $action)) {
            $controller->$action($params);
        } else {
            $this->notFound();
        }
    }
    
    private function notFound() {
        header("HTTP/1.0 404 Not Found");
        require BASE_PATH . '/views/errors/404.php';
        exit;
    }
}