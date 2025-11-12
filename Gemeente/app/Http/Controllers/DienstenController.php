<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DienstenController extends Controller
{
    public function paspoort()
    {
        return view('diensten.paspoort');
    }

    public function rijbewijs()
    {
        return view('diensten.rijbewijs');
    }

    public function vergunning()
    {
        return view('diensten.vergunning');
    }

    public function parkeren()
    {
        return view('diensten.parkeren');
    }

    public function subsidie()
    {
        return view('diensten.subsidie');
    }

    public function storeAfspraak(Request $request)
    {
        $validated = $request->validate([
            'dienst' => 'required|string',
            'datum' => 'required|date|after:today',
            'tijd' => 'required',
            'opmerking' => 'nullable|string|max:1000'
        ]);

        DB::table('afspraken')->insert([
            'user_id' => Auth::id(),
            'dienst' => $validated['dienst'],
            'datum' => $validated['datum'],
            'tijd' => $validated['tijd'],
            'opmerking' => $validated['opmerking'] ?? null,
            'status' => 'gepland',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('home')->with('success', 'Afspraak succesvol ingepland!');
    }
}
