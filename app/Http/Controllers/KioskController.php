<?php

namespace App\Http\Controllers;

use App\Support\KioskToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KioskController extends Controller
{
    public function index()
    {
        return view('kiosk.index');
    }

    public function display()
    {
        $user = Auth::user();

        if (!in_array(($user->role ?? null), ['super_admin', 'admin_dinas'], true)) {
            return redirect()->route('dashboard');
        }

        return view('kiosk.display');
    }

    public function absensi()
    {
        return view('kiosk.absensi');
    }

    public function token(Request $request)
    {
        return response()->json(KioskToken::issue($request->getSchemeAndHttpHost()));
    }
}
