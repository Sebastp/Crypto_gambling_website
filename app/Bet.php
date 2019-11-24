<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;


use Carbon\Carbon;
use App\Price;
use App\User;

class Bet extends Model
{
  protected $fillable = [
    'bet_id', 'user_id', 'amount', 'type', 'result', 'return', 'starts_at', 'updated_at'
  ];

  public $incrementing = false;
  protected $primaryKey = null;



  public static function newBet($usr_id, $bet_amount, $bet_type)
  {
    $currTime = Carbon::now();
    $timeNow2secs = date('s', strtotime($currTime));
    $betStart = Carbon::parse($currTime)->subSecond($timeNow2secs % 30)->addSecond(30)->toDateTimeString();
    $betId = self::uid(9);

    if ($bet_amount <= 0) {
      return response()->json([
        'success' => false
      ]);
    }


    $insrtReq = self::insert([
      'bet_id' => $betId,
      'user_id' => $usr_id,
      'amount' => $bet_amount,
      'type' => $bet_type,
      'starts_at' => $betStart
    ]);

    if ($insrtReq) {
      if (!Redis::exists('all_betsNr')) {
        Redis::set('all_betsNr', 1);
      }else {
        Redis::incr('all_betsNr');
      }


      $postBalance = number_format(session('balance')-$bet_amount, 4, '.', '');
      session(['balance' => $postBalance]);
      session(['user_bets' => session('user_bets')+1]);
      return response()->json([
        'success' => true,
        'bet_start' => Carbon::parse($betStart)->format('H:i:s'),
        'curr_balance' => $postBalance
      ]);
    }else {
      return response()->json([
        'success' => false
      ]);
    }
  }



  public static function getBetPrices($min_data)
  {
    $max_data = Carbon::parse($min_data)->addSecond(19);
    return Price::where([
    ['created_at', '<=', $max_data],
    ['created_at', '>=', $min_data]])->select('price_usd')->get()->pluck('price_usd')->toArray();
  }


  public static function getUserLastBet($usr_id, $max_data)
  {
    $min_data = Carbon::parse($max_data)->subSecond(50);
    $betQury = self::where([
    ['user_id', '=', $usr_id],
    ['starts_at', '<=', $max_data],
    ['starts_at', '>=', $min_data]])->limit(1);


    if ($betQury->exists()) {
      $betInstance = $betQury->orderBy('starts_at', 'desc')->limit(1)->get()[0];
      if (empty($betInstance->result)) {
        return self::updateResults($usr_id, $max_data);
      }else {
        return $betInstance;
      }
    }else {
      return null;
    }
  }




  public static function updateResults($usr_id = null, $max_data)
  {
    $timeNow2secs = date('s', strtotime($max_data));
    $max_data = Carbon::parse($max_data)->subSecond($timeNow2secs % 30)->addSecond(30)->toDateTimeString();

    $min_data = Carbon::parse($max_data)->subSecond(30)->toDateTimeString();
    $max_data = Carbon::parse($max_data)->subSecond(10)->toDateTimeString();
    $betsHist = self::where([
      ['starts_at', '<=', $max_data],
      ['starts_at', '>=', $min_data]
    ])->whereNull('result')->get();

    $betingUsers = $betsHist->pluck('user_id');


    $prices = self::getBetPrices($min_data);

    $frstPrice = $prices[0];
    $lstPrice = $prices[count($prices)-1];
    if ($frstPrice > $lstPrice) {
      $up_bet = false;
    }else if($frstPrice < $lstPrice){
      $up_bet = true;
    }else if ($frstPrice === $lstPrice){
      $up_bet = 'draw';
    }

    if (is_string($up_bet) && $up_bet === 'draw') {
      $betQury = self::whereIn('user_id', $betingUsers)->where([
      ['starts_at', '<=', $max_data],
      ['starts_at', '>=', $min_data]]);

      if ($betQury->exists()) {
        $betQury->update(['result' => 'draw']);
      }
    }else {
      if ($up_bet) {
        $betWinQury = self::whereIn('user_id', $betingUsers)->where([['type', '=', 'up'],
        ['starts_at', '<=', $max_data],
        ['starts_at', '>=', $min_data]]);

        $betLoseQury = self::whereIn('user_id', $betingUsers)->where([['type', '=', 'down'],
        ['starts_at', '<=', $max_data],
        ['starts_at', '>=', $min_data]]);
      }else {
        $betWinQury = self::whereIn('user_id', $betingUsers)->where([['type', '=', 'down'],
        ['starts_at', '<=', $max_data],
        ['starts_at', '>=', $min_data]]);

        $betLoseQury = self::whereIn('user_id', $betingUsers)->where([['type', '=', 'up'],
        ['starts_at', '<=', $max_data],
        ['starts_at', '>=', $min_data]]);
      }



      if ($betLoseQury->exists()) {
        $betLoseQury->update(['result' => 'lose']);
        $lostAmount = $betLoseQury->sum('amount');
      }else {
        $lostAmount = 0;
      }

      if ($betWinQury->exists()) {
        // $betWinQury->update(['result' => 'win']);
        $betWinners = $betWinQury->select('bet_id', 'amount')->get();

        $wonAmount = $betWinners->sum('amount');

        //prevent from adding to the updated
        if ((int)Carbon::now()->format('i') % 5 != 0 && Carbon::now()->format('s')!='00') {
          Redis::set('wonThisWeek', Redis::get('wonThisWeek')+$wonAmount);
          Redis::set('wonToday', Redis::get('wonToday')+$wonAmount);
        }


        $oddsReturn = $lostAmount/$wonAmount;


        foreach ($betWinners as $winner) {
          if ($oddsReturn==1) {
            $betFee = 0;
          }else {
            $betFee = config('app.bet_fee');
          }
          $finalRet = ($oddsReturn*$winner->amount)*(1-$betFee);
          $finallReturnRounded = number_format($finalRet, 4, '.', '')+0;
          self::where('bet_id', '=', $winner->bet_id)->update(['return' => $finallReturnRounded, 'result' => 'win']);
        }
      }
    }



    if (!empty($usr_id)) {
      return self::where([['user_id', '=', $usr_id], ['starts_at', '<=', $max_data], ['starts_at', '>=', $min_data]])->get()[0];
    }
  }


  //gives on up % ratio
  public static function getRatio($round_startTime = null)
  {
    if (empty($round_startTime)) {
      $currTime = Carbon::now();
      $timeNow2secs = date('s', strtotime($currTime));
      $round_startTime = Carbon::parse($currTime)->subSecond($timeNow2secs % 30)->addSecond(30)->toDateTimeString();
    }

    $betsQury = self::where('starts_at', '=', $round_startTime);
    if (!$betsQury->exists()) {
      return 50;
    }

    $grBets = $betsQury->get()->groupBy('type');
    if (!empty($grBets['up'])) {
      $upSum = $grBets['up']->sum('amount');
    }else {
      $upSum = 0;
    }

    if (!empty($grBets['down'])) {
      $downSum = $grBets['down']->sum('amount');
    }else {
      $downSum = 0;
    }
    // $upSum = $betsQury->where('type', '=', 'up')->sum('amount');
    // $downSum = $betsQury->where('type', '=', 'down')->sum('amount');


    if ($upSum == 0 && $downSum == 0) {
      return 50;
    }else if($downSum == 0){
      return 100;
    }else if($upSum == 0){
      return 0;
    }

    $wholeSum = $upSum+$downSum;
    return round(($upSum/$wholeSum)*100, 0);
  }



  public static function getBetters()
  {
    $currTime = Carbon::now();
    $timeNow2secs = date('s', strtotime($currTime));
    $round_startTime = Carbon::parse($currTime)->subSecond($timeNow2secs % 30)->addSecond(30)->toDateTimeString();


    $betsQury = self::where('starts_at', '=', $round_startTime);

    return $betsQury->count();
  }








  public static function updateNullResults(){
    $currTime = Carbon::now();
    $timeNow2secs = date('s', strtotime($currTime));
    $currRound_startTime = Carbon::parse($currTime)->subSecond($timeNow2secs % 30)->subSecond(30)->toDateTimeString();
    $checkedDates = [];

    $nullBetsQury = self::where('starts_at', '<', $currRound_startTime)->whereNull('result');

    if ($nullBetsQury->exists()) {
      $nullBets = array_unique($nullBetsQury->get()->pluck('starts_at')->toArray());

      foreach ($nullBets as $nBET) {
        if (!in_array($nBET, $checkedDates)) {
          self::updateResults(null, $nBET);

          array_push($checkedDates, $nBET);
        }
      }
    }
  }


  public static function getLatest($limit)
  {
    $baseQury = self::limit($limit);
    if (!$baseQury->exists()) {
      return [];
    }else {
      $betsArr = $baseQury->whereNotNull('result')->orderBy('starts_at', 'desc')->get();

      foreach ($betsArr as $betsObj) {
        $betsObj->time = Carbon::parse($betsObj->starts_at)->format('H:i A');
        $betsObj->usr_name = User::where('user_id', '=', $betsObj->user_id)->select('nickname')->get()[0]->nickname;

        if ($betsObj->amount <= 0) {
          $betsObj->payout = 0;
          $betsObj->return = 0;
        }else {
          if ($betsObj->result == 'win') {
            $betsObj->payout = $betsObj->return/$betsObj->amount;
          }elseif ($betsObj->result == 'lose') {
            $betsObj->payout = 0;
            $betsObj->return = $betsObj->amount*-1;
          }else {
            $betsObj->payout = 1;
            $betsObj->return = $betsObj->amount;
          }
        }



        $betsObj->payout = number_format($betsObj->payout, 2, '.', '');


        $betsObj->return = number_format($betsObj->return, 4, '.', '');
        $betsObj->amount = number_format($betsObj->amount, 4, '.', '');
      }

      return $betsArr;
    }
  }



  public static function getWonInTime($since){
    $baseQury = self::where('starts_at', '>=', $since)->where('result', '=', 'win');
    if ($baseQury->exists()) {
      return $baseQury->sum('return');
    }else {
      return 0;
    }
  }

  public static function getBestBeters($when = 'all', $limit = 10){
    $currTime = Carbon::now();
    $currTimeFormat = $currTime->format('H:i');

    if ($when == 'today') {
      $RedisName = 'bets_leaders__today';
      $yesterday_time = Carbon::parse($currTime)->subDay()->toDateTimeString();
      $since_time = $yesterday_time;
    }else if($when == 'all') {
      $RedisName = 'bets_leaders__all';
    }


    if (!Redis::exists($RedisName)) {
      $cache_data = [
        'time' => $currTimeFormat,
        'leadersArr' => []
      ];
      Redis::set($RedisName, json_encode($cache_data));
    }

    $redisResp = json_decode(Redis::get($RedisName));
    $currMins = (int)explode(":", $currTimeFormat)[1];
    if ($when == 'today' && $redisResp->time != $currTimeFormat) {
      $toRedis = true;
    }elseif ($when == 'all' && $currMins%15 == 0) {
      $toRedis = true;
    }else {
      $toRedis = false;
    }
    $toRedis = true;


    if ($toRedis) {
      $baseLdrsQury = Bet::select('user_id', 'amount', 'result', 'return');
      if (!empty($since_time)) {
        $baseLdrsQury = $baseLdrsQury->where('starts_at', '>=', $since_time);
      }

      if (!$baseLdrsQury->exists()) {
        $leadersArr = [];
      }else {
        $leadersArr = $baseLdrsQury->get()->groupBy('user_id');
      }






      $finalLeaders = [];
      foreach ($leadersArr as $key => $bets) {
        // key = user id
        if(empty($finalLeaders[$key])){
          $finalLeaders[$key] = ['return' => 0, 'win' => 0, 'lose' => 0];
        }

        foreach ($bets as $bet) {
          if ($bet->result == 'win') {
            $finalLeaders[$key]['win']++;
            $finalLeaders[$key]['return'] = $finalLeaders[$key]['return'] + $bet->return;
          }
          if ($bet->result == 'lose') {
            $finalLeaders[$key]['return'] = $finalLeaders[$key]['return'] - $bet->amount;
            $finalLeaders[$key]['lose']++;
          }
        }
      }

      $finalLeadersSort = collect($finalLeaders)->sortByDesc('return');
      if (!empty($limit)) {
        $finalLeadersSort = $finalLeadersSort->take($limit);
      }
      $finalLeadersSort = $finalLeadersSort->toArray();


      foreach ($finalLeadersSort as $usrId => $fLeader) {
        $leaderNick = User::where('user_id', '=', $usrId)->select('nickname')->get()[0]->nickname;
        $finalLeadersSort[$usrId]['usr_name'] = $leaderNick;
        $finalLeadersSort[$usrId]['usr_id'] = $usrId;
      }
      $finalLeadersSort = array_values($finalLeadersSort);
      $cache_data = [
        'time' => $currTimeFormat,
        'leadersArr' => $finalLeadersSort
      ];

      Redis::set($RedisName, json_encode($cache_data));
    }

    $redisResp = json_decode(Redis::get($RedisName));
    $finalLeadersSort = $redisResp->leadersArr;

    return $finalLeadersSort;
  }


}
