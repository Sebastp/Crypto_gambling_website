<?php

namespace App;

use Illuminate\Http\Request;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Support\Facades\Redis;
use App\Activity;

use Carbon\Carbon;

class User extends Authenticatable
{
    public $incrementing = false;
    protected $primaryKey = null;

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'nickname', 'url', 'password', 'remember_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];


    public static function createNew($usr_ip, $usr_agent)
    {
      $generatedNick = self::generateUserNick();

      $newData = self::create([
          'user_id' => self::uid(9),
          'nickname' => $generatedNick,
          'url' => self::uStringId(60, 'url'),
          'remember_token' => User::uStringId(10, 'remember_token')
      ]);

      if ($newData) {
        Activity::logActivity($newData->user_id, $usr_ip, $usr_agent);
      }
      return $newData;
    }



    public static function createAndRedirect($currIp, $usrAgent)
    {
      $newUsrData = User::createNew($currIp, $usrAgent);
      $usrUrl2redirect = $newUsrData->url;
      return redirect('/?user='.$usrUrl2redirect)->cookie('remember_token', $newUsrData->remember_token);
    }



    public static function logAgain(Request $request)
    {
      if (empty($request->cookie('remember_token'))) {
        return false;
      }

      $currIp = $request->ip();
      // $currIp = '151.101.65.121';
      $usrAgent = $request->header('user-agent');

      $lastUserAcc = User::getByRemmemberToken($request->cookie('remember_token'));

      if (empty($lastUserAcc->user_id)) {
        return false;
      }


      Activity::logActivity($lastUserAcc->user_id, $currIp, $usrAgent);
      return true;
    }



    public static function getByRemmemberToken($token)
    {
      $quryBase = self::where('remember_token', '=', $token);
      if (!empty($token) && $quryBase->exists()) {
        $usrObj = $quryBase->get()[0];
      }else {
        $usrObj = collect([]);
        $usrObj->url = Null;
        $usrObj->nickname = Null;
        $usrObj->user_id = Null;
        $usrObj->remember_token = Null;
        $usrObj->password = Null;
      }

      return $usrObj;
    }


    public static function sessionValid()
    {
      if (empty(session('user_id')) || empty(session('user_ip')) || empty(session('nickname')) || empty(session('user_url')) ||
          empty(session('user_bets')) || empty(session('balance')) || empty(session('sesion_exp')) || session('sesion_exp') < Carbon::now()) {
        return false;
      }else {
        return true;
      }
    }


    public static function setActiveUsr($usr_id, $time = null)
    {
      // //$time == H:i:s
      // if (empty($time)) {
      //   $currTime = Carbon::now();
      //   $time = $currTime->format('H:i:s');
      // }


      $cacheKey = 'act_usrs:'.$usr_id;
      Redis::set($cacheKey, null);
      Redis::expire($cacheKey, 5);
    }


    public static function getActiveNr()
    {
      return count(Redis::scan(null, 'match', 'act_usrs:*')[1]);
    }


    public static function generateUserNick($after_fixNr = null)
    {
      if (empty($after_fixNr)) {
        $after_fixNr = self::count()+1;
      }
      $generatedNick = 'User_'.$after_fixNr;

      if (self::where('nickname', '=', $generatedNick)->exists()) {
        return self::generateUserNick($after_fixNr+1);
      }else {
        return $generatedNick;
      }
    }

}
