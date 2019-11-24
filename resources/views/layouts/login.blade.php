<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
  <head>
    @include('partials._head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{config('app.name')}}</title>

    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
  </head>
  <body>
    <div class="log-topbar">
      <div class="container__1 topbar-inner">
        <div id="topbar-left">
          @if (!empty($curr_logged->nickname) && $curr_logged->url != $requested_url)
            <a href="{{route('home').session('user_url__param')}}"><button type="button" class="btn__1">Login as {{$curr_logged->nickname}}</button></a>
          @endif
        </div>

        <div id="topbar-center">
          <a href="{{route('home').session('user_url__param', '')}}" class="topbar-brand-inner">
            <p class="topbar-logo__text">ElementCoin</p>
          </a>
        </div>
      </div>
    </div>

    <div id="app">
      <div class="wrapper log-cont">

        <section id="log-window" class="container__4">
          <div class="log-top">
            <div class="container__5">
              <h3 class="f1_h0">Login</h3>
            </div>
          </div>

          <div class="log-mid container__5">

            @if (!empty(session('login_err')))
              <p class="f1_3__light log-error">{{session('login_err')}}</p>
            @endif
            
            <form novalidate role="form" action="{{ route('login') }}" method="POST">
              {{ csrf_field() }}
              <div class="log-inpt labeled-inpt">
                <label for="log-usrname" class="inpt-label__float @if(!empty($requested_url)) label__float-active label__float-blur @endif">Url Token</label>
                  <div class="inpt-undrline-cont">
                    <input undrline-onfcs label-onfcs spellcheck="false" class="neutralize-input f1_6" type="text" name="usr_url" id="log-usr_url" value="{{ $requested_url }}">
                    <div class="inpt-undrline">
                      <div class="inpt-line__fcsed inpt-line-positiv"></div>
                      <div class="inpt-line__unfcsed"></div>
                    </div>
                  </div>
                </div>

                <div class="log-inpt labeled-inpt">
                  <label for="log-password" class="inpt-label__float">Password</label>
                  <div class="inpt-undrline-cont">
                    <input undrline-onfcs label-onfcs spellcheck="false" class="neutralize-input f1_6" type="password" name="password" id="log-password">
                    <div class="inpt-undrline">
                      <div class="inpt-line__fcsed inpt-line-positiv"></div>
                      <div class="inpt-line__unfcsed"></div>
                    </div>
                  </div>
                </div>

                <div id="log-ctas">
                  <button type="submit" class="btn__2" id="log-submit">Login</button>
                  <div id="log-regist">
                    <a href="{{route('register')}}">
                      <button type="button" class="btn__1">Register</button>
                    </a>
                  </div>
                </div>

              </form>
            </div>
          </section>
        </div>



        <footer>
          <div class="container__1">
            <div id="footer-top">
              <div id="footer-top-left">
                @if (!empty($curr_logged->nickname))
                  <span class="f1_1"><a href="{{route('home').session('user_url__param')}}">Home</a></span>
                  <span class="f1_1"><a href="{{route('profile')}}">Profile</a></span>
                @else
                  <span class="f1_1"><a href="{{route('register')}}">Home</a></span>
                  <span class="f1_1"><a href="{{route('register').'/profile'}}">Profile</a></span>
                @endif


              </div>

              <div id="footer-top-right">
                <span class="f1_1"><a href="#">Facebook</a></span>
              </div>
            </div>

            <div id="footer-down">
              <p class="f2_2">Copyright ElementCoin© 2018 ElementCoin.com.  All rights reserved</p>
              <div id="footer-down-right">
                <span class="f1_3"><a href="#">Privacy Policy</a></span>
                <span class="f1_3"><a href="#">Terms of Use</a></span>
              </div>
            </div>
          </div>
        </footer>
    </div>

    @include('partials._scripts')
    <script type="text/javascript" src="{{ asset('js/chart.min.js') }}"></script>
  </body>
</html>
