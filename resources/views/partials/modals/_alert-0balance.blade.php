<div class="modal-window modal-balanceAction" id="modal-depositAlert" modal-id="{{$modalId}}">
  <div class="modal-inner">
    <div class="modal-mid container__5">
      <h3 class="f1_h0 modal-mainMsg">You Don't have enough Funds</h3>
      <div class="modal-balance">
        <p class="f1_1" id="modal-usrBalance">
          <span class="f1_2">Your Balance</span>
          <span class="usr_balance">{{session('balance', "0")}}</span>{{' '.config('app.main_currency')}}
        </p>
      </div>

      <a href="{{route('profile')}}#deposit">
        <button type="button" class="btn__0">Deposit Now</button>
      </a>
    </div>
  </div>
  <div class="modal-bck"></div>
</div>
