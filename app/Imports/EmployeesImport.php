<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class EmployeesImport implements ToArray
{
    public function array(array $array)
    {
        return $array;
    }
}