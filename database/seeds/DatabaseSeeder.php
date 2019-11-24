<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Redis;


class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      session()->flush();
      Redis::flushall();

      $usrsObjs = factory(App\User::class, 100)->create();
      $usrIds = $usrsObjs->pluck('user_id')->all();
      foreach ($usrIds as $uId) {
        factory(App\Transaction::class, 1)->create(['user_id' => $uId]);
      }
    }
}
