<?php
class PdfHelper {
    public static function generateSalePdf($sale, $saleDetails) {        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Invoice #' . $sale['invoice_number'] . '</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                }
                .invoice-header {
                    text-align: center;
                    margin-bottom: 30px;
                }
                .invoice-details {
                    margin-bottom: 20px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                th, td {
                    padding: 10px;
                    border: 1px solid #ddd;
                    text-align: left;
                }
                th {
                    background-color: #f2f2f2;
                }
                .totals {
                    margin-top: 20px;
                    text-align: right;
                }
            </style>
        </head>
        <body>
            <div class="invoice-header">
                <h1>' . APP_NAME . '</h1>
                <h2>Invoice #' . $sale['invoice_number'] . '</h2>
            </div>
            
            <div class="invoice-details">
                <p><strong>Date:</strong> ' . date('Y-m-d H:i', strtotime($sale['sale_date'])) . '</p>
                <p><strong>Customer:</strong> ' . $sale['first_name'] . ' ' . $sale['last_name'] . '</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($saleDetails as $detail) {
            $html .= '
                    <tr>
                        <td>' . $detail['product_name'] . ' (' . $detail['product_code'] . ')</td>
                        <td>' . $detail['quantity'] . '</td>
                        <td>$' . number_format($detail['unit_price'], 2) . '</td>
                        <td>$' . number_format($detail['subtotal'], 2) . '</td>
                    </tr>';
        }
        
        $html .= '
                </tbody>
            </table>
            
            <div class="totals">
                <p><strong>Subtotal:</strong> $' . number_format($sale['subtotal'], 2) . '</p>
                <p><strong>Tax (19%):</strong> $' . number_format($sale['tax_amount'], 2) . '</p>
                <p><strong>Total:</strong> $' . number_format($sale['total_amount'], 2) . '</p>
            </div>
        </body>
        </html>';
        
        return $html;
    }
}