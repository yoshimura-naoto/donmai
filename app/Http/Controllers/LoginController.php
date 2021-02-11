<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

use App\User;

class LoginController extends Controller
{
    // ログイン処理
    public function login(Request $request) 
    {
        $form = $request
                ->validate(User::$loginRules, User::$loginValMessages);

        if (Auth::attempt($form))
        {
            return response()->json(Auth::user(), 200);
        } else {
            throw ValidationException::withMessages([
                'msg' => '認証に失敗しました！',
            ]);
        }
    }


    // ログアウト処理
    public function logout() 
    {
        Auth::logout();
    }
}
