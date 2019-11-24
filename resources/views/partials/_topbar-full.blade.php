<div class="topbar-full">
  <div class="topbar-brand-cont">
    <div class="topbar-brand">
      <a href="{{'/?user='.session('user_url', '/')}}" class="topbar-brand-inner">
        <div class="topbar-logo">

        </div>
        <p class="topbar-logo__text">Pump.gg</p>
      </a>
    </div>
  </div>


  <div class="topbar-rightcont">
    <nav role="navigation">
      <div class="topbar-nav-icon" nav-action="tpbarfull-nav">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="3" y1="12" x2="21" y2="12"></line>
          <line x1="3" y1="6" x2="21" y2="6"></line>
          <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
      </div>
      <ul class="topbar-nav" nav-id="tpbarfull-nav">
        <li class="nav__active"><a href="{{'/?user='.session('user_url', '')}}" class="topbar-nav-li f1_5">Home</a></li>
        <li><a href="{{route('game1')}}" class="topbar-nav-li f1_5">Game</a></li>
        <li><a href="{{route('profile')}}" class="topbar-nav-li f1_5">Deposit</a></li>
        <li><a href="{{route('profile')}}" class="topbar-nav-li f1_5">Withdraw</a></li>
      </ul>
    </nav>

  </div>
</div>
