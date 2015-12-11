<?php

namespace Heureka\Overeno;

class Requester
{

    /** @var string */
    private $body;

    /**
     * Requests defined URL
     *
     * @param string $url
     *
     * @return int HTTP code
     * @throws CurlException
     */
    public function request($url)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url
        ]);

        if (!$result = curl_exec($curl)) {
            throw new CurlException(sprintf('Curl error: %s, error number: %d', curl_error($curl), curl_errno($curl)));
        }
        $this->body = $result;
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);
        return $httpCode;
    }

    /**
     * Gets error message
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

}
