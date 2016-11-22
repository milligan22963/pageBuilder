<?php
/*
 * Created on Jul 21, 2005
 *
 * Author: D.W. Milligan
 * Copyright: AFM Software 2005
 * Project: package_name
 * File: page.php
 */
$baseSiteDir = System::getInstance()->getBaseSystemDir();

include_once $baseSiteDir . 'pageBuilder/flattenjs.php';
include_once $baseSiteDir . 'pageBuilder/displayObject.php';

define("DTD_STRICT", "DTD_STRICT");
define("DTD_TRANSITIONAL", "DTD_TRANSITIONAL");
define("DTD_FRAMESET", "DTD_FRAMESET");
define("DTD_HTML5", "DTD_HTML5");

define("PAGE_DOMAIN", "PAGE_DOMAIN");
define("PAGE_DOMAIN_USER", "user");
define("PAGE_DOMAIN_ADMIN", "admin");
define("PAGE_DOMAIN_OTHER", "other");
define("PAGE_DOMAIN_DEBUG", "debug");

define("BODY_ATTRIBUTE_NAME", "0");
define("BODY_ATTRIBUTE_VALUE", "1");
 
# This needs to be cleaned up to store objects by type
# Perhaps a multi dimensional array?
class PageData
{
    var $m_displayObjects;          /*!< Objects to display */
    var $m_startSession;            /*!< true to start a session on this page */
    var $m_totalDisplayObjects;     /*!< The total number of display objects */
    var $m_pageType;                /*!< The type of page this  will be */
    var $m_languageSelection;       /*!< The language to specify */
    var $m_directDisplay;           /*!< True to echo directly, false to return the page data */
    var $m_bodyAttributes;          /*!< The attributes associated with the page body */
    var $m_bodyAttributeIndex;
    var $m_pageDomain;				/*!< what domain does this page belong to? admin/user/debug/other */

    function __construct()
    {
    	$this->PageData();
    }
    
    function PageData()
    {
        $this->m_displayObjects = array();
        $this->m_displayObjects[COMMENT_DATA] = null;
        $this->m_displayObjects[HEADER_DATA] = null;
        $this->m_displayObjects[STYLESHEET_DATA] = null;
        $this->m_displayObjects[STYLESHEET_LINK] = null;
        $this->m_displayObjects[JAVASCRIPT_DATA] = null;
        $this->m_displayObjects[JAVASCRIPT_LINK] = null;
        $this->m_displayObjects[TITLE_DATA] = null;
        $this->m_displayObjects[BODY_DATA] = null;
        $this->m_displayObjects[META_DATA] = null;
        $this->m_totalDisplayObjects = 0;
        $this->m_languageSelection = "EN";
        $this->m_pageType = DTD_HTML5;
        $this->m_directDisplay = true; /* default to direct display */
        $this->m_bodyAttributes = array();
        $this->m_bodyAttributeIndex = 0;
        $this->m_bodyAttributes[BODY_ATTRIBUTE_NAME] = array();
        $this->m_bodyAttributes[BODY_ATTRIBUTE_VALUE] = array();
        $this->m_pageDomain = PAGE_DOMAIN_USER;
        
        // add defaut page meta for char-set
        $metaCharacterType = new MetaDataObject();
        $metaCharacterType->setNameValue("charset", "UTF-8");
        
        $this->addDisplayObject($metaCharacterType);
    }
    
    function setDirectDisplay($directDisplay) { $this->m_directDisplay = $directDisplay; }
    function getDirectDisplay() { return $this->m_directDisplay; }
    
    function setPageDomain($pageDomain)
    {
    	$this->m_pageDomain = $pageDomain;
    }
    
    function getPageDomain()
    {
    	return $this->m_pageDomain;
    }
    
    function setPageType($pageType)
    {
        $this->m_pageType = $pageType;
    }
    
    function getPageType()
    {
        return $this->m_pageType;
    }
    
    function setLanguageSelection($languageSelection)
    {
        $this->m_languageSelection = $languageSelection;
    }
    
    function getLanguageSelection()
    {
        return $this->m_languageSelection;
    }

    /*
     * return an array of display objects of the given type
     */
    function getDisplayObjects($objectType)
    {
    	$retArray = array();
    	
    	if (array_key_exists($objectType, $this->m_displayObjects))
    	{
    		foreach ($this->m_displayObjects[$objectType] as $object)
    		{
    			$retArray[] = $object;
    		}
    	}
    	return $retArray;
    }
    
    function addDisplayObject($displayObject)
    {
        # If there is an array then add it, otherwise create it and add it
        # I did it this way so the default path is to add it.  I am not sure
        # If this is faster or not.
        if ($this->m_displayObjects[$displayObject->getDataType()] != null)
        {
        	$bAddIt = true;
        	
        	/*
        	 * We need to ensure that scripts are not specified multiple times
        	 */
        	if ($displayObject->getDataType() == JAVASCRIPT_LINK)
        	{
        		foreach ($this->m_displayObjects[JAVASCRIPT_LINK] as $object)
        		{
        			if ($object->getData() == $displayObject->getData())
        			{
        				$bAddIt = false;
        			}
        		}
        	}
        	
        	/*
        	 * Ensure that each css file is unique
        	 */
        	if ($displayObject->getDataType() == STYLESHEET_LINK)
        	{
        		foreach ($this->m_displayObjects[STYLESHEET_LINK] as $object)
        		{
        			if ($object->getData() == $displayObject->getData())
        			{
        				$bAddIt = false;
        			}
        		}
        	}
        	
        	// CHeck for title data, replace what is there
        	if ($displayObject->getDataType() == TITLE_DATA)
        	{
        		/* Replace what is there with what is coming in */
            	$this->m_displayObjects[$displayObject->getDataType()] = array();
        	}
        	
        	if ($bAddIt)
        	{
	            $this->m_displayObjects[$displayObject->getDataType()][$this->m_totalDisplayObjects] = $displayObject;
        	}
        }
        else
        {
            $this->m_displayObjects[$displayObject->getDataType()] = array();
            $this->m_displayObjects[$displayObject->getDataType()][$this->m_totalDisplayObjects] = $displayObject;
        }
        $this->m_totalDisplayObjects++;
    }

    function addBodyAttribute($attributeName, $attributeValue)
    {
      $this->m_bodyAttributes[BODY_ATTRIBUTE_NAME][$this->m_bodyAttributeIndex] = $attributeName;
      $this->m_bodyAttributes[BODY_ATTRIBUTE_VALUE][$this->m_bodyAttributeIndex] = $attributeValue;
      $this->m_bodyAttributeIndex++;
    }

    function renderPage()
    {
        # Send the page type
        # Note the line breaks in the prints are intentional as they
        # will show up in the end output
        switch ($this->m_pageType)
        {
            case DTD_STRICT:
            default:
            {
                $docTypeString = <<<DOC_TYPE_SNIPPET
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//$this->m_languageSelection"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
DOC_TYPE_SNIPPET;
            }
            break;
        
            case DTD_TRANSITIONAL:
            {
                $docTypeString = <<<DOC_TYPE_SNIPPET
            
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//$this->m_languageSelection"
    "http://www.w3.org/TR/html4/loose.dtd">
            
DOC_TYPE_SNIPPET;
            }
            break;
        
            case DTD_FRAMESET:
            {
                $docTypeString = <<<DOC_TYPE_SNIPPET
            
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//$this->m_languageSelection"
    "http://www.w3.org/TR/html4/frameset.dtd">
                
DOC_TYPE_SNIPPET;
            }
            break;
            
            case DTD_HTML5:
            {
                $docTypeString = <<<DOC_TYPE_SNIPPET
	<!DOCTYPE html>                
DOC_TYPE_SNIPPET;
         	}
            break;
        }
        $renderedPage = $docTypeString . "\n<html";
        if ($this->m_pageType == DTD_STRICT)
        {
        	$renderedPage .= " xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"". $this->m_languageSelection . "\" xml:lang=\"";
        	$renderedPage .= $this->m_languageSelection . "\"";
        }
        
        // testing for svg
//        $renderedPage .= " xmlns:svg=\"http://www.w3.org/2000/svg\"";

		$renderedPage .= ">\n";
        
        # Send the header
        $renderedPage .= "<head>\n";        

        #------------------------------------------------------------------------
		# Does a favorite icon exist for this site?
        #------------------------------------------------------------------------
        $iconFiles = [
            "ico" => "favicon.ico",
            "png" => "favicon.png",
        ];

        $noIcon = true;

        foreach ($iconFiles as $type => $fileName)
        {
	        if (file_exists($fileName))
	        {
                $noIcon = false;
	    	    $renderedPage .= "<link rel=\"shortcut icon\" href=\"$fileName\" />";
                break;
	        }
        }

        if ($noIcon == true)
        {
        	$renderedPage .= "<!-- no icon -->";
        }
        
        #------------------------------------------------------------------------
        # Dump out titles, style sheet links and anything else that might be here
        #------------------------------------------------------------------------
        if ($this->m_displayObjects[TITLE_DATA] != null)
        {
            foreach ($this->m_displayObjects[TITLE_DATA] as $displayObject)
            {
                $objectText = $displayObject->getData();
                $displayData = <<<DISPLAY_DATA
                
                <title>$objectText</title>
                
DISPLAY_DATA;
                $renderedPage .= $displayData;
            }
        }
        else
        {
            $displayData = $_SERVER['PHP_SELF'];
            $renderedPage .= $displayData;
        }

		if ($this->m_displayObjects[META_DATA] != null)
		{
			$metaData = "";
			
			# dump out each meta item
			foreach ($this->m_displayObjects[META_DATA] as $metaObject)
			{
				$metaData .= '<meta ' . $metaObject->getData() . '>';
			}
			$renderedPage .= $metaData . "\n";
		}
        if ($this->m_displayObjects[HEADER_DATA] != null)
        {
            # Dump out each header item
            foreach ($this->m_displayObjects[HEADER_DATA] as $displayObject)
            {
                $renderedPage .= $displayObject->getData();
            }
        }

        if ($this->m_displayObjects[STYLESHEET_LINK] != null)
        {
            # Dump out each of the style sheet links
            foreach ($this->m_displayObjects[STYLESHEET_LINK] as $displayObject)
            {
                $objectText = $displayObject->getData();
                $displayData = <<<DISPLAY_DATA
    <link rel="stylesheet" href="$objectText" type="text/css" />
                
DISPLAY_DATA;

            $renderedPage .= $displayData;
            }
        }        

        if ($this->m_displayObjects[STYLESHEET_DATA] != null)
        {
            $renderedPage .= "<style type=\"text/css\">\n";
            # dump out any style sheet objects
            foreach ($this->m_displayObjects[STYLESHEET_DATA] as $displayObject)
            {
                $renderedPage .= $displayObject->getData();
            }
            $renderedPage .= "</style>\n";
        }
        
        if ($this->m_displayObjects[JAVASCRIPT_LINK] != null)
        {
        	$baseSiteDir = System::getInstance()->getBaseSystemDir();
        	$webAddr = System::getInstance()->getBaseScriptURL();
        	$compressedFile = $baseSiteDir . '/js/compressed.js'; //tempnam($baseSiteDir . '/js', 'compjs');
        	if (is_file($compressedFile) == TRUE)
        	{
	        	unlink($compressedFile);
        	}
        	$haveCompressedFile = false;
        	foreach ($this->m_displayObjects[JAVASCRIPT_LINK] as $displayObject)
        	{
        		if ($displayObject->getCanFlatten() == true)
        		{
	        		$jsFileName = $displayObject->getData();
    	    		$jsFileName = str_replace($webAddr, $baseSiteDir, $jsFileName);
        			file_put_contents($compressedFile, FlattenJavaScript($jsFileName), FILE_APPEND);
        			$haveCompressedFile = true;
        		}
        		else
        		{
		        	$renderedPage .= "<script ";
		        	if ($this->m_pageType != DTD_STRICT)
		        	{
		        		$renderedPage .= "language=\"javascript\" ";
		        	}
		        	$renderedPage .= "type=\"text/javascript\" src=\"";
					$renderedPage .= $displayObject->getData();
					$renderedPage .= "\" ></script>" . PHP_EOL;
        		}
        	}
        	if ($haveCompressedFile == true)
        	{
//        		rename($compressedFile, $compressedFile . '.js');
	        	$renderedPage .= "<script ";
	        	if ($this->m_pageType != DTD_STRICT)
	        	{
	        		$renderedPage .= "language=\"javascript\" ";
	        	}
	        	$renderedPage .= "type=\"text/javascript\" src=\"";
				$renderedPage .= $webAddr . 'js/' . basename($compressedFile);
//				$renderedPage .= $webAddr . 'js/' . basename($compressedFile) . '.js';
				$renderedPage .= "\" ></script>" . PHP_EOL;
        	}
        }        

        if ($this->m_displayObjects[JAVASCRIPT_DATA] != null)
        {        
            $renderedPage .= "<script ";
	        if ($this->m_pageType != DTD_STRICT)
	        {
	            $renderedPage .= "language=\"javascript\"";
	        }
            $renderedPage .= " type=\"text/javascript\">\n";
            # Now for any scripts
            foreach ($this->m_displayObjects[JAVASCRIPT_DATA] as $displayObject)
            {
                $renderedPage .= $displayObject->getData();
            }
            $renderedPage .= "\n</script>\n";
        }

        # End the head section
        $renderedPage .= "</head>\n";
 
        # Send the body
        $renderedPage .= "<body ";

        $bodyAttrData = "";

        # Any body specific attributes to be assigned?
        for ($attrIndex = 0; $attrIndex < $this->m_bodyAttributeIndex; $attrIndex++)
        {
          $bodyAttrData .= $this->m_bodyAttributes[BODY_ATTRIBUTE_NAME][$attrIndex];
          $bodyAttrData .= "=\"";
          $bodyAttrData .= $this->m_bodyAttributes[BODY_ATTRIBUTE_VALUE][$attrIndex];
          $bodyAttrData .= "\"";
        }

        $renderedPage .= $bodyAttrData;
        $renderedPage .= ">\n";
        if ($this->m_displayObjects[BODY_DATA] != null)
        {
            foreach ($this->m_displayObjects[BODY_DATA] as $displayObject)
            {
                $renderedPage .= $displayObject->getData();
            }
        }
        $renderedPage .= "</body>\n</html>";

        # If they want us to dump then do it otherwise return it
        if ($this->m_directDisplay)
        {
            echo $renderedPage;
        }
        else
        {
            return $renderedPage;
        }
    }
    
    function getDisplayObject($displayObject)
    {
        if (array_key_exists($displayObject, $this->m_displayObjects))
        {
            return $this->m_displayObjects[$displayObject];
        }
        else
        {
            return null;
        }
    }
    
    function getTotalDisplayObjects() { return $this->m_totalDisplayObjects; }  
};

class Page extends PageData
{
	/*
	 * __construct
	 */
	function __construct()
	{
		$this->PageData();
	}
	
	/*
	 * setTitle
	 * 
	 * sets the title for this page
	 * 
	 * @param $pageTitle - the title to be set - standard text no html
	 * 
	 * @return none
	 */
	function setTitle($pageTitle)
	{
		$titleData = new TitleObject();
		$titleData->setData($pageTitle);
		$this->addDisplayObject($titleData);
	}
	
	/*
	 * addStyleSheet
	 * 
	 * adds the specified style sheet to the page object
	 * 
	 * @param $styleSheet - the full URL to the style sheet to be added no html
	 * 
	 * @return the newly created object
	 */
	function addStyleSheet($styleSheet)
	{
		$styleSheetObj = new StyleSheetLinkObject();
		$styleSheetObj->setData($styleSheet);
		$this->addDisplayObject($styleSheetObj);
		
		return $styleSheetObj;
	}
	
	function addJavaScriptLink($jsLink)
	{
		$scriptObject = new JavaScriptLink();
		$scriptObject->setData($jsLink);
		$this->addDisplayObject($scriptObject);
		
		return $scriptObject;
	}
	
	/*
	 * addBodyData
	 * 
	 * To add an object to the body of the page
	 * 
	 * @param $bodyData - the text data to add to the page - html etc. ok other then <body></body>
	 * 
	 * @return the newly created body object
	 */
	function addBodyData($bodyData)
	{
		$bodyObject = new BodyObject();
		$bodyObject->setData($bodyData);
		$this->addDisplayObject($bodyObject);
		
		return $bodyObject;
	}
	
	function addInlineJavaScript($scriptData)
	{
		$inlineJavaScript = new JavaScriptObject();
		$inlineJavaScript->setData($scriptData);
		$this->addDisplayObject($inlineJavaScript);
		
		return $inlineJavaScript;
	}
};

?>
