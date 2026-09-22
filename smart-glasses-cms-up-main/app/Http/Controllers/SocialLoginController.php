<?php

namespace App\Http\Controllers;

use App\Exceptions\CantOpenFileFromUrlException;
use App\Models\UserAccount;
use App\Services\RandomStrings;
use App\Services\UrlUploadedFile;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class SocialLoginController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use RandomStrings;

    function socialLiteRedirect($provider): SymfonyRedirectResponse|RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    function socialLiteCallback(Request $request, $provider): RedirectResponse
    {
        $socialUser = Socialite::driver($provider)->user();


        $email = $socialUser->getEmail();
        if(!$email) {
            /** @var UserAccount $userAccount */
            $userAccount = UserAccount::query()->where([
                'id' => $socialUser->id,
                'provider' => $provider
            ])->first();
            if($userAccount) $email = $userAccount->user->getAttribute('email');
        }

        /** @var User $user */
        $user = User::query()->updateOrCreate([
            'email' => $email ?? sprintf("%s@%s",Str::random(),env('APP_DOMAIN','amuz.co.kr'))
        ],[
            'name' => $socialUser->getNickname() ?? sprintf("%s %s %s %s",$this->getActionRand(), $this->getColorRand(), $this->getStrRand(),$this->getNameRand()),
        ]);

        $profileImage = $socialUser->getAvatar();
        if($profileImage != null && $profileImage != ''){
            try {
                $uploadedPhoto = UrlUploadedFile::createFromUrl($profileImage);
                $user->updateProfilePhoto($uploadedPhoto);
            } catch (CantOpenFileFromUrlException $e) {
                dd($e);
            }
        }

        $userAccount = UserAccount::query()->updateOrCreate([
            'id' => $socialUser->id,
            'provider' => $provider,
        ], [
            'user_id' => $user->getKey(),
            'token' => $socialUser->token,
            'refresh_token' => $socialUser->refreshToken,
        ]);
//        $userAccount->refresh();

        Auth::login($user);

        return redirect()->to('/');
    }
}
