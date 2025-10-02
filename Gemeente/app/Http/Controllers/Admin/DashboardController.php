<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_complaints' => Complaint::count(),
            'open_complaints' => Complaint::open()->count(),
            'in_behandeling_complaints' => Complaint::inBehandeling()->count(),
            'opgelost_complaints' => Complaint::opgelost()->count(),
        ];

        $recent_complaints = Complaint::with(['attachments'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_complaints'));
    }
}
