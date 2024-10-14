<?php

require('conexion.php');
require('fpdf.php');


$sql = "SELECT DNI, NOMBRE, APELLIDO, DIRECCIÓN FROM queso ";
$resultado = $conexion->query($sql);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

$pdf->Cell(190, 10, 'Lista de Docentes', 1, 1, 'C');

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(50, 10, 'Nombre', 1);
$pdf->Cell(50, 10, 'Apellido', 1);
$pdf->Cell(45, 10, 'DNI', 1);
$pdf->Cell(45, 10, 'Direccion', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
while ($fila = $resultado->fetch_assoc()) {
    $pdf->Cell(50, 10, $fila['DNI'], 1);
    $pdf->Cell(50, 10, $fila['NOMBRE'], 1);
    $pdf->Cell(45, 10, $fila['APELLIDO'], 1);
    $pdf->Cell(45, 10, $fila['DIRECCIÓN'], 1);
    $pdf->Ln();
}

// Salida del archivo PDF
$pdf->Output();
?>
