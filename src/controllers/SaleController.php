<?php
class SaleController extends Controller {
    protected $db;
    private $saleModel;
    private $productModel;
    
    public function __construct() {
        parent::__construct();
        $this->db = Database::getInstance();
        $this->saleModel = new Sale();
        $this->productModel = new Product();
    }

    public function index() {
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;

        $sales = $this->saleModel->getAll($startDate, $endDate);
        
        $this->view('sales/index', [
            'sales' => $sales,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'title' => 'Gestión de Ventas'
        ]);
    }

    public function create() {
        $invoiceNumber = $this->saleModel->generateInvoiceNumber();
        
        $sql = "SELECT * FROM products WHERE stock > 0 ORDER BY name";
        $products = $this->db->fetchAll($sql);
        
        $this->view('sales/create', [
            'products' => $products,
            'invoiceNumber' => $invoiceNumber,
            'title' => 'Nueva Venta'
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $invoice_number = $_POST['invoice_number'] ?? '';
            $customer_name = $_POST['customer_name'] ?? '';
            $payment_method = $_POST['payment_method'] ?? 'Efectivo';
            $notes = $_POST['notes'] ?? '';
            
            $total_amount = 0;
            

            if (!isset($_POST['product_id']) || !is_array($_POST['product_id']) || count($_POST['product_id']) === 0) {
                $_SESSION['flash_message'] = 'Debe agregar al menos un producto a la venta';
                $_SESSION['flash_type'] = 'danger';
                $this->redirect('/sales/create');
                return;
            }
            
            for ($i = 0; $i < count($_POST['product_id']); $i++) {
                $quantity = floatval($_POST['quantity'][$i]);
                $unit_price = floatval($_POST['unit_price'][$i]);
                $total_amount += $quantity * $unit_price;
            }
            
            $saleData = [
                'invoice_number' => $invoice_number,
                'user_id' => $_SESSION['user_id'] ?? 1, 
                'customer_name' => $customer_name,
                'total_amount' => $total_amount,
                'payment_method' => $payment_method,
                'notes' => $notes,
                'items' => []
            ];
            
            for ($i = 0; $i < count($_POST['product_id']); $i++) {
                $product_id = $_POST['product_id'][$i];
                $quantity = floatval($_POST['quantity'][$i]);
                $unit_price = floatval($_POST['unit_price'][$i]);
                $item_subtotal = $quantity * $unit_price;
                
                $product = $this->productModel->getById($product_id);
                if (!$product || $product['stock'] < $quantity) {
                    $_SESSION['flash_message'] = 'Stock insuficiente para el producto: ' . ($product['name'] ?? 'Desconocido');
                    $_SESSION['flash_type'] = 'danger';
                    $this->redirect('/sales/create');
                    return;
                }
                
                $saleData['items'][] = [
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'unit_price' => $unit_price,
                    'subtotal' => $item_subtotal
                ];
            }
            
            try {
                $sale_id = $this->saleModel->create($saleData);
                
                $_SESSION['flash_message'] = 'Venta registrada correctamente';
                $_SESSION['flash_type'] = 'success';
                $this->redirect('/sales/details/' . $sale_id);
            } catch (Exception $e) {
                $_SESSION['flash_message'] = 'Error al registrar la venta: ' . $e->getMessage();
                $_SESSION['flash_type'] = 'danger';
                $this->redirect('/sales/create');
            }
        } else {
            $this->redirect('/sales/create');
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
        
        $sale = $this->saleModel->getById($id);
        
        if (!$sale) {
            $_SESSION['flash_message'] = 'Venta no encontrada';
            $_SESSION['flash_type'] = 'danger';
            $this->redirect('/sales');
            return;
        }
        
        $details = $this->saleModel->getDetailsBySaleId($id);
        
        $this->view('sales/view', [
            'sale' => $sale,
            'details' => $details,
            'title' => 'Detalle de Venta #' . $sale['invoice_number']
        ]);
    }

    public function generatePdf($id) {
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
        
        $sale = $this->saleModel->getById($id);
        
        if (!$sale) {
            $_SESSION['flash_message'] = 'Venta no encontrada';
            $_SESSION['flash_type'] = 'danger';
            $this->redirect('/sales');
            return;
        }
        
        $details = $this->saleModel->getDetailsBySaleId($id);
        
        $fpdf_path = BASE_PATH . '/lib/fpdf/fpdf186/fpdf.php';
        
        if (!file_exists($fpdf_path)) {
            $_SESSION['flash_message'] = 'Error: No se encontró la biblioteca FPDF en ' . $fpdf_path;
            $_SESSION['flash_type'] = 'danger';
            $this->redirect('/sales/details/' . $id);
            return;
        }
        
        require_once $fpdf_path;
        
        try {
            $pdf = new FPDF();
            $pdf->AddPage();
            
            // Configurar fuente
            $pdf->SetFont('Arial', 'B', 16);
            
            // Título
            $pdf->Cell(0, 10, APP_NAME ?? 'Sistema de Ventas', 0, 1, 'C');
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 10, 'FACTURA DE VENTA', 0, 1, 'C');
            $pdf->Ln(5);
            
            // Información de la factura
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(40, 10, 'Factura No:', 0);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(60, 10, $sale['invoice_number'], 0);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(40, 10, 'Fecha:', 0);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(50, 10, date('d/m/Y', strtotime($sale['sale_date'])), 0);
            $pdf->Ln();
            
            // Información del cliente
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(40, 10, 'Cliente:', 0);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(60, 10, $sale['customer_name'] ?? 'N/A', 0);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(40, 10, 'Método de Pago:', 0);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(50, 10, $sale['payment_method'] ?? 'N/A', 0);
            $pdf->Ln(15);
            
            // Tabla de productos
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(20, 10, 'Código', 1);
            $pdf->Cell(70, 10, 'Producto', 1);
            $pdf->Cell(25, 10, 'Cantidad', 1);
            $pdf->Cell(35, 10, 'Precio Unit.', 1);
            $pdf->Cell(40, 10, 'Subtotal', 1);
            $pdf->Ln();
            
            // Detalles de productos
            $pdf->SetFont('Arial', '', 10);
            $total = 0;
            foreach ($details as $detail) {
                $pdf->Cell(20, 10, $detail['product_code'] ?? 'N/A', 1);
                $pdf->Cell(70, 10, $detail['product_name'] ?? 'N/A', 1);
                $pdf->Cell(25, 10, $detail['quantity'] ?? '0', 1);
                $pdf->Cell(35, 10, '$' . number_format($detail['unit_price'] ?? 0, 2), 1);
                $pdf->Cell(40, 10, '$' . number_format($detail['subtotal'] ?? 0, 2), 1);
                $pdf->Ln();
                $total += $detail['subtotal'] ?? 0;
            }
            
            // Totales
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(150, 10, 'Total:', 0, 0, 'R');
            $pdf->Cell(40, 10, '$' . number_format($sale['total_amount'] ?? $total, 2), 1);
            $pdf->Ln(15);
            
            // Notas
            if (!empty($sale['notes'])) {
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(0, 10, 'Notas:', 0, 1);
                $pdf->SetFont('Arial', '', 10);
                $pdf->MultiCell(0, 10, $sale['notes'], 0);
            }
            
            // Pie de página
            $pdf->Ln(10);
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->Cell(0, 10, 'Gracias por su compra', 0, 0, 'C');
            
            // Salida del PDF
            $filename = 'factura_' . $sale['invoice_number'] . '.pdf';
            $pdf->Output('D', $filename);
            exit;
        } catch (Exception $e) {
            $_SESSION['flash_message'] = 'Error al generar el PDF: ' . $e->getMessage();
            $_SESSION['flash_type'] = 'danger';
            $this->redirect('/sales/details/' . $id);
            return;
        }
    }
}