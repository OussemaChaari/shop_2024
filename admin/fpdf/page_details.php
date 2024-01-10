<?php
include '../../config.php';

if (isset($_GET['order_id'])) {
	$order_id = $_GET['order_id'];
	
	$query = "SELECT orders.*, cart.*, user_info.*
          FROM orders
          INNER JOIN cart ON orders.order_id = cart.order_id
          INNER JOIN user_info ON orders.user_id = user_info.id
          WHERE orders.order_id = $order_id";
	$result = mysqli_query($conn, $query);
	if ($result) {
		$cartDetails = mysqli_fetch_all($result, MYSQLI_ASSOC);
		require('fpdf.php');
		$pdf = new FPDF('P', 'mm', 'A5');
		$pdf->AddPage();
		$pdf->Image('../../logo/logo.png', 10, 5, 130, 20);
		$pdf->Ln(18);
		$pdf->SetFont('Arial', 'B', 16);
		$pdf->Cell(0, 10, 'Order Details', 0, 1, 'C');
		$pdf->Ln(10);
		$pdf->Cell(0, 10, 'Name: ' . $cartDetails[0]['name'], 0, 1);
		$pdf->Cell(0, 10, 'Total: ' . intval($cartDetails[0]['total']), 0, 1);
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(30, 10, 'Product', 1, 0, 'C');
		$pdf->Cell(30, 10, 'Price', 1, 0, 'C');
		$pdf->Cell(30, 10, 'Quantity', 1, 0, 'C');
		$pdf->Cell(30, 10, 'Total Product', 1, 1, 'C');
		foreach ($cartDetails as $cartDetail) {
			$pdf->SetFont('Arial', '', 12);
			$pdf->Cell(30, 10, $cartDetail['name'], 1, 0);
			$pdf->Cell(30, 10, intval($cartDetail['price']), 1, 0);
			$pdf->Cell(30, 10, $cartDetail['quantity'], 1, 0);
			$pdf->Cell(30, 10, $cartDetail['price'] * $cartDetail['quantity'], 1, 1);
		}

		$pdf->Ln(5);
		$pdf->Output('', '', true);
	}
}
// Fermez la connexion à la base de données
mysqli_close($conn);
?>