<?php

namespace Eticsoft\Sanalpospro;

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class Payment
{
    public static function createPayment($params)
    {
        $apiClient = ApiClient::getInstanse();
        return $apiClient->post('/payment/create', $params);
    }

    public static function validatePayment($token)
    {
        $apiClient = ApiClient::getInstanse();
        return $apiClient->get('/process/getbytoken/' . $token);
    }
}