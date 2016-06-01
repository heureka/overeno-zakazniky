Heureka 'Ověřeno zákazníky' API Dokumentace
=================================================================

Dokumentace pro API služby [Heureka Ověřeno zákazníky](http://overeno.heureka.cz/).

**Poznámka:** Toto je technický popis API pro vývojáře klientských knihoven pro jiné programovací jazyky než je PHP.
Hotová klientská knihovna pro PHP je k dispozici [zde](https://github.com/heureka/overeno-zakazniky).

Základní popis
--------------

Pro zaznamenání objednávky z vašeho e-shopu do služby Ověřeno zákazníky je třeba provést HTTP POST request s
JSON-serializovanými daty o provedené objednávce na URL adresu našeho API.

Formát URL
----------

URL požadavku má následující formát:
`https://api.heureka.[TLD]/shop-certification/v2/[ACTION]`

**Pozor:** K dispozici jsou dvě URL adresy API služby Ověřeno zákazníky - jedna pro českou Heureku a druhá pro
slovenskou. Liší se pouze rozdílnou doménou prvního řádu (cz/sk) v URL adrese API: `https://api.heureka.cz` /
`https://api.heureka.sk`

Ujistěte se, že používáte správnou URL jinak API pošle odpověď, že zadaný API klíč je nevalidní, protože API klíče
jsou rozdílné pro českou a slovenskou Heureku.

V současné době API podporuje pouze jednu [ACTION] což je `order/log` (uložení objednávky).

[TLD] je buď `cz` nebo `sk` v závislosti na tom jestli chcete zaznamenat objednávku na české nebo slovenské Heurece.

Popis předávaných dat
---------------------

Pro úspěšné zaznamenání dat o objednávce na straně Heureky je zapotřebí předat všechna vyžadovaná data. Volitelně
můžete předávat i data o objednaných produktech a ID objednávky. Tyto data je následně třeba serializovat do formátu
JSON a poslat je spolu s HTTP POST požadavkem na URL našeho API.

Toto jsou podporovaná data, která API služba umí zpracovat:

|      Pole      |  Typ   | Vyžadováno |                        Poznámka                      |
|:--------------:|:------:|:----------:|:----------------------------------------------------:|
| apiKey         | string | ano        | API klíč získáte po přihlášení v e-shop administraci |
| email          | string | ano        | e-mail zákazníka, který provedl objednávku           |
| orderId        | int    | ne         | unikátní identifikátor objednávky ve vašem e-shopu   |
| productItemIds | array  | ne         | ITEM_ID produktů které předáváte v XML feedu         |

Příklad
-------

Následující URL je použita pro zaznamenání objednávky na straně Heureky (CZ heureka):
`https://api.heureka.cz/shop-certification/v2/order/log`

JSON-serializovaná data, která je nutné poslat společně s POST požadavkem musí mít tuto formu:
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

Pokud vše proběhne v pořádku a HTTP POST je úspěšný, API pošle zpět tuto odpověď:
```json
{
    "code": 200,
    "message": "ok"
}
```
To znamená, že na straně Heureky došlo k úspěšnému zaznamenání objednávky.


Časté problémy
--------------
API odpověď:

```json
{
    "code": 400,
    "message": "bad-request",
    "description": "There is a problem with your request. Please see the documentation for details."
}
```

Možné řešení: Ujistěte se, že zasílaný JSON payload je validní JSON.

API odpověď:

```json
{
    "code": 401,
    "message": "unauthorized",
    "description": "Unknown API key \"xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx\"."
}
```

Možné řešení: Ujistěte se, že používáte správný API klíč. Česká a slovenská Heureka používá pro službu Ověřeno
zákazníky rozdílné API klíče.

API odpověď:

```json
{
    "code": 415,
    "message": "unsupported-media-type",
    "description": "This service accepts data only in the \"application/json\" format with UTF-8 charset. Please use Content-Type header with \"application/json\" or \"application/json;charset=utf-8\" to send the data. See the documentation for details."
}
```

Možné řešení: Ujistěte se, že zaslaný HTTP POST request obsahuje v hlavičce HTTP requestu pole **Content-type** s
hodnotou **application/json** nebo **application/json;charset=utf-8**.

Poznámka
--------
Pokud budete mít s implementací problémy, nebo dostanete jako odpověď jinou chybu než zde popsanou neváhejte
kontaktovat podporu na e-mailu [podpora@heureka.cz](podpora@heureka.cz).
