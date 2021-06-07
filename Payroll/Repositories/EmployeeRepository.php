<?php

namespace Payroll\Repositories;

use Cache;
use Payroll\Models\Employee;
use Payroll\Models\EmployeeLeave;
use Payroll\Models\Leave;

class EmployeeRepository
{
    public static function getCacheKey()
    {
        return \DB::getDefaultConnection() . '_PAYROLL_EMPLOYEES';
    }

    public static function reCache()
    {
        Cache::forget(self::getCacheKey());
        Cache::rememberForever(self::getCacheKey(), function () {
            return Employee::with([
                'contract', 'allowances.allowance', 'deductions.deduction.slabs', 'paymentStructure',
                'daysWorked', 'hoursWorked', 'unitsMade', 'advances', 'leaves' => function ($builder) {
                    return $builder->where('status', EmployeeLeave::APPROVED);
                }
            ])->get();
        });
    }

    public static function checkCache()
    {
        if (! Cache::has(self::getCacheKey())) {
            self::reCache();
        }
    }

    public static function getBaseDetails($employeeId)
    {
        self::checkCache();

        if (is_array($employeeId)) {
            return Cache::get(self::getCacheKey())
                ->whereIn('id', $employeeId);
        }

        return Cache::get(self::getCacheKey())
            ->where('id', $employeeId);
    }

    public static function getRawBaseDetails($employees = [])
    {
        return Employee::with([
            'contract', 'allowances.allowance', 'deductions.deduction.slabs', 'paymentStructure',
            'daysWorked', 'hoursWorked', 'unitsMade', 'advances', 'leaves' => function ($builder) {
                return $builder->where('status', EmployeeLeave::APPROVED);
            }
        ])
            ->whereIn('id', $employees);
    }

    public static function getRawBaseDetailsByPaymentStructure($structureId)
    {
        return Employee::with([
            'contract', 'allowances.allowance', 'deductions.deduction.slabs', 'paymentStructure',
            'daysWorked', 'hoursWorked', 'unitsMade', 'advances', 'leaves' => function ($builder) {
                return $builder->where('status', EmployeeLeave::APPROVED);
            }
        ])
            ->whereIn('payment_structure_id', $structureId);
    }
}
