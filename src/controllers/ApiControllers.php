<?php
class ApiController extends Controller {
    private $productModel;
    private $saleModel;
    
    public function __construct() {
        parent::__construct();
        $this->productModel = new Product();
        $this->saleModel = new Sale();
    }
    
    public function getProducts() {
        $products = $this->productModel->getAll();
        $this->json($products);
    }
    
    public function getProduct($params) {
        $id = $params['id'] ?? 0;
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            $this->json(['error' => 'Product not found'], 404);
        }
        
        $this->json($product);
    }
    
    public function createSale() {
        // Check if user is authenticated
        if (!$this->isAuthenticated()) {
            $this->json(['error' => 'Unauthorized'], 401);
        }
        
        // Get JSON data
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            $this->json(['error' => 'Invalid JSON data'], 400);
        }
        
        // Validate required fields
        if (empty($data['items']) || !is_array($data['items'])) {
            $this->json(['error' => 'Items are required'], 400);
        }
        
        // Calculate totals
        $subtotal = 0;
        foreach ($data['items'] as &$item) {
            $product = $this->productModel->getById($item['product_id']);
            
            if (!$product) {
                $this->json(['error' => 'Product not found: ' . $item['product_id']], 400);
            }
            
            if ($item['quantity'] > $product['stock']) {
                $this->json(['error' => 'Insufficient stock for product: ' . $product['name']], 400);
            }
            
            $item['unit_price'] = $product['sale_price'];
            $item['subtotal'] = $item['unit_price'] * $item['quantity'];
            $subtotal += $item['subtotal'];
        }
        
        $taxAmount = $subtotal * TAX_RATE;
        $totalAmount = $subtotal + $taxAmount;
        
        // Create sale data
        $saleData = [
            'invoice_number' => $this->saleModel->generateInvoiceNumber(),
            'user_id' => $_SESSION['user_id'],
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'notes' => $data['notes'] ?? null,
            'items' => $data['items']
        ];
        
        try {
            $saleId = $this->saleModel->create($saleData);
            
            $sale = $this->saleModel->getById($saleId);
            $saleDetails = $this->saleModel->getDetailsBySaleId($saleId);
            
            $this->json([
                'success' => true,
                'sale' => $sale,
                'details' => $saleDetails
            ]);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function getSales() {
        // Check if user is authenticated
        if (!$this->isAuthenticated()) {
            $this->json(['error' => 'Unauthorized'], 401);
        }
        
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;
        
        $sales = $this->saleModel->getAll($startDate, $endDate);
        
        $this->json($sales);
    }
}