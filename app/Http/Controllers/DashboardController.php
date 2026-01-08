<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        if (($user->role ?? null) === 'admin') {
            return view('dashboard');
        }

        return redirect()->route('intern.userProfile');
    }
}
