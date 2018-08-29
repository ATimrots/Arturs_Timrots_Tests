<?php
interface iDatabase
{
    public function connect($query);
    public function disconnect();
}

class Database implements iDatabase
{
    private $dbHost;
    private $dbUser;
    private $dbPass;
    private $dbName;
    private $connection;

    /**
     * Database constructor.
     * @param $settings
     */
    public function __construct($settings)
    {
        $this->dbHost = $settings['host'];
        $this->dbUser = $settings['user'];
        $this->dbPass = $settings['password'];
        $this->dbName = $settings['schema'];
    }

    /**
     * @param $query
     * @return array|bool
     */
    public function connect($query)
    {
        $mysqli = new mysqli($this->dbHost, $this->dbUser, $this->dbPass, $this->dbName);
        $mysqli->set_charset('utf8');

        if ($mysqli->connect_error) {
            die('Connection error - '.mysqli_connect_errno().' - '. mysqli_connect_error());
            $this->connection = TRUE;
        } else {
            $result = $mysqli->query($query);

            if (is_bool($result)) {
                (bool)$output = $result;
            } else {
                $output = array();

                while ($row = $result->fetch_assoc()) {
                    $output[] = $row;
                }
            }
            $this->disconnect();

            return $output;
        }
    }

    /**
     * @return void
     */
    public function disconnect()
    {
        if ($this->connection) {
            mysqli::close();
            $this->connection = FALSE;
        }
    }
}