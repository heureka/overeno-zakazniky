<?php

namespace Heureka\ShopCertification;

/**
 * @author Jakub Chábek <jakub.chabek@heureka.cz>
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider providerConstruct
     *
     * @param string $json
     * @param array  $expected
     */
    public function testConstruct($json, $expected)
    {
        $response = new Response($json);
        $this->assertSame($expected, (array)$response);
    }

    /**
     * @return array
     */
    public function providerConstruct()
    {
        return [
            ['{"code":200,"message":"ok"}', ['code' => 200, 'message' => 'ok', 'description' => null]],
            [
                '{"code":404,"message":"not-found","description":"Resource does not exist."}',
                ['code' => 404, 'message' => 'not-found', 'description' => 'Resource does not exist.']
            ],
        ];
    }

    public function testConstructWithMissingFields()
    {
        $this->setExpectedException('Heureka\ShopCertification\InvalidResponseException');
        new Response('{"message":"There was an error"}');
    }

    /**
     * @dataProvider providerConstructWithInvalidJsonResponse
     *
     * @param string $json
     */
    public function testConstructWithInvalidJsonResponse($json)
    {
        $this->setExpectedException('Heureka\ShopCertification\JsonException');
        new Response($json);
    }

    /**
     * @return array
     */
    public function providerConstructWithInvalidJsonResponse()
    {
        return [
            ['ok'], ['Chyba'], ['{'], ['{ok}'], [null], [true], [false], [0], [1],
        ];
    }

}
