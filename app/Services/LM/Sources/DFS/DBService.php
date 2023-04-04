<?php

namespace App\Services\LM\Sources\DFS;

use App\Services\LM\BD\Database as LMDatabase;
use DB;
use Exception;
use Illuminate\Support\Collection;
use function collect;

class DBService extends LMDatabase
{
    protected $type = 'DFS';
    protected $db;

    public function __construct()
    {
        $this->db = DB::connection('DFS.EXPRESO_PALMIRA'); // TODO Implements Facade for multiple companies with DFS system integration;
    }

    /**
     * @throws Exception
     */
    function prepareConnection()
    {

    }

    /**
     * @param $sql
     * @throws Exception
     */
    function statement($sql)
    {

    }

    /**
     * @param $query
     * @return Collection
     * @throws Exception
     */
    function select($query)
    {
        $data = collect([]);
        $result = $this->db->select($query);

        foreach ($result as $row) {
            $data->push((object)$row);
        }

        return $data;
    }
}