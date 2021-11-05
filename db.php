<?php

class DB
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $dbUser;

    /**
     * @var string
     */
    private $dbPass;

    /**
     * @var string
     */
    private $dbName;

    /**
     * @var mysqli
     */
    private $connection;

    /**
     * @throws Exception
     */

    public function __construct()
    {
        $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {

            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);


            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;

            }
        }
        $this->host = $_ENV['DBHOST'] ?? '' . ':' . $_ENV['DBPOST'] ?? '';
        $this->dbUser = $_ENV['DBUSER'] ?? '';
        $this->dbPass = $_ENV['DBPASSWORD'] ?? '';
        $this->dbName = $_ENV['DBNAME'] ?? '';
    }

    /**
     * @return mysqli
     */
    public function connect(): mysqli
    {
        $this->connection = new mysqli($this->host, $this->dbUser, $this->dbPass, $this->dbName);
        if ($this->connection->connect_errno) {
            throw new RuntimeException('mysqli connection error: ' . $this->connection->connect_error);
        }
        return $this->connection;
    }
}