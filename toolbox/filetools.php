<?php
/*
 * Created on May 28, 2007
 *
 * Author: 	daniel
 * File:   	filetools.php
 * Project: toolbox
 * 
 * Purpose:
 * 			Various file realted tools useful regardless of site
 */

 /**
  * Check to see if this file was the page the user loaded or included...
  * 
  * @param $fileName which is the file to check to see if it was loaded or included i.e. filetools.php to see if
  *        this file was loaded or included.
  * 
  * @return true if loaded, otherwise false
  */
 function isFileLoaded($fileName)
 {
	 $fileLoaded = strpos($_SERVER['SCRIPT_FILENAME'], $fileName);
	 
	 return $fileLoaded;
 }
 
 /*
  * getMyDirectory
  * 
  * used to determine the current files directory
  */
 function getMyDirectory($fileName)
 {
 	$returnName = dirname($fileName);
 	
 	return $returnName;
 }
 
 /*
  * getMyDirectoryName
  * 
  * used to get just the directory name and not the full path
  */
 function getMyDirectoryName($fileName)
 {
 	$returnName = basename(dirname($fileName));
 	
 	return $returnName;
 }
 
?>
