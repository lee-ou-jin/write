<?php

namespace App\Http\ApiControllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use Throwable;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        //Validated
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&^,_\-=+\/])[A-Za-z\d@$!%*#?&\-=+\/]+$/',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => -1,
                'message' => $validator->errors()->all()
            ], 422);
        }

        $user = User::query()->where('email', $request->get('email'))->first();
        if ($user == null) {
            return response()->json([
                'error' => -2,
                'message' => "Invalid Email Address"
            ], 422);
        }

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            // 로그인 성공
            /** @var User $userAuth */
            $userAuth = Auth::user();

            //기존 토큰 삭제 (기존 로그인 다 풀림. 여러 기기를 지원하려면 이걸 건너뛰면됨.)
            $userAuth->tokens()->delete();

            //새로운 sanctum-token 생성
            $token = $userAuth->createToken($userAuth->getKey() . '-loginAuthToken')->plainTextToken;
            return response()->json([
                'error' => 0,
                'message' => 'success',
                'user' => $userAuth,
                'token' => $token
            ]);
        } else {
            if (!Hash::check($request->get('password'), $user->getAttribute('password'))) {
                return response()->json([
                    'error' => -3,
                    'message' => "Password Mismatch."
                ], 422);
            } else {
                return response()->json([
                    'error' => -4,
                    'message' => "Failed Auth."
                ], 422);
            }
        }
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        return response()->json(["status" => "success"]);
    }

    public function checkDuplicateEmail(Request $request): JsonResponse
    {
        //Validated
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|max:255|unique:users',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => -2, 'message' => $validator->errors()->all()], 422);
        }

        $user = User::query()->where('email', $request->get('email'))->first();
        if ($user) {
            $response = ["error" => -1, "message" => 'In used email address'];
            return response()->json($response, 422);
        } else {
            $response = ["error" => 0, "message" => "Success"];
            return response()->json($response, 200);
        }
    }

    public function updateFcmToken(Request $request): JsonResponse
    {
        $authCode = $request->get('auth_code');
        $fcmToken = $request->get('fcm_token');
        $smartGlass = \App\Models\SmartGlasses::query()
            ->where('auth_code', $authCode)
            ->first();
        if ($smartGlass) {
            $smartGlass->fcm_token = $fcmToken;
            $smartGlass->save();
            return response()->json([
                'status' => 'OK',
                'error' => 0,
                'data' => $smartGlass
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'error' => 1,
                'message' => '인증 코드가 일치하는 스마트 글래스가 없습니다.'
            ]);
        }
    }
}

