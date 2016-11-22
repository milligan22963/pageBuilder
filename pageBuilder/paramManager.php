<?php

/*
 * paramManager
 * 
 * an object that can load an xml based command file indicating which params are needed for which command.  This would be base
 * functionality for any class that needs to be able to determine if a command has all of the required parameters
 * in order to run properly
 */

class ParamManager
{
	private $m_commandArray;
	
	function __construct()
	{
		$this->m_commandArray = array();
	}
		
	/*
	 * registerParam
	 * 
	 * allows the user to register each param for incoming xml requests
	 * and indicate if its required or not based on the command
	 * 
	 * @param $command - the command that the parameter applies to
	 * @param $name - the name of the param
	 * @param $required - true if required for this command, false otherwise
	 * 
	 * @return none
	 */
	function registerParam($command, $name, $required)
	{
		if (array_key_exists($command, $this->m_commandArray) != true)
		{
			$this->m_commandArray[$command] = array();
		}
		$this->m_commandArray[$command][$name] = $required;
	}
	
	function validateParams($command, $paramArray)
	{
		$success = false;
		if (array_key_exists($command, $this->m_commandArray) == true)
		{
			$success = true;
			$validateArray = $this->m_commandArray[$command];
			
			foreach ($validateArray as $name=>$required)
			{
				if ($required == true)
				{
					if (array_key_exists($name, $paramArray) == false)
					{
error_log("Required parameter missing: " . $name);
						$success = false;
					}
				}
			}
		}
		else
		{
			error_log('Param Man - Command doesnt exist: ' . $command);
			error_log('Known commands: ' . print_r($this->m_commandArray, true));
//			error_log('Param Array: ' . print_r($paramArray, true));
		}
		return $success;
	}
	
	function loadCommandFile($commandFile)
	{
		// Load in the command file and register our commands/params
  		$xmlDoc = new DOMDocument();

  		$xmlDoc->load($commandFile);

  		// Load all of the commands in
  		$domList = $xmlDoc->getElementsByTagName("command");
  		foreach ($domList as $domNode)
  		{
  			// as it should
  			if ($domNode->hasAttributes())
  			{
  				$commandName = "none";
		      	foreach ($domNode->attributes as $attr)
		      	{
		        	if ($attr->name == "name")
		        	{
		          		$commandName = $attr->value;
		        	}
		      	}
  				if ($commandName != "none")
  				{
  					$paramList = $domNode->getElementsByTagName("param");
					foreach ($paramList as $paramNode)
					{
	  					$paramName = null;
	  					$paramRequired = null;
	      				foreach ($paramNode->attributes as $attr)
	      				{
	        				if ($attr->name == "name")
	        				{
	          					$paramName = $attr->value;
	        				}
	        				if ($attr->name == "required")
	        				{
	        					if ($attr->value == "true")
	        					{
	          						$paramRequired = true;
	        					}
	        					else
	        					{
	        						$paramRequired = false;
	        					}
	        				}
	      				}
	      				
	      				if (($paramName != null) && ($paramRequired !== null))
	      				{
//	      					error_log("registering: " . $commandName);
	      					$this->registerParam($commandName, $paramName, $paramRequired);
	      				}
	      				else if ($paramRequired == null)
	      				{
	      					error_log('paramRequired is null for:' . $commandName);
	      				}
					}
  				}
  			}
  		}
//		error_log(print_r($this->m_commandArray, true));
	}
}
?>
