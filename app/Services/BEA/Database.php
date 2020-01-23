<?php


namespace App\Services\BEA;


use App\Models\Company\Company;
use Exception;
use Illuminate\Support\Collection;

class Database
{
    private $connection = null;
    private $company = null;

    function __construct($companyId)
    {
        $valid = Company::find($companyId);

        if (!$valid) {
            $this->throwException("Company with id $companyId not found!");
        }

        $this->company = $companyId;
    }

    public function for(Company $company)
    {
        $this->company = $company->id;
        return $this;
    }

    public function getDataConnection()
    {
        return collect(config('database.connections'))->where('company_id', $this->company)->first();
    }

    function prepareConnection()
    {
        try {
            $cx = $this->getDataConnection();
            $this->connection = ibase_connect($cx['path'], $cx['username'], $cx['password']);
        } catch (Exception $x) {
            $this->connection = null;
        }
    }

    public function checkConnection()
    {
        if (!$this->connection) {
            $this->throwException("No connection with BEA DB. Company ID $this->company");
        }
    }

    /**
     * @param $sql
     * @throws Exception
     */
    function statement($sql)
    {
        $this->prepareConnection();
        $this->checkConnection();
        ibase_query($this->connection, $sql) or self::throwException(ibase_errmsg());
    }

    /**
     * @param $query
     * @return Collection
     * @throws Exception
     */
    function select($query)
    {
        $this->prepareConnection();
        $this->checkConnection();

        $data = collect([]);
        $result = ibase_query($this->connection, $query) or self::throwException(ibase_errmsg(), $query);

        while ($row = ibase_fetch_object($result)) {
            $data->push((object)$row);
        }

        return $data;
    }

    /**
     * @param $message
     * @param null $query
     * @throws Exception
     */
    function throwException($message, $query = null)
    {
        throw new Exception($message.". SQL $query");
    }
}