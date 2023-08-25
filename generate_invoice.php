<?php
// Import the TCPDF library
require_once '../TCPDF-main/tcpdf.php'; // Replace with the actual path to TCPDF

// Check if the form was submitted
if (isset($_POST['generate_invoice'])) {
    // Get the order ID from the submitted form
    $order_id = $_POST['order_id'];

    // Replace the following with your database connection details
    $servername = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'shop_db';

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch the order details from the database based on the $order_id
        $select_order = $conn->prepare("SELECT * FROM `orders` WHERE id = ?");
        $select_order->execute([$order_id]);

        if ($select_order->rowCount() > 0) {
            $order_details = $select_order->fetch(PDO::FETCH_ASSOC);

            // Now, use TCPDF to generate the PDF invoice
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // Set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Your Name');
            $pdf->SetTitle('Invoice');
            $pdf->SetSubject('Invoice');

            // Add a page
            $pdf->AddPage();

            // Set font to DejaVu Sans (supports Indian Rupee symbol)
            $pdf->SetFont('dejavusans', '', 14); // Increase font size to 14

            // Set the background color to a modern blue color (#3498db)
            $pdf->SetFillColor(142, 20, 21); // RGB values for the blue color

            // Add a cell for the header background
            $pdf->Cell(0, 20, '', 0, 1, 'C', true);

            // Set font color to white (#ffffff) for the header
            $pdf->SetTextColor(255, 255, 255); // RGB values for white color

            // Custom header for your website name
            $pdf->SetFont('dejavusans', 'B', 20); // Font size 18 and bold style for the header
            $pdf->SetXY(10, 15); // Set position for the header (X, Y) - Adjust as needed
            $pdf->Cell(0, 10, 'ElectraTech', 0, 1, 'C'); // 'L' for left alignment

            // Reset font size and style and font color for the rest of the content
            $pdf->SetFont('dejavusans', 'B', 18); // Reset to default font size and style
            $pdf->SetTextColor(0, 0, 0); // Black text color

            // Output your invoice content
            $pdf->Ln(15); // Add some space between the header and the content

            // Set font color to black (#000000) for the rest of the content
            $pdf->SetTextColor(0, 0, 0);

            // Add border around the content
            $pdf->SetLineWidth(0.5);
            $pdf->Rect(10, 55, 190, 110);

            // Output individual order details as separate lines
            $pdf->SetFont('dejavusans', 'B', 12); // Bold for Invoice for Order #
            $pdf->SetXY(15, 60);
            $pdf->Cell(100, 10, 'Invoice for Order #' . $order_details['id'], 0, 1, 'C'); // Centered
            $pdf->SetFont('dejavusans', '', 12); // Reset to regular font
            $pdf->SetXY(15, 75);
            $pdf->Cell(100, 10, 'Placed on: ' . $order_details['placed_on'], 0, 1);
            $pdf->SetXY(15, 90);
            $pdf->Cell(100, 10, 'Customer Name: ' . $order_details['name'], 0, 1);
            $pdf->SetXY(15, 105);
            $pdf->Cell(100, 10, 'Email: ' . $order_details['email'], 0, 1);
            $pdf->SetXY(15, 120);
            $pdf->Cell(100, 10, 'Number: ' . $order_details['number'], 0, 1);

            // Payment Information
           // ... Previous code ...

// Payment Information
$pdf->SetFont('dejavusans', 'B', 14);
$pdf->SetXY(15, 140);
$pdf->Cell(100, 10, 'Payment Information', 0, 1);

// Add border around the payment information
$pdf->SetLineWidth(0.5);
$pdf->Rect(10, 141, 190, 35);

// Remove strikeout line for Payment Method and Total Price
$pdf->SetLineStyle(array('dash' => 0, 'color' => array(0, 0, 0))); // Remove strikeout line
$pdf->SetLineWidth(0); // Set line width to 0 for the cells without lines

$pdf->SetXY(15, 150);
$pdf->Cell(100, 10, 'Payment Method: ' . $order_details['method'], 0, 1);
$pdf->SetLineWidth(0.5); // Reset line width for other cells

$pdf->SetXY(15, 165);
$pdf->Cell(100, 10, 'Total Products: ' . $order_details['total_products'], 0, 1);
$pdf->SetXY(15, 180);
$pdf->Cell(100, 10, 'Payment Status: ' . $order_details['payment_status'], 0, 1);

// Total Price
$pdf->SetFont('dejavusans', 'B', 16);
$pdf->SetXY(140, 140);
$pdf->Cell(50, 10, 'Total Price:', 0, 0, 'R');
$pdf->SetXY(140, 150);
$pdf->SetFont('dejavusans', 'B', 20);
$pdf->Cell(50, 10, '₹' . $order_details['total_price'], 0, 0, 'R');

// Output the PDF as a file named "invoice_orderID.pdf"
$file_name = 'invoice_' . $order_details['id'] . '.pdf';
$pdf->Output($file_name, 'D');

        } else {
            echo 'Order not found.';
        }
    } catch (PDOException $e) {
        echo 'Database connection failed: ' . $e->getMessage();
    }
}
