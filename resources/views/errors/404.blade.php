<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
  <head>
    @include('partials._head')
  </head>
  <body>
    @include('partials._topbar', ['no_shadow' => true])


    <div id="app">
      <div class="wrapper padding-topbar error-page">
        <div class="container__1 error-inner">
          <div class="error-top">
            <img src="{{asset('img/404.png')}}">
            <p class="f1_h2" id="error-subtitle">Page not found</p>
          </div>
          <span class="f1_6__pos" id="error-cta"><a href="{{route('home').session('user_url__param')}}">Go Back Home</a></span>
        </div>
      </div>

      @include('partials._footer')
    </div>
    @include('partials._scripts')
  </body>
</html>
