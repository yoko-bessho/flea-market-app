<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;


class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $user = auth()->user();

        // プロフィール情報（住所または郵便番号）が未登録の場合はプロフィール設定ページへ
        if (is_null($user->address) || is_null($user->postal_code)) {
            return redirect()->intended('/mypage/profile');
        }

        // 登録済みの場合はトップページへ
        return redirect()->intended('/');
    }
}
