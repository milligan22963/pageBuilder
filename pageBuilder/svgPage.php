<?php
/*
 * Created on Jan 09, 2016
 *
 * Author: D.W. Milligan
 * Copyright: AFM Software 2016
 * Project: aquasim
 * File: svgPage.php
 */

define("SVG_ATTRIBUTE_NAME", "0");
define("SVG_ATTRIBUTE_VALUE", "1");

class SVGDataObject
{
  var $m_name;
  var $m_value;
  var $m_attrList;
  var $m_attrIndex;
  var $m_childDataObject;
  var $m_childIndex;
  var $m_xPosition;
  var $m_yPosition;
  var $m_width;
  var $m_height;
  var $m_style;

  function __construct()
  {
    $this->m_name = null;
    $this->m_value = null;
    $this->m_childDataObject = array();
    $this->m_childIndex = 0;
    $this->m_xPosition = null;
    $this->m_yPosition = null;
    $this->m_width = null;
    $this->m_height = null;
    $this->m_style = null;
    $this->resetAttributes();
  }

  function resetAttributes()
  {
    $this->m_attrList = array();
    $this->m_attrList[SVG_ATTRIBUTE_NAME] = array();
    $this->m_attrList[SVG_ATTRIBUTE_VALUE] = array();
    $this->m_attrIndex = 0;	  
  }
  
  function setPosition($x, $y)
  {
    $this->m_xPosition = $x;
    $this->m_yPosition = $y;
  }

  function setDimensions($width, $height)
  {
    $this->m_width = $width;
    $this->m_height = $height;
  }

  function setStyle($style)
  {
    $this->m_style = $style;
  }

  function addBaseAttributes()
  {
	  if ($this->m_xPosition != null)
	  {
	  	$this->addAttribute("x", $this->m_xPosition);
	  }
	  
	  if ($this->m_yPosition != null)
	  {
	  	$this->addAttribute("y", $this->m_yPosition);
	  }

	  if ($this->m_width != null)
	  {
	  	$this->addAttribute("width", $this->m_width);
	  }
	  
	  if ($this->m_height != null)
	  {
	  	$this->addAttribute("height", $this->m_height);
	  }
	  
	  if ($this->m_style != null)
	  {
	  	$this->addAttribute("style", $this->m_style);
	  }
  }
  
  function renderObject()
  {
	  $this->addBaseAttributes();
	  
  	$renderedText = "";
  	
  	if ($this->m_name != null)
  	{
    	$renderedText = "<";
    	$renderedText .= $this->m_name;
  	
	    if ($this->m_attrIndex > 0)
	    {
	      # Add the attributes if any
	      for ($attrIndex = 0; $attrIndex < $this->m_attrIndex; $attrIndex++)
	      {
	        $renderedText .= " ";
	        $renderedText .= $this->m_attrList[SVG_ATTRIBUTE_NAME][$attrIndex];
	        $renderedText .= "=\"";
	        $renderedText .= $this->m_attrList[SVG_ATTRIBUTE_VALUE][$attrIndex];
	        $renderedText .= "\"";
	      }
	      $renderedText .= ">";
	    }
	    else
	    {
	      $renderedText .= ">";
	    }
  	}
    if ($this->m_value !== null)
    {
      $renderedText .= $this->m_value;
    }

    for ($childIndex = 0; $childIndex < $this->m_childIndex; $childIndex++)
    {
      $renderedText .= $this->m_childDataObject[$childIndex]->renderObject();
    }

  	if ($this->m_name !== null)
  	{
	    $renderedText .= "</";
	    $renderedText .= $this->m_name;
	    $renderedText .= ">";
  	}
    return $renderedText;
  }

  function setName($name)
  {
    $this->m_name = $name;
  }

  function getName()
  {
    return $this->m_name;
  }

  function setValue($value)
  {
    $this->m_value = $value;
  }

  function getValue()
  {
    return $this->m_value;
  }

  function addAttribute($attributeName, $attributeValue)
  {
    $this->m_attrList[SVG_ATTRIBUTE_NAME][$this->m_attrIndex] = $attributeName;
    $this->m_attrList[SVG_ATTRIBUTE_VALUE][$this->m_attrIndex] = $attributeValue;
    $this->m_attrIndex++;
  }

  function getAttributeCount()
  {
    return $this->m_attrIndex;
  }

  function getAttributeName($attrIndex)
  {
    if ($attrIndex < $this->m_attrIndex)
    {
      return $this->m_attrList[SVG_ATTRIBUTE_NAME][$attrIndex];
    }
    else
    {
      return "Invalid";
    }
  }

  function getAttributeValue($attrIndex)
  {
    if ($attrIndex < $this->m_attrIndex)
    {
      return $this->m_attrList[SVG_ATTRIBUTE_VALUE][$attrIndex];
    }
    else
    {
      return "Invalid";
    }
  }
  
  /*
   * addChild
   * 
   * to add a child to the current node
   * 
   * @param - $name - the name of the child node
   * @param - $value - the value for the child node if any
   * 
   * @return - the newly added/created child node
   */
  function addChild($name, $value = null)
  {
  	$childDataObject = new XmlDataObject();
  	$childDataObject->setName($name);
  	
  	if ($value != null)
  	{
	  	$childDataObject->setValue($value);
  	}
  	
  	$this->addChildObject($childDataObject);
  	
  	return $childDataObject;
  }

  function addChildObject($childDataObject)
  {
    $this->m_childDataObject[$this->m_childIndex] = $childDataObject;
    $this->m_childIndex++;
  }

  function getChildCount()
  {
    return $this->m_childIndex;
  }

  function getChildObject($childIndex)
  {
    if ($childIndex < $this->m_childIndex)
    {
      return $this->m_childDataObject[$childIndex];
    }
    else
    {
      return "none";
    }
  }
  
  function findChild($name)
  {
  	$childObject = array();
  	
  	foreach ($this->m_childDataObject as $childDataObject)
  	{
  		if ($childDataObject->getName() == $name)
  		{
  			$childObject[] = $childDataObject;
  		}
  		else
  		{
  			if ($childDataObject->getChildCount() > 0)
  			{
  				$subchildObject = $childDataObject->findChild($name);
  				if ($subchildObject != null)
  				{
  					/* Merge will overwrite keys and this array is all of the same type */
  					foreach ($subchildObject as $subChild)
  					{
  						$childObject[] = $subChild;
  					}
  				}
  			}
  		}
  	}
  	
  	if (count($childObject) == 0)
  	{
  		$childObject = null;
  	}
  	return $childObject;
  }
};

class SVGGroup extends SVGDataObject
{
    function __construct()
    {
        parent::__construct();
        
        $this->setName("g");
    }
        
    function renderObject()
    {
	    parent::resetAttributes();
	    
	    // add our attributes
	    
	    // render our page
	    return parent::renderObject();
	}	
};

class SVGRectangle extends SVGDataObject
{
    function __construct()
    {
        parent::__construct();
        
        $this->setName("rect");
    }
        
    function renderObject()
    {
	    parent::resetAttributes();
	    
	    // add our attributes
	    
	    // render our page
	    return parent::renderObject();
	}
};

class SVGText extends SVGDataObject
{
    function __construct()
    {
        parent::__construct();
        
        $this->setName("text");
    }
        
    function renderObject()
    {
	    parent::resetAttributes();
	    
	    // add our attributes

//		$renderedText = parent::renderObject();
//	    error_log("Rendering text: " . $renderedText);
			    
	    // render our page
	    return parent::renderObject();
	}
};

class SVGEmbeddedFile extends SVGDataObject
{
	function __construct()
	{
        parent::__construct();
        
        $this->setName("svg");		
	}
    
    function loadFile($fileName)
    {
	    $svgFileData = file_get_contents($fileName);
	    
	    if ($svgFileData !== FALSE)
	    {
		    $this->setValue($svgFileData);
	    }
    }
    
    function replaceToken($token, $value)
    {
	    // replace the token with the value specified
	    $this->m_value = str_replace($token, $value, $this->m_value);
    }
};

class SVGPageData extends SVGDataObject
{
    var $m_startSession;            /*!< true to start a session on this page */
    var $m_languageSelection;       /*!< The language to specify */
    var $m_directDisplay;           /*!< True to echo directly, false to return the page data */

    function __construct()
    {
        parent::__construct();
        $this->m_languageSelection = "EN";
        $this->m_directDisplay = false; /* default to not direct display */
    }
        
    function setDirectDisplay($directDisplay) { $this->m_directDisplay = $directDisplay; }
    function getDirectDisplay() { return $this->m_directDisplay; }
    
    function setLanguageSelection($languageSelection)
    {
        $this->m_languageSelection = $languageSelection;
    }
    
    function getLanguageSelection()
    {
        return $this->m_languageSelection;
    }

    function renderPage()
    {
        $renderedPage = $this->renderObject();

        # If they want us to dump then do it otherwise return it
        if ($this->m_directDisplay)
        {
            # Set the appropriate headers for this document
            header("Content-Type: image/svg+xml");

			$pageStart = <<<DOC_TYPE_SNIPPET
<?xml version="1.0" standalone="no" ?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">

DOC_TYPE_SNIPPET;

			echo $pageStart;			
            echo $renderedPage;
        }
        else
        {
            return $renderedPage;
        }
    }
};

?>
