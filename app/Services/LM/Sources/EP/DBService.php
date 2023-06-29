<?php

namespace App\Services\LM\Sources\EP;

use App\Services\LM\BD\Database as LMDatabase;

//use DB;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use function collect;

class DBService extends LMDatabase
{
    protected $type = 'LM';
    protected $db;

    public function __construct()
    {
        $this->db = DB::connection('LM.EXPRESO_PALMIRA');
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
        $this->db->statement($sql);
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