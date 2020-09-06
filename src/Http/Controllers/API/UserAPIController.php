<?php
declare(strict_types=1);

namespace ArtisanCloud\SaaSMonomer\Http\Controllers\API;


use ArtisanCloud\SaaSFramework\Http\Requests\RequestUserRegister;
use ArtisanCloud\SaaSFramework\Http\Requests\RequestUserRegisterInvitation;
use ArtisanCloud\SaaSFramework\Http\Controllers\API\APIController;
use ArtisanCloud\SaaSFramework\Http\Controllers\API\APIResponse;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;


class UserAPIController extends APIController
{
    private $userService;

//    function __construct(UserService $userService, Request $request, User $user)
    function __construct(Request $request)
    {
        // init the default value
        // parent will construction automatically
        parent::__construct($request);

//        $this->m_user = $user;
//        $this->userService = $userService;
    }


    /**
     * API Get Service
     * name: user.read.service
     * description: get service
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function apiGetService(Request $request) : JsonResponse
    {

        $data = $request->all();

        $this->m_apiResponse->pushDataWithKeyValue("client says:", $data);
        $this->m_apiResponse->pushDataWithKeyValue("Service says", "I am User Service");

        $this->m_apiResponse->setCode(API_ERR_CODE_REQUEST_PARAMETER, API_RETURN_CODE_WARNING);

        return $this->m_apiResponse->toResponse();

    }

    public function apiRegisterInvitation(RequestUserRegisterInvitation $request)
    {
        dd($request->all());

        return $this->m_apiResponse->toResponse();
    }


    /**
     * API Get Me.
     * Name: user.read.me
     * Description: my profile and ping server
     *
     * @param Request
     *
     * @return JsonResponse
     *
     */
    public function apiMe(Request $request) : JsonResponse
    {

//        $user = UserService::getAuthUser();
//        $meID = $user->uuid;
//
//        $cacheTag = class_basename('User');
//        $cacheKey = "me.{$meID}";
//        //暫時不用緩存
////        $user = \Cache::tags($cacheTag)->remember($cacheKey, CacheAPIController::SYSTEM_CACHE_TIMEOUT, function () use ($user) {
////
////            $user->lead;
////            $user->lead->studio;
////            $user->account;
////            $user->account->studio;
////
////            return $user;
////        });
//
////        dump($user);

//        return SpaceAPIResponse::success(new UserResource($user));

        return $this->m_apiResponse->toResponse();

    }



    /**
     * API Update User locale
     * name: me.update.locale
     * description: update user locale
     *
     * @@param Request $request
     *
     * @return JsonResponse
     */
    public function apiUpdateUserLocale(Request $request) : JsonResponse
    {
////        dd($this->m_requestData);
//        $me = UserService::getAuthUser();
////        dd($me);
//        $me->locale = $this->m_requestData['locale'];
////        dd($me->locale);
//        $bResult = $me->save();
//        if ($me->save()) {
//
//            // reset client local
//            $client = (new ClientProfile())->getProfile();
//            $client->locale = $me->locale;
//            $client->save();
//
//
//            $meID = $me->id;
//
////            $cacheTag = User::COMMON_NAME;
//            $cacheKey = "me.{$meID}.locale";
//            \Cache::forget($cacheKey);
//            $cacheTag = class_basename('ClientProfile');
//            $cacheKey = "client.{$client->platform}.{$client->channel}.{$client->uuid}.locale";
////            dd($cacheKey);
//            \Cache::tags($cacheTag)->forget($cacheKey);
//
//            $this->setData($bResult);
//        } else {
//            $this->setCode(API_ERR_CODE_FAIL_TO_UPDATE_LOCALE);
//        }
//
//        return $this->getJSONResponse();
        return $this->m_apiResponse->toResponse();

    }


    /**
     * API Reset password
     * name: user.update.password-reset
     * description: reset user password
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function apiResetPassword(Request $request)
    {
////        dd($request->get('mobile'));
//        $username = $request->get('mobile');
//        $user = $this->m_user->getUserByUserName($username);
////        dd($user);
//
//        $strEncodePassword = encodeHashedPassword($this->m_requestData['password']);
//        $user->password = $strEncodePassword;
//        $bResult = $user->save();
//        if ($user->save()) {
//
//            $this->setResultCode(API_RESULT_CODE_SUCCESS_RESET_PASSWORD);
//            $this->setData($bResult);
//        } else {
//            $this->setCode(API_ERR_CODE_FAIL_TO_UPDATE_LOCALE);
//        }
//
//        return $this->getJSONResponse();

        return $this->m_apiResponse->toResponse();
    }


    /**
     * API Update password
     * name: me.update.password-update
     * description: update user password
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function apiUpdatePassword(Request $request) : JsonResponse
    {
//        dd($this->m_requestData);
//        $me = UserService::getAuthUser();
//
//        $strEncodePassword = encodeHashedPassword($this->m_requestData['password']);
//        $me->password = $strEncodePassword;
//        $bResult = $me->save();
//        if ($me->save()) {
//
//            $this->setResultCode(API_RESULT_CODE_SUCCESS_RESET_PASSWORD);
//            $this->setData($bResult);
//        } else {
//            $this->setCode(API_ERR_CODE_FAIL_TO_UPDATE_LOCALE);
//        }
//
//        return $this->getJSONResponse();

        return $this->m_apiResponse->toResponse();
    }



}
