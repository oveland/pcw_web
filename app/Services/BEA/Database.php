<?php


namespace App\Services\BEA;


use Exception;
use Illuminate\Support\Collection;

class Database
{
    static $connection = null;

    function __construct($path, $username, $password)
    {
        self::$connection = ibase_connect($path, $username, $password);
    }

    /**
     * @param $sql
     * @throws Exception
     */
    function statement($sql)
    {
        ibase_query(self::$connection, $sql) or self::throwException(ibase_errmsg());
    }

    /**
     * @param $query
     * @return Collection
     * @throws Exception
     */
    function select($query)
    {
        $data = collect([]);
        $result = ibase_query(self::$connection, $query) or self::throwException(ibase_errmsg());

        while ($row = ibase_fetch_object($result)) {
            $data->push((object)$row);
        }

        return $data;
    }

    /**
     * @param $message
     * @throws Exception
     */
    function throwException($message)
    {
        throw new Exception($message);
    }
}