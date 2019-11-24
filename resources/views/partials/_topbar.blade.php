@if (empty($act_nav))
  @php
    $act_nav = '';
  @endphp
@endif

<div class="modal-alert modal-window" modal-id="errAlert">
  <div class="modal-inner">
    <div class="modal-mid container__5" modal-action="errAlert">
      <h3 class="f1_h0 modal-mainMsg">Sorry, something went Wrong<br>Please Try Later</h3>
    </div>
  </div>
  <div class="modal-bck"></div>
</div>

<div @if(!empty($no_shadow)) data-shadow="hide" @endif class="topbar @if(!empty($start_hide)) topbar__hidden @endif @if(!empty($no_shadow)) topbar-no_shadow @endif">
  <div class="container__1 topbar-inner">
    <div class="topbar-brand-cont">
      <div class="topbar-brand">
        <a href="{{route('home').session('user_url__param')}}" class="topbar-brand-inner">
          <div class="topbar-logo">

          </div>
          <p class="topbar-logo__text">Pump.gg</p>
        </a>
      </div>
    </div>


    <div class="topbar-rightcont">
      <nav role="navigation">
        <div class="topbar-nav-icon" nav-action="tpbar-nav">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
          </svg>
        </div>
        <ul class="topbar-nav" nav-id="tpbar-nav">
          <li @if ($act_nav == 'home')class="nav__active"@endif><a href="{{route('home').session('user_url__param')}}" class="topbar-nav-li f1_5">Home</a></li>
          <li @if ($act_nav == 'game1')class="nav__active"@endif><a href="{{route('game1')}}" class="topbar-nav-li f1_5">Game</a></li>
          <li><a href="{{route('profile')}}#deposit" modal-action="depModa0" class="topbar-nav-li f1_5">Deposit</a></li>
          <li><a href="{{route('profile')}}#withdraw" modal-action="withModa0" class="topbar-nav-li f1_5">Withdraw</a></li>
        </ul>
      </nav>

      <div class="topbar-usersect">
        <div class="topbar-usersect-inner">
          <div class="usersect">
            <a href="{{route('profile')}}">
              <span class="f1_1" title="Balance" id="topbar-usrBalance"><span class="usr_balance">{{session('balance', "0") + 0}}</span>{{' '.config('app.main_currency')}}</span>
              <span id="topbar-user" title="User Name" class="user-nick text-over_elip-l1 f1_anach_0 nickname-text">{{session('nickname', "User Nickname")}}</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
