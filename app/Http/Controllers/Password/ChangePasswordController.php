<?php

namespace App\Http\Controllers\Password;

use App\Rules\IsValidPassword;
use App\Rules\MatchOldPassword;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function __construct()
    {
        if (config('custom.settings.authentication') != 'database') {
            abort(403);
        }
    }

    public function index()
    {
        $data = [
            'pageTitle' => 'Change Password'
        ];

        return response()->view('password.change_password', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPassword()],
            'new_password' => [
                'required',
                'min:8',
                'confirmed',
                new IsValidPassword()
            ],
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->new_password)
        ]);

        Auth::logout();

        return redirect('login')->with('message', 'Successfully password changed');
    }
}
