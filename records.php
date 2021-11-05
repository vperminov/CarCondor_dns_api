<?php
require_once 'db.php';

class recordsRepository
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
     * @param array $params
     * @return bool
     */
    public function storeRecord(array $params): bool
    {
        if ($this->isPrivate($params['val'])) {
            throw new RuntimeException('Private IP detected', 400);
        }
        if ($this->validateDomain($params['domain'])) {
            $stmt = $this->connection->prepare("SELECT id FROM record WHERE 
                                                    type = ? AND domain = ? AND name= ? AND val= ? AND ttl= ?");
            $stmt->bind_param('sisdi', $params['type'], $params['domain'], $params['name'], $params['val'], $params['ttl']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                throw new RuntimeException('Record already exist', 400);
            }

            $stmt = $this->connection->prepare("INSERT INTO record (type, domain, name, val, ttl) 
                                                    VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('sisdi', $params['type'], $params['domain'], $params['name'], $params['val'], $params['ttl']);
            $stmt->execute();
            return true;
        }
        return false;
    }

    /**
     * @param string $domain
     * @return bool
     */
    private function validateDomain(string $domain): bool
    {
        $stmt = $this->connection->prepare("SELECT fqdn FROM domain WHERE id = ?");
        $stmt->bind_param('s', $domain);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 0) {
            throw new RuntimeException('Domain not exist', 400);
        }
        return true;
    }

    /**
     * @param string $ip
     * @return bool
     */
    private function isPrivate(string $ip) : bool
    {
        return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }
}