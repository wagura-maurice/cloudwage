<?php

namespace Payroll\Repositories;

use Cache;
use Payroll\Models\Payroll;
use Payroll\Models\Policy;

class PolicyRepository
{
    public static function getCacheKey()
    {
        return getConnection() . '_PAYROLL_POLICIES';
    }

    public static function reCache()
    {
        Cache::forget(self::getCacheKey());
        Cache::rememberForever(self::getCacheKey(), function () {
            return Policy::all();
        });
    }

    public static function checkCache()
    {
        if (! Cache::has(self::getCacheKey())) {
            self::reCache();
        }
    }

    public static function get($module, $policy)
    {
        self::checkCache();

        return Cache::get(self::getCacheKey())
            ->where('module_id', $module)
            ->where('policy', $policy)
            ->first()->value;
    }
}
