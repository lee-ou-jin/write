<?php
namespace App\Http\Responses;

use App\Providers\RouteServiceProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{

    public function toResponse($request)
    {
        if($request->wantsJson()) return response()->json(['two_factor' => false]);

        return Inertia::location(session('requested_url',RouteServiceProvider::HOME));
    }
}
