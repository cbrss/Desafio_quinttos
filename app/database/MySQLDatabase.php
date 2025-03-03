<?php

require_once 'DatabaseInterface.php';

class MySQLDatabase implements DatabaseInterface {
    private $connection;
    private $dsn;
    private $user;
    private $password;
    private $options;

    public function __construct($host, $dbname, $user, $password, $port) {
        $this->dsn = "mysql:host=$host;port=$port;dbname=$dbname;";
        $this->user = $user;
        $this->password = $password;
        $this->options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
    }

    public function connect() {
         $this->connection = new PDO($this->dsn, $this->user, $this->password, $this->options);
    }
    
    public function disconnect() {
        $this->connection = NULL;
    }

    public function query($sql, $params = []) {
        if (!$this->connection) {
            throw new Exception("DB connection error");
        }
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
?>
