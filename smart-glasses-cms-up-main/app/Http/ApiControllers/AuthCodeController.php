<?php

namespace App\Http\ApiControllers;

use App\Http\Controllers\Controller;
use App\Models\SmartGlasses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthCodeController extends Controller
{
    /**
     * 인증코드 생성 및 저장
     */
    public function generateAuthCode(Request $request): JsonResponse
    {
        $request->validate([
            'smart_glasses_id' => 'required|string',
        ]);

        $smartGlassesId = $request->get('smart_glasses_id');

        /** @var SmartGlasses $smartGlasses */
        $smartGlasses = SmartGlasses::query()->findOrFail($smartGlassesId);
        $smartGlasses->generateAuthCode();

        return response()->json([
            'status' => 'success',
            'message' => '인증코드 생성 성공',
            'data' => [
                'auth_code' => $smartGlasses->getAttribute('auth_code')
            ],
        ], 200);
    }

    /**
     * 인증코드 검증
     */
    public function verifyAuthCode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'auth_code' => 'required|string|size:8',
            'ship_id' => 'required|int',
            'user_name' => 'required|string',
            'user_position' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all()
            ], 422);
        }

        $authCode = strtoupper($request->get('auth_code'));
        $shipId = $request->get('ship_id');
        $userName = $request->get('user_name');
        $userPosition = $request->get('user_position');

        /** @var SmartGlasses $smartGlasses */
        $smartGlasses = SmartGlasses::query()
            ->where([
                'auth_code' => $authCode,
                'ship_id' => $shipId,
                'user_name' => $userName,
                'position' => $userPosition,
            ])->first();

        if ($smartGlasses && $smartGlasses->verifyAuthCode($authCode)) {
            // 토큰 생성
            $token = $smartGlasses->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => '인증 성공',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'data' => $smartGlasses,
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Auth code not found or invalid',
        ], 401);
    }
}

