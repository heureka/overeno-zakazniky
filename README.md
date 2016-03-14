Heureka 'Ověřeno zákazníky' PHP API
===================================

[Heureka Ověřeno zákazníky](http://overeno.heureka.cz/) (ShopCertification) service API client implementation for PHP.

Examples
--------

You can check working examples in the folder `examples` of this repository.

Usage
-----

Initialize class `Heureka\ShopCertification` using
[your API key](http://sluzby.heureka.cz/sluzby/certifikat-spokojenosti/) (you need to log in):

```php
require_once __DIR__ . '/vendor/autoload.php';

$shopCertification = new \Heureka\ShopCertification('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
```

**Keep in mind that your API key is only yours and it is supposed to be a secret.** Never post your API key to anyone,
never put it into JavaScript or anywhere else. It should live on your server only. If you feel the need to break this
rule then you are doing something wrong - please consult our [support department](http://onas.heureka.cz/kontakty)
prior to any actions.

SK shops should initialize the class with a service parameter in the options:

```php
$options = ['service' => \Heureka\ShopCertification::HEUREKA_SK];
$shopCertification = new \Heureka\ShopCertification('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', $options);
```

Set the customer e-mail address:

```php
$shopCertification->setEmail('jan.novak@muj-eshop.cz');
```

Set the customer's order ID (only integers are allowed):

```php
$shopCertification->setOrderId(15195618851564);
```

Add products which the customer ordered (use IDs which you used in ITEM_ID field of the Heureka XML feed):
```php
$shopCertification->addProductItemId('B1234');
$shopCertification->addProductItemId('15968421');
$shopCertification->addProductItemId('814687');
```
And finally send request to log the order:

```php
$shopCertification->logOrder();
```
