<?php
require_once("Database.php");

/**
 * Class Parser
 * 
 * In this class the xml file is parsed and the data inserted in the Database
 */
class Parser
{
    /**
     * @var SimpleXMLElement of the file
     */
    private $_xml;
    /**
     * @var bool use a given config or insert the data automatically
     */
    private $_config;

    /**
     * 
     */
    public function __construct($filename, $useConfig, $dbName = "", $tableStructure = array()) 
    {
        $xml = simplexml_load_file($filename);

        $this->_xml = $xml;
        $this->_config = $useConfig;
    }

    /**
     * parseXmlToDB define wich kind of parsing is needed
     */
    function parseXmlToDB() 
    {
        if ($this->_config) 
        {
            return $this->parseXMLToDB_config();
        }
        return $this->parseXMLToDB_auto();
    }

    /**
     * parseXMLToDB_auto
     * 
     * insert into db the xml data with the following scheme
     * <db><table><column>data</column> [...] </table> [...] </db>
     */
    function parseXMLToDB_auto()
    {
        // create DB
        $db = new Database($this->_xml->getName());

        foreach ($this->_xml->children() as $table) 
        {
            //check if table exist or create table
            $tableName = $table->getName();
            $db->checkCreateTable($tableName);

            //query column preparation
            $queryColumns = array();
            //query values preparation
            $queryValues = array();

            foreach ($table->children() as $column => $value) 
            {
                array_push($queryColumns,$column);
                array_push($queryValues,"'" . $db->escapeString($value->__toString()) . "'");
                //check if column exist or add it
                $db->checkCreateColumn($tableName, $column, "text");
            }
            // insert values to table
            $db->checkInsertValues($tableName, $queryColumns, $queryValues);
        }
    }

    /**
     * @TODO configurable
     */
    function parseXMLToDB_config() : String 
    {
        return "";
    }
    
    /**
     * 
     */
    public function __destruct() 
    {
    }
}

?>