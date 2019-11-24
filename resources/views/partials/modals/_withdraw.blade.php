<div class="modal-window modal-balanceAction" id="modal-withdraw" modal-id="{{$modalId}}">
  <div class="modal-inner">
    <div class="modal-top">
      <div class="container__5">
        <h3 class="f1_h0">Withdraw Monero</h3>
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
        <p class="f2_5 balanceAction-label">Amount</p>

        <div class="inpt-undrline-cont">
          <input undrline-onfcs spellcheck="false" min="0" type="number" class="neutralize-input f1_6 withdraw-inpt" type="text" name="withdraw-amount" id="withdraw-amount" placeholder="Withdraw Amount" value="0">
          <div class="inpt-undrline">
            <div class="inpt-line__fcsed inpt-line-positiv"></div>
            <div class="inpt-line__unfcsed"></div>
          </div>
        </div>
      </div>

      <div class="modal-balanceAction-address">
        <p class="f2_5 balanceAction-label">Your Personal Withdraw Address</p>

        <div class="inpt-undrline-cont">
          <input undrline-onfcs spellcheck="false" class="neutralize-input f1_6 withdraw-inpt" type="text" name="withdraw-add" id="withdraw-address" placeholder="Withdraw Address">
          <div class="inpt-undrline">
            <div class="inpt-line__fcsed inpt-line-positiv"></div>
            <div class="inpt-line__unfcsed"></div>
          </div>
        </div>
      </div>

      <div class="modal-balanceAction-address">
        <p class="f2_5 balanceAction-label">Payment Id</p>

        <div class="inpt-undrline-cont">
          <input undrline-onfcs spellcheck="false" class="neutralize-input f1_6 withdraw-inpt" type="text" name="withdraw-payment_id" id="withdraw-payment_id" placeholder="Payment Id">
          <div class="inpt-undrline">
            <div class="inpt-line__fcsed inpt-line-positiv"></div>
            <div class="inpt-line__unfcsed"></div>
          </div>
        </div>
      </div>

      <button type="button" class="btn__0" id="withdraw-submit">Withdraw {{config('app.main_currency')}}</button>

      <p class="f2_4">
        Withdraw will arrive with deducted 0.05 XMR fee<br>
        Minimum withdraw is 0.06 XMR<br>
        Withdraw are credited within 4 confirmation depending on risk. Most withdraws are credited within
        2 minutes.</p>
    </div>

  </div>
  <div class="modal-bck"></div>
</div>
