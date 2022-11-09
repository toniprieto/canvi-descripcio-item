<?php

namespace UPCSBPA\CanviDescripcioItem\ALMA\exceptions;

use GuzzleHttp\Exception\ClientException;

class ALMAExceptionFactory
{
    /**
     * @param ClientException $clientException
     *
     * @return ALMAHTTPException
     */
    public static function createHTTPThrowable(ClientException $clientException, $localMessage = '')
    {
        $contents = $clientException->getResponse()->getBody()->getContents();
        if ($contents != null) {
            $data = json_decode($contents);
        } else {
            $data = null;
        }

        $code = 0;
        $message = '';

        if ($localMessage != null && $localMessage != '') {
            $message = $localMessage;
        }

        if ($clientException->getResponse()->getStatusCode() == 400) {
            if (!empty($data->errorList)) {
                //FIXME: només mirem el primer
                if (isset($data->errorList->error[0])) {
                    $error = $data->errorList->error[0];
                    if (!empty($error->errorCode)) {
                        $code = $error->errorCode;
                    }

                    if (!empty($error->errorMessage)) {
                        if ($message != '') {
                            $message .= " - " . $error->errorMessage;
                        } else {
                            $message = $error->errorMessage;
                        }
                    }
                }
            }
        } else {
            if ($message != '') {
                $message .= " - " . ALMAExceptionFactory::httpMessage($clientException->getResponse()->getStatusCode());
            }
        }

        return new ALMAHTTPException($message, $code, $clientException, $data, $clientException->getResponse());
    }


    /**
     * Retorna un missatge per codi d'error http
     *
     * @param $httpCode
     * @return string
     */
    private static function httpMessage($httpCode)
    {
        switch ($httpCode) {
            case 400: $message = 'Bad Request (400)'; break;
            case 401: $message = 'Unauthorized (401)'; break;
            case 402: $message = 'Payment Required (402)'; break;
            case 403: $message = 'Forbidden (403)'; break;
            case 404: $message = 'Not Found (404)'; break;
            case 405: $message = 'Method Not Allowed (405)'; break;
            case 406: $message = 'Not Acceptable (406)'; break;
            case 407: $message = 'Proxy Authentication Required (407)'; break;
            case 408: $message = 'Request Time-out (408)'; break;
            case 409: $message = 'Conflict (409)'; break;
            case 410: $message = 'Gone (410)'; break;
            case 411: $message = 'Length Required (411)'; break;
            case 412: $message = 'Precondition Failed (412)'; break;
            case 413: $message = 'Request Entity Too Large (412)'; break;
            case 414: $message = 'Request-URI Too Large (414)'; break;
            case 415: $message = 'Unsupported Media Type (415)'; break;
            case 500: $message = 'Internal Server Error (500)'; break;
            case 501: $message = 'Not Implemented (501)'; break;
            case 502: $message = 'Bad Gateway (502)'; break;
            case 503: $message = 'Service Unavailable (503)'; break;
            case 504: $message = 'Gateway Time-out (504)'; break;
            case 505: $message = 'HTTP Version not supported  (505)'; break;
            default: $message = 'Undefined (' . $httpCode . ')';
        }
        return $message.' ('.$httpCode.')';
    }


}
