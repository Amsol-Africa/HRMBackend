<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BankAdviceExport implements FromArray, WithHeadings
{
    protected $payroll;

    public function __construct($payroll)
    {
        $this->payroll = $payroll;
    }

    public function array(): array
    {
        return $this->payroll->employeePayrolls->map(function ($ep) {
            return [
                'employee_name' => $ep->employee->user->name ?? 'N/A',
                'employee_code' => $ep->employee->employee_code ?? 'N/A',
                'bank_name' => $ep->employee->paymentDetails->bank_name ?? 'N/A',
                'bank_code' => $ep->employee->paymentDetails->bank_code ?? 'N/A',
                'bank_branch' => $ep->employee->paymentDetails->bank_branch ?? 'N/A',
                'bank_branch_code' => $ep->employee->paymentDetails->bank_branch_code ?? 'N/A',
                'account_number' => $ep->employee->paymentDetails->account_number ?? 'N/A',
                'payment_mode' => $ep->employee->paymentDetails->payment_mode ?? 'N/A',
                'currency' => $ep->employee->paymentDetails->currency ?? 'N/A',
                'net_pay' => number_format($ep->net_pay ?? 0, 2),
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Employee Code',
            'Bank Name',
            'Bank Code',
            'Bank Branch',
            'Branch Code',
            'Account Number',
            'Payment Mode',
            'Currency',
            'Net Pay',
        ];
    }
}