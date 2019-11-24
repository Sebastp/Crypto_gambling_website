<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
  <head>
    @include('partials._head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{config('app.name')}}</title>

    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
  </head>
  <body >
    @include('partials._topbar', ['no_shadow' => true])

    @include('partials.modals._deposit', ['modalId' => 'depModa0'])
    @include('partials.modals._withdraw', ['modalId' => 'withModa0'])
    <div id="app">
      <div class="wrapper profile-cont padding-topbar">

        <section id="profile-window" class="container__3">
          <div class="pw-top">
            <div class="container__5">
              <h3 class="f1_h0">User Profile</h3>
            </div>
          </div>

          <div class="container__5 pw-mid">
            <div novalidate class="pw-edit" role="form">
              <div class="pw-inpt labeled-inpt">
                <label for="pw-usrname" class="inpt-label__float @if(!empty(session('nickname'))) label__float-active label__float-blur @endif">User Name</label>
                  <div class="inpt-undrline-cont">
                    <input undrline-onfcs label-onfcs spellcheck="false" class="neutralize-input f1_6__pos" type="username" name="username" id="pw-usrname" value="{{ session('nickname') }}">
                    <div class="inpt-undrline">
                      <div class="inpt-line__fcsed inpt-line-positiv"></div>
                      <div class="inpt-line__unfcsed"></div>
                    </div>
                  </div>
                </div>

                @if (session('has_password', false))
                  <div class="pw-inpt labeled-inpt">
                    <label for="pw-password" class="inpt-label__float">Current Password</label>
                    <div class="inpt-undrline-cont">
                      <input undrline-onfcs label-onfcs spellcheck="false" class="neutralize-input f1_6" type="password" name="curr_password" id="pw-curr_password">
                      <div class="inpt-undrline">
                        <div class="inpt-line__fcsed inpt-line-positiv"></div>
                        <div class="inpt-line__unfcsed"></div>
                      </div>
                    </div>
                  </div>
                @endif

                <div class="pw-inpt labeled-inpt">
                  <label for="pw-password" class="inpt-label__float">Password</label>
                  <div class="inpt-undrline-cont">
                    <input undrline-onfcs label-onfcs spellcheck="false" class="neutralize-input f1_6" type="password" name="password" id="pw-password">
                    <div class="inpt-undrline">
                      <div class="inpt-line__fcsed inpt-line-positiv"></div>
                      <div class="inpt-line__unfcsed"></div>
                    </div>
                  </div>
                </div>

                <div class="pw-inpt labeled-inpt">
                  <label for="pw-rp_password" class="inpt-label__float">@if (session('has_password', false))Repeat old Password @else Repeat Password @endif</label>
                  <div class="inpt-undrline-cont">
                    <input undrline-onfcs label-onfcs spellcheck="false" class="neutralize-input f1_6" type="password" name="rp_password" id="pw-rp_password">
                    <div class="inpt-undrline">
                      <div class="inpt-line__fcsed inpt-line-positiv"></div>
                      <div class="inpt-line__unfcsed"></div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="pw-balance-cont">
                <p class="f1_1" id="pw-usrBalance">
                  <span class="f1_2">Balance</span>
                  {{session('balance', "0.00").' '.config('app.main_currency')}}
                </p>

                <div id="pw-balance-btns">
                  <button type="button" class="btn__0" modal-action="depModa0">Deposit {{config('app.main_currency')}}</button>
                  <span class="f1_2">or</span>
                  <button type="button" class="btn__0" modal-action="withModa0">Withdraw {{config('app.main_currency')}}</button>
                </div>

                <p class="f2_3">Clicking this buttons will show you a window with more informations about chosen action.</p>
              </div>


              <div class="pw-stats">
                <div class="pw-stat">
                  <p class="f1_1">
                    <span class="f1_2">Bets made</span>
                    {{session('user_bets')}}
                  </p>
                </div>

                <div class="pw-stat">
                  <p class="f1_1">
                    <span class="f1_2">Total Won</span>
                    {{$usr_won}} {{config('app.main_currency')}}
                  </p>
                </div>

                <div class="pw-stat">
                  <p class="f1_1" id="pw__lastLog" title="UTC timezone"><span class="f1_2">Last logged</span>{{$usr_lastLog}}</p>
                </div>
              </div>
            </div>

            <div class="container__5 pw-down">
              <div class="pw-btns">
                <a href="{{route('home').session('user_url__param')}}"><button type="button" class="btn__1 pw-action__btn" id="pw-action__neg">Cancel</button></a>
                <button type="button" class="btn__1 pw-action__btn" id="pw-action__pos">Save</button>
              </div>
            </div>
          </section>
        </div>

        @include('partials._footer')
    </div>

    @include('partials._scripts')
    <script type="text/javascript">
      var csrf_token = "{{csrf_token()}}";

      $(function () {
        if(window.location.hash) {
          switch (window.location.hash.slice(1)) {
            case 'deposit':
              $('[modal-action="depModa0"]').click();
            break;
            case 'withdraw':
              $('[modal-action="withModa0"]').click();
            break;
            case 'password':
              $('#pw-password').focus();
            break;
          }
        }

        $('#pw-action__pos').click(function() {
          $.ajax({
            method: 'POST',
            url: '{{route('profile_upd')}}',
            dataType: 'json',
            data: {
                _token: csrf_token,
                username: $('#pw-usrname').val(),
                password: $('#pw-password').val(),
                rp_password: $('#pw-rp_password').val(),
                @if(session('has_password', false)) curr_password: $('#pw-curr_password').val() @endif
            }
          })
          .done(function(r) {
            if (!r.success) {
              alert(r.msg);
              console.log(r.msg);
            }else {
              $('.nickname-text').text(r.new_nick);
              $('#pw-action__pos').text('Saved');
              location.reload();
            }
          })
          .fail(function() {
            showAlertModal('Something went Wrong, please try later');
          });
        });


        $('#withdraw-amount').keypress(function(eve) {
          var that = $(this);
          if ((eve.which != 46 || $(this).val().indexOf('.') != -1) && (eve.which < 48 || eve.which > 57) || (eve.which == 46 && $(this).caret().start == 0)) {
            eve.preventDefault();
            return;
          }
          // this part is when left part of number is deleted and leaves a . in the leftmost position. For example, 33.25, then 33 is deleted
          $('#withdraw-amount').keyup(function(eve) {
            if ($(this).val().indexOf('.') == 0) {
              $(this).val($(this).val().substring(1));
            }
          });
        });
        document.getElementById('withdraw-amount').addEventListener("paste",function(e){
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


        $('#withdraw-submit').click(function() {
          var Waddress = $('#withdraw-address').val(),
              WpayId = $('#withdraw-payment_id').val(),
              Wamount = $('#withdraw-amount').val(),
              reg = /4[a-zA-Z|\d]{94}/;


          if (Waddress.match(reg) != null &&
              (parseFloat(Wamount) == Wamount ||  parseInt(Wamount, 10) == Wamount) &&
              parseFloat(Wamount, 10) > 0 && Wamount != '' && Waddress != '' && WpayId != '') {
            if (parseFloat(Wamount) <= 0.06) {
              alert("Withdraw is too small");
              return false;
            }

            if (parseFloat($('.usr_balance').text()) < Wamount) {
              alert("You don't have enough funds");
              return false;
            }
            performTransaction(Waddress, WpayId, Wamount);
          }else {
            alert("Entered value is not valid");
          }
        });


        function performTransaction(address, payment_id, ammount){
          $.ajax({
            method: 'POST',
            url: '{{route('transactio_w')}}',
            dataType: 'json',
            data: {
                _token: csrf_token,
                tr_address: address,
                tr_payment_id: payment_id,
                tr_ammount: ammount
            }
          })
          .done(function(r) {
            if (!r.success) {
              alert('Something went Wrong, please try later');
            }else {
              location.reload();
            }
          })
          .fail(function() {
            alert('Something went Wrong, please try later');
          });
        }

      });
    </script>
  </body>
</html>
