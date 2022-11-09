<?php
namespace UPCSBPA\CanviDescripcioItem\ALMA;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use UPCSBPA\CanviDescripcioItem\ALMA\exceptions\ALMAExceptionFactory;

class AlmaClient {

    const ENDPOINT_SETS = "/almaws/v1/conf/sets";
    const ENDPOINT_BIBS = "/almaws/v1/bibs";

    private $apiUrl = null;
    private $apiKey = null;
    private $apiTimeout = 300;

    /** @var \GuzzleHttp\Client */
    private $restClient = null;

    public function __construct() {

    }

    public function configAPI($apiUrl, $apiKey) {
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;

        $this->restClient = new Client([
            'base_uri' => $this->apiUrl,
            'timeout'  => $this->apiTimeout,
        ]);
    }

    /**
     * Recupera el resultat de la consulta dels membres del set
     *
     * @param $setid
     * @param int $limit
     * @param int $offset
     * @return mixed|null
     * @throws exceptions\ALMAHTTPException
     */
    public function getSetMembers($setid, $limit = 10, $offset = 0) {

        $query = [
            'apikey' => $this->apiKey,
            'limit' => $limit,
            'offset' => $offset
        ];

        if ($setid == null) {
            return null;
        }

        try {
            $response = $this->restClient->get($this::ENDPOINT_SETS . "/" . $setid . "/members", [
                'query' => $query,
                'headers' => ['Accept' => 'application/json']
            ]);
        } catch (ClientException $exception) {
            throw ALMAExceptionFactory::createHTTPThrowable($exception, "Error obtenint set");
        }

        return json_decode($response->getBody(), false);
    }

    /**
     * Retorna la informació d'un bibliogràfic
     *
     * @param $mms_id
     * @param string $view
     * @param array $expand valors possibles p_avail, e_avail, d_avail, requests
     * @return mixed
     * @throws exceptions\ALMAHTTPException
     */
    public function getBib($mms_id, $view = "full", $expand = []) {

        $query = [
            'apikey' => $this->apiKey,
            'view' => $view
        ];

        if (is_array($expand) && sizeof($expand) > 0) {
            $query["expand"] = implode(",", $expand);
        }

        try {
            $response = $this->restClient->get($this::ENDPOINT_BIBS . "/" . $mms_id, [
                'query' => $query,
                'headers' => ['Accept' => 'application/json']
            ]);
        } catch (ClientException $exception) {
            throw ALMAExceptionFactory::createHTTPThrowable($exception, "Error recuperant bibliogràfic");
        }

        return json_decode($response->getBody(), false);
    }

    /**
     * Retorna una llista de holdings d'un bibliogràfic
     *
     * @param $mms_id
     * @return mixed
     * @throws exceptions\ALMAHTTPException
     */
    public function getHoldings($mms_id) {

        $query = [
            'apikey' => $this->apiKey
        ];

        try {
            $response = $this->restClient->get($this::ENDPOINT_BIBS . "/" . $mms_id . "/holdings", [
                'query' => $query,
                'headers' => ['Accept' => 'application/json']
            ]);
        } catch (ClientException $exception) {
            throw ALMAExceptionFactory::createHTTPThrowable($exception, "Error holdings de bibliogràfic");
        }

        return json_decode($response->getBody(), false);
    }

    /**
     * Retorna informació sobre els items d'un holding concret
     *
     * @param $mms_id
     * @param $holding_id string el ID del holding o 'ALL' per obtenir tots els del bibliogràfic
     * @param array $expand opcions per expandir: due_date o due_date_policy
     * @param array $params amb clau i valor a incloure a la query
     * @return mixed
     * @throws exceptions\ALMAHTTPException
     */
    public function getHoldingItems($mms_id, $holding_id, $expand = [], $params = []) {

        $_available_params = [
            "limit",
            "offset",
            "user_id",
            "current_library",
            "current_location",
            "q",
            "order_by",
            "direction",
            "create_date_from",
            "create_date_to",
            "modify_date_from",
            "modify_date_to",
            "receive_date_from",
            "receive_date_to",
            "expected_receive_date_from",
            "expected_receive_date_to",
            "view"
        ];

        $query = [
            'apikey' => $this->apiKey,
        ];

        foreach($params as $key => $value) {
            if (in_array($key,$_available_params)) {
                $query[$key] = $value;
            }
        }

        if (is_array($expand) && sizeof($expand) > 0) {
            $query["expand"] = implode(",", $expand);
        }

        try {
            $response = $this->restClient->get($this::ENDPOINT_BIBS . "/" . $mms_id . "/holdings/" . $holding_id . "/items", [
                'query' => $query,
                'headers' => ['Accept' => 'application/json']
            ]);
        } catch (ClientException $exception) {
            throw ALMAExceptionFactory::createHTTPThrowable($exception, "Error recuperant llistat d'items");
        }

        return json_decode($response->getBody(), false);
    }

    /**
     * Obté les dades d'un item
     *
     * Note: It is also possible to retrieve item information by barcode using:
     * GET /almaws/v1/items?item_barcode={item_barcode}.
     * Calling this shorthand URL will return an HTTP 302 redirect response leading to a
     * URL with the structure documented here. Please note that the redirect request will be
     * counted as a seperate request, in regards to the daily threshold.]
     *
     * @param $mms_id
     * @param $holding_id
     * @param $pid
     * @param null $view Special view of Item object. Optional. Currently supported: label -
     *                      adds fields relevant for label printing.
     * @param array $expand Parameter for enhancing result with additional information. Currently supported:
     *                          due_date_policy, due_date
     * @param string $user_id The id of the user which the due_date_policy expand will be calculated for.
     *                          Default: GUEST.
     * @return mixed
     * @throws exceptions\ALMAHTTPException
     */
    public function getItem($mms_id, $holding_id, $pid, $view = null, $expand = [], $user_id = "") {

        $query = [
            'apikey' => $this->apiKey,
        ];

        if (is_array($expand) && sizeof($expand) > 0) {
            $query["expand"] = implode(",", $expand);
        }

        if ($view != null && in_array($view, ["brief", "label"])) {
            $query["view"] = $view;
        }

        if ($user_id != null && $user_id != "") {
            $query["user_id"] = $user_id;
        }

        try {
            $response = $this->restClient->get($this::ENDPOINT_BIBS . "/" . $mms_id . "/holdings/" . $holding_id . "/items/" . $pid, [
                'query' => $query,
                'headers' => ['Accept' => 'application/json']
            ]);
        } catch (ClientException $exception) {
            throw ALMAExceptionFactory::createHTTPThrowable($exception, "Error recuperant llistat d'items");
        }

        return json_decode($response->getBody(), false);
    }

    /**
     * Actualitza la informació d'un item
     *
     * @param $mms_id string id del bibliogràfic
     * @param $holding_id string id del holding
     * @param $item_pid string id del item
     * @param $item mixed informació de l'ítem a carregar
     * @param false $generate_description boolean si es genera automàticament la descripció
     * @return mixed
     * @throws exceptions\ALMAHTTPException
     */
    public function updateItem($mms_id, $holding_id, $item_pid, $item, $generate_description = false) {

        $query = [
            'apikey' => $this->apiKey
        ];

        if ($generate_description) {
            $query["generate_description"] = "true";
        }

        try {
            // Endpoint a /almaws/v1/bibs/{mms_id}/holdings/{holding_id}/items/{item_pid}
            $response = $this->restClient->put($this::ENDPOINT_BIBS . "/" . $mms_id . "/holdings/" . $holding_id . "/items/" . $item_pid, [
                'query' => $query,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json'
                ],
                'body' => json_encode($item)
            ]);
        } catch (ClientException $exception) {
            throw ALMAExceptionFactory::createHTTPThrowable($exception, "Error modificant item");
        }

        return json_decode($response->getBody(), false);

    }

    /**
     * Recupera les reserves d'un ítem concret
     *
     * @param $mms_id
     * @param $holding_id
     * @param $item_pid
     * @return mixed
     * @throws exceptions\ALMAHTTPException
     */
    public function getItemRequests($mms_id, $holding_id, $item_pid) {

        $query = [
            'apikey' => $this->apiKey,
        ];

        try {
            $response = $this->restClient->get($this::ENDPOINT_BIBS . "/" . $mms_id . "/holdings/" .
                $holding_id . "/items/" . $item_pid . "/requests", [
                'query' => $query,
                'headers' => ['Accept' => 'application/json']
            ]);
        } catch (ClientException $exception) {
            throw ALMAExceptionFactory::createHTTPThrowable($exception, "Error recuperant llistat d'items");
        }

        return json_decode($response->getBody(), false);
    }


}
