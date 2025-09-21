<?php
namespace App\Libraries;

use setasign\Fpdi\Fpdi;
use FPDF_Protection;

class PdfWithProtection extends FPDF_Protection
{
    use \setasign\Fpdi\TraitFpdi;
}
