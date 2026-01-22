<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $role = $user->role ?? null;
        if ($role === 'admin') {
            $role = 'super_admin';
        }

        if ($role === 'super_admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($role === 'admin_dinas') {
            return redirect()->route('admin.attendance.manage');
        }

        return redirect()->route('intern.userProfile');
    }
}
