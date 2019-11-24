<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
  <head>
    @include('partials._head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{config('app.name')}}</title>

    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
  </head>
  <body>
    @include('partials._topbar', ['start_hide' => true, 'act_nav' => 'home'])

    <div class="wrapper">
      <section class="sect-cont" id="sect-landing">
        <div class="container__1" id="sect-landing-inner">
          <div id="landing-top">
            @include('partials._topbar-full')
          </div>

          <div id="landing-content">
            <div id="landing-left">
              <div id="landing-usr">
                <div id="landing-usr-labeltop">
                  <p class="f1_1" id="landing-usr__bets">
                    <span class="f1_2">Bets made</span>
                    {{session('user_bets', 0)}}
                  </p>
                  <span class="f1_10">Hello,</span>
                </div>

                <div id="landing-usr-nick">
                  <a href="{{route('profile')}}" id="usrnick" class="landing-nick__show"><span class="f1_anach_0 nickname-text">{{session('nickname', 'User')}}</span></a>

                  <div class="inpt-undrline-cont" id="landing-nick-edit">
                    <input spellcheck="false" class="neutralize-input f1_anach_0" type="username" name="username" id="landing-nick__inpt" data-prev-val="{{session('nickname')}}" value="{{ session('nickname') }}">
                    <div class="inpt-undrline">
                      <div class="inpt-line__unfcsed"></div>
                    </div>
                  </div>

                  <span class="f1_2 landing-nick__show landing-cta" id="landing-nick-action">Edit</span>
                </div>

                @if(!session('has_password', false))
                  <span class="f1_2 landing-cta" id="landing-usr__setpsw"><a href="{{route('profile')}}#password">Set password</a></span>
                @endif


                <div id="landing-usr__url">
                  <p class="f1_10" id="landing-url">Url:<span class="f1_4" id="landing-usr__urlcontent">{{$usr_shortUrl}}</span></p>
                  <span class="f1_2 landing-cta" data-fullurl="{{session('user_url__param')}}"  id="landing-url__show">Show All</span>
                  <p class="f2_3">This url works as your login token.
                    Don't show it to anyone</p>
                </div>
              </div>

              <div id="landing-left-divline"></div>

              <div id="landing-left-balance">
                <p class="f1_1" id="landing-usrBalance">
                  <span class="f1_2">Balance</span>
                  {{(session('balance', "0") + 0).' '.config('app.main_currency')}}
                </p>

                <div id="landing-balance-btns">
                  <a href="{{route('profile')}}#deposit">
                    <button type="button" class="btn__0">Deposit {{config('app.main_currency')}}</button>
                  </a>
                  <span class="f1_2">or</span>
                  <a href="{{route('profile')}}#withdraw">
                    <button type="button" class="btn__0">Withdraw {{config('app.main_currency')}}</button>
                  </a>
                </div>

                <p class="f2_3">Clicking this buttons will show you a window with more informations about chosen action.</p>
              </div>
            </div>


            <div id="landing-right">
              <h2 class="f1_h1">Welcome in the Best Betting service <br>For Cryptocurrency</h2>
              <div id="landing-right-stats">
                <p id="landing-betsAll">
                  <span class="f1_8">Bets Made</span>
                  <span class="f1_9">{{$all_betsNr or 0}}</span>
                </p>

                <p id="landing-won24">
                  <span class="f1_8">Won last 24h</span>
                  <span class="f1_9">{{$wonToday.' '.config('app.main_currency')}}</span>
                </p>

                <div class="users__active-cont" id="landing-usrOnline">
                  <div class="users__active-dot"></div>
                  <span class="users__active-info f1_9">
                    <span class="active-users">{{$active_users}}</span> Users Online
                  </span>
                </div>
              </div>


              <div id="landing-right-bottom">
                <span class="f1_7" id="lrb-label">No Registration Needed</span>
                <a href="{{route('game1')}}">
                  <button type="button" class="btn__0-light">Play Now And Win Crypto</button>
                </a>
              </div>

              <div id="landing-right-bckg">
                <img src="{{asset('img/blue-bck.png')}}" class="landing-right-shapes">
              </div>
            </div>
          </div>
        </div>
      </section>


      <section class="sect-cont" id="sect-game__showcase">
        <div class="sect-top container__1">
          <a href="{{route('game1')}}">
            <h1 class="f2_h0 sect-top-title">Prediction Game</h1>
            <p class="f2_1 sect-top-desc">Can you predict if the BTC will rise or fall?</p>
          </a>
          <div class="sect-arrow">
            <a href="{{route('game1')}}">
              <svg viewBox="0 0 16.003 9.009">
                <path d="M15.747,1.468L8.443,8.754c-0.343,0.342-0.887,0.336-1.224,0L0.257,1.817c-0.341-0.336-0.341-0.884-0.004-1.22
                c0.342-0.336,0.887-0.336,1.224,0l6.353,6.329l6.698-6.674c0.337-0.336,0.882-0.336,1.219,0C16.088,0.588,16.088,1.132,15.747,1.468
                z"/>
              </svg>
            </a>
          </div>
        </div>

        <div id="sect-game-chart" class="container__1">
          @include('partials._chart', ['chartId' => 'game_chart1'])
        </div>

        <div class="sect-bottom container__1">
          <div class="sect-bottom__left">
            <div class="users__active-cont">
              <div class="users__active-dot"></div>
              <span class="users__active-info f1_1">
                <span class="active-users">{{$active_users}}</span> Users Play
              </span>
            </div>
          </div>

          <div class="game__showcase-mid">
            <a href="{{route('game1')}}" class="showcase-action">
              <button type="button" class="btn__0">Play Now</button>
            </a>
          </div>

          <div class="sect-bottom__right">
            <p class="f1_1" id="game__showcase-wonweek">
              <span class="f1_2">Won this Week</span>
              {{$wonThisWeek.' '.config('app.main_currency')}}
            </p>
          </div>
        </div>
      </section>



      <div class="sect__double container__1 full-onmobile" id="statsfeed-sect">
        <section class="sect-cont stats-list" id="sect-bets">
          <div class="stats-list-top stlst-padding">
            <h3 class="f1_h0">Recent Bets</h3>
          </div>

          <div class="stats-list-content">
            <div class="stlst-padding stats-list-labels">
              <ul class="stats-list-labels-inner stats-list-rowsizes">
                <li class="stlst-label stlst-rs1__user">
                  <span class="f1_4">User</span>
                </li>

                <li class="stlst-label stlst-rs2__bet">
                  <span class="f1_4">Bet</span>
                  <span class="f1_2">({{config('app.main_currency')}})</span>
                </li>

                <li class="stlst-label stlst-rs1__return">
                  <span class="f1_4">Return</span>
                  <span class="f1_2">({{config('app.main_currency')}})</span>
                </li>
              </ul>
            </div>


            <ul class="stats-list-data">
              @foreach ($latest_bets as $indx => $lbet)
                <li class="stlst-data-row @if($indx%2 != 0)stlst-row-dark @endif">
                  <div class="stats-list-rowsizes stlst-padding">
                    <span class="stlst-rs1__user text-over_elip-l1 f1_anach_0">{{$lbet->usr_name}}</span>
                    <span class="stlst-rs2__bet text-over_elip-l1 f1_0">{{$lbet->amount}}</span>
                    <span class="stlst-rs1__return text-over_elip-l1 @if($lbet->result == 'lose') f1_0-neg @else f1_0-pos @endif">{{$lbet->return}}</span>
                  </div>
                </li>
              @endforeach
            </ul>
          </div>
        </section>



        <section class="sect-cont stats-list" id="sect-leaders">
          <div class="stats-list-top stlst-padding">
            <h3 class="f1_h0">Leaderboard</h3>
          </div>

          <div class="stats-list-content">
            <div class="stlst-padding stats-list-labels">
              <ul class="stats-list-labels-inner stats-list-rowsizes">
                <li class="stlst-label stlst-rs1__nr">
                  <span class="f1_4">No.</span>
                </li>

                <li class="stlst-label stlst-rs1__user">
                  <span class="f1_4">User</span>
                </li>

                <li class="stlst-label stlst-rs1__winlose">
                  <span class="f1_4">Win/Lose</span>
                </li>

                <li class="stlst-label stlst-rs1__return">
                  <span class="f1_4">Return</span>
                  <span class="f1_2">({{config('app.main_currency')}})</span>
                </li>
              </ul>
            </div>


            <ul class="stats-list-data">
              @foreach ($leadersAll as $indx => $leader)
                <li class="stlst-data-row @if($indx%2 != 0)stlst-row-dark @endif">
                  <div class="stats-list-rowsizes stlst-padding">
                    <span class="stlst-rs1__nr text-over_elip-l1 f1_0">{{$indx+1}}.</span>
                    <span class="stlst-rs1__user text-over_elip-l1 f1_anach_0">{{$leader->usr_name}}</span>
                    <span class="stlst-rs1__winlose text-over_elip-l1 f1_0">{{$leader->win.'/'.$leader->lose}}</span>
                    <span class="stlst-rs1__return text-over_elip-l1 @if($leader->return < 0) f1_0-neg @else f1_0-pos @endif">{{$leader->return}}</span>
                  </div>
                </li>
              @endforeach
            </ul>
          </div>
        </section>
      </div>
    </div>

    @include('partials._footer')

    @include('partials._scripts')
    <script type="text/javascript" src="{{ asset('js/game-chart.js') }}"></script>
    <script type="text/javascript">
      $(function () {
        setInterval(function(){
          getChartInfo('game_chart1');
        }, 1000);


        if ($(window).scrollTop() > $('.topbar-full').height()) {
          $('.topbar').removeClass('topbar__hidden');
          $('.topbar-nav[style="display: flex;"]').hide();
        }

        $(window).scroll(function () {
          if ($(this).scrollTop() < $('.topbar-full').height()) {
            $('.topbar').addClass('topbar__hidden');
            $('.topbar-nav[style="display: flex;"]').hide();
          } else {
            $('.topbar').removeClass('topbar__hidden');
          }
        });


        $('#landing-nick__inpt').blur(function() {
          prevVal = $(this).attr('data-prev-val');
          currVal = $(this).val();
          if (prevVal != currVal) {
            $.ajax({
              method: 'POST',
              url: '{{route('profile_upd')}}',
              dataType: 'json',
              data: {
                  _token: csrf_token,
                  username: currVal,
              }
            })
            .done(function(r) {
              if (!r.success) {
                showAlertModal(r.msg);
                $('.nickname-text').text(prevVal);
              }else {
                $(this).attr('data-prev-val', currVal);
                $('.nickname-text').text(currVal);
              }
            })
            .fail(function() {
              showAlertModal('Something went Wrong, please try later');
            });

          }
        });
      })
    </script>


  </body>
</html>
