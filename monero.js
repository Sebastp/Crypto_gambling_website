var moneroWallet = require('monero-nodejs'),
    mysql = require('mysql');

// var Wallet = new moneroWallet('127.0.0.1', '18082');

var con = mysql.createConnection({
  host: "127.0.0.1",
  user: "root",
  password: "tBkNeNtZ6v1TU9FS",
  database: "cryptoG"
});

con.connect(function(err) {
  if (err) throw err;
  console.log("Connected!");
});

var sql = "SELECT FROM transactions (id, payment_id, address, created_at) WHERE type = 'depositInProgress'";
con.query(sql, function (err, result) {
  if (err) throw err;
  console.log(utcDate);
  console.log(avgRound);
});

// Wallet.balance().then(function(balance) {
//   console.log(balance);
// });
