<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{

    // ユーザー登録の処理
    public function register(Request $request)
    {
        $form = $request->
                validate(User::$registerRules, User::$registerValMessages);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    }
}
