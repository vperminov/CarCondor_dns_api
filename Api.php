<?php

abstract class Api
{
    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    public $requestUri;

    /**
     * @var array
     */
    public $requestParams;

    /**
     * @var string
     */
    protected $action;


    /**
     * @throws Exception
     */
    public function __construct()
    {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");

        $this->requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        $this->requestParams = $_REQUEST;

        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                throw new Exception("Unexpected Header");
            }
        }
    }

    /**
     * @return mixed
     */
    public function run()
    {
        $this->action = $this->getAction();

        if (method_exists($this, $this->action)) {
            return $this->{$this->action}();
        } else {
            throw new RuntimeException('Invalid Method', 405);
        }
    }

    /**
     * @param $data
     * @param int $status
     * @return string
     */
    protected function response($data, $status = 200) : string
    {
        header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));
        return json_encode($data);
    }

    /**
     * @param int $code
     * @return string
     */
    private function requestStatus(int $code): string
    {
        $status = [
            200 => 'OK',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        ];
        return $status[$code] ?? $status[$code] ?? $status[500];
    }

    /**
     * @return string
     */
    protected function getAction(): string
    {
        $action = 'indexAction';
        switch ($this->method) {
            case 'GET':
                if (empty($this->requestUri[0])) {
                    $action = 'indexAction';
                }
                switch ($this->requestUri[0]) {
                    case 'search':
                        $action = 'searchAction';
                        break;
                    case 'get':
                        $action = 'viewAction';
                        break;
                }
                break;
            case 'POST':
                switch ($this->requestUri[0]) {
                    case 'create':
                        $action = 'createDomainAction';
                        break;
                    case 'create-record':
                        $action = 'createRecordAction';
                        break;

                }
                break;
            default:
                $action = null;
        }
        return $action;
    }

    abstract protected function indexAction();

    abstract protected function searchAction();

    abstract protected function viewAction();

    abstract protected function createDomainAction();

    abstract protected function createRecordAction();

}