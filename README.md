# Finrock JWT tokens 
Finrock API relies on using JWT web tokens encrypted with RSA 4096, for authenticating HTTP requests. Each endpoint that requires authentication expects the following two headers:

## JWT Bearer Token
The JWT token is created using the payload of the request to be sent to us and the payload should contain the following fields:

- uri - The URI part of the request e.g. /dac/v1/transactions.
- nonce - Unique number or string, each request must specify a unique nonce. - 
- iat - The timestamp at which the JWT was issued, in seconds since Epoch.
- exp - The expiration time on and after which the JWT must not be accepted for processing, in seconds since Epoch. It must be less than iat+30sec.
- sub - Your API Key, generate one using this article
- bodyHash - Hex-encoded SHA-256 hash of the raw HTTP request body.

The JWT must be signed using your RSA private key and the RS256 (RSASSA-PKCS1-v1_5 using SHA-256 hash) algorithm. 

## bodyHash Example
Appended below is an example of a body and its bodyHash value

JSON Body
```
{"asset":"BTC","amount":0.0675,"type":"Withdraw","ts":1715933321398}
```

bodyHash
```
6fd6a31980c78e97a5b1a12138b979934fb825f035c1ea2d3a8037a595bcedfb
```

## Snippets (Examples)
- C#
- NodeJS
- Python
- PHP
