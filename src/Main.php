<?php
require_once("MyException.php");
require_once("Parser.php");

/**
 * Class Main
 * 
 * This is the class lauched from the cmd.
 * It will manage the input/output of the user and call other classes to work with the data
 */
class Main
{

    /**
     * 
     */
    public function __construct() 
    {
        $filename = $this->getFileName();
        $parser = new Parser($filename, false);
        echo $parser->parseXmlToDB();
    }

    /**
     * getFileName      check and manage the command line arguments
     * 
     * @return String $filename     the path of the existing file
     */
    function getFileName() : String 
    {
        // Check if command line arguments are provided
        if ($_SERVER['argc'] < 2) 
        {
            new MyException("No arguments provided", __CLASS__, __FUNCTION__, __LINE__, true);
        }

        // Extract command line arguments
        $arguments = array_slice($_SERVER['argv'], 1);
        if (count($arguments) > 1) 
        {
            new MyException("Too many arguments provided, only the first will be used", __CLASS__, __FUNCTION__, __LINE__, false);
        }

        //get file if exist
        $filename = $arguments[0];
        if(file_exists($filename))
        {
            return $filename;
        }
        else
        {
            new MyException("The file doesn't exist", __CLASS__, __FUNCTION__, __LINE__, true);
        }
    }

    /**
     * 
     */
    public function __destruct() 
    {
    }
}

new Main();

?>