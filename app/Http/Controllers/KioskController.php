<?php

namespace App\Http\Controllers;

use App\Support\KioskToken;
use Illuminate\Http\Request;

class KioskController extends Controller
{
    public function index()
    {
        return view('kiosk.index');
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
