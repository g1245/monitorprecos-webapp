<?php

namespace App\Observers;

use App\Models\Department;
use Illuminate\Support\Facades\Cache;

class DepartmentObserver
{
    /**
     * Handle the Department "saved" event.
     * Clears the department menu cache whenever a department is created or updated.
     */
    public function saved(Department $department): void
    {
        Cache::forget('department_menu');
    }

    /**
     * Handle the Department "deleted" event.
     */
    public function deleted(Department $department): void
    {
        Cache::forget('department_menu');
    }

    /**
     * Handle pivot attachment on the Department "products" relationship.
     * Fires when one or more products are attached to a department.
     */
    public function pivotAttached(Department $department, string $relationName, array $pivotIds, array $pivotIdsAttributes): void
    {
        if ($relationName === 'products') {
            Cache::forget('department_menu');
        }
    }

    /**
     * Handle pivot detachment on the Department "products" relationship.
     * Fires when one or more products are detached from a department.
     */
    public function pivotDetached(Department $department, string $relationName, array $pivotIds): void
    {
        if ($relationName === 'products') {
            Cache::forget('department_menu');
        }
    }

    /**
     * Handle pivot sync on the Department "products" relationship.
     * Fires when the full product list of a department is synced.
     */
    public function pivotSynced(Department $department, string $relationName, array $changes): void
    {
        if ($relationName === 'products') {
            Cache::forget('department_menu');
        }
    }
}
