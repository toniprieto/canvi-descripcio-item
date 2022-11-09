<?php

namespace UPCSBPA\CanviDescripcioItem\ALMA\exceptions;


use GuzzleHttp\Psr7\Response;

class ALMAHTTPException extends ALMAException
{

    /**
     * @var Response
     */
    private $response = null;

    /**
     * @param $message string missatge de l'excepció
     * @param $code int codi de l'excepció
     * @param $contents string content contingut de l'excepció
     * @param \Exception $previous excepció previa
     */
    public function __construct($message = '', $code = 0, $previous = null, $contents = null, $response = null)
    {
        parent::__construct($message, $code, $previous, $contents);
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

}