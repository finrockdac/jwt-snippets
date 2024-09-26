'use strict';
const fs = require('fs');
const jwt = require('jsonwebtoken');
const crypto = require('crypto');

var privateKEY  = `-----BEGIN PRIVATE KEY-----
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
40rDmLs9J2K3fvxr2Uz+Hw3KxjP+
-----END PRIVATE KEY-----`;

var now = 1715945186; // in sec
var expiry = 1816031578;
var nonce = 12345; // unique value
var apiKey = "513d5dd4-9c7f-4487-b782-a8274cc9700a";
var json = {
  asset: "BTC",
  amount: 0.0675,
  type: "Withdraw",
};
var body = JSON.stringify(json);

var payload = {
 iat: now,
 nonce: nonce,
 exp: expiry,
 sub: apiKey,
 uri: "/wallet/withdraw",
 bodyhash: crypto.createHash('sha256').update(body).digest('hex')
};

var signOptions = {
 algorithm:  "RS256"
};

var token = jwt.sign(payload, privateKEY, signOptions);
console.log("Token :" + token);





