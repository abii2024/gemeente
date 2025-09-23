<?php

namespace App\Policies;

use App\Models\User;

class AdminPolicy
{
    /**
     * Determine whether the user can access the admin dashboard.
     */
    public function viewDashboard(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'medewerker']);
    }

    /**
     * Determine whether the user can manage complaints.
     */
    public function manageComplaints(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'medewerker']);
    }

    /**
     * Determine whether the user can delete complaints.
     */
    public function deleteComplaints(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage database records.
     */
    public function manageDatabases(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage system settings.
     */
    public function manageSettings(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view system logs.
     */
    public function viewLogs(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
