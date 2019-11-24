<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

use App\User;
use App\Transaction;
use App\Bet;

class Activity extends Model
{
  protected $fillable = [
    'user_id', 'ip_address', 'user_agent', 'created_at'
  ];

  public $timestamps = false;
  public $incrementing = false;
  protected $primaryKey = null;


  public static function logActivity($user_id, $ip, $user_agent)
  {
    self::create([
        'user_id' => $user_id,
        'ip_address' => $ip,
        'user_agent' => $user_agent,
    ]);
    self::where('created_at', '<', Carbon::now()->subDays(50))->delete(); //free space - too many records slows the game
    // upgrade server in the future and migrate to AWS or GCP for better analysis


    $usrObj = User::where('user_id', '=', $user_id)->select('nickname', 'url', 'password')->get()[0];
    $usrBalance = Transaction::gatBalance($user_id);
    if (empty($usrObj->password)) {
      $hasPSW = false;
    }else {
      $hasPSW = true;
    }

    $betsMade = Bet::where('user_id', '=', session('user_id'))->count();
    session()->put(['user_id' => $user_id, 'user_ip' => $ip, 'nickname' => $usrObj->nickname, 'has_password' => $hasPSW,
                    'user_url' => $usrObj->url, 'user_url__param' => '/?user='.$usrObj->url, 'user_bets' => $betsMade,
                    'balance' => $usrBalance, 'sesion_exp' => Carbon::now()->addMinutes(2)]);
  }




  public static function getUserUrl($ip, $user_agent)
  {
    $act_qury = self::where([['ip_address', '=', $ip], ['user_agent', '=', $user_agent]]);
    if ($act_qury->exists()) {
      $userId = $act_qury->latest()->first()->pluck('user_id');
      return User::where('user_id', '=', $userId)->select('url')->get()[0]->url;
    }else {
      return Null;
    }
  }


  public static function latestLogged($usr_id)
  {
    return self::where('user_id', '=', $usr_id)->select('ip_address', 'user_agent')->latest()->first();
  }
}
