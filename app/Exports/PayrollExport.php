<?php

namespace App\Exports;

use App\Models\Payroll;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PayrollExport implements FromCollection, WithHeadings
{
    protected $payroll;

    public function __construct(Payroll $payroll)
    {
        $this->payroll = $payroll;
    }

    public function collection()
    {
        return $this->payroll->employeePayrolls->map(function ($payroll) {
            $deductions = json_decode($payroll->deductions, true);
            return [
                'Employee' => $payroll->employee->user->name ?? 'N/A',
                'Basic Salary' => $payroll->basic_salary,
                'Gross Pay' => $payroll->gross_pay,
                'Overtime' => $payroll->overtime,
                'SHIF' => $deductions['shif'] ?? 0,
                'NSSF' => $deductions['nssf'] ?? 0,
                'PAYE' => $deductions['paye'] ?? 0,
                'Housing Levy' => $deductions['nhdf'] ?? 0,
                'HELB' => $deductions['helb'] ?? 0,
                'Loan Repayment' => $deductions['loan_repayment'] ?? 0,
                'Advance Recovery' => $deductions['advance_recovery'] ?? 0,
                'Absenteeism Charge' => $deductions['absenteeism_charge'] ?? 0,
                'Net Pay' => $payroll->net_pay,
                'Bank Details' => $payroll->employee->paymentDetails->bank_name . ' (' . $payroll->employee->paymentDetails->account_number . ')',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Employee',
            'Basic Salary',
            'Gross Pay',
            'Overtime',
            'SHIF',
            'NSSF',
            'PAYE',
            'Housing Levy',
            'HELB',
            'Loan Repayment',
            'Advance Recovery',
            'Absenteeism Charge',
            'Net Pay',
            'Bank Details',
        ];
    }
}