<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Bet;
use Monero\Wallet;

use Carbon\Carbon;

class Transaction extends Model
{
  protected $fillable = [
      'id', 'user_id', 'type', 'payment_id', 'address', 'amount', 'created_at'
  ];

  public $timestamps = false;

  public static function deposit($usr_id)
  {
    $hostname = '127.0.0.1';
    $port = '18082';
    $wallet = new \Monero\Wallet($hostname, $port);
    $intAddressObj = json_decode($wallet->getAddress());

    $payId = bin2hex(openssl_random_pseudo_bytes(32));
    $int_address = $intAddressObj->address;

    $qury = self::insert([
      'user_id' => $usr_id,
      'type' => 'depositInProgress',
      'amount' => 0,
      'payment_id' => $payId,
      'address' => $int_address
    ]);

    if ($qury) {
      return [
        'payment_id' => $payId,
        'deposit_address' => $int_address,
      ];
    }else {
      return [
        'payment_id' => null,
        'deposit_address' => null,
      ];
    }
    // session(['balance' => session('balance')+$amount]);
  }

  public static function test()
  {
    $hostname = '127.0.0.1';
    $port = '18082';
    $wallet = new \Monero\Wallet($hostname, $port);
    $genPayId = bin2hex(openssl_random_pseudo_bytes(32));
    // $genPayId = bin2hex(openssl_random_pseudo_bytes(8));
    $options = [
      'destinations' => (object) [
        'amount' => 0.01,
        'address' => '44n6UXrfEKDeCVSUeXJNNMC7dL3kWkHYuPET4JMwmwHKavNzqhBYathJ1yNg2E1dhfa4zMkEHsZGqiqEkLQPDqtAHmLgZSo',
        'payment_id' => '24f005f416cc217c',
        'mixin' => 5,
        'priority' => 1,
        'do_not_relay' => true,
        'get_tx_hex' => true
      ]
    ];
    $respObj = json_decode($wallet->transfer($options));
    return var_dump($respObj);
    return var_dump(json_decode($wallet->getPayments('7f2c3387122e9cc8398862188917956586cac32cf0597980473cea1f43d5a183')));
  }


  public static function withdraw($usr_id, $address, $payId, $amount)
  {
    $currBalance = self::gatBalance($usr_id);
    if ($amount > $currBalance || empty($address) || !is_string($address) || $amount <= 0.06) {
      return false;
    }

    $hostname = '127.0.0.1';
    $port = '18082';
    $wallet = new \Monero\Wallet($hostname, $port);


    $walletBalance = json_decode($wallet->getBalance())->balance;

    if ($walletBalance < $amount) {
      return false;
    }

    $options = [
      'destinations' => (object) [
        'amount' => $amount,
        'address' => $address,
        'payment_id' => $payId,
        'mixin' => 4,
        'priority' => 1
      ]
    ];
    $respObj = json_decode($wallet->transfer($options));

    if (!empty($respObj->code) || empty($respObj->tx_hash)) {
      return false;
    }

    $qury = self::insert([
      'user_id' => $usr_id,
      'type' => 'withdraw',
      'amount' => $amount,
      'payment_id' => $payId,
      'tx_hash' => $respObj->tx_hash,
      'address' => $address
    ]);

    if ($qury) {
      session(['balance' => $currBalance-$amount]);
      return true;
    }else {
      return false;
    }
  }



  public static function gatBalance($usr_id)
  {
    $tranQury = self::where('user_id', '=', $usr_id);
    $trDepSUM = $tranQury->where('type', '=', 'deposit')->sum('amount');
    $trWthSUM = $tranQury->where('type', '=', 'withdraw')->sum('amount');
    self::updateTransactions($usr_id);

    $trBLNC = $trDepSUM - $trWthSUM; //transaction balance

    $betQury = Bet::where('user_id', '=', $usr_id);
    $betLostSUM = $betQury->where(function ($query) {
                    $query->where('result', '=', 'lose')
                        ->orWhereNull('result');
                  })->sum('amount');

    $betWinSUM = Bet::where([['user_id', '=', $usr_id], ['result', '=', 'win']])->sum('return');

    $betBLNC = (double)$betWinSUM-(double)$betLostSUM; //bets made balance

    $finalBLNCE = $trBLNC+$betBLNC;

    return number_format($finalBLNCE, 4, '.', '');
  }


  public static function updateTransactions($usr_id)
  {
    $tranQury = self::where('user_id', '=', $usr_id)->where('type', '=', 'depositInProgress');
    $hostname = '127.0.0.1';
    $port = '18082';
    $wallet = new \Monero\Wallet($hostname, $port);
    $date = new Carbon;

    if ($tranQury->exists()) {
      $pendingTrans = $tranQury->get();
      foreach ($pendingTrans as $tr) {
        $paymentId = $tr->payment_id;

        $walletTrans = json_decode($wallet->getPayments($paymentId));
        if (!empty($walletTrans) && $walletTrans->payments[0]->block_height > 3) {
          $paymentObj = $walletTrans->payments[0];
          $amount = $paymentObj->amount/1000000000000;
          $amount = number_format($amount, 4, '.', '');
          $block_h = $paymentObj->block_height;
          if(!self::where([['payment_id', '=', $paymentId], ['type', '=', 'deposit']])->exists()){
            self::where([['payment_id', '=', $paymentId], ['type', '=', 'depositInProgress']])->limit(1)->update(['type' => 'deposit', 'amount' => $amount]);
          }else {
            self::where([['payment_id', '=', $paymentId], ['type', '=', 'depositInProgress']])->delete();
          }
        }else {
          $created = $tr->created_at;
          if($date->subDays(7) > $created)
          {
            self::where([['payment_id', '=', $paymentId], ['type', '=', 'depositInProgress']])->delete();
          }
        }
      }
    }
  }

}
