<?php

namespace App\Services\LM\Sources\BEA;

use App\Services\LM\BD\Database as LMDatabase;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use function collect;

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
            $this->connection = new \PDO("firebird:dbname=" . $cx['path'], $cx['username'], $cx['password']);
        } catch (Exception $x) {
            $this->connection = null;
            Log::channel('lm')->error('Error on database connection. Path = ' . $cx['path'] . '. Message: ' . $x->getMessage());
        }

        if (!$this->connection) {
            $this->throwException("No connection with LM DB. Company ID $this->company and dbId = $this->dbId");
        }
    }

    /**
     * @param $sql
     * @throws Exception
     */
    function statement($sql)
    {
        $this->connection->exec($sql) or self::throwException($this->connection->errorInfo(), $sql);
    }

    /**
     * @param $query
     * @return Collection
     * @throws Exception
     */
    function select($query)
    {
        $data = collect([]);
        $result = $this->connection->query($query) or self::throwException($this->connection->errorInfo(), $query);

        $rows = $result->fetchAll();
        foreach ($rows as $row) {
            $row = collect($row)->filter(function ($value, $key) {
                return is_string($key);
            });
            $data->push((object)$row->toArray());
        }

        return $data;
    }
}