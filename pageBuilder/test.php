<?php

include_once('jsonPage.php');

/*
{"menu": {
  "id": "file",
  "value": "File",
  "popup": {
    "menuitem": [
      {"value": "New", "onclick": "CreateNewDoc()"},
      {"value": "Open", "onclick": "OpenDoc()"},
      {"value": "Close", "onclick": "CloseDoc()"}
    ]
  }
}}

{"menu": {
   "id": "file",
   "value": "File",
   "popup": {
      "menuitem": [
        {"value":"New","onclick":"CreateNewDoc()"},
        {"value":"Open","onclick":"OpenDoc()"}
	  ]
	}
}}
 */
$page = new JSONPageData();

$page->setName("menu");
$page->addChild("id", "file");
$page->addChild("value", "File");

$popObject = new JSONDataObject();
$popObject->setName("popup");
$page->addChildObject($popObject);

$arrayItem = new JSONArrayObject();
$arrayItem->setName("menuitem");

// add new array item
$arrayChild = new JSONDataObject();
$arrayLine = new JSONDataObject();
$arrayLine->setName("value");
$arrayLine->setValue("New");
$arrayChild->addChildObject($arrayLine);

$arrayLine = new JSONDataObject();
$arrayLine->setName("onclick");
$arrayLine->setValue("CreateNewDoc()");
$arrayChild->addChildObject($arrayLine);

$arrayItem->addChildObject($arrayChild);

// add open array item
$arrayChild = new JSONDataObject();
$arrayLine = new JSONDataObject();
$arrayLine->setName("value");
$arrayLine->setValue("Open");
$arrayChild->addChildObject($arrayLine);

$arrayLine = new JSONDataObject();
$arrayLine->setName("onclick");
$arrayLine->setValue("OpenDoc()");
$arrayChild->addChildObject($arrayLine);

$arrayItem->addChildObject($arrayChild);

// set the menu items array items
$popObject->addChildObject($arrayItem);

echo $page->renderPage();
?>
