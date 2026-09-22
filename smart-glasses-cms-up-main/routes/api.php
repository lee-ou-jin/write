<?php

use App\Http\ApiControllers\AgoraController;
use App\Http\ApiControllers\AuthController;
use App\Http\ApiControllers\AuthCodeController;
use App\Http\ApiControllers\DocumentController;
use App\Http\ApiControllers\EquipmentController;
use App\Http\ApiControllers\MessageController;
use App\Http\ApiControllers\ShipController;
use App\Http\ApiControllers\TaskController;
use App\Http\ApiControllers\TaskLogController;
use App\Http\ApiControllers\VimeoController;
use App\Http\ApiControllers\TaskLogSendController;
use App\Http\ApiControllers\MeetingController;
use App\Http\ApiControllers\RecordingController;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use App\Models\SmartGlasses;
use App\Services\FcmService;

// Auth Routes
Route::controller(AuthController::class)
    ->prefix('/auth')
    ->name('auth.')
    ->group(function () {
        Route::post("/login", "login")->name("login");
        Route::post("/logout", "logout")->name('logout');
        Route::post("/check/email", 'checkDuplicateEmail')->name('check.email');
        Route::middleware('auth:sanctum')->post('/fcm-token', 'updateFcmToken')->name('fcm-token');
    });

// Auth Code Routes
Route::controller(AuthCodeController::class)
    ->prefix('/auth-code')
    ->name('auth-code.')
    ->group(function () {
        Route::post('/test', 'test');
        Route::post("/verify", "verifyAuthCode")->name("verify");
    });

// Protected Routes
Route::middleware('auth:sanctum')
    ->name('api.')
    ->group(function (Router $router) {
        $router->get('/user', function (Request $request) {
            return $request->user();
        })->name('user');

        Route::get('/protected-route', function () {
            return response()->json(['message' => '인증된 사용자입니다.']);
        });
    });

// Ship Routes
Route::controller(ShipController::class)
    ->group(function () {
        Route::get('/ship', 'index');
        Route::get('/position', 'positions');
    });

// Equipment Routes
Route::controller(EquipmentController::class)
    ->group(function () {
        Route::get('/equipment', 'index');
    });

// Task Routes
Route::controller(TaskController::class)
    ->prefix('/task')
    ->group(function () {
        Route::get('/history', 'history');
        Route::get('/history/{code}', 'historyDetail');
        Route::get('/search/{input}', 'search');
        Route::get('/new/{glassesId}', 'getNewMeeting');

        Route::prefix('/upload')
            ->name('upload.')
            ->group(function () {
                Route::post('/image/{taskCode}', [TaskLogController::class, 'storeImage'])->name('base64');
            });
        // Route::get('/sended/{taskCode}', [TaskLogController::class, 'getSendedImages'])->name('sended');
    });

Route::get(
    '/task/images/{taskCode}',
    [TaskLogController::class, 'getTaskImages']
);

Route::post(
    '/task-log-send/{taskCode}',
    [TaskLogSendController::class, 'store']
);

Route::get(
    '/task/sended/{taskCode}',
    [TaskLogSendController::class, 'getSendedImages']
);



// Document Routes
Route::controller(DocumentController::class)
    ->prefix('/documents')
    ->group(function () {
        Route::get('/list', 'index');
        Route::get('/search/{input}', 'search');
        Route::get('/{id}', 'show');
    });

// Message Routes
Route::controller(MessageController::class)
    ->prefix('/messages')
    ->group(function () {
        Route::get('/{meeting_id}', 'getByMeetingId');
        Route::get('/task/{code}', 'getByTaskCode');
    });

// Agora Routes
Route::controller(AgoraController::class)
    ->prefix('/agora')
    ->group(function () {
        Route::get('/token/{userId}/{meetingId}', 'generateToken');
    });

// Vimeo Routes
Route::controller(VimeoController::class)
    ->prefix('/vimeo')
    // ->middleware('auth:sanctum')
    ->group(function () {
        Route::post('/upload', 'upload');
        Route::get('/videos', 'index');
        Route::get('/videos/{id}', 'show');
    });

// Test Route
Route::get('/test', function () {
    return response()->json(['message' => 'Hello World']);
});

Route::get('/task-logs/{taskId}/check-shown', [TaskLogController::class, 'checkImageShown']);
Route::post('/task-logs/{taskId}/mark-shown', [TaskLogController::class, 'markImageAsShown']);

Route::post('/recording/start', [RecordingController::class, 'startRecording']);
Route::post('/recording/stop', [RecordingController::class, 'stopRecording']);


Route::get('/file/drone1_compress3.mp4', function () {
    $filename = "drone1_compress3.mp4";
    return response()->file(storage_path('app/public/' . $filename));
});

Route::get('/file/drone_label_1080.mov', function () {
    $filename = "drone_label_1080.mov";
    return response()->file(storage_path('app/public/' . $filename));
});

Route::get('/file/drone_label_2160.mov', function () {
    $filename = "drone_label_2160.mov";
    return response()->file(storage_path('app/public/' . $filename));
});

Route::get('/test-fcm/{authCode}', function ($authCode) {

    $smartGlass = SmartGlasses::query()
        ->where('auth_code', $authCode)
        ->whereNotNull('fcm_token')
        ->where('fcm_token', '!=', '')
        ->first();

    if (!$smartGlass) {
        return response()->json([
            'ok' => false,
            'message' => 'FCM token 없음',
        ]);
    }

    $sent = app(FcmService::class)->sendToToken(
        $smartGlass->fcm_token,
        '테스트 알림',
        'Laravel에서 보낸 테스트 푸시입니다.',
        [
            'type' => 'test',
        ]
    );

    return response()->json([
        'ok' => $sent,
    ]);
});
