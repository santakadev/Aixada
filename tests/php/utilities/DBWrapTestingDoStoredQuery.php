<?php

require_once(__DIR__.'/../../../php/inc/database.php');

final class DBWrapTestingDoStoredQuery extends DBWrap
{
    private $executedQuery;

    public function __construct()
    {
    }

    public function do_stored_query($strSQL)
    {
        $this->executedQuery = $strSQL;
    }

    /**
     * @return mixed
     */
    public function executedQuery()
    {
        return $this->executedQuery;
    }
}
