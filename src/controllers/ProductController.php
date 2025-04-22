<?php
class ProductController extends Controller {
    private $productModel;
    
    public function __construct() {
        parent::__construct();
        $this->productModel = new Product();
    }
    
    public function index() {
        $this->requireAuth();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
    
        $products = $this->productModel->getAll($limit, $offset);
        $totalProducts = $this->productModel->count();
        $totalPages = ceil($totalProducts / $limit);

        $this->view('products/index', [
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts
        ]);
    }
    
    public function create() {
        $this->requireAuth();

        $this->view('products/create', [
            'errors' => [],
            'product' => []
        ]);
    }
    
    public function store() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /products/create');
            exit;
        }

        $errors = [];
        
        if (empty($_POST['code'])) {
            $errors['code'] = 'El código es obligatorio';
        } else {
            $existingProduct = $this->productModel->getByCode($_POST['code']);
            if ($existingProduct) {
                $errors['code'] = 'Este código ya está en uso';
            }
        }
        
        if (empty($_POST['name'])) {
            $errors['name'] = 'El nombre es obligatorio';
        }
        
        if (!isset($_POST['purchase_price']) || !is_numeric($_POST['purchase_price']) || $_POST['purchase_price'] < 0) {
            $errors['purchase_price'] = 'El precio de compra debe ser un número positivo';
        }
        
        if (!isset($_POST['sale_price']) || !is_numeric($_POST['sale_price']) || $_POST['sale_price'] < 0) {
            $errors['sale_price'] = 'El precio de venta debe ser un número positivo';
        }
        
        if (!isset($_POST['stock']) || !is_numeric($_POST['stock']) || $_POST['stock'] < 0) {
            $errors['stock'] = 'El stock debe ser un número positivo';
        }
        
        // Si hay errores, volver a mostrar el formulario con los errores
        if (!empty($errors)) {
            $this->view('products/create', [
                'errors' => $errors,
                'product' => $_POST
            ]);
            return;
        }
        
        $productData = [
            'code' => $_POST['code'],
            'name' => $_POST['name'],
            'description' => $_POST['description'] ?? '',
            'purchase_price' => $_POST['purchase_price'],
            'sale_price' => $_POST['sale_price'],
            'stock' => $_POST['stock']
        ];
        
        $productId = $this->productModel->create($productData);
        
        if ($productId) {
            $_SESSION['flash_message'] = 'Producto creado correctamente';
            $_SESSION['flash_message_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Error al crear el producto';
            $_SESSION['flash_message_type'] = 'danger';
        }
        
        header('Location: /products');
        exit;
    }
    
    public function edit($id) {
        $this->requireAuth();

        if (is_array($id)) {
            if (isset($id['id'])) {
                $id = $id['id'];
            } elseif (isset($id[0])) {
                $id = $id[0];
            } else {
                $id = null;
            }
        }
        
        if (!$id) {
            $_SESSION['flash_message'] = 'ID de producto no válido';
            $_SESSION['flash_message_type'] = 'danger';
            header('Location: /products');
            exit;
        }
        
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            $_SESSION['flash_message'] = 'Producto no encontrado';
            $_SESSION['flash_message_type'] = 'danger';
            header('Location: /products');
            exit;
        }
        
        $this->view('products/edit', [
            'errors' => [],
            'product' => $product
        ]);
    }
    
    public function update($id) {
        $this->requireAuth();

        if (is_array($id)) {
            if (isset($id['id'])) {
                $id = $id['id'];
            } elseif (isset($id[0])) {
                $id = $id[0];
            } else {
                $id = null;
            }
        }
        
        if (!$id) {
            $_SESSION['flash_message'] = 'ID de producto no válido';
            $_SESSION['flash_message_type'] = 'danger';
            header('Location: /products');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /products/edit/' . $id);
            exit;
        }
        
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            $_SESSION['flash_message'] = 'Producto no encontrado';
            $_SESSION['flash_message_type'] = 'danger';
            header('Location: /products');
            exit;
        }
        
        $errors = [];
        
        if (empty($_POST['code'])) {
            $errors['code'] = 'El código es obligatorio';
        } else {
            $existingProduct = $this->productModel->getByCode($_POST['code']);
            if ($existingProduct && $existingProduct['id'] != $id) {
                $errors['code'] = 'Este código ya está en uso';
            }
        }
        
        if (empty($_POST['name'])) {
            $errors['name'] = 'El nombre es obligatorio';
        }
        
        if (!isset($_POST['purchase_price']) || !is_numeric($_POST['purchase_price']) || $_POST['purchase_price'] < 0) {
            $errors['purchase_price'] = 'El precio de compra debe ser un número positivo';
        }
        
        if (!isset($_POST['sale_price']) || !is_numeric($_POST['sale_price']) || $_POST['sale_price'] < 0) {
            $errors['sale_price'] = 'El precio de venta debe ser un número positivo';
        }
        
        if (!isset($_POST['stock']) || !is_numeric($_POST['stock']) || $_POST['stock'] < 0) {
            $errors['stock'] = 'El stock debe ser un número positivo';
        }
        
        if (!empty($errors)) {
            $this->view('products/edit', [
                'errors' => $errors,
                'product' => array_merge($product, $_POST)
            ]);
            return;
        }
        
        $productData = [
            'code' => $_POST['code'],
            'name' => $_POST['name'],
            'description' => $_POST['description'] ?? '',
            'purchase_price' => $_POST['purchase_price'],
            'sale_price' => $_POST['sale_price'],
            'stock' => $_POST['stock']
        ];
        
        $result = $this->productModel->update($id, $productData);
        
        if ($result) {
            $_SESSION['flash_message'] = 'Producto actualizado correctamente';
            $_SESSION['flash_message_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Error al actualizar el producto';
            $_SESSION['flash_message_type'] = 'danger';
        }
        
        header('Location: /products');
        exit;
    }
    
    public function delete($id) {
        $this->requireAuth();
    
        if (is_array($id)) {
            if (isset($id['id'])) {
                $id = $id['id'];
            } elseif (isset($id[0])) {
                $id = $id[0];
            } else {
                $id = null;
            }
        }
        
        if (!$id) {
            $_SESSION['flash_message'] = 'ID de producto no válido';
            $_SESSION['flash_message_type'] = 'danger';
            header('Location: /products');
            exit;
        }
        
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            $_SESSION['flash_message'] = 'Producto no encontrado';
            $_SESSION['flash_message_type'] = 'danger';
            header('Location: /products');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $db = Database::getInstance();
            $relatedCount = $db->fetch("SELECT COUNT(*) as count FROM purchase_details WHERE product_id = ?", [$id])['count'];
            
            $this->view('products/confirm-delete', [
                'product' => $product,
                'relatedCount' => $relatedCount
            ]);
            return;
        }
        try {
            $result = $this->productModel->delete($id);
            
            if ($result) {
                $_SESSION['flash_message'] = 'Producto eliminado correctamente';
                $_SESSION['flash_message_type'] = 'success';
            } else {
                $_SESSION['flash_message'] = 'Error al eliminar el producto';
                $_SESSION['flash_message_type'] = 'danger';
            }
        } catch (Exception $e) {
            $_SESSION['flash_message'] = 'Error al eliminar el producto: ' . $e->getMessage();
            $_SESSION['flash_message_type'] = 'danger';
        }
        
        header('Location: /products');
        exit;
    }
    public function search() {
        $this->requireAuth();
        $term = isset($_GET['term']) ? $_GET['term'] : '';
        
        if (empty($term)) {
            header('Location: /products');
            exit;
        }
        
        $products = $this->productModel->search($term);
        
        $this->view('products/search', [
            'products' => $products,
            'term' => $term
        ]);
    }
    
    public function details($id) {
        $this->requireAuth();

        if (is_array($id)) {
            if (isset($id['id'])) {
                $id = $id['id'];
            } elseif (isset($id[0])) {
                $id = $id[0];
            } else {
                $id = null;
            }
        }
        
        if (!$id) {
            $_SESSION['flash_message'] = 'ID de producto no válido';
            $_SESSION['flash_message_type'] = 'danger';
            header('Location: /products');
            exit;
        }
        
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            $_SESSION['flash_message'] = 'Producto no encontrado';
            $_SESSION['flash_message_type'] = 'danger';
            header('Location: /products');
            exit;
        }
        
        $this->view('products/view', [
            'product' => $product
        ]);
    }

}