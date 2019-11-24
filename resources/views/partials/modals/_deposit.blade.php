<div class="modal-window modal-balanceAction" id="modal-deposit" modal-id="{{$modalId}}">
  <div class="modal-inner">
    <div class="modal-top">
      <div class="container__5">
        <h3 class="f1_h0">Deposit Monero</h3>
      </div>
    </div>

    <div class="modal-mid container__5">
      <div class="modal-balance">
        <p class="f1_1" id="modal-usrBalance">
          <span class="f1_2">Balance</span>
          {{session('balance', "0").' '.config('app.main_currency')}}
        </p>
      </div>

      <div class="modal-balanceAction-address">
        <p class="f2_5 balanceAction-label">Your Personal Deposit Address</p>

        <div class="balanceAction-address-cont">
          <input spellcheck="false" class="neutralize-input f1_6 text-over_elip-l1 inpt-select" id="deposit-address" readonly="readonly" type="text" value="{{$deposit_addr}}">
          <button type="button" class="btn__2 modal-copybtn" copy-btn="deposit-address">Copy to Clipboard</button>
        </div>
      </div>

      <div class="modal-balanceAction-address">
        <p class="f2_5 balanceAction-label">Payment Id</p>

        <div class="balanceAction-address-cont">
          <input spellcheck="false" class="neutralize-input f1_6 text-over_elip-l1 inpt-select" id="deposit-paymentid" readonly="readonly" type="text" value="{{$deposit_pId}}">
          <button type="button" class="btn__2 modal-copybtn" copy-btn="deposit-paymentid">Copy to Clipboard</button>
        </div>
      </div>

      @if (!session('has_password', 0))
        <div class="balanceAction-authReminder">
          <p class="f1_2">It is highly required for you to set the password before deposition</p>
          <span class="f1_anach_0"><a href="{{route('profile')}}">Set Password</a></span>
        </div>
      @endif

      <p class="f2_4">Deposits are credited within 1 confirmation depending on risk. Most deposits are credited within
        30 seconds.</p>
    </div>

  </div>
  <div class="modal-bck"></div>
</div>
