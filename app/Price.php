<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

use Carbon\Carbon;

class Price extends Model
{
    protected $fillable = [
      'price_usd', 'created_at'];

    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = null;


    public static function getLatest($sinceTime = null, $limit = null)
    {
      if (!empty($sinceTime)) {
        $priceQury = self::where('created_at', '>', $sinceTime)->limit($limit);
      }else {
        $priceQury = self::latest()->limit($limit);
      }

      if ($priceQury->exists()) {
        return $priceQury->get();
      }else {
        return null;
      }
    }



    public static function getChartBase()
    {
      $latestPrices = Price::latest()->limit(1)->get()[0];
      if (!empty($latestPrices)) {
        $pVar_usd = $latestPrices->price_usd;
      }else {
        $pVar_usd = 150;
      }


      $pVar_time = Carbon::now()->format('H:i:s');
      $timeNow2secs = date('s', strtotime($pVar_time));
      $pVar_time = Carbon::parse($pVar_time)->subSecond($timeNow2secs % 10)->addSecond(10);



      $percentPer1000 = 0.5;

      $Ysize = 1;
      $first_price = round($pVar_usd, $percentPer1000);



      $data = array(
          'chartYsize' => $Ysize,
          'chartXsize' => 10,
          'first_price' => $first_price,
          'first_time' => $pVar_time,
          'prices_usd' => $pVar_usd,
      );

      return $data;
    }


}
