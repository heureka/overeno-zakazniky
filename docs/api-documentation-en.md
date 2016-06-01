Heureka 'Ověřeno zákazníky' (ShopCertification) API Documentation
=================================================================

Documentation for [Heureka Ověřeno zákazníky](http://overeno.heureka.cz/) (ShopCertification) API
service endpoint.

Basics
------

To report an order created in your e-shop to the Heureka ShopCertification service, you have to make a HTTP POST
request to the API endpoint URL with JSON-serialized data payload that describe the e-shop order (payload fields are
described below).

URL format
----------

Request URL has the following format:
`https://api.heureka.cz/shop-certification/v2/[ACTION]`

**Please note:** There are two available API endpoints. One is for Czech Heureka and the other one for Slovak Heureka
. The only difference is in different top-level domain name (cz/sk) in the API endpoint URL, i.e. `https://api.heureka.cz` /
`https://api.heureka.sk`

Make sure you are using the correct API endpoint URL, otherwise
you can get an error stating that you have an invalid API key, because keys are different for Czech and Slovak Heureka.

The only [ACTION] available at this time is `order/log`

Data payload fields
-------------------

To be able to report an e-shop order to the Heureka ShopCertification service, you have to provide all the
required information in the form of JSON-serialized data payload. Supported fields are following:

|      Field     |  Type  | Required |                         Notes                        |
|:--------------:|:------:|:--------:|:----------------------------------------------------:|
| apiKey         | string | yes      | you can obtain this in heureka e-shop administration |
| email          | string | yes      | e-mail of the customer who made the order            |
| orderId        | int    | no       | unique identification of an order in your e-shop     |
| productItemIds | array  | no       | ITEM_IDs of purchased products from your XML feed    |

Example
-------

Following URL is used to report the e-shop order:
`https://api.heureka.cz/shop-certification/v2/order/log`

JSON-serialized POST payload has to be in the following form:
```json
{
  "apiKey": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
  "email": "test@test.hu",
  "orderId": 12345,
  "productItemIds": [
    "ITEM01",
    "ITEM02",
    "ITEM03"
  ]
}
```

When everything goes smooth and the HTTP POST request is successful, API answers with following response:
```json
{
    "code": 200,
    "message": "ok"
}
```
That means order was successfully logged on Heureka side.


Common problems
---------------
API response:

```json
{
    "code": 400,
    "message": "bad-request",
    "description": "There is a problem with your request. Please see the documentation for details."
}
```

Possible solution: Make sure your JSON payload is valid.

API response:

```json
{
    "code": 401,
    "message": "unauthorized",
    "description": "Unknown API key \"xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx\"."
}
```

Possible solution: Make sure you use the correct API key. Also note that Czech and Slovak Heureka use different API
 keys. Double check you use the correct one.

API response:

```json
{
    "code": 415,
    "message": "unsupported-media-type",
    "description": "This service accepts data only in the \"application/json\" format with UTF-8 charset. Please use Content-Type header with \"application/json\" or \"application/json;charset=utf-8\" to send the data. See the documentation for details."
}
```

Possible solution: Make sure your HTTP POST request header has **Content-type** field set to **application/json** or **application/json;charset=utf-8**.

Footnote
--------
In case of any other implementation problems feel free to contact support at [podpora@heureka.cz](podpora@heureka.cz)
