<?php
class DashboardController extends Controller {
    public function __construct() {
        parent::__construct();
    }
    public function index() {
        $this->requireAuth();
        
        $productModel = new Product();
        $productCount = $productModel->count();
        
        $saleModel = new Sale();
        $recentSales = $saleModel->getAll(null, null);
        $recentSales = array_slice($recentSales, 0, 5);
        
        $purchaseModel = new Purchase();
        $recentPurchases = $purchaseModel->getAll(null, null);
        $recentPurchases = array_slice($recentPurchases, 0, 5);
        
        $this->view('dashboard/index', [
            'productCount' => $productCount,
            'recentSales' => $recentSales,
            'recentPurchases' => $recentPurchases
        ]);
    }
}