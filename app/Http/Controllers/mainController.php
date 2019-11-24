<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cookie;

use Carbon\Carbon;

use App\User;
use App\Activity;
use App\Price;
use App\Transaction;
use App\Bet;


class mainController extends Controller
{
  public static function showHome(Request $request)
  {
    $requestedUUrl = $request->input('user');
    $currIp = $request->ip();
    // $currIp = '151.101.65.121';
    $usrAgent = $request->header('user-agent');


    $shortUrl = substr(session('user_url__param'), 1, 25).'***';


    $chartData = Price::getChartBase();
    $newBets = Bet::getLatest(7);
    $betLeaders = Bet::getBestBeters('all');




    $data = [
      'usr_shortUrl' => $shortUrl,
      'wonToday' => Redis::get('wonToday'),
      'wonThisWeek' => Redis::get('wonThisWeek') or 0,
      'all_betsNr' => Redis::get('all_betsNr'),
      'active_users' => Redis::get('activeUsersNr'),
      'latest_bets' => $newBets,
      'leadersAll' => $betLeaders,
    ];

    $data = array_merge($data, $chartData);

    if (empty($requestedUUrl)) {
      $usrQury = User::where('remember_token', '=', $request->cookie('remember_token'));
      if(!empty($request->cookie('remember_token')) && $usrQury->exists()){
        $dbUsrUrl = $usrQury->select('url')->get()[0]->url;


        $needTokenCookie = false;
      }else if (!empty($request->session()->get('user_url'))) {
        $dbUsrUrl = session('user_url');
        $needTokenCookie = true;
      }

      if (empty($dbUsrUrl)) {
        return User::createAndRedirect($currIp, $usrAgent);
      }else {
        $usrUrl2redirect = $dbUsrUrl;
        if ($needTokenCookie) {
          $remTokenQury = User::where('url', '=', $usrUrl2redirect);
          if (!$remTokenQury->exists()) {
            return User::createAndRedirect($currIp, $usrAgent);
          }else {
            $remToken = $remTokenQury->select('remember_token')->get()[0]->remember_token;
            return redirect('/?user='.$usrUrl2redirect)->cookie('remember_token', $remToken);
          }
        }else {
          return redirect('/?user='.$usrUrl2redirect);
        }
      }
    }else {
      $requQury = User::where('url', '=', $requestedUUrl);
      if (!$requQury->exists()) {
        if ($requestedUUrl == session('user_url')) {
          $logResp = User::logAgain($request);
          if (!$logResp) {
            return redirect('/');
          }
        }else {
          return abort(404);
        }
      }

      $requObj = $requQury->select('user_id', 'remember_token', 'password')->get()[0];
      $requestedUId = $requObj->user_id;

      $lastUserAcc = User::getByRemmemberToken($request->cookie('remember_token'));

      if ($lastUserAcc->remember_token != $requObj->remember_token) {
        if (!empty($requObj->password)) {
          $data['requested_url'] = $requestedUUrl;
          $data['curr_logged'] = $lastUserAcc;
          return view('layouts.login')->with($data);
        }else {
          Activity::logActivity($requestedUId, $currIp, $usrAgent);
          $newBets = Bet::getLatest(8);
          $data['latest_bets'] = $newBets;

          return redirect('/?user='.$requestedUUrl)->cookie('remember_token', $requObj->remember_token);
          // return view('layouts.home')->with($data)->cookie('remember_token', $requObj->remember_token);
        }
      }else if (!User::sessionValid()) {
        Activity::logActivity($requestedUId, $currIp, $usrAgent);
      }

      return view('layouts.home')->with($data);
    }
  }


  public static function showWallet(Request $request)
  {
    return Transaction::test();
  }




  public static function show_profits(Request $request)
  {
    $url_passwrd = $request->input('passwrd');

    if ($url_passwrd != 'YZzveItIsjyOJivZ') {
      return abort(403);
    }

    $betFee = config('app.bet_fee');

    $wonallt = Bet::where('result', '=', 'win')->sum('return');
    return var_dump([
      'won from the beginning' => $wonallt,
      "owner's profit (".($betFee*100)."%)" => ($wonallt/((1-$betFee)*100))*$betFee*100
    ]);
  }

}
