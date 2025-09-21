<?php
namespace App\Libraries;

use setasign\Fpdi\Fpdi;

require_once __DIR__ . '/Fpdf/fpdf_protection.php';

class PdfWithProtection extends Fpdi
{
    use \FPDF_Protection_Trait; // if your fpdf_protection is a trait, otherwise extend the class

    public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        parent::__construct($orientation, $unit, $size);
    }

    /**
     * Wrapper for protection method
     */
    public function SetProtection($permissions = [], $user_pass = '', $owner_pass = null)
    {
        // Call method from FPDF_Protection
        return parent::SetProtection($permissions, $user_pass, $owner_pass);
    }
}
