<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
  <head>
    @include('partials._head')
    <title>{{config('app.name')}}</title>

    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
  </head>
  <body>
    @include('partials._topbar', ['no_shadow' => true, 'act_nav' => 'game1'])

    <div class="game-topBetAlert">
      <div class="container__1 topBetAlert-inner">
        <p class="f1_h1 topBetAlert-mainMsg">Congratulations</p>
        <div class="topBetAlert-mid">
          <p class="f1_8 topBetAlert-subMsg">You just won</p>
          <p class="f1_9">
            <span class="game-betAmount">0.00</span>{{config('app.main_currency')}}
          </p>
        </div>

        <div class="game-topBetAlert-close">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </div>
      </div>
    </div>

    @include('partials.modals._alert-0balance', ['modalId' => 'balanceModal'])

    <div class="wrapper game-cont padding-topbar">
      <section class="sect-cont" id="sect-game">
        <div class="sect-top container__1">
          <h1 class="f2_h0 sect-top-title">Prediction Game</h1>
          <p class="f2_1 sect-top-desc">Can you predict if the BTC will rise or fall?</p>
        </div>

        <div class="container__1">
          <div id="game-chart-cont">
            <div class="loader" id="game-chartLoader">
              <svg class="circular-loader" viewBox="25 25 50 50" >
                <circle class="loader-path" cx="50" cy="50" r="20" fill="none" stroke="#2086f8" stroke-width="5" />
              </svg>
            </div>
            @include('partials._chart', ['chartId' => 'game_chart1'])
          </div>

          <div id="game-bet" bet-type="">
            <div id="bet-left">
              <div id="bet-left-todo">
                <div id="bet-left-top">
                  <div class="bet-lefttop">
                    <label for="bet-inpt" class="inpt-label__float label__float-active label__float-blur bet-label">Bet Amount</label>
                    <div class="bet-inpt-cont">
                      <div class="inpt-undrline-cont">
                        <input undrline-onfcs spellcheck="false" min="0" class="neutralize-input f1_6" name="bet-inpt" id="bet-inpt" value="0">
                        <div class="inpt-undrline">
                          <div class="inpt-line__fcsed inpt-line-neutral"></div>
                          <div class="inpt-line__unfcsed"></div>
                        </div>
                      </div>
                      <span class="bet-Currshort">{{config('app.main_currency')}}</span>
                      <div class="bet-inpt-helpers">
                        <span class="f1_2" id="bet-inpt__half">1/2</span>
                        <span class="f1_2" id="bet-inpt__2">2x</span>
                        <span class="f1_2" id="bet-inpt__max">Max</span>
                      </div>
                    </div>
                  </div>

                  <span class="f1_6__pos" id="bet-timesRatio" data-betFee="{{config('app.bet_fee')}}">?</span>

                  <div class="bet-lefttop">
                    <span class="bet-label">Return on Win</span>
                    <p class="f1_6" id="bet-profit"><span id="bet-profit__final">?</span><span class="bet-Currshort"> {{config('app.main_currency')}}</span></p>
                  </div>
                </div>

                <div id="bet-left-btns">
                  <button type="button" class="neutralize-btn bet-type-btn" name="up" id="bet-btn__up">
                    <div class="bet-btn__icon">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="20" x2="12" y2="4"></line><polyline points="6 10 12 4 18 10"></polyline>
                      </svg>
                    </div>
                    <span class="f1_8">Moon</span>
                  </button>

                  <button type="button" class="neutralize-btn bet-type-btn" name="down" id="bet-btn__down">
                    <div class="bet-btn__icon">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="4" x2="12" y2="20"></line><polyline points="18 14 12 20 6 14"></polyline>
                      </svg>
                    </div>
                    <span class="f1_8">Doom</span>
                  </button>
                </div>

                <button type="button" class="btn__0" id="bet-left-play" disabled>Play</button>
              </div>





              <div id="bet-left-done">
                <div id="bet-left-done-inner">
                  <div id="bet-left-done-top">
                    <div class="bet-lefttop">
                      <label for="bet-inpt" class="inpt-label__float label__float-active label__float-blur bet-label">Bet Amount</label>
                      <span class="f1_6" id="bet-done-mount">56</span>
                      <span class="bet-Currshort">{{config('app.main_currency')}}</span>
                    </div>

                    <span class="f1_6__pos" id="bet-done-timesRatio" data-betFee="{{config('app.bet_fee')}}">x1.0</span>

                    <div class="bet-lefttop">
                      <span class="bet-label">Return on Win</span>
                      <span id="bet-done-profit__final" class="f1_6">1.0</span>
                      <span class="bet-Currshort"> {{config('app.main_currency')}}</span>
                    </div>
                  </div>


                  <div id="bet-left-done-type">
                    <span class="bet-label">Your Bet</span>

                    <span class="f1_8" id="bet-done-up">Moon</span>

                    <span class="f1_8" id="bet-done-down">Doom</span>
                  </div>
                </div>
              </div>
            </div>


            <div id="bet-right">
              <div id="bet-right-top">
                <div class="bet-righttop">
                  <span class="bet-label">Round Starts In</span>
                  <span class="f1_6" id="bet-counter">--:--</span>
                </div>

                <div class="bet-righttop">
                  <span class="bet-label">Current Price</span>
                  <span class="chart_price f1_6" id="bet-price">${{number_format($prices_usd, 2, '.', '')}}</span>
                </div>
              </div>

              <div id="bet-right-mid">
                <span class="bet-label">Bets Ratio</span>
                <div id="bet-ratiobar">
                  <span class="f1_3__big ratiobar-numb" id="ratio-up">--%</span>
                  <div id="ratiobar">
                    <div id="ratiobar-green"></div>
                    <div id="ratiobar-red"></div>
                  </div>
                  <span class="f1_3__big ratiobar-numb" id="ratio-down">--%</span>
                </div>
              </div>

              <div id="bet-right-down">
                <p id="bet-right-won">
                  <span class="f1_2">Won last round</span>
                  <span class="f1_1" id="bet-won__count"><span id="bet-won__lstR">0</span> {{config('app.main_currency')}}</span>
                </p>

                <div class="users__active-cont" id="bet-right-usrOnline">
                  <div class="users__active-dot"></div>
                  <span class="users__active-info f1_1">
                    <span class="active-users">{{$active_users}}</span> Active Users
                  </span>
                </div>
              </div>
            </div>

          </div>
        </div>
      </section>


      <div class="container__1 full-onmobile">
        <section class="sect-cont stats-list" id="game-sect-bets">
          <div class="stats-list-top stlst-padding">
            <h3 class="f1_h0">Recent Bets</h3>
          </div>

          <div class="stats-list-content">
            <div class="stlst-padding stats-list-labels">
              <ul class="stats-list-labels-inner stats-list-rowsizes">
                <li class="stlst-label stlst-rs1__user">
                  <span class="f1_4">User</span>
                </li>

                <li class="stlst-label stlst-rs1__time">
                  <span class="f1_4">Time</span>
                </li>

                <li class="stlst-label stlst-rs1__bet">
                  <span class="f1_4">Bet</span>
                  <span class="f1_2">({{config('app.main_currency')}})</span>
                </li>

                <li class="stlst-label stlst-rs1__payout">
                  <span class="f1_4">Payout</span>
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
                    <span class="stlst-rs1__time text-over_elip-l1 f1_0">{{$lbet->time}}</span>
                    <span class="stlst-rs1__bet text-over_elip-l1 f1_0">{{$lbet->amount}}</span>
                    <span class="stlst-rs1__payout text-over_elip-l1 f1_0">{{$lbet->payout}}x</span>
                    <span class="stlst-rs1__return text-over_elip-l1 @if($lbet->result == 'lose') f1_0-neg @else f1_0-pos @endif">{{$lbet->return}}</span>
                  </div>
                </li>
              @endforeach
            </ul>
          </div>
        </section>


        <section class="sect-cont stats-list" id="game-sect-tophigh">
          <div class="stats-list-top stlst-padding">
            <h3 class="f1_h0">Top Winners Today</h3>
          </div>

          <div class="stats-list-content">
            <div class="stlst-padding stats-list-labels">
              <ul class="stats-list-labels-inner stats-list-rowsizes">
                <li class="stlst-label stlst-rs1__nr">
                  <span class="f1_4">No.</span>
                </li>

                <li class="stlst-label stlst-rs2__user">
                  <span class="f1_4">User</span>
                </li>


                <li class="stlst-label stlst-rs1__wl">
                  <span class="f1_4">Win</span>
                </li>

                <li class="stlst-label stlst-rs1__wl">
                  <span class="f1_4">Lose</span>
                </li>

                <li class="stlst-label stlst-rs1__return">
                  <span class="f1_4">Return</span>
                  <span class="f1_2">({{config('app.main_currency')}})</span>
                </li>
              </ul>
            </div>

            <ul class="stats-list-data">
              @foreach ($leadersToday as $indx => $lbet)
                <li class="stlst-data-row @if($indx%2 != 0)stlst-row-dark @endif">
                  <div class="stats-list-rowsizes stlst-padding">
                    <span class="stlst-rs1__nr text-over_elip-l1 f1_0">{{$indx+1}}.</span>
                    <span class="stlst-rs2__user text-over_elip-l1 f1_anach_0">{{$lbet->usr_name}}</span>
                    <span class="stlst-rs1__wl text-over_elip-l1 f1_0">{{$lbet->win}}</span>
                    <span class="stlst-rs1__wl text-over_elip-l1 f1_0">{{$lbet->lose}}</span>
                    {{-- <span class="stlst-rs1__winlose text-over_elip-l1 f1_0">{{$lbet['win'].'/'.$lbet['lose']}}</span> --}}
                    <span class="stlst-rs1__return text-over_elip-l1 @if($lbet->return < 0) f1_0-neg @else f1_0-pos @endif">{{number_format($lbet->return, 4, '.', '')}}</span>
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

        setTimeout(function(){
          $('#game-chartLoader').fadeOut('300');
          $('#game_chart1').css('opacity', '1');
        }, 3000);


        $('#bet-inpt').keypress(function(eve) {
          var that = $(this);
          if ((eve.which != 46 || $(this).val().indexOf('.') != -1) && (eve.which < 48 || eve.which > 57) || (eve.which == 46 && $(this).caret().start == 0)) {
            eve.preventDefault();
            return;
          }
          // this part is when left part of number is deleted and leaves a . in the leftmost position. For example, 33.25, then 33 is deleted
          $('#bet-inpt').keyup(function(eve) {
            if ($(this).val().indexOf('.') == 0) {
              $(this).val($(this).val().substring(1));
            }
          });

          if ($(this).val() != '') {
            setTimeout(function(){
              window.updateFinalProfit();
            }, 10);
          }
        });


        $('#bet-inpt').blur(function(eve) {
          var floatDigs = $(this).val().split('.')[1];
          if (typeof floatDigs != 'undefined') {
            if (floatDigs.length > 4) {
              var toInsert = parseFloat($(this).val()).toFixed(4);
              if (toInsert < 0.0001 ) {
                toInsert = 0.0001;
              }
            }
          }

          if (typeof toInsert != 'undefined') {
            var finalInsert = parseFloat(toInsert);
            $(this).val(finalInsert);
          }
          if ($(this).val() == '') {
            $(this).val('0');
          }
        });


        document.getElementById('bet-inpt').addEventListener("paste",function(e){
          var copyText = e.clipboardData.getData('text/plain');

          if (copyText.indexOf('.') != -1) {
            var toInsert = parseFloat(copyText);
            if (toInsert.split('.')[1].length > 4) {
              toInsert = toInsert.toFixed(4);
            }
          }else {
            var toInsert = parseInt(copyText);
          }

          if (toInsert < 0.0001) {
            toInsert = 0.0001;
          }
          document.execCommand("insertHTML", false, toInsert);
        },false);

        $('#bet-inpt__half').click(function() {
          var currInptVal = parseFloat($('#bet-inpt').val());
          if (currInptVal > 0.0001) {
            $('#bet-inpt').val(currInptVal/2);
          }
        });

        $('#bet-inpt__2').click(function() {
          var currInptVal = parseFloat($('#bet-inpt').val()),
              currBalance = parseFloat($('#topbar-usrBalance .usr_balance').text()),
              finalVal = currInptVal*2;

          if (currInptVal > 0) {
            if (finalVal > currBalance) {
              $('#bet-inpt').val(currBalance);
            }else {
              $('#bet-inpt').val(finalVal);
            }
          }
        });


        $('#bet-inpt__max').click(function() {
          var usrBalance = parseFloat($('#topbar-usrBalance .usr_balance').text());

          if (usrBalance>0) {
            $('#bet-inpt').val(usrBalance);
          }else {
            $('#bet-inpt').val(0);
          }
        });

        $('.bet-type-btn').click(function() {
          var typeName = $(this).attr('name'),
              betCont = $('#game-bet');

          if (betCont.attr('bet-type') == typeName) {
            // betCont.attr('bet-type', '');
            // $('#bet-left-play')[0].setAttribute('disabled');
          }else {
            betCont.attr('bet-type', typeName);
            $('#bet-left-play')[0].removeAttribute('disabled');
          }

          if ($('#bet-timesRatio').text() != '?') {
            updateTimesRatio(false);
          }
        });


        $('#bet-left-play').click(function() {
          if ($('#game-bet')[0].hasAttribute('disabled') || $('#game-bet')[0].hasAttribute('disabled-error')) {
            return;
          }

          if(parseFloat($('.usr_balance').text()) <= 0){
            $('#modal-depositAlert').addClass('modalActive');
            $('body').addClass('modalMode');
            return;
          }

          var gameSect = findParentBySelector($(this)[0], '#sect-game'),
              betType = $('#game-bet')[0].getAttribute('bet-type'),
              betPrice = $('#bet-inpt').val();
          if (betType == null) {
            return;
          }
          placeBet(betPrice, betType, csrf_token);
        });
      });
    </script>
  </body>
</html>
