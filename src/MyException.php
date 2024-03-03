<?php

/**
 * Class MyException
 * 
 * Manage the Exceptions and put them in a log file
 * If needed close the program
 */
class MyException extends Exception
{
    /**
     * @var string The path to the error log file.
     */
    private $_logfile = __DIR__ . "/../Log/error_log";  

    private $_class;
    private $_function;
    private $_line;

    /**
     * {@inheritdoc}
     * Constructor.
     *
     * This constructor initializes the Exception object with more parameters to be able to log everything.
     *
     * @param string $message   the exception's message.
     * @param string $class     the class who threw the exception.
     * @param string $function  the function who threw the exception.
     * @param int $line         the line of code who threw the exception.
     * @param bool $exit define if the whole program has to be stopped after the exception.
     * 
     */
    public function __construct($message, $class, $function, $line, $exit, $code = 0, Throwable $previous = null) 
    {
        parent::__construct($message, $code, $previous);
        $this->_class = $class;
        $this->_function = $function;
        $this->_line = $line;
    	$this->logError($message);
        if($exit)
        {
            exit();
        }
    }

    /**
     * __toString       format the error message
     * 
     * @return string date(Y.m.d h:i:s:m); class; function; line; code; message
     */
    public function __toString() 
    {
        return date("Y.m.d h:i:s:m", $_SERVER['REQUEST_TIME']) . "; Class: {$this->_class}; Function: {$this->_function}; Line: {$this->line}; Code: [{$this->code}] : {$this->message}\n";
    }

    /**
     * logError         put the errors in the log file
     */
    public function logError($message) 
    {
        if($message != "")
        {
            $formattedMsg = $this->__toString();
            file_put_contents($this->_logfile, $formattedMsg,FILE_APPEND);
        }
    }

    /**
     * 
     */
    public function __destruct() 
    {
    }
}


?>