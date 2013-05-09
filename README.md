heureka-overeno-php-api
=======================

[Heureka Overeno](http://overeno.heureka.cz/) service API for PHP. 

Usage
-----

Inicialize Service using [your API key](http://sluzby.heureka.cz/sluzby/certifikat-spokojenosti/):

    require_once 'heureka-overeno-php-api/src/HeurekaOvereno.php';

    $overeno = new HeurekaOvereno('9b011a7086cfc0210cccfbdb7e51aac8');
      
SK shops should initialize Heureka Overeno service with second parameter HeurekaOvereno::LANGUAGE_SK:
      
    $overeno = new HeurekaOvereno('9b011a7086cfc0210cccfbdb7e51aac8', HeurekaOvereno::LANGUAGE_SK);
      
Set customer email:

    $overeno->setEmail('jan.novak@example.com');
  
Add product from order - encoded in UTF8 if possible. Service can handle WINDOWS-1250 and ISO-8859-2 if necessary  
  
    $overeno->addProduct('Nokia N95');

Or add multiple products:

    // array $products is populated elsewhere by shop application
    foreach ($products as $product) {
      $overeno->addProduct($product);
    }
    
or/and add products using [item ID](http://sluzby.heureka.cz/napoveda/xml-feed/#ITEM_ID):

    $overeno->addProductItemId('B1234');
  
Provide order ID - BIGINT (0 - 18446744073709551615):
    
    $overeno->addOrderId(123456);
  
Send the request:

    $overeno->send();
    
[View all examples](https://github.com/heureka/heureka-overeno-php-api/tree/master/examples)
