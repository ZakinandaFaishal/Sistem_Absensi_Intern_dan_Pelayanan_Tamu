<?php

namespace App\Http\Controllers;

use App\Models\Location;
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
        $locations = Location::query()->orderBy('name')->get();

        return view('kiosk.absensi', [
            'locations' => $locations,
        ]);
    }

    public function token(Request $request)
    {
        $validated = $request->validate([
            'location_id' => ['required', 'integer', 'exists:locations,id'],
        ]);

        return response()->json(
            KioskToken::issue((int) $validated['location_id'], $request->getSchemeAndHttpHost())
        );
    }
}
