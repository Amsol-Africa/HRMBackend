<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'access.dashboard', // business.index
            'access.clients', // business.clients.*
            'access.locations', // business.locations.index
            'access.organization', // business.organization-setup, business.job-categories.index, business.departments.index, business.shifts.index, business.roster.index, business.pay-grades.index
            'access.roles', // business.roles.*
            'access.employees', // business.employees.*, business.employees.download*
            'access.payroll', // business.payroll.*, business.payslips, business.payroll.payslip, business.payroll.download*, business.payroll.send_payslips
            'access.payroll-settings', // business.payroll-formulas.*, business.deductions, business.reliefs.index, business.employee-reliefs.index, business.allowances.index
            'access.leave', // business.leave.*
            'access.attendance', // business.attendances.*, business.overtime.*, business.clock-in-out.index, business.reports.index
            'access.performance', // business.performance.*
            'access.crm', // business.crm.*
            'access.recruitment', // business.recruitment.*, business.applicants.*, business.applications.*
            'access.profile', // business.profile.index
            'access.support', // business.support.*
            'edit.any', // Edit privileges
            'delete.any', // Delete privileges
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permissions to roles
        $adminItPermissions = $permissions; // Full access for business-admin and business-it
        $financePermissions = array_diff($permissions, ['access.roles']); // All except roles
        $marketingPermissions = [
            'access.dashboard',
            'access.crm',
            'access.performance',
            'access.profile',
            'access.support',
        ];

        $businessAdmin = Role::findByName('business-admin', 'web');
        $businessIt = Role::findByName('business-it', 'web');
        $businessFinance = Role::findByName('business-finance', 'web');
        $businessMarketing = Role::findByName('business-marketing', 'web');

        $businessAdmin->syncPermissions($adminItPermissions);
        $businessIt->syncPermissions($adminItPermissions);
        $businessFinance->syncPermissions($financePermissions);
        $businessMarketing->syncPermissions($marketingPermissions);
    }
}
