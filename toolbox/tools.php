<?php
function convertStringToBoolean($dataString)
{
	$retValue = false;
	if (strcasecmp($dataString, "true") == 0)
	{
		$retValue = true;
	}
	
	return $retValue;
}

// from http://php.net/manual/en/function.explode.php
function multiExplode($delimiters, $string)
{
   
    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return  $launch;
}

define('LOG_SYSTEM_ALL', 0);
define('LOG_SYSTEM_TRACE', 1);
define('LOG_SYSTEM_INFO', 2);
define('LOG_SYSTEM_WARNING', 4);
define('LOG_SYSTEM_ERROR', 8);

class LogToFile
{
	private static $m_instance = null;
	private $m_fileName;
	private $m_currentLogLevel;
	
	private function __construct()
	{
		$this->LogToFile();
	}
	
	private function LogToFile()
	{
		$this->m_fileName = dirname(__FILE__) . '/../system.log';
		$this->m_currentLogLevel = LOG_SYSTEM_ERROR;
	}
	
	static function getInstance()
	{
		if (self::$m_instance == null)
		{
			self::$m_instance = new LogToFile();
		}
		
		return self::$m_instance;
	}
	
	function setLogLevel($logLevel)
	{
		$this->m_currentLogLevel = $logLevel;
	}
	
	function setLogFileName($fileName)
	{
		$this->m_fileName = $fileName;
	}
	
	function logInformation($logLevel, $logMessage)
	{
		if ($logLevel >= $this->m_currentLogLevel)
		{
			$logFile=fopen($this->m_fileName, "a");
			if ($logFile)
			{
				$time = date("d M Y H:i:s"); 
		      	fputs($logFile, 'Date: ' . $time . ', LogLevel: ' . $logLevel . ' Message: ' . $logMessage); 
	    	  	fclose($logFile);
			}
		}
	}
}
?>