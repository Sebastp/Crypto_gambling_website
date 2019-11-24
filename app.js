//this is node.js implementation
Array.prototype.sum = Array.prototype.sum || function (){
  return this.reduce(function(p,c){return p+c},0);
};

Array.prototype.avg = Array.prototype.avg || function () {
  return this.sum()/this.length;
};

function isEmpty(obj) {
    for(var prop in obj) {
        if(obj.hasOwnProperty(prop))
            return false;
    }

    return JSON.stringify(obj) === JSON.stringify({});
}

function getRandomFromArr(arr, n) {
    var result = new Array(n),
        len = arr.length,
        taken = new Array(len);
    if (n > len)
        throw new RangeError("getRandom: more elements taken than available");
    while (n--) {
        var x = Math.floor(Math.random() * len);
        result[n] = arr[x in taken ? taken[x] : x];
        taken[x] = --len;
    }
    return result;
}


//if the data varies a lot from the last one eg. if second ago btc was 8k and
// now is 800 cos of Exchanges error

function priceInRangeOfPercent(priceFrst, priceScnd, percent){
  if (typeof priceFrst == 'undefined') {
    return true;
  }


  var diffPercentIn = (priceFrst-priceScnd)/(priceFrst+priceScnd)*100;
  if (diffPercentIn <= percent*-1 || diffPercentIn >= percent) {
    console.log('difff ' + diffPercentIn);
    console.log('priceFrst: '+priceFrst);
    console.log('priceScnd: '+priceScnd);
    return false;
  }else {
    return true;
  }
  // return 100-scndPercentIn <= percent;
}

function lavrageTicks(priceFrst, priceScnd){
  var randInt = Math.floor(Math.random() * 20)+1,
      priceDiff = (priceFrst-priceScnd).toFixed(2),
      xpercentOfTick = priceDiff*(randInt/100),
      finalPrice= [priceFrst, priceFrst+xpercentOfTick].avg();
  return finalPrice;
}


function apisInit(){
  for (var i = 0; i < echamgesAvlible.length; i++) {
    cryptoSocket.start(echamgesAvlible[i], currToGet);
  }
}


var http = require('http'),
    request = require('request'),
    mysql = require('mysql'),
    cryptoSocket = require("crypto-socket");


var con = mysql.createConnection({
  host: "127.0.0.1",
  user: "root",
  // password: "tBkNeNtZ6v1TU9FS",
  // database: "cryptoG"
  password: "",
  database: "cryptog"
});



con.connect(function(err) {
  if (err) throw err;
  console.log("Connected!");
});


//crypto-socket may crash some times so add
// if (response != null) {
// in node_modules/crypto-socket/index.js at 209:0
// before var responseObj = response.result

// ['BTCUSD', 'ETHUSD', 'XMRUSD']
var currToGet = ['BTCUSD'],
    echamgesAvlible = ['bitfinex', 'bittrex', 'bitstamp', 'poloniex', 'gdax',
                    'gemini', 'cex', 'okcoin', 'bitmex'];


apisInit();

echamgesToUse = echamgesAvlible;
var pricesAvg = [],
    echamgesData = [],
    sameDataTrack = 0,
    iterTrack = 0,
    pastRoundAvg = 0,
    errorMarginPercent = 1;
var mainInterval = setInterval(function(){
  var utcDate = new Date().toISOString().replace(/T/, ' ').replace(/\..+/, ''),
  prices = [];

  for (var b = 0; b < currToGet.length; b++) {
    prices[b] = [];
    for (var i = 0; i < echamgesAvlible.length; i++) {
        prices[b].push(0);
    }
  }
  // echamgesToUse = getRandomFromArr(echamgesAvlible, 8);

  for (var e = 0; e < echamgesToUse.length; e++) {
    exData = cryptoSocket.Exchanges[echamgesToUse[e]];

    if (typeof echamgesData[e] == 'undefined') {
      echamgesData[e] = [];
    }

    if (isEmpty(exData) && typeof echamgesData[e] !== 'undefined' && !isEmpty(echamgesData[e])) {
      exData = echamgesData[e];
    }

    // console.log(exData);
    for(var propt in exData) {
      switch (propt) {
        case currToGet[0]:
          if(priceInRangeOfPercent(echamgesData[e][propt], exData[propt], 5) && priceInRangeOfPercent(pricesAvg[0], exData[propt], errorMarginPercent)){
            prices[0][e] = exData[propt];
          }else {
            // prices[0][e] = lavrageTicks(pricesAvg[0], exData[propt]);
          }
          break;
        case currToGet[1]:
          if(priceInRangeOfPercent(echamgesData[e][propt], exData[propt], 5) && priceInRangeOfPercent(pricesAvg[1], exData[propt], errorMarginPercent)){
            prices[1][e] = exData[propt];
          }else {
            // prices[1][e] = lavrageTicks(pricesAvg[1], exData[propt]);
          }
          break;
        case currToGet[2]:
          if(priceInRangeOfPercent(echamgesData[e][propt], exData[propt], 5) && priceInRangeOfPercent(pricesAvg[2], exData[propt], errorMarginPercent)){
            prices[2][e] = exData[propt];
          }else {
            // prices[2][e] = lavrageTicks(pricesAvg[2], exData[propt]);
          }
          break;
      }

    }

    echamgesData[e] = exData;
  }


  for (var m = 0; m < prices.length; m++) {
    if (prices[m].length > 0) {
      console.log(prices[m].filter(Number));
      pricesAvg[m] = prices[m].filter(Number).avg();
    }else {
      console.log('EMPTY:  '+m);
    }
  }

  var avgCurr = pricesAvg.avg();

  if (typeof pastAvg == 'undefined') {
    pastAvg = avgCurr;
  }

  var avgWhole = [avgCurr, pastAvg].avg(),
      pastAvg = avgWhole,
      avgRound = Math.round(avgWhole * 100) / 100;

  if (typeof avgRound == 'number' && !isNaN(avgRound)) {
    var sql = "INSERT INTO prices (price_usd, created_at) VALUES ("+avgRound+", '"+utcDate+"')";
    con.query(sql, function (err, result) {
      if (err) throw err;
      console.log('------------------------------------------');
      console.log(utcDate);
      console.log(avgRound);
      console.log('------------------------------------------');
    });
  }



  if (pastRoundAvg == avgRound) {
    sameDataTrack++;
    if (sameDataTrack > 15) {
      console.log('---ERROR---');
      // clearInterval(mainInterval);
      apisInit();
    }
  }else {
    sameDataTrack = 0;
  }

  if (iterTrack > 600) {
    pricesAvg = [];
    echamgesData = [];
    iterTrack = 0;
  }

  pastRoundAvg = avgRound;
},1000);
