<?php

namespace App\Services\BEA;

use App\Services\LM\BD\Database as LMDatabase;
use Exception;
use Illuminate\Support\Collection;

class DBService extends LMDatabase
{
    protected $type = 'BEA';

    /**
     * @throws Exception
     */
    function prepareConnection()
    {
        $cx = $this->getDataConnection();
        try {
            $this->connection = ibase_connect($cx['path'], $cx['username'], $cx['password']);
        } catch (Exception $x) {
            $this->connection = null;
            Log::channel('lm')->error('Error on database connection. Params = ' . json_encode($cx) . '. Message: ' . $x->getMessage());
        }

        if (!$this->connection) {
            $this->throwException("No connection with LM DB. Company ID $this->company");
        }
    }

    /**
     * @param $sql
     * @throws Exception
     */
    function statement($sql)
    {
        ibase_query($this->connection, $sql) or self::throwException(ibase_errmsg());
    }

    /**
     * @param $query
     * @return Collection
     * @throws Exception
     */
    function select($query)
    {
        $data = collect([]);
        $result = ibase_query($this->connection, $query) or self::throwException(ibase_errmsg(), $query);

        while ($row = ibase_fetch_object($result)) {
            $data->push((object)$row);
        }

        return $data;
    }
}