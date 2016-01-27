<?php

namespace Heureka;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * @author vladimir.kaspar <vladimir.kaspar@heureka.cz>
 */
class OverenoTest extends \PHPUnit_Framework_TestCase
{

    const TEST_LANG = Overeno::LANGUAGE_CZ;

    public function tearDown()
    {
        \Mockery::close();
    }

    /**
     * @param array  $parameters
     * @param string $expectedUrl
     *
     * @dataProvider successfulDataProvider
     */
    public function testSuccessfulOvereno($parameters, $expectedUrl)
    {
        $requester = \Mockery::mock('\Heureka\Overeno\IRequester');
        $requester->shouldReceive('request')->once()->with($expectedUrl)->andReturn(200);
        $requester->shouldReceive('getBody')->never();

        $overeno = new Overeno($parameters['apiKey'], self::TEST_LANG, $requester);
        $overeno->setEmail($parameters['customerEmail']);

        if (isset($parameters['productNames'])) {
            foreach ($parameters['productNames'] as $productName) {
                $overeno->addProduct($productName);
            }
        }
        if (isset($parameters['productIds'])) {
            foreach ($parameters['productIds'] as $productId) {
                $overeno->addProductItemId($productId);
            }
        }
        if (isset($parameters['orderId'])) {
            $overeno->setOrderId($parameters['orderId']);
        }

        $this->assertTrue($overeno->send());
    }

    public function successfulDataProvider()
    {
        return [
            [
                'parameters' => [
                    'apiKey' => 'xxxyyy',
                    'orderId' => 12345,
                    'customerEmail' => 'jan.novak@example.com',
                    'productNames' => ['Samsung', 'Nokia'],
                    'productIds' => ['B1', 'B2', 'B3'],
                ],
                'expectedUrl' => sprintf(Overeno::URL_PATTERN, self::TEST_LANG)
                    . '?id=xxxyyy'
                    . '&email=jan.novak%40example.com'
                    . '&produkt[]=Samsung&produkt[]=Nokia'
                    . '&itemId[]=B1&itemId[]=B2&itemId[]=B3'
                    . '&orderid=12345'
            ],
            [
                'parameters' => [
                    'apiKey' => 'xxxyyy',
                    'customerEmail' => 'jan.novak@example.com',
                ],
                'expectedUrl' => sprintf(Overeno::URL_PATTERN, self::TEST_LANG)
                    . '?id=xxxyyy'
                    . '&email=jan.novak%40example.com'
            ]
        ];
    }

    /**
     * @param array  $parameters
     * @param string $expectedUrl
     *
     * @dataProvider failureDataProvider
     */
    public function testFailureHttpError($parameters, $expectedUrl)
    {
        $error = ['httpCode' => 406, 'body' => 'Spatne predavany API klic xxxyyy'];

        $requester = \Mockery::mock('IRequester');
        $requester->shouldReceive('request')->once()->with($expectedUrl)->andReturn($error['httpCode']);
        $requester->shouldReceive('getBody')->once()->andReturn($error['body']);

        $overeno = new Overeno($parameters['apiKey'], self::TEST_LANG, $requester);
        $overeno->setEmail($parameters['customerEmail']);

        try {
            $overeno->send();

            $this->fail();
        } catch (Overeno\Exception $e) {
            $expectedError = sprintf('HTTP error - http code: %d, body: %s', $error['httpCode'], $error['body']);
            $this->assertEquals($expectedError, $e->getMessage());
        }
    }
    /**
     * @param array  $parameters
     * @param string $expectedUrl
     *
     * @dataProvider failureDataProvider
     */
    public function testFailureCurlError($parameters, $expectedUrl)
    {
        $errorMsg = 'Curl error: curlErr , error number: 999';

        $requester = \Mockery::mock('IRequester');
        $requester->shouldReceive('request')->once()->with($expectedUrl)->andThrow(
            '\\Heureka\\Overeno\\CurlException', $errorMsg);

        $overeno = new Overeno($parameters['apiKey'], self::TEST_LANG, $requester);
        $overeno->setEmail($parameters['customerEmail']);

        try {
            $overeno->send();

            $this->fail();
        } catch (Overeno\Exception $e) {
            $this->assertEquals($errorMsg, $e->getMessage());
        }
    }

    public function failureDataProvider()
    {
        return [
            [
                'parameters' => [
                    'apiKey' => 'xxxyyy',
                    'customerEmail' => 'jan.novak@example.com',
                ],
                'expectedUrl' => sprintf(Overeno::URL_PATTERN, self::TEST_LANG)
                    . '?id=xxxyyy'
                    . '&email=jan.novak%40example.com',
            ]
        ];
    }

}
