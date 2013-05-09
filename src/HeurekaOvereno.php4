<?php

/**
 * Heureka Overeno service for PHP4 
 *
 * Provides access to Heureka Overeno service. 
 * 
 * <code>  
 *     $overeno = new HeurekaOvereno('API_KLIC');
 *     // SK shops should use $overeno = new HeurekaOvereno('API_KLIC', HEUREKA_LANGUAGE_SK);
 *     $overeno->setEmail('ondrej.cech@heureka.cz');
 *     // add product using name
 *     $overeno->addProduct('Nokia N95');
 *     // and/or add product using itemId 
 *     $overeno->addProductItemId('B1234');
 *     if (false === $overeno->send()) {
 *         // error should be handled
 *         print 'Heureka Overeno service error: ' . $overeno->getLastError();
 *     }
 * </code>
 *  
 * @author Heureka.cz <podpora@heureka.cz>
 */

/**
 * Heureka endpoint URL
 *
 * @var string     
 */
define('HEUREKA_BASE_URL', 'http://www.heureka.cz/direct/dotaznik/objednavka.php');
define('HEUREKA_BASE_URL_SK', 'http://www.heureka.sk/direct/dotaznik/objednavka.php');

/**
 * Language IDs
 *
 * @var int     
 */
define('HEUREKA_LANGUAGE_CZ', 1);
define('HEUREKA_LANGUAGE_SK', 2);
class HeurekaOvereno
{

    /**
     * Shop API key
     *
     * @var string     
     */
    var $apiKey;

    /**
     * Customer email
     *
     * @var string     
     */
    var $email;

    /**
     * Ordered products
     *
     * @var array     
     */
    var $products = array();

    /**
     * Order ID
     *
     * @var int    
     */
    var $orderId;

    /**
     * Ordered products provided using item ID
     * 
     * @var array
     */
    var $productsItemId = array();

    /**
     * Error message
     *
     * @var tring     
     */
    var $errstr = NULL;

    /**
     * Current language identifier
     *
     * @var int     
     */
    var $languageId = 1;

    /**
     * Initialize Heureka Overeno service 
     *
     * @param string $apiKey Shop API key
     * @param int $languageId Language version settings
     */
    function HeurekaOvereno($apiKey, $languageId = HEUREKA_LANGUAGE_CZ)
    {
        $this->apiKey = $apiKey;
        $this->languageId = $languageId;
    }

    /**
     * Sets customer email
     *
     * @param string $email Customer email address
     */
    function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Adds ordered products using name
     * 
     * Products names should be provided in UTF-8 encoding. The service can handle
     * WINDOWS-1250 and ISO-8859-2 if necessary
     *
     * @param string $productName Ordered product name
     */
    function addProduct($productName)
    {
        $this->products[] = $productName;
    }

    /**
     * Adds ordered products using item ID
     *
     * @param string $itemId Ordered product item ID
     */
    function addProductItemId($itemId)
    {
        $this->productsItemId[] = $itemId;
    }

    /**
     * Adds order ID
     * 
     * @param int Order ID
     */
    function addOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Creates HTTP request and returns response body
     * 
     * @param string $url URL
     * @return string Response body
     */
    function sendRequest($url)
    {
        $parsed = parse_url($url);
        $fp = fsockopen($parsed['host'], 80, $errno, $errstr, 5);
        if (!$fp) {
            $this->errstr = $errstr . ' (' . $errno . ')';
            return false;
        } else {
            $return = '';
            $out = "GET " . $parsed['path'] . "?" . $parsed['query'] . " HTTP/1.1\r\n" .
                    "Host: " . $parsed['host'] . "\r\n" .
                    "Connection: Close\r\n\r\n";
            fputs($fp, $out);
            while (!feof($fp)) {
                $return .= fgets($fp, 128);
            }
            fclose($fp);
            $returnParsed = explode("\r\n\r\n", $return);

            return empty($returnParsed[1]) ? '' : trim($returnParsed[1]);
        }
    }

    /**
     * Returns domain for given language version
     *
     * @return String 
     */
    function getUrl()
    {
        return HEUREKA_LANGUAGE_CZ == (int) $this->languageId ? HEUREKA_BASE_URL : HEUREKA_BASE_URL_SK;
    }

    /**
     * Sends request to Heureka Overeno service and checks for valid response
     * 
     * @return boolean true
     */
    function send()
    {
        if (empty($this->email)) {
            $this->errstr = 'Customer email address not set';
            return false;
        }

        // create URL
        $url = $this->getUrl() . '?id=' . $this->apiKey . '&email=' . urlencode($this->email);
        foreach ($this->products as $product) {
            $url .= '&produkt[]=' . urlencode($product);
        }
        foreach ($this->productsItemId as $itemId) {
            $url .= '&itemId[]=' . urlencode($itemId);
        }
        if (isset($this->orderId)) {
            $url .= '&orderid=' . urlencode($this->orderId);
        }

        // send request and check for valid response
        $contents = $this->sendRequest($url);
        if (false === $contents) {
            $this->errstr = 'Unable to create HTTP request to Heureka Overeno service';
            return false;
        } elseif ('ok' == $contents) {
            return true;
        } else {
            $this->errstr = $contents;
            return false;
        }
    }

    /**
     * Returns last error message
     *
     * @return string Error message
     */
    function getLastError()
    {
        return $this->errstr;
    }

}