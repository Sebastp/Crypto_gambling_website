<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;


use App\User;
use App\Activity;
use App\Bet;
use App\Transaction;

class accountsController extends Controller
{
  public static function showProfileDashboard(Request $request)
  {
    $loggedObj = User::getByRemmemberToken($request->cookie('remember_token'));
    $usrActivity = Activity::where('user_id', '=', $loggedObj->user_id)->latest()->first();

    $lastActFormat = Carbon::parse($usrActivity->created_at)->format('n.j.Y  h:i A');
    $usrWonTotal = Bet::where([['user_id', '=', $loggedObj->user_id], ['result', '=', 'win']])->sum('return');

    $walletInfo = Transaction::deposit(session('user_id'));

    $data = array(
      'usr_won' => $usrWonTotal,
      'usr_lastLog' => $lastActFormat,
      'deposit_addr' => $walletInfo['deposit_address'],
      'deposit_pId' => $walletInfo['payment_id']
    );

    return view('layouts.profile')->with($data);
  }

  public static function withdraw(Request $request)
  {
    $address = $request->tr_address;
    $amount = $request->tr_ammount;
    $payId = $request->tr_payment_id;
    $trResp = Transaction::withdraw(session('user_id'), $address, $payId, $amount);

    return response()->json([
      'success' => $trResp,
    ]);
  }

  public static function edit_prof(Request $request)
  {
    $loggedObj = User::getByRemmemberToken($request->cookie('remember_token'));
    if (empty($loggedObj->user_id)) {
      return response()->json([
        'success' => false,
      ]);
    }

    $nickName = $request->username;
    $password = $request->password;
    $rp_password = $request->rp_password;

    $curr_password = $request->curr_password;

    if (!empty(session('has_password')) && session('has_password') && !empty($password)) {
      if (empty($curr_password) || !Hash::check($curr_password, $loggedObj->password)) {
        return response()->json([
          'success' => false,
          'msg' => 'Current Password is not valid'
        ]);
      }
    }

    $rules = array(
      'nickname' => 'string|max:25|min:2|unique:users,nickname',
      'password' => 'string|max:40|min:5|same:rp_password',
      'rp_password' => 'string',
    );

    $messages = [
      'nickname.unique' => 'Nickname already exists',
      'nickname.max' => 'Nickname is too long',
      'nickname.min' => 'Nickname is too short',
      'password.max' => 'Password is too long',
      'password.min' => 'Password is too short',
      'password.same' => 'Passwords do not match',
      'string' => ':attribute is not valid',
    ];

    $toUpdateArr = [];
    $toValidArr = [];

    if (!empty($nickName) && $nickName != session('nickname')) {
      $toUpdateArr['nickname'] = $nickName;

      $toValidArr['nickname'] = $nickName;
    }

    if (!empty($password)) {
      $toUpdateArr['password'] = Hash::make($password);
      $toUpdateArr['remember_token'] = User::uStringId(10, 'remember_token');

      $toValidArr['password'] = $password;
      $toValidArr['rp_password'] = $rp_password;
    }



    $vaildator = Validator::make($toValidArr, $rules, $messages);



    if ($vaildator->fails()) {
      return response()->json([
        'success' => false,
        'msg' => $vaildator->errors()->all()
      ]);
    }else {
      $userId = $loggedObj->user_id;
      $updSuccess = User::where('user_id', '=', $userId)->update($toUpdateArr);
      $respJSON = ['success' => $updSuccess];

      if ($updSuccess && !empty($toUpdateArr['nickname'])) {
        session()->put(['nickname' => $nickName]);
        $respJSON['new_nick'] = $nickName;
      }

      if ($updSuccess && !empty($toUpdateArr['password'])) {
        session()->put(['has_password' => true]);
        return response()->json(
          $respJSON
        )->cookie('remember_token', $toUpdateArr['remember_token']);
      }else {
        return response()->json(
          $respJSON
        );
      }

    }
  }



  public static function login(Request $request)
  {
    $currIp = $request->ip();
    // $currIp = '151.101.65.121';
    $usrAgent = $request->header('user-agent');


    $pswQury = User::where('url', '=', $request->usr_url);
    $error = 0;
    if (!$pswQury->exists()) {
      $error = 1;
    }

    $usrObj = $pswQury->select('user_id', 'password', 'remember_token')->get()[0];
    $hashedPassword = $usrObj->password;
    if (!Hash::check($request->password, $hashedPassword)) {
      $error = 1;
    }

    if ($error) {
      return back()->with('login_err', 'Url Token or Password is wrong');
    }else {
      Activity::logActivity($usrObj->user_id, $currIp, $usrAgent);
      return redirect('/?user='.$request->usr_url)->withCookie(cookie()->forever('remember_token', $usrObj->remember_token));
    }
  }





  public static function generateNew(Request $request, $redirectNext = null)
  {
    $currIp = $request->ip();
    // $currIp = '151.101.65.121';
    $usrAgent = $request->header('user-agent');


    $newUsrData = User::createNew($currIp, $usrAgent);
    $usrUrl2redirect = $newUsrData->url;
    if (empty($redirectNext)) {
      $redirectNext = '/?user='.$usrUrl2redirect;
    }

    return redirect($redirectNext)->cookie('remember_token', $newUsrData->remember_token);
  }




}
