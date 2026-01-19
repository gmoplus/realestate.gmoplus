<?php

/******************************************************************************
 *  
 *  PROJECT: Flynax Classifieds Software
 *  VERSION: 4.9.3
 *  LICENSE: FL30UFXTM56M - https://www.flynax.com/flynax-software-eula.html
 *  PRODUCT: General Classifieds
 *  DOMAIN: gmowin.online
 *  FILE: REQUESTS.PHP
 *  
 *  The software is a commercial product delivered under single, non-exclusive,
 *  non-transferable license for one domain or IP address. Therefore distribution,
 *  sale or transfer of the file in whole or in part without permission of Flynax
 *  respective owners is considered to be illegal and breach of Flynax License End
 *  User Agreement.
 *  
 *  You are not allowed to remove this information from the file without permission
 *  of Flynax respective owners.
 *  
 *  Flynax Classifieds Software 2024 | All copyrights reserved.
 *  
 *  https://www.flynax.com
 ******************************************************************************/

namespace Flynax\Plugin\WordPressBridge;

/**
 * Class Response
 *
 * @since 2.0.0
 *
 * @package Flynax\Plugin\WordPressBridge
 */
class Response
{
    /**
     * Generate JSON response
     *
     * @param array $array - Provided data will be converted to JSON
     */
    public static function json($array)
    {
        header('Content-Type: application/json');

        $data = [
            'data' => $array,
        ];
        echo json_encode($data);
    }

    /**
     * Generate error response
     *
     * @param string $message - Error message
     * @param int    $code    - HTTP response error code
     */
    public static function error($message, $code = 500)
    {
        http_response_code($code);

        $data = [
            'message' => $message,
            'code' => $code,
        ];

        self::json($data);
    }

    /**
     * Generate success response
     *
     * @param string|array $message - Response answer (string) message / (array) body
     * @param int          $code    - HTTP response success code
     */
    public static function success($message, $code = 200)
    {
        http_response_code($code);

        $data['code'] = $code;

        if (!is_array($message)) {
            $data['message'] = $message;
        } else {
            $data = $message;
        }

        self::json($data);
    }
}
