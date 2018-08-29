<?php
session_start();

require_once(''.__DIR__.'/db/database.php');
require_once(''.__DIR__.'/db/dbQuery.php');
require_once(''.__DIR__.'/app/testController.php');

class Configuration
{
    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    */
    const DATABASE_HOST = 'localhost';
    const DATABASE_USER = 'TestAdmin';
    const DATABASE_PASS = 'wCWRAxT2LV7fVjVi';
    const DATABASE_SCHEMA = 'tests';

    private $db;

    /**
     * @return Configuration
     */
    public static function make()
    {
        return new self;
    }

    /**
     * Configuration constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return $this
     */
    public function provideSettings()
    {
        $this->db = new Database(array(
            'host' => self::DATABASE_HOST,
            'user' => self::DATABASE_USER,
            'password' => self::DATABASE_PASS,
            'schema' => self::DATABASE_SCHEMA,
        ));

        return $this;
    }

    /**
     * @param $query
     * @return mixed
     */
    public function runQuery($query)
    {
        return $this->db->connect($query);
    }

    /**
     * @param $user_id
     * @param $name
     * @param $test_id
     */
    public function setUserSession($user_id, $name, $test_id)
    {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $name;
        $_SESSION['test_id'] = $test_id;
        $_SESSION['question_no'] = 0;
    }

    /**
     * @return void
     */
    public function destroySession()
    {
        session_destroy();
    }
}