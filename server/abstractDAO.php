<?php

mysqli_report(MYSQLI_REPORT_STRICT);

class abstractDAO {
    protected $mysqli;
    private static $DB_HOST = 'localhost';
    private static $DB_USERNAME = 'root';
    private static $DB_PASSWORD = '';
    private static $DB_DATABASE = 'blog';

    function __construct(){
        try{
            $this->mysqli = new mysqli(self::$DB_HOST, self::$DB_USERNAME, self::$DB_PASSWORD, self::$DB_DATABASE);
        } catch(mysqli_sql_exception $e){
            throw $e;
        }
    }

    public function getMysqli(){
        return $this->mysqli;
    }
}
?>
