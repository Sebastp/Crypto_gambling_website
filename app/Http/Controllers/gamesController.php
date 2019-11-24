<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Price;
use App\Bet;
use App\User;
use App\Transaction;

use Carbon\Carbon;

use GuzzleHttp\Client;


class gamesController extends Controller
{
  public static function showGame1()
  {
    $data = Price::getChartBase();


    $newBets = Bet::getLatest(7);
    $data['latest_bets'] = $newBets;


    $betLeaders = Bet::getBestBeters('today');
    $data['leadersToday'] = $betLeaders;


    // $data['active_users'] = count(Redis::get('active_users'));
    $data['active_users'] = Redis::get('activeUsersNr');

    return view('layouts.game-1')->with($data);
  }



  public static function getParameter(Request $request)
  {
    $lp = $request->last_price;

    if (empty($lp)) {
      return response()->json([
        'success' => false
      ]);
    }

    $currTime = Carbon::now();
    $currTimeFormat = $currTime->format('H:i:s');

    $timeAfterRound = false;

    $redisResp = json_decode(Redis::get('game_param'));

    if (empty($redisResp->time)) {
      $cache_SameTime = false;
    }else {
      if ($currTimeFormat != $redisResp->time) {
        $cache_SameTime = false;
      }else {
        $cache_SameTime = true;
      }
    }




    if (!Redis::exists('game_param') || !$cache_SameTime) {
      // Carbon::now()->format('H:i:s') == '01:00:00'
      if (Carbon::now()->format('s') == '00') {
        Price::where('created_at', '<', Carbon::now()->subDays(1))->delete();
      }

      $ltsParam = Price::getLatest(null, 1)[0];
      $ltstTimeFormat = Carbon::parse($ltsParam->created_at)->format('H:i:s');
      $ltstRatio = Bet::getRatio();
      $betters = Bet::getBetters();


      $cache_data = [
        'price' => $ltsParam->price_usd,
        'bet_ratio' => $ltstRatio,
        'beting_users' => $betters,
        'time' => $ltstTimeFormat
      ];


      $timeNow2secs = date('s', strtotime($ltsParam->created_at));
      $round_stopsTime = Carbon::parse($currTime)->subSecond($timeNow2secs % 30)->addSecond(20);
      $round_startsTime = Carbon::parse($currTime)->subSecond($timeNow2secs % 30);
      //adds 2 secs delay
      if ($ltstTimeFormat == $round_stopsTime->addSecond(2)->format('H:i:s')) {
        $timeAfterRound = true;

        $pastRoundStart = Carbon::parse($round_startsTime->toDateTimeString())->toDateTimeString();
        $cache_data['won_lastRound'] = number_format(Bet::getWonInTime($pastRoundStart), 4, '.', '');
      }



      //1 sec after round start
      if ($currTimeFormat == Carbon::parse($currTime)->subSecond($timeNow2secs % 30)->format('H:i:s')) {
        $timeNow2secs2 = date('s', strtotime($currTimeFormat));
        $betQury = Bet::where('starts_at', '<', Carbon::parse($currTime)->subSecond($timeNow2secs2 % 30))
        ->orderBy('starts_at', 'desc')->limit(1);

        if ($betQury->exists()) {
          $max_data = $betQury->select('starts_at')->get()[0]->starts_at;
          $ltstRatio = Bet::getRatio($max_data);
          $cache_data['lastRatio'] = $ltstRatio;
        }else {
          $cache_data['lastRatio'] = null;
        }
      }else {
        $cache_data['lastRatio'] = null;
      }


      Redis::set('game_param', json_encode($cache_data));
      Redis::set('activeUsersNr', User::getActiveNr());
      Bet::updateNullResults();



      //update every 10 min
      if ((int)Carbon::parse($currTime)->format('i') % 5 == 0 && Carbon::parse($currTime)->format('s')=='00') {
        $pastWeekStart = Carbon::parse($currTime)->subWeeks(1)->toDateTimeString();
        $wonWeek = number_format(Bet::getWonInTime($pastWeekStart), 4, '.', '');
        Redis::set('wonThisWeek', $wonWeek);

        $pastDayStart = Carbon::parse($currTime)->subHours(24)->toDateTimeString();
        $wonToday = number_format(Bet::getWonInTime($pastDayStart), 4, '.', '');
        Redis::set('wonToday', $wonToday);
      }
    }


    $redisResp = json_decode(Redis::get('game_param'));
    $data = [
      'price' => $redisResp->price,
      'bet_ratio' => $redisResp->bet_ratio,
      'time' => $redisResp->time,
      'beting_users' => $redisResp->beting_users,
      'active_users' => Redis::get('activeUsersNr'),
    ];

    if (!is_null($redisResp->lastRatio)) {
      $data['lastRatio'] = $redisResp->lastRatio;
    }

    if ($timeAfterRound) {
      $data['won_lastRound'] = $redisResp->won_lastRound;
    }



    if ($request->getRound === 'next_round') {
      $pVar_time = Carbon::now();
      $timeNow2secs = date('s', strtotime($pVar_time));
      $data['rounds'] = [];
      for ($r=0; $r < 5; $r++) {
        $r1 = ($r+1)*30;
        $data['rounds'][$r]['round_start'] = Carbon::parse($pVar_time)->subSecond($timeNow2secs % 30)->addSecond($r1)->format('H:i:s');
        $data['rounds'][$r]['round_stop'] = Carbon::parse($pVar_time)->subSecond($timeNow2secs % 30)->addSecond($r1+20)->format('H:i:s');
      }
    }


    /*$userIds = User::get()->pluck('user_id');
    $userIds = array_diff($userIds->toArray(), [session('user_id')]);
    // $userIds = [];
    foreach ($userIds as $uId) {
      if (rand(0,100)>99) {
        if (rand(0,100) < 30) {
          $betType = 'up';
        }else {
          $betType = 'down';
        }

        //generate rand float
        $Min = 0;
        $Max = 5;
        $round=4;
        if ($Min>$Max) { $Min=$Max; $Max=$Min; }
              else { $Min=$Min; $Max=$Max; }
        $randomfloat = $Min + mt_rand() / mt_getrandmax() * ($Max - $Min);
        if($round>0)
          $randomfloat = round($randomfloat,$round);

      	$betAmm = $randomfloat;

        $usrBlnce = Transaction::gatBalance($uId);
        // if(!Bet::where([['user_id', '=', $uId],['result', '=', null]])->exists()){
          if ($usrBlnce > 0 && $betAmm > 0) {
            if ($usrBlnce >= $betAmm) {
              Bet::newBet($uId, $betAmm, $betType);
            }else{
              $betAmm = $usrBlnce;
              Bet::newBet($uId, $betAmm, $betType);
            }
          }
        // }
      }
    }*/



    return response()->json($data);
  }


  public static function saveBet(Request $request)
  {
    $betAmount = $request->amount;
    $betType = $request->type;

    $currUsrBalance = Transaction::gatBalance(session('user_id'));



    $rules = array(
      'amount' => 'required|numeric|min:0.0001',
      'type' => ['required',
                  Rule::in(['up', 'down'])]
    );

    $vaildator = Validator::make([
      'amount' => $betAmount,
      'type' => $betType
    ], $rules);


    if ($currUsrBalance < 0.0001 || $currUsrBalance < $betAmount || $betAmount <= 0 || $vaildator->fails()) {
      return response()->json([
        'success' => false
      ]);
    }

    $newBetResult = Bet::newBet(session('user_id'), $betAmount, $betType);

    return $newBetResult;
  }



  public static function betResult(Request $request)
  {
    $maxTime = $request->curr_time;
    $currDay = explode(' ', Carbon::today())[0];
    $maxTimeCarb = Carbon::parse($currDay.' '.$maxTime)->toDateTimeString();

    $betResult = Bet::getUserLastBet(session('user_id'), $maxTimeCarb);

    if (empty($betResult)) {
      return response()->json([
        'success' => false,
        'sp' => 258
      ]);
    }
    if ($betResult->result == 'win') {
      $betRet = $betResult->return+$betResult->amount;
    }elseif ($betResult->result == 'draw') {
      $betRet = 0;
    }else{
      $betRet = $betResult->amount*-1;
    }

    $postBalance = Transaction::gatBalance(session('user_id'));
    session(['balance' => $postBalance]);





    return response()->json([
      'success' => true,
      'bet_result' => $betResult->result,
      'bet_return' => $betRet,
      'curr_balance' => $postBalance,
      'bet_start' => $betResult->starts_at
    ]);
  }
}
