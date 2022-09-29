<?php

namespace App\Http\Controllers\Password;

use App\Rules\IsValidPassword;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ForceChangePasswordController extends Controller
{
    public function __construct()
    {
        if (config('custom.settings.authentication') != 'database') {
            abort(403);
        }
    }

    public function index()
    {
        if (!empty(auth()->user()->password_changed_at)) {
            abort(404);
        }

        $data = [
            'pageTitle' => 'Change Password',
            'alert' => 'You need to change password first then you are able to browse other features'
        ];

        return response()->view('password.force_password_change', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'max:20',
                new IsValidPassword()
            ]
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
            'password_changed_at' => now()
        ]);

        Auth::logout();

        return redirect('login')->with('message', 'Successfully password changed');
    }
}
