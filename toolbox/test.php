<?php
include_once 'upload.php';
include_once("../sitesetup.php");
//include_once("../pageBuilder/page.php");
//include_once("../toolbox/filetools.php");
//include_once("../toolbox/scriptmanager.php");

echo '<html><header><title>Test Toolbox</title>';
$styleSheetData = <<<STYLESHEETDATA
<link rel="stylesheet" href="http://127.0.0.1/calfn8/calfn8.css" type="text/css" />
                    <link rel="stylesheet" href="http://127.0.0.1/calfn8/assets/themes/default/default.css" type="text/css" />
                    <link rel="stylesheet" href="http://127.0.0.1/calfn8/js/farbtastic/farbtastic.css" type="text/css" />
                    <style type="text/css">
                    #filelist
                    {
                      color: white;
                      background-color: red;
                    }
                    #fileselect
                    {
						position: absolute;
						margin: -5px 0 0 -175px;
						padding: 0;
						width: 220px;
						height: 30px;
						font-size: 14px;								
						opacity: 0;
						cursor: pointer;
						display: none;
						z-index:  9999;
					}
					iframe
					{
					display:none;
					}
					body
					{
						color: white;
					}
                    </style>
STYLESHEETDATA;

echo $styleSheetData;
echo "</header><body>";

# Are they uploading files or select them?
if (IsSet($_POST['uploading']))
{
	$systemObject = System::getInstance();

//	print_r($_POST);
error_log(	print_r($_FILES, true));
	$imageLocation = $systemObject->getUserContentPath(true);
	$userRepository = '../' . $imageLocation . 'admin/test/images';
	
	$userText = "Hi There";
	$returnArray = UploadFile($_FILES, $userRepository);
	foreach ($returnArray as $key=>$fileName)
	{
		echo 'Creating thumbnail for: ' . $fileName['name'] . '<br/>';
		IVCreateThumb($fileName['name'], 72, 72, true);
		
		$userText .= $fileName['name'] . ' ';
	}
	if (IsSet($_POST['iframe']))
	{
		$iframeContent =<<<IFRAME_CONTENT
				<script>
				  alert("${userText}");

				</script>
IFRAME_CONTENT;
	
		echo $iframeContent;
	}
}
else
{ 
	echo testUpload();
}
echo "</body>";
echo "</html>";
?>
