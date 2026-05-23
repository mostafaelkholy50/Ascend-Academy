<?php

namespace App\Traits;

use App\Models\User;

trait HasRegionalAccess
{
    /**
     * Get the list of countries the user is allowed to access.
     * 
     * @param User $user
     * @return array
     */
    public function getAllowedCountries($user): array
    {
        // SuperAdmins always see everything
        if ($user->hasRole('SuperAdmin')) {
            return [];
        }

        $allowedCountries = $user->allowed_countries ?? [];
        
        foreach ($user->roles as $role) {
            $roleCountries = is_array($role->allowed_countries) 
                ? $role->allowed_countries 
                : json_decode($role->allowed_countries ?? '[]', true);
                
            if (!empty($roleCountries)) {
                $allowedCountries = array_merge($allowedCountries, $roleCountries);
            }
        }

        return array_unique($allowedCountries);
    }

    /**
     * Check if the user has access to a specific country.
     * 
     * @param User $user
     * @param string|null $country
     * @return bool
     */
    public function hasAccessToCountry($user, $country): bool
    {
        // SuperAdmins always have access
        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        $allowed = $this->getAllowedCountries($user);
        
        // If no countries are specified for a restricted user, they have no access
        if (empty($allowed)) {
            return false;
        }

        return in_array($country, $allowed);
    }

    /**
     * Apply country filtering to a query.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $column The column to filter by (e.g. 'country' or 'student.country')
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applyRegionalFilter($query, $column = 'country')
    {
        $user = auth()->user();
        
        // SuperAdmins are never restricted
        if ($user->hasRole('SuperAdmin')) {
            return $query;
        }

        $allowedCountries = $this->getAllowedCountries($user);

        // For roles like Accountant, if no countries are specified, they should see NOTHING
        // as per the requirement "only based on countries specified".
        if (str_contains($column, '.')) {
            $parts = explode('.', $column);
            $actualColumn = array_pop($parts);
            $relationPath = implode('.', $parts);
            
            return $query->whereHas($relationPath, function($q) use ($actualColumn, $allowedCountries) {
                $q->whereIn($actualColumn, $allowedCountries);
            });
        }

        return $query->whereIn($column, $allowedCountries);
    }
    /**
     * Check if the user has access to teacher payroll.
     * 
     * @param User $user
     * @return bool
     */
    public function canAccessPayroll($user): bool
    {
        // SuperAdmins always have access
        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        // Direct access
        if ($user->can_access_payroll) {
            return true;
        }

        // Role-based access
        foreach ($user->roles as $role) {
            if ($role->can_access_payroll) {
                return true;
            }
        }

        return false;
    }
}
