<?php
function loadAdminOptions($sourceFile)
{
	$optionArray = array();
	
	$xmlPage = new XmlPageData();
	
	$xmlPage->loadXmlFile($sourceFile);
	
	/*
	 * Need to pull out the theme file data including
	 *   Name
	 *   Description
	 *   StyleSheet(s)
	 *   ImageDirectory
	 *   JavaScript Directory
	 */
	
	/* The main node is admin followed by each of the entries */
	$mainNode = $xmlPage->getChildObject(0);
	if ($mainNode != null)
	{
		$numChildren = $mainNode->getChildCount();
		for ($index = 0; $index < $numChildren; $index++)
		{
			$childObject = $mainNode->getChildObject($index);
			if ($childObject != null)
			{
				$storedName = null;
				$storedCode = null;
				$enabled = "false";
				$attrCount = $childObject->getAttributeCount();
				for ($attrIndex = 0; $attrIndex < $attrCount; $attrIndex++)
				{
					$attrName = $childObject->getAttributeName($attrIndex);
					$attrValue = $childObject->getAttributeValue($attrIndex);
					if ($attrName == "option")
					{
						$storedName = $attrValue;
					}
					elseif ($attrName == "code")
					{
						$storedCode = $attrValue;
					}
					elseif ($attrName == "enabled")
					{
						$enabled = $attrValue;
					}
				}
				
				if ($enabled == "true")
				{
					$optionArray[$storedName] = $storedCode;
				}
			}
		}	
	}
	return $optionArray;
}
?>