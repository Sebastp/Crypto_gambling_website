function minTwoDigits(n) {
  return (n < 10 ? '0' : '') + n;
}

function floorFigure(figure, decimals){
    if (!decimals) decimals = 2;
    var d = Math.pow(10,decimals);
    return (parseInt(figure*d)/d).toFixed(decimals);
};

$('.game-topBetAlert-close').click(function() {
  $(findParentBySelector($(this)[0], '.game-topBetAlert')).hide();
});

window.getChartInfo = function(chartId){
  var roundLinesArr = $('.chart-a__y-rounds')[0].querySelectorAll('[round-stop=true]'),
      linesContX = getTransfParams($('.chart-a__y-rounds g').attr('transform'))[0],
      lastRoundLine = roundLinesArr[roundLinesArr.length-1],
      roundInfo = 'next_round';

  if (typeof lastRoundLine != 'undefined') {
    if (parseInt(lastRoundLine.getAttribute('x1')) > (linesContX*-1)+8000) {
      roundInfo = null;
    }
  }



  $.ajax({
    method: 'POST',
    url: 'predict/info',
    dataType: 'json',
    data: {
      _token: csrf_token,
      getRound: roundInfo,
      last_price: $('#'+chartId).find('[chart-content]').attr('chart-cont-before')
    }
  })
  .done(function(r) {
    updateChart($('#'+chartId), r);

    if (typeof r.rounds != 'undefined') {
      var dateObj = new Date(),
          todaysMins = dateObj.getMinutes(),
          todaysSecs = dateObj.getSeconds();
      var month = dateObj.getMonth() + 1,
          day = dateObj.getDate(),
          year = dateObj.getFullYear(),
          todayDays = year + "/" + month + "/" + day,
          roundDateObj = new Date(todayDays+' '+r.rounds[0].round_start),
          todaysDateObj = new Date(todayDays+' '+r.time),
          todayDate2sec = todaysDateObj.getMinutes()*60+todaysDateObj.getSeconds(),
          roundDate2sec = roundDateObj.getMinutes()*60+roundDateObj.getSeconds(),
          diffSecs = roundDate2sec - todayDate2sec,
          newMins = Math.floor(diffSecs/60),
          newSecs = Math.floor(diffSecs%60);

      if (newMins < 0 || newSecs == 30 || newSecs < 0) {
        var diffFormat = '00:00';
      }else {
        var diffFormat = minTwoDigits(newMins)+':'+minTwoDigits(newSecs);
      }


      $('#bet-counter').text(diffFormat);
      if ($('#bet-counter').text() == '--:--') {
        setInterval(function(){
          updateRoundDownCounter();
        }, 1000);
      }
    }

    if (typeof r.bet_ratio != 'undefined') {
      updateRatio(r.bet_ratio, r.beting_users);
      if (typeof r.beting_users != 'undefined' && r.beting_users != 0) {
        updateFinalProfit();
      }
    }

    if (typeof r.lastRatio != 'undefined') {
      updateTimesRatio(true, r.lastRatio);
    }


    if (typeof r.beting_users != 'undefined' && r.beting_users == 0) {
      $('#bet-timesRatio').text('?');
    }


    if (typeof r.won_lastRound != 'undefined') {
      $('#bet-won__lstR').text(r.won_lastRound);
    }

    if (typeof r.active_users != 'undefined') {
      $('.active-users').text(r.active_users);
    }
  })
  .fail(function() {
    console.log("error");
  });
}

window.errorTimer;
var noErrorTimer, noNewData;
function chartGetNewX(chartElem, newTime, dataType){
  var nedd;
  nedd = new Date('2018-12-12 '+ newTime);
  nedd.setSeconds(nedd.getSeconds() - nedd.getSeconds() % 10);
  neededTime = minTwoDigits(nedd.getHours())+':'+minTwoDigits(nedd.getMinutes())+':'+minTwoDigits(nedd.getSeconds()),
  latestChartX = parseInt(chartElem[0].querySelector('[chart-last-x]').getAttribute('chart-last-x'));

  var chartXsize = chartElem.attr('data-chart-size-x');

  var firstTimeSpan = $('.chart-axis__y-labels')[0].children[1];
  newTimeSpan = $('.chart-axis__y-labels')[0].querySelector('span[data-time="'+neededTime+'"]');

  chartAddXaxis(chartElem);
  if (typeof newTimeSpan == 'undefined' || newTimeSpan == null && dataType == 'line') {
    console.log('Error: Could not get a time');
    if (typeof errorTimer == 'undefined') {
      errorTimer = setTimeout(function(){
        showErrorChart();
      }, 10000);
    }
    noErrorTimer = 0;
    return null;
  }else if(dataType == 'line'){
    noErrorTimer++;
    if (noErrorTimer > 1 && noNewData == 0) {
      $('.chart-inner-error').css('opacity', '0');
      if (typeof $('#bet-left-play')[0] != 'undefined') {
        $('#bet-left-play')[0].removeAttribute('disabled-error');
      }
      if (typeof errorTimer != 'undefined') {
        clearTimeout(errorTimer);
        delete window.errorTimer;
      }
    }
  }

  var newXRoundPos = chartXsize*(parseInt(firstTimeSpan.style.left) - parseInt(newTimeSpan.style.left))/chartXsize*-1;
  newXPos = newXRoundPos*100;

  var newd;
  newd = new Date('2018-12-12 '+ newTime);

  secsDiff = (newd.getSeconds() - nedd.getSeconds()) % chartXsize;
  notRoundAdd = 1000/chartXsize * secsDiff;
  finalX = newXPos+notRoundAdd;

  if (finalX == parseInt(chartElem[0].querySelector('[chart-content]').getAttribute('chart-last-x')) &&
      dataType == 'line') {
    noErrorTimer = 0;
    noNewData++;
    if (noNewData > 5 && typeof errorTimer == 'undefined') {
      errorTimer = setTimeout(function(){
        showErrorChart();
      }, 10000);
    }
    return null;
  }else if (dataType == 'line'){
    noNewData = 0;
  }


  if (dataType == 'line' && finalX < latestChartX){
    return latestChartX;
  }
  return finalX;
}



function chartGetNewY(chartElem, newPrice){
  var yPosForOneDolar = 1000*(1/chartElem.attr('data-chart-size-y')),
      Y0val = parseFloat(chartElem[0].querySelector('[data-axis_y0]').getAttribute('data-axis_y0'));

  return Math.round((Y0val-newPrice)*yPosForOneDolar, 0);
}

//add round lines
function chartAddRound(chartElem, start, stop){
  var yLabelsCont = $(chartElem).find('.chart-axis__y-labels')[0],
      yLinesCont = $(chartElem).find('.chart-a__y-lines')[0],
      RoundLinesCont = $(chartElem).find('.chart-a__y-rounds')[0],
      RoundLinesArr = RoundLinesCont.querySelectorAll('line'),
      yLinesArr = yLinesCont.querySelectorAll('line'),
      chartYsize = parseInt(chartElem.attr('data-chart-size-x'));





  startLineX = chartGetNewX(chartElem, start);
  stopLineX = chartGetNewX(chartElem, stop);

  var existing1 = RoundLinesCont.querySelector('line[x1="'+startLineX+'"]');
  var existing2 = RoundLinesCont.querySelector('line[x1="'+stopLineX+'"]');

  if (existing1 != null || existing2 != null) {
    return;
  }

  lineNodeStart = $(yLinesArr[0]).clone();
  lineNodeStop = $(yLinesArr[0]).clone();
  lineNodeStart.attr(
  {
    "x1": startLineX,
    "x2": startLineX,
    "round-start": start,
    "transform": ''
  });

  lineNodeStop.attr(
  {
    "x1": stopLineX,
    "x2": stopLineX,
    "round-stop": stop,
    "transform": ''
  });

  $('.chart-a__y-rounds').find('g')[0].append(lineNodeStart[0]);
  $('.chart-a__y-rounds').find('g')[0].append(lineNodeStop[0]);

  if (RoundLinesArr.length>60) {
    for (var i = 0; i < RoundLinesArr.length-40; i++) {
      RoundLinesArr[i].parentNode.removeChild(RoundLinesArr[i])
    }
  }
}



function updateChart(chartElem, data){
  var newPrice = data.price,
      newPathY = chartGetNewY(chartElem, newPrice),
      newTime = data.time,
      areaElem = chartElem[0].querySelector('[chart-area]'),
      strokeElem = chartElem[0].querySelector('[chart-stroke]');


  if (typeof data.rounds != 'undefined') {
    for (var dr = 0; dr < data.rounds.length; dr++) {
      chartAddRound(chartElem, data.rounds[dr].round_start, data.rounds[dr].round_stop);
    }
  }



  strokePath = strokeElem.getAttribute('d');
  if (strokePath == null) {
    pathChoped = [];
    newPathX = 0;
  }else{
    pathChoped = Array.from(strokePath.split("L"));
    if (pathChoped.length == 1) {
      newPathX = 0;
    }else {
      newPathX = chartGetNewX(chartElem, newTime, 'line');
      deletePastRounds(newPathX);
    }
  }

  if (newPathX == null || newPrice == 0) {
    return;
  }

  areaPathFirstPart = 'M0,10000000L';
  areaPathLastPart = 'L'+newPathX+',10000000';


  newPathPart = String(newPathX+','+newPathY);

  pathChoped.push(newPathPart);
  if (pathChoped.length > 250) {
    newPathChoped = pathChoped.splice(1, 100);
  }
  newStrokePath = pathChoped.join('L');

  if (pathChoped.length == 1) {
    newStrokePath = 'M'+newStrokePath;
    newAreaPath = areaPathFirstPart+areaPathLastPart.slice(1);
  }else {
    newAreaPath = areaPathFirstPart+newStrokePath.slice(1)+areaPathLastPart;
  }

  strokeElem.setAttribute('d', newStrokePath);
  areaElem.setAttribute('d', newAreaPath);
  chartElem.find('[chart-content]').attr('chart-cont-before', newPrice);
  chartElem.find('[chart-content]').attr('chart-last-x', newPathX);

  var betLockBar = parseInt($('#gameLine-currPrice').attr('bet-lockbar'));

  if (betLockBar != null && betLockBar < newPathX && betLockBar+2000 > newPathX) {
    /*var betBarY = $('#gameLine-currPrice').attr('y1');
    $('#gameLine-currPrice').attr(
    {
      "y1": betBarY,
      "y2": betBarY
    });*/

  }else {
    $('#gameLine-currPrice').attr('y1', newPathY).attr('y2', newPathY);
    if (betLockBar != null && betLockBar+2000 < newPathX) {
      endBet(newTime);
    }

    if (betLockBar == null || (betLockBar != null && betLockBar > newPathX)) {
      updateTimesRatio(true);
      updateFinalProfit(true);
    }else {
      updateFinalProfit();
    }
  }



  $('.chart_price').text('$'+newPrice.toFixed(2));
  // $('#bet-counter').text(newTime);

  positionChartView(newPathX, newPathY, chartElem[0]);
}




chartAddXaxis($('#game_chart1'));
function chartAddXaxis(chartElem){
  labelsCont = $(chartElem).find('.chart-axis__y-labels');
  LabelsArr = $(chartElem).find('.chart-axis__y-labels')[0].querySelectorAll('span');
  lastLabel = $(chartElem).find('.chart-axis__y-labels').find('span').last();

  linesCont = $(chartElem).find('.chart-a__y-lines g');
  linesArr = linesCont.find('line');
  lastLine = linesArr.last();
  latestX = parseInt(lastLine[0].getAttribute('x1'));



  if (linesArr.length > 50) {
    var latestChart = parseInt(document.querySelector('[chart-last-x]').getAttribute('chart-last-x'))
    for (var i = 0; i < 20; i++) {
      var lineX = parseInt(linesArr[i].getAttribute('x1'));
      if (latestChart-lineX > 10000) {
        // LabelsArr[i].parentNode.removeChild(LabelsArr[i])
        linesArr[i].parentNode.removeChild(linesArr[i])
      }
    }
  }

  if (linesArr.length > 100) {
    return;
  }


  lastTime = lastLabel[0].getAttribute('data-time');

  latestLEFT = parseInt(lastLabel[0].style.left);
  for (var i = 0; i < 20; i++) {
    latestX += 1000;
    latestLEFT += 10;

    var d, t;
    d = new Date('2018-12-12 '+ lastTime);
    t = new Date('2018-12-12 '+ lastTime);
    d.setSeconds(d.getSeconds() + 10);
    t.setMinutes(d.getMinutes() + (d.getTimezoneOffset()*-1));
    t.setSeconds(t.getSeconds() + 10);
    lastTime = minTwoDigits(d.getHours())+':'+minTwoDigits(d.getMinutes())+':'+minTwoDigits(d.getSeconds());
    lastTimeTIMEZONE = minTwoDigits(t.getHours())+':'+minTwoDigits(t.getMinutes())+':'+minTwoDigits(t.getSeconds());

    lineNode = lastLine.clone();
    lineNode[0].setAttribute('x1', latestX);
    lineNode[0].setAttribute('x2', latestX);
    linesCont.append(lineNode);

    labelNode = lastLabel.clone();

    labelNode[0].style.left = latestLEFT+'%';
    labelNode[0].innerText = lastTimeTIMEZONE;
    labelNode[0].setAttribute('data-time', lastTime);
    labelsCont.append(labelNode);
  }
}



function chartAddYaxis(chartElem, direction){
  labelsCont = chartElem.getElementsByClassName('chart-axis__x-labels');
  LabelsArr = chartElem.getElementsByClassName('chart-axis__x-labels')[0].querySelectorAll('span');
  linesCont = chartElem.getElementsByClassName('chart-axis__x')[0].querySelector('g');
  linesArr = linesCont.querySelectorAll('line');

  priceDiff = parseFloat(chartElem.getAttribute('data-chart-size-y'));

  if (direction == 'top') {
    firstLabel = LabelsArr[0];

    firstLine = linesArr[0];
    latestY = parseInt(firstLine.getAttribute('y1'));

    lastPrice = parseFloat(firstLabel.innerText.slice(1));
    latestTOP = parseInt(firstLabel.style.top);
    for (var i = 0; i < 10; i++) {
      latestY -= 1000;
      latestTOP -= 10;
      lastPrice += priceDiff;


      lineNode = $(firstLine).clone();
      lineNode[0].setAttribute('y1', latestY);
      lineNode[0].setAttribute('y2', latestY);
      $(linesCont).prepend(lineNode);

      labelNode = $(firstLabel).clone();
      labelNode[0].style.top = latestTOP+'%';
      labelNode[0].innerText = '$'+lastPrice.toFixed(2);
      $(labelsCont).prepend(labelNode);
    }

  }else {
    lastLabel = LabelsArr[LabelsArr.length -1];

    lastLine = linesArr[linesArr.length-1];
    latestY = parseInt(lastLine.getAttribute('y1'));

    lastPrice = parseFloat(lastLabel.innerText.slice(1));
    latestTOP = parseInt(lastLabel.style.top);
    for (var i = 0; i < 10; i++) {
      latestY += 1000;
      latestTOP += 10;
      lastPrice -= priceDiff;


      lineNode = $(lastLine).clone();
      lineNode[0].setAttribute('y1', latestY);
      lineNode[0].setAttribute('y2', latestY);
      $(linesCont).append(lineNode);


      labelNode = $(lastLabel).clone();
      labelNode[0].style.top = latestTOP+'%';
      labelNode[0].innerText = '$'+lastPrice.toFixed(2);
      $(labelsCont).append(labelNode);
    }
  }
}




function positionChartView(newX, newY, chartElem){
  var runToLeft = 70;
  var runToTopBottom = 30,
      chartMoveAt = 8000;
  var chartWidth = parseInt($('.chart').width());

  chartGElem = chartElem.querySelector('[chart-content]');
  TransformArr = getTransfParams(chartGElem.getAttribute('transform'));
  transformX = TransformArr[0];
  transformY = TransformArr[1];

  if (chartWidth < 600 && parseInt($('.chart-right').width()) > 600) {
    if (chartWidth > 640) {
      chartMoveAt = 9000;
      runToLeft = 80;
    }

    if (chartWidth < 640) {
      chartMoveAt = 8000;
      runToLeft = 70;
    }

    if (chartWidth < 570) {
      chartMoveAt = 7000;
      runToLeft = 60;
    }

    if (chartWidth < 500) {
      chartMoveAt = 6000;
      runToLeft = 50;
    }

    if (chartWidth < 430) {
      chartMoveAt = 5000;
      runToLeft = 50;
    }

    if (chartWidth < 360) {
      chartMoveAt = 4000;
      runToLeft = 40;
    }
  }


//left
  if (newX > (transformX*-1)+chartMoveAt) {
    transformX = transformX-runToLeft * 100;
    yLabelsArr = $(chartElem).find('.chart-axis__y-labels')[0].querySelectorAll('span');
    yLabelLinesArr = $(chartElem).find('.chart-a__y-lines line');

    lastLine = yLabelLinesArr[yLabelLinesArr.length-1];
    lastLineX = parseInt(lastLine.getAttribute('x1'));

    yLinesXtransf = getTransfParams(chartElem.getElementsByClassName('chart-a__y-lines')[0].querySelector('g').getAttribute('transform'))[0];


    $(chartElem).find('.chart-a__y-lines g').attr('transform', 'translate('+String(yLinesXtransf-runToLeft * 100)+', 0)');



    if (lastLineX+yLinesXtransf < 40000 ) {
      chartAddXaxis(chartElem);
      yLabelsArr = $(chartElem).find('.chart-axis__y-labels')[0].querySelectorAll('span');
      yLabelLinesArr = $(chartElem).find('.chart-a__y-lines line');
    }



    for (var i = 0; i < yLabelsArr.length; i++) {
      labelLeft = parseInt(yLabelsArr[i].style.left);
      yLabelsArr[i].style.left = labelLeft-runToLeft+'%';
    }
  }



  xLabelsArr = chartElem.getElementsByClassName('chart-axis__x-labels')[0].querySelectorAll('span');
  xLabelLinesArr = chartElem.getElementsByClassName('chart-axis__x')[0].querySelectorAll('line');

  //top
  if (newY < (transformY*-1)+1000) {
    transformY = transformY+runToTopBottom * 100;
    firstXLine = xLabelLinesArr[0];
    firstXLineY = parseInt(firstXLine.getAttribute('y1'));

    firstXLineXtransf = parseInt(Array.from(firstXLine.getAttribute('transform').replace('translate(', '').slice(0, -1).split(", "))[0]);


    for (var l1 = 0; l1 < xLabelLinesArr.length; l1++) {
      lineTransform = getTransfParams(xLabelLinesArr[l1].getAttribute('transform'));
      lineTransformX = parseInt(lineTransform[0]);
      lineTransformY = parseInt(lineTransform[1]);
      xLabelLinesArr[l1].setAttribute('transform', 'translate('+lineTransformX+', '+String(lineTransformY+runToTopBottom * 100)+')');
    }

    for (var i = 0; i < xLabelsArr.length; i++) {
      labelTop = parseInt(xLabelsArr[i].style.top);
      xLabelsArr[i].style.top = labelTop+runToTopBottom+'%';
    }


    if (firstXLineY+firstXLineXtransf < 1000 ) {
      chartAddYaxis(chartElem, 'top');
    }
  }


  //bottom
  if (newY-3000 > (transformY*-1)+2000) {
    transformY = transformY-runToTopBottom * 100;
    firstXLine = xLabelLinesArr[0];
    firstXLineY = parseInt(firstXLine.getAttribute('y1'));
    firstXLineXtransf = getTransfParams(firstXLine.getAttribute('transform'))[0];


    for (var l2 = 0; l2 < xLabelLinesArr.length; l2++) {
      lineTransform = getTransfParams(xLabelLinesArr[l2].getAttribute('transform'));
      lineTransformX = parseInt(lineTransform[0]);
      lineTransformY = parseInt(lineTransform[1]);
      xLabelLinesArr[l2].setAttribute('transform', 'translate('+lineTransformX+', '+String(lineTransformY-runToTopBottom * 100)+')');
    }

    for (var i = 0; i < xLabelsArr.length; i++) {
      labelTop = parseInt(xLabelsArr[i].style.top);
      xLabelsArr[i].style.top = labelTop-runToTopBottom+'%';
    }


    if (firstXLineY+firstXLineXtransf < 1000 ) {
      chartAddYaxis(chartElem, 'bottom');
    }
  }


  $('#gameLine-currPrice').attr('transform', 'translate('+transformX*-1+', 0)');
  $(chartGElem).attr('transform', 'translate('+transformX+', '+transformY+')');
  $(chartElem).find('.chart-a__y-rounds g').attr('transform', 'translate('+transformX+', 0)');
}



function deletePastRounds(currX){
  var roundLinesArr = $('.chart-a__y-rounds')[0].querySelectorAll('line');

  for (var i = 0; i < roundLinesArr.length; i++) {
    var roundPosX = roundLinesArr[i].getAttribute('x1');
    if (currX-roundPosX > 0) {
      roundLinesArr[i].remove();
    }
  }
}


function updateRoundDownCounter(){
  var elContent = $('#bet-counter').text(),
      elSecs = parseInt(elContent.split(':')[1]);
  if (elSecs < 0) {
    elSecs = elSecs*-1;
  }

  if (elSecs-- == 0) {
    $('#bet-counter').text("00:29");
  }else {
    $('#bet-counter').text("00:"+minTwoDigits(elSecs--));
  }
}


function updateRatio(bet_ratio, betters){
  var upRatio = parseInt(bet_ratio),
      downRatio = 100-upRatio;

  $('#ratiobar-green').css('width', upRatio+'%');
  $('#ratio-up').text(upRatio+'%');
  $('#ratio-down').text(downRatio+'%');

  updateTimesRatio();
}



updateTimesRatio = function(updateDone, upRatio){
  var betType = $('#game-bet').attr('bet-type'),
      betFee = parseFloat($('#bet-timesRatio').attr('data-betFee'));
  if (betType == 'down') {
    if (typeof upRatio != 'undefined') {
      var winPercent = parseInt(upRatio);
    }else {
      var winPercent = parseInt($('#ratio-up').text());
    }
  }else {
    if (typeof upRatio != 'undefined') {
      var winPercent = 100-parseInt(upRatio);
    }else {
      var winPercent = parseInt($('#ratio-down').text());
    }
  }
  var loseRatio = 100-winPercent,
      winRatio = winPercent/loseRatio;

  var roundDecimals = parseFloat(floorFigure(1+(winRatio*(1-betFee)), 2)).toFixed(2);


  if (typeof upRatio == 'undefined') {
    $('#bet-timesRatio').text('x'+roundDecimals);
  }

  if (typeof updateDone != 'undefined' && !isNaN(roundDecimals)) {
    $('#bet-done-timesRatio').text('x'+roundDecimals);
  }
}


updateFinalProfit = function(updateDone){
  var currVal = parseFloat($('#bet-inpt').val()),
      multRatio = parseFloat($('#bet-timesRatio').text().substring(1)),
      finalProf = currVal*multRatio;

  var floatDigs = String(finalProf).split('.')[1];

  if (typeof floatDigs != 'undefined') {
    if (floatDigs.length > 4) {
      var finalProf = parseFloat(currVal).toFixed(4);
      if (finalProf < 0.0001 ) {
        finalProf = 0;
      }
    }
  }

  if ($('#bet-timesRatio').text() == '?') {
    $('#bet-profit__final').text('?');
  }else {
    $('#bet-profit__final').text(finalProf);
  }


  if (typeof updateDone != 'undefined') {
    $('#bet-done-profit__final').text(finalProf);
  }
}





placeBet = function(bet_amount, bet_type, csrf_token){
  $.ajax({
    method: 'POST',
    url: 'predict/bet',
    dataType: 'json',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: {
      _token: csrf_token,
      amount: bet_amount,
      type: bet_type
    }
  })
  .done(function(r) {
    if (r.success) {
      var betStart = r.bet_start,
          betX = chartGetNewX($('#game_chart1'), betStart),
          newBalance = r.curr_balance;
      $('#gameLine-currPrice')[0].setAttribute('bet-lockbar', betX);
      if (typeof newBalance != 'undefined') {
        $('.usr_balance').text(newBalance);
      }

      showDoneBetWindow();
    }else {
      showAlertModal('Sorry, we could not transfer your bet');
    }
  })
  .fail(function() {
    showAlertModal('Sorry, we could not transfer your bet');
  });
}

function showDoneBetWindow(){
  $('#bet-done-mount').text($('#bet-inpt').val());
  $('#bet-done-profit__final').text($('#bet-profit__final').text());
  $('#bet-done-timesRatio').text($('#bet-timesRatio').text());

  $('#bet-left-todo').css('display', 'none');
  $('#bet-left-done').css('display', 'inline-block');

}

function hideDoneBetWindow(){
  $('#bet-left-todo').css('display', 'inline-block');
  $('#bet-left-done').css('display', 'none');

}

function endBet(time){
  $.ajax({
    method: 'POST',
    url: 'predict/last_bet',
    dataType: 'json',
    data: {
      _token: csrf_token,
      curr_time: time
    }
  })
  .done(function(r) {
    if (r.success) {
      var newBalance = r.curr_balance,
          betResult = r.bet_result,
          betStart = r.bet_start;

      if (betStart == $('.game-topBetAlert').attr('lastBet')) {
        return;
      }

      if (betResult == 'win') {
        $('.topBetAlert-mainMsg').text('Congratulations');
        $('.topBetAlert-subMsg').text("You won");
        $('.game-betAmount').text(r.bet_return);
        $('.game-topBetAlert').slideDown(150);
        setTimeout(function () {
          $('.game-topBetAlert').slideUp(150);
        }, 5000);
      }

      if (betResult == 'lose') {
        $('.topBetAlert-mainMsg').text('Maybe Next Time');
        $('.topBetAlert-subMsg').text("You lost");
        $('.game-betAmount').text(r.bet_return);
        $('.game-topBetAlert').slideDown(150);
        setTimeout(function () {
          $('.game-topBetAlert').slideUp(150);
        }, 5000);
      }

      if (betResult == 'draw') {
        $('.topBetAlert-mainMsg').text('Draw...');
        $('.topBetAlert-subMsg').text('You just won');
        $('.game-betAmount').text(r.bet_return);
        $('.game-topBetAlert').slideDown(150);
        setTimeout(function () {
          $('.game-topBetAlert').slideUp(150);
        }, 5000);
      }

      if (typeof newBalance != 'undefined') {
        $('.usr_balance').text(newBalance);
      }

      $('.game-topBetAlert').attr('lastBet', betStart);

      hideDoneBetWindow();
    }else {
      showAlertModal('Sorry, we could not transfer your request');
      hideDoneBetWindow();
    }
    $('#gameLine-currPrice')[0].removeAttribute('bet-lockbar');
  })
  .fail(function() {
    showAlertModal('Sorry, we could not transfer your request');
  });
}


function showErrorChart(){
  if (noErrorTimer < 10) {
    $('.chart-inner-error').css('opacity', '1');
    if (typeof $('#bet-left-play')[0] != 'undefined') {
      $('#bet-left-play')[0].setAttribute('disabled-error', true);
    }
  }
};

$('#error-reload').click(function() {
  location.reload();
});
