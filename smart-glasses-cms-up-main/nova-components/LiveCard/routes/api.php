<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Jerry\LiveCard\RtcTokenBuilder2;
use Illuminate\Support\Facades\Route;
use TomatoPHP\LaravelAgora\Services\Agora;
use App\Http\ApiControllers\TaskLogController;
use App\Http\ApiControllers\TaskLogSendController;
use App\Http\ApiControllers\MeetingController;
use App\Http\ApiControllers\AgoraController;

/*
|--------------------------------------------------------------------------
| Card API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your card. These routes
| are loaded by the ServiceProvider of your card. You're free to add
| as many additional routes to this file as your card may require.
|
*/

// Route::get('/endpoint', function (Request $request) {
//     //
// });


// agora chat generate token
//Route::get('/agora/token', function (Request $request) {
//    $appID = env('AGORA_APP_ID');
//    $appCertificate = env('AGORA_APP_CERTIFICATE');
////    $userId = $request->get('userId');
////    $channelName = $request->get('channelName');
////    $uid = $request->get('uid');
////    $role = $request->get('role');
////    $expireTime = 0;
////    $currentTime = time();
////    $privilegeExpiredTs = $currentTime + $expireTime;
////    $token = RtcTokenBuilder::buildTokenWithUid($appID, $appCertificate, $channelName, $uid, $role, $privilegeExpiredTs);
////    $uId = auth()->user()->agora_uid;
//    $uId = 3819893824;
//
////    dd($uId);
//
//    $token = Agora::make(4)->uId($uId)->token();
//    return response()->json([
//        'status' => 'OK',
//        'error' => 0,
////        'data' => $token
//        'data' => $token
//    ]);
//});

Route::get('/agora/token/{userId}/{meetingId}', [AgoraController::class, 'generateToken']);

Route::get('/tasks/{taskCode}/latest-image', [TaskLogController::class, 'getLatestImage']);

Route::get('/tasks/{taskCode}/latest-task-log-send', [TaskLogSendController::class, 'getLatest']);

Route::get('/task/images/{taskCode}', [TaskLogController::class, 'getTaskImages']);

Route::post('/task-log-send/{taskLogId}', [TaskLogSendController::class, 'store']);

Route::post('/meetings/{meetingId}/end', [MeetingController::class, 'endMeeting']);
Route::post('/tasks/{taskId}/complete', [MeetingController::class, 'completeTask']);
