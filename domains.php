<?php
require_once 'db.php';

class domainsRepository
{
    /**
     * @var DB
     */
    private $connection;

    public function __construct()
    {
        $db = new DB();
        $this->connection = $db->connect();
    }

    /**
     * @return mixed
     */
    public function getAllDomains()
    {
        $result = $this->connection->query('SELECT * FROM domain');
        return $result->fetch_all(1);
    }

    /**
     * @param string $searchString
     * @return mixed
     */
    public function searchDomains(string $searchString)
    {
        $stmt = $this->connection->prepare("SELECT * FROM domain WHERE fqdn LIKE CONCAT('%',?,'%') ");

        $stmt->bind_param('s',$searchString);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_all(1);
    }

    /**
     * @return array
     */
    public function getDomainsWithDns() : array
    {
        $result = $this->connection->query('SELECT 
                                                    domain.id as id, 
                                                    domain.fqdn as fqdn, 
                                                    record.name as name, 
                                                    record.val as val, 
                                                    record.ttl as ttl, 
                                                    record.domain as domain_id
                                                  FROM domain
                                                    LEFT JOIN record ON record.domain = domain.id');
        $res = [];
        while ($row = $result->fetch_assoc()) {
            if (!isset($res[$row['id']])) {
                $res[$row['id']] = [
                    'fqdn' => $row['fqdn'],
                    'dns' => [
                        [
                            'name' => $row['name'],
                            'val' => $row['val'],
                            'ttl' => $row['ttl']
                        ]
                    ]
                ];

            } else {
                $res[$row['id']]['dns'][] = ['name' => $row['name'], 'val' => $row['val'], 'ttl' => $row['ttl']];
            }

        }
        return $res;
    }

    /**
     * @param string $domain
     * @return array|false
     */
    public function storeDomain(string $domain)
    {
        if ($this->validateDomain($domain)) {
            $stmt = $this->connection->prepare("SELECT fqdn FROM domain WHERE fqdn = ?");
            $stmt->bind_param('s',$domain);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                throw new RuntimeException('Domain already exist', 400);
            }

            $stmt = $this->connection->prepare("INSERT INTO domain (fqdn) VALUES (?)");
            $stmt->bind_param('s',$domain);
            $stmt->execute();
            $stmt->execute();
            if ($stmt->errno) {
                throw new RuntimeException($stmt->error, 400);
            }

            $result = $this->connection->query('SELECT * FROM domain WHERE id=LAST_INSERT_ID()');
            return $result->fetch_assoc();
        }
        return false;
    }

    /**
     * @param string $domain
     * @return bool
     */
    private function validateDomain(string $domain): bool
    {
        if (preg_match('^(?!\-)(?:(?:[a-zA-Z\d][a-zA-Z\d\-]{0,61})?[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}$^', $domain)) {
            return true;
        }
        throw new RuntimeException('Domain incorrect', 400);
    }
}