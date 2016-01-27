heureka-overeno-php-api
=======================

[Heureka Overeno](http://overeno.heureka.cz/) service API for PHP. 

Usage
-----

Initialize Service using [your API key](http://sluzby.heureka.cz/sluzby/certifikat-spokojenosti/):

```php
require_once 'heureka-overeno-php-api/src/HeurekaOvereno.php';
$overeno = new HeurekaOvereno('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
```
      
SK shops should initialize Heureka Overeno service with second parameter HeurekaOvereno::LANGUAGE_SK:
      
    $overeno = new HeurekaOvereno('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', HeurekaOvereno::LANGUAGE_SK);
      
Set customer email:

```php
$overeno->setEmail('jan.novak@example.com');
```
  
Add product from order - encoded in UTF8 if possible. Service can handle WINDOWS-1250 and ISO-8859-2 if necessary  

```php
$overeno->addProduct('Nokia N95');
```

Or add multiple products:

```php
// array $products is populated elsewhere by shop application
foreach ($products as $product) {
  $overeno->addProduct($product);
}
```
    
or/and add products using [item ID](http://sluzby.heureka.cz/napoveda/xml-feed/#ITEM_ID):

```php
$overeno->addProductItemId('B1234');
```
  
Provide order ID - BIGINT (0 - 18446744073709551615):

```php
$overeno->setOrderId(123456);
```
  
Send the request:

```php
$overeno->send();
```
    
[View all examples](https://github.com/heureka/heureka-overeno-php-api/tree/master/examples)
