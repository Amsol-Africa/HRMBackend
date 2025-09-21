<?php
namespace App\Libraries;

class PdfWithProtection extends \App\Libraries\FPDF_Protection
{
    public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        parent::__construct($orientation, $unit, $size);
    }
}
