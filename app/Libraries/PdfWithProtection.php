<?php
namespace App\Libraries;

require_once __DIR__ . '/Fpdf/fpdf.php';
require_once __DIR__ . '/Fpdf/fpdf_protection.php';

class PdfWithProtection extends \FPDF_Protection
{
    public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        parent::__construct($orientation, $unit, $size);
    }
}
