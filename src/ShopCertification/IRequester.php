<?php

namespace Heureka\ShopCertification;

/**
 * @author Vladimír Kašpar <vladimir.kaspar@heureka.cz>
 * @author Jakub Chábek <jakub.chabek@heureka.cz>
 */
interface IRequester
{

    const ACTION_LOG_ORDER = 'order/log';

    /**
     * @param ApiEndpoint $endpoint
     */
    public function setApiEndpoint(ApiEndpoint $endpoint);

    /**
     * @param string $action @see self::ACTION_*
     * @param array  $data
     *
     * @return Response
     * @throws RequesterException A RequesterException must be thrown if response is invalid or has code other than 200
     */
    public function request($action, array $data);

}

class RequesterException extends Exception {}
