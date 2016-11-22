<?php
include_once '../sitesetup.php';
include_once 'page.php';
include_once 'flattenjs.php';

$index = new Page();
$index->setTitle('Test Flatten');

$sourceFile = 'js/textObject.js';
$destFile = '../testing.js';
$sourceFP = fopen($sourceFile, 'r');

$statResults = fstat($sourceFP);
fclose($sourceFP);

$index->addBodyData('Before flattening: ' . $statResults['size'] . PHP_EOL);
file_put_contents($destFile, FlattenJavaScript('js/textObject.js'), FILE_APPEND);

$destFP = fopen($destFile, 'r');

$statResults = fstat($destFP);
fclose($destFP);
$index->addBodyData('After flattening: ' . $statResults['size'] . PHP_EOL);

$index->renderPage();
?>