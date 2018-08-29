<?php

interface iDbQuery
{
    public function execute();
}

class DbQuery implements iDbQuery
{
    private $sqlQuery;

    /**
     * @param $sql
     * @return DbQuery
     */
    public static function make($sql)
    {
        return new self($sql);
    }

    /**
     * DbQuery constructor.
     * @param $sqlQuery
     */
    public function __construct($sqlQuery)
    {
        $this->sqlQuery = $sqlQuery;
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $result = Configuration::make()
            ->provideSettings()
            ->runQuery($this->sqlQuery);

        return $result;
    }

    /**
     * @param $array
     * @return int
     */
    public function getCount($array)
    {
        return (int)count($array);
    }
}
