<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Note;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function store(Request $request, Complaint $complaint): RedirectResponse
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        Note::create([
            'complaint_id' => $complaint->id,
            'user_id' => auth()->id(),
            'body' => $request->body,
        ]);

        return redirect()->back()->with('success', 'Notitie toegevoegd.');
    }

    public function destroy(Note $note): RedirectResponse
    {
        $note->delete();

        return redirect()->back()->with('success', 'Notitie verwijderd.');
    }
}
