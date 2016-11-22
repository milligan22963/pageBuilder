<?php

function storeOptions(& $optionArray, $optionSetElement)
{
  $optionList = $optionSetElement->getElementsByTagName("option");
  foreach ($optionList as $optionNode)
  {
    if ($optionNode->hasAttributes())
    {
      $name = null;
      $value = null;

      foreach ($optionNode->attributes as $option)
      {
        if ($option->name == "name")
        {
          $name = $option->value;
        }
        if ($option->name == "value")
        {
          $value = $option->value;
        }

        if (($name != null) && ($value != null))
        {
        	//error_log("Defining: $name");
          define($name, $name);
          $optionArray[$name] = $value;
          $name = null;
          $value = null;
        }
      }
    }
  }
}

function loadXmlData($optionList, $xmlData)
{
  $optionArray = array();

  $xmlDoc = new DOMDocument();

  $xmlDoc->loadXML($xmlData);

  foreach ($optionList as $option)
  {
    $domList = $xmlDoc->getElementsByTagName($option);
    foreach ($domList as $domNode)
    {
      storeOptions($optionArray, $domNode);
    }
  }

  return $optionArray;	
}

function loadFile($optionList, $fileName)
{
  $optionArray = array();

  $xmlDoc = new DOMDocument();

  $xmlDoc->load($fileName);

  foreach ($optionList as $option)
  {
    $domList = $xmlDoc->getElementsByTagName($option);
    foreach ($domList as $domNode)
    {
      storeOptions($optionArray, $domNode);
    }
  }

  return $optionArray;
}

/*
$g_configArray = array();

loadFile($g_configArray, array("database", "site"), "config.xml");

print_r($g_configArray);

print "Done."; */
?>
