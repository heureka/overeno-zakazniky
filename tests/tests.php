<?php

/**
 * Heureka Overeno test suite
 *
 * @author Heureka <podpora@heureka.cz>
 */
class HeurekaOverehoTestCase extends PHPUnit_Framework_TestCase
{
    const API_KEY = '9b011a7086cfc0210cccfbdb7e51aac8';
    
    public function testSend()
    {
        $overeno = new HeurekaOvereno(self::API_KEY);
        $overeno->setEmail('jan.novak@example.com');
        $res = $overeno->send();
        
        $this->assertSame(TRUE, $res);
    }
    
    /**
     * 
     */
    public function testInvalidApiKey()
    {
        $overeno = new HeurekaOvereno(self::API_KEY);
        $overeno->setEmail('jan.novak@example.com');
        $res = $overeno->send();
        
        $this->assertSame(TRUE, $res);
    }
}