<?php

namespace Heureka\Overeno;

interface IRequester
{

    /**
     * Requests defined URL
     *
     * @param string $url
     *
     * @return int HTTP code
     * @throws CurlException
     */
    public function request($url);

    /**
     * Gets error message
     *
     * @return string
     */
    public function getBody();

}
