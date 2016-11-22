<?php
/*
 * Created on Jul 21, 2005
 *
 * Author: D.W. Milligan
 * Copyright: AFM Software 2005
 * Project: package_name
 * File: displayObject.php
 */

define("COMMENT_DATA", "COMMENT_DATA");
define("HEADER_DATA", "HEADER_DATA");
define("META_DATA", "META_DATA");
define("STYLESHEET_DATA", "STYLESHEET_DATA");
define("STYLESHEET_LINK", "STYLESHEET_LINK");
define("JAVASCRIPT_DATA", "JAVASCRIPT_DATA");
define("JAVASCRIPT_LINK", "JAVASCRIPT_LINK");
define("TITLE_DATA", "TITLE_DATA");
define("BODY_DATA", "BODY_DATA");
 
class DisplayObject
{
    private $m_data;        /*!< The data to be displayed */
    private $m_type;        /*!< The type of data being displayed */
    
	public function __construct()
	{
        $this->m_data = "";
        $this->m_type = COMMENT_DATA;
    }
    	
    function setData($data) { $this->m_data = $data; }
    function getData() { return $this->m_data; }
    function addData($data) { $this->m_data .= $data; }

    function setDataType($type) { $this->m_type = $type; }
    function getDataType() { return $this->m_type; }
};

class HeaderObject extends DisplayObject
{
    function __construct()
    {
		parent::__construct();
		$this->setDataType(HEADER_DATA);
    }
};

class StyleSheetDataObject extends DisplayObject
{
    function __construct()
    {
		parent::__construct();
		$this->setDataType(STYLESHEET_DATA);
    }
};

class StyleSheetLinkObject extends DisplayObject
{
    function __construct()
    {
		parent::__construct();
		$this->setDataType(STYLESHEET_LINK);
    }
};

class TitleObject extends DisplayObject
{
    function __construct()
    {
		parent::__construct();
		$this->setDataType(TITLE_DATA);
    }
};

class BodyObject extends DisplayObject
{
    function __construct()
    {
		parent::__construct();
		$this->setDataType(BODY_DATA);
    }
};

class MetaDataObject extends DisplayObject
{	
	function __construct()
	{
		parent::__construct();
		$this->setDataType(META_DATA);
	}
	
	private function getNameValue($name, $value)
	{
		$metaData = $name . '=' . '"' . $value . '" '; // include space for more

		return $metaData;		
	}
	
	// typically will be name=XXXXX content=XXXXX
	function setNameContent($name, $content)
	{
		$metaData = $this->getNameValue("name", $name);
		$metaData .= $this->getNameValue("content", $content);
		
		$this->setData($metaData);
	}

	function setNameValue($name, $value)
	{
		$metaData = $name . '=' . '"' . $value . '" '; // include space for more

		$this->setData($metaData);
	}	
};

class JavaScriptObject extends DisplayObject
{
    function __construct()
    {
		parent::__construct();
		$this->setDataType(JAVASCRIPT_DATA);
    }
};

class JavaScriptLink extends DisplayObject
{
	private $canFlatten = false;
	
	function __construct()
	{
		parent::__construct();
		$this->setDataType(JAVASCRIPT_LINK);
	}
	
	function setCanFlatten($flatten)
	{
		$this->canFlatten = $flatten;
	}
	
	function getCanFlatten()
	{
		return $this->canFlatten;
	}
};
?>