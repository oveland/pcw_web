<?php

namespace App\Services\LM\BD;

use App\Models\Company\Company;
use Exception;
use function collect;

abstract class Database
{
    protected $type;

    protected $connection = null;
    protected $company = null;
    protected $dbId = 1;

    function for(Company $company, $dbId = 1)
    {
        $this->company = $company->id;
        $this->dbId = $dbId;
        $this->prepareConnection();
        
        return $this;
    }

    function getDataConnection()
    {
        return collect(config('database.connections')[$this->type])
            ->where('company_id', $this->company)
            ->where('db_id', $this->dbId)
            ->first();
    }

    /**
     * @param $message
     * @param null $query
     * @throws Exception
     */
    function throwException($message, $query = null)
    {
        throw new Exception($message . ". SQL $query");
    }

    abstract function prepareConnection();

    abstract function statement($sql);

    abstract function select($query);
}