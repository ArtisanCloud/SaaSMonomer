<?php

namespace ArtisanCloud\SaaSMonomer\Models;

use Illuminate\Database\Eloquent\Model;

class ClientProfile extends Model
{
    // Disable Laravel's mass assignment protection
    protected $guarded = [];
    
    //
    protected $table = 'client_profiles';

    const CACHE_TIMEOUT = 60;
    const PLATFORM_SALESFORCE = 'Salesforce';
    const PLATFORM_WECHAT_MINI_PROGRAM = 'WeChat Mini Program';
    const PLATFORM_IOS = 'iOS';
    const PLATFORM_ANDROID = 'Android';
    const PLATFORM_RETAIL = 'Retail';
    const PLATFORM_JD = 'JD';
    const PLATFORM_TMALL = 'TMall';
    const PLATFORM_DIANPING = 'DianPing';
    const PLATFORM_ALL = 'All';

    const CHANNEL_SPACE = 'Space';
    const CHANNEL_BENZ = 'Mercedes';
    const CHANNEL_WEWORK = 'WeWork';

    const OS_TYPE_IOS = 1;
    const OS_TYPE_ANDROID = 2;

    const LOCALE_EN = 'en_US';
    const LOCALE_CN = 'zh_CN';

    const TIMEZONE = 'UTC';
    const REQUEST_TIMEZONE = 'Asia/Shanghai';

    const ARRAY_PLATFORM = [
        self::PLATFORM_SALESFORCE,
        self::PLATFORM_WECHAT_MINI_PROGRAM,
        self::PLATFORM_IOS,
        self::PLATFORM_ANDROID,
        self::PLATFORM_RETAIL,
        self::PLATFORM_JD,
        self::PLATFORM_TMALL,
        self::PLATFORM_DIANPING,
        self::PLATFORM_ALL,
    ];

    const ARRAY_CHANNEL = [
        self::CHANNEL_SPACE,
        self::CHANNEL_WEWORK,
        self::CHANNEL_BENZ,
    ];

    const ARRAY_OS_TYPE = [
        self::OS_TYPE_IOS,
        self::OS_TYPE_ANDROID,
    ];

    const ARRAY_LOCALE = [
        self::LOCALE_EN,
        self::LOCALE_CN,
    ];

    const ARRAY_TIMEZONE = [
        self::LOCALE_EN,
        self::LOCALE_CN,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'platform', 'channel', 'uuid', 'locale'
    ];


    /**
     * Create profile
     *
     * @param array $data
     *
     * @return \App\Models\ClientProfile
     *
     */
    public function createProfile($data)
    {
        $user = $this->create([
            // customer info
            'platform' => $data["platform"],
            'channel' => $data["channel"],
            'uuid' => $data["uuid"],
            'locale' => $data["locale"] ?? self::LOCALE_CN,
            'wx_mp_session_key' => $data["wxMPSessionKey"] ?? null,

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        return $user;
    }


    /**
     * Get profile
     *
     * @param array $data
     *
     * @return \App\Models\ClientProfile
     *
     */
    public function getProfile($data = [])
    {

        $data = [
            'platform' => $data["platform"] ?? request()->header('platform'),
            'channel' => $data["channel"] ?? request()->header('channel'),
            'uuid' => $data["uuid"] ?? request()->header('uuid'),
        ];
//        dd($data);

        $profile = $this->firstOrCreate($data);

        return $profile;
    }

    public static function _getCurrentPlatform()
    {
        return request()->header('platform');
    }

    public static function _getCurrentChannel()
    {
        return request()->header('channel');
    }

    public static function _getCurrentUUIT()
    {
        return request()->header('uuid');
    }

    public function setCurrentSession(string $sessionKey)
    {
        $para['platform'] = request()->header('platform');
        $para['channel'] = request()->header('channel');
        $para['uuid'] = request()->header('uuid');

        $profile = $this->getProfile($para);
        $profile->wx_mp_session_key = $sessionKey;

        return $profile->save();

    }

    /**
     * Get session locale.
     *
     * @return string $locale
     */
    public static function getSessionLocale()
    {

        // client cannot override the message locales.
        $locale = \App\Models\ClientProfile::getRequestLocale();
//        dd($locale);
        if (!$locale) {
//            $locale = ClientProfile::_getCachedClientLocale();
            $locale = ClientProfile::LOCALE_CN;
        }
        return $locale;
    }

    /**
     * Get request locale.
     *
     * @return string $locale
     */
    public static function getRequestLocale()
    {

        $requestLocal = request()->header('locale') ?? null;
        if ($requestLocal && in_array($requestLocal, ClientProfile::ARRAY_LOCALE)) {
            return $requestLocal;
        }
        return null;
    }


    /**
     * Get cached client locale.
     *
     * @return string $locale
     */
    public static function _getCachedClientLocale()
    {

        // this check is used for request header keys themselves with null value and default report with chinese string
        if (!request()->header('platform')
            || !request()->header('channel')
            || !request()->header('uuid')) {
            return env('APP_LOCALE');
        }
        $para['platform'] = request()->header('platform');
        $para['channel'] = request()->header('channel');
        $para['uuid'] = request()->header('uuid');

        // need cached for every requested.
        $cacheTag = class_basename('ClientProfile');
        $cacheKey = "client.{$para['platform']}.{$para['channel']}.{$para['uuid']}.locale";
//        dd($cacheKey);
        $locale = \Cache::tags($cacheTag)->remember($cacheKey, self::CACHE_TIMEOUT, function () use ($para) {

            // the default locale is defined in migration
            $locale = (new ClientProfile())->getProfile($para);
//            dump($locale);
            return $locale;
        });
//        dd($locale);
        return $locale->locale;
    }

    public static function doesMatchCurrentLocal($lang)
    {
//        dd($lang, ClientProfile::_getCachedClientLocale());
//        dump($lang, ClientProfile::_getCachedClientLocale());
//        return  $lang == ClientProfile::_getCachedClientLocale();
        return $lang == ClientProfile::getSessionLocale();
    }


    /**
     * Get session timezone.
     *
     * @return string $timezone
     */
    public static function getSessionTimezone()
    {
        // client cannot override the message locales.
        $timezone = ClientProfile::getRequestTimezone();
//        dd($locale);
        if (!$timezone) {
//            $timezone = ClientProfile::_getCachedClientLocale();
            $timezone = ClientProfile::REQUEST_TIMEZONE;
        }
        return $timezone;
    }

    /**
     * Get request timezone.
     *
     * @return string $timezone
     */
    public static function getRequestTimezone()
    {

        $requestTimezone = request()->header('timezone') ?? null;
        $arrayTimezone = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
//        dump($requestTimezone, $arrayTimezone);
        if (in_array($requestTimezone, $arrayTimezone)) {
            return $requestTimezone;
        }

        return null;
    }

    public static function isPlatformMP()
    {
        return self::_getCurrentPlatform() == self::PLATFORM_WECHAT_MINI_PROGRAM;
    }
}
