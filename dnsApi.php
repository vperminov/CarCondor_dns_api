<?php
require_once 'Api.php';
require_once 'domains.php';
require_once 'records.php';

class dnsApi extends Api
{
    /**
     * @var domainsRepository
     */
    private $domainsRepository;

    /**
     * @var recordsRepository
     */
    private $recordsRepository;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->domainsRepository = new domainsRepository;
        $this->recordsRepository = new recordsRepository;
    }


    /**
     * method GET
     * return all domains
     * http://example.com/
     * @return string
     */
    protected function indexAction(): string
    {
        return $this->response($this->domainsRepository->getAllDomains());
    }

    /**
     * method GET
     * search domains by 'domain' string
     * http://example.com/search/?domain=someting
     * @return string
     */
    protected function searchAction(): string
    {
        return $this->response($this->domainsRepository->searchDomains($this->requestParams['domain'] ?? ''));
    }

    /**
     * method GET
     * return all domains with connected records
     * http://example.com/get
     * @return string
     */
    protected function viewAction(): string
    {
        return $this->response($this->domainsRepository->getDomainsWithDns());
    }

    /**
     * method POST
     * create new domain
     * http://example.com/create
     * @return string
     */
    protected function createDomainAction(): string
    {
        return $this->response($this->domainsRepository->storeDomain($this->requestParams['domain'] ?? ''));
    }

    /**
     * method POST
     * create new record
     * http://example.com/create-record
     * @return string
     */
    protected function createRecordAction(): string
    {
        if ($this->recordsRepository->storeRecord($this->requestParams)) {
            return $this->response(['success' => true]);
        }
        return $this->response(['success' => false], 400);
    }


}