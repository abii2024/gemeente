<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DatabaseController extends Controller
{
    public function index(): View
    {
        $tables = [
            'users' => User::count(),
            'complaints' => Complaint::count(),
            'attachments' => DB::table('attachments')->count(),
            'notes' => DB::table('notes')->count(),
            'status_histories' => DB::table('status_histories')->count(),
        ];

        return view('admin.database', compact('tables'));
    }

    public function table(string $table): View
    {
        // Voor veiligheid - alleen specifieke tabellen toestaan
        $allowedTables = [
            'users', 'complaints', 'attachments', 'notes', 'status_histories',
            'roles', 'permissions', 'role_has_permissions', 'model_has_roles',
            'model_has_permissions', 'cache', 'cache_locks', 'jobs', 'job_batches',
            'failed_jobs', 'password_reset_tokens', 'sessions',
        ];

        if (! in_array($table, $allowedTables)) {
            abort(404, 'Tabel niet gevonden');
        }

        try {
            // Haal kolommen op
            $columns = \DB::getSchemaBuilder()->getColumnListing($table);

            // Haal data op met paginatie
            $data = \DB::table($table)->paginate(20);

            return view('admin.database-table', compact('table', 'columns', 'data'));
        } catch (\Exception $e) {
            return redirect()->route('admin.database.index')
                ->with('error', 'Kon tabel niet laden: '.$e->getMessage());
        }
    }
}
