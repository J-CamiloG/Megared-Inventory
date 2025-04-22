<?php
class PurchaseController extends Controller {
    protected $purchaseModel;
    protected $productModel;
    protected $supplierModel;
    
    public function __construct() {
        parent::__construct();
        $this->purchaseModel = new Purchase();
        $this->productModel = new Product();
        $this->supplierModel = new Supplier();
        $this->requireAuth(); 
    }

    public function index() {
        $purchases = $this->purchaseModel->getAll();
        
        $this->view('purchases/index', [
            'purchases' => $purchases,
            'title' => 'Gestión de Compras'
        ]);
    }

    public function create() {
        $products = $this->productModel->getAll();
        $suppliers = $this->supplierModel->getAll();
        
        $this->view('purchases/create', [
            'products' => $products,
            'suppliers' => $suppliers,
            'referenceNumber' => $this->purchaseModel->generateReferenceNumber(), 
            'title' => 'Nueva Compra'
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $reference_number = $_POST['reference_number'] ?? '';
            $supplier_id = $_POST['supplier_id'] ?? '';
            $purchase_date = $_POST['purchase_date'] ?? date('Y-m-d H:i:s');
            $notes = $_POST['notes'] ?? '';
            
            if (!isset($_POST['product_id']) || !is_array($_POST['product_id']) || count($_POST['product_id']) === 0) {
                $_SESSION['flash_message'] = 'Debe agregar al menos un producto a la compra';
                $_SESSION['flash_type'] = 'danger';
                
                $this->redirect('/purchases/create');
                return;
            }
            
            $items = [];
            $total_amount = 0;
            
            for ($i = 0; $i < count($_POST['product_id']); $i++) {
                $product_id = $_POST['product_id'][$i];
                $quantity = floatval($_POST['quantity'][$i]);
                $unit_price = floatval($_POST['unit_price'][$i]);
                $subtotal = $quantity * $unit_price;
                
                $items[] = [
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'unit_price' => $unit_price,
                    'subtotal' => $subtotal
                ];
                
                $total_amount += $subtotal;
            }
            
            $purchaseData = [
                'reference_number' => $reference_number,
                'user_id' => $_SESSION['user_id'] ?? 1,
                'supplier_id' => $supplier_id,
                'total_amount' => $total_amount,
                'notes' => $notes,
                'purchase_date' => $purchase_date,
                'items' => $items
            ];
            
            try {
                $purchase_id = $this->purchaseModel->create($purchaseData);
                
                $_SESSION['flash_message'] = 'Compra registrada correctamente';
                $_SESSION['flash_type'] = 'success';
                
                $this->redirect('/purchases');
            } catch (Exception $e) {
                $_SESSION['flash_message'] = 'Error al registrar la compra: ' . $e->getMessage();
                $_SESSION['flash_type'] = 'danger';
                
                $this->redirect('/purchases/create');
            }
        } else {
            $this->redirect('/purchases/create');
        }
    }

    public function details($id) {
        if (is_array($id)) {
            if (isset($id['id'])) {
                $id = $id['id'];
            } elseif (isset($id[0])) {
                $id = $id[0];
            } else {
                $id = 0;
            }
        }
        
        $id = (int)$id;
        
        $purchase = $this->purchaseModel->getById($id);
        
        if (!$purchase) {
            $_SESSION['flash_message'] = 'Compra no encontrada';
            $_SESSION['flash_type'] = 'danger';
            
            $this->redirect('/purchases');
            return;
        }
        
        $details = $this->purchaseModel->getDetailsByPurchaseId($id);
        
        $this->view('purchases/view', [
            'purchase' => $purchase,
            'details' => $details,
            'title' => 'Detalle de Compra #' . $purchase['reference_number']
        ]);
    }
}