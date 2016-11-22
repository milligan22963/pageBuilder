<?php
/*
 * upload - used to upload files to the server
 * 
 * currently files can only be uploaded to the user content area
 * 
 * @param $fileArray - the array of files to upload - the assumption is this is a two deep array
 *                     such that each index points to an array with the indicies 'name' and 'error', 'tmp_name'
 * @param $destinationDirectory - the relative path for where the files should go
 * 
 * @return - array of files based on their current location - array[0] = array('file', 'size', 'type'), etc
 */

function UploadFile($fileArray, $destinationDirectory)
{
	$returnArray = Array();
	
	$systemObject = System::getInstance();

	// Prep the directory - assumes all content is going into the user content area
	// up and over since this is in the toolbox
	if (is_dir($destinationDirectory) == false)
	{
//		error_log('Creating directory: ' . $destinationDirectory);
    	mkdir($destinationDirectory, 0775, true); // create it recursively so we get everything
	}
	
	// For each file in the array, store it in the destination directory
	foreach ($fileArray as $indexName=>$index)
	{
        $fileName = basename($index['name']);
        $errorCode = $index['error'];
        if ($errorCode == UPLOAD_ERR_OK)
        {
            # Move this file from its current home to the users area
            $newFileName = $destinationDirectory . '/' . $fileName;

			if (!file_exists($newFileName))
            {
            	if (move_uploaded_file($index['tmp_name'], $newFileName))
                {
                	$detailArray = Array();
                	$detailArray['name'] = $newFileName;
                	$detailArray['size'] = $index['size'];
                	$detailArray['type'] = $index['type'];
                	array_push($returnArray, $detailArray);
                }
                else
                {
                    if ($errorCode != UPLOAD_ERR_NO_FILE)
                    {
                        switch ($errorCode)
                        {
                            case UPLOAD_ERR_INI_SIZE:
                            {
                                error_log("File: $fileName surpasses the max size for this site.");
                            }
                            break;
                            case UPLOAD_ERR_FORM_SIZE:
                            {
                                error_log("File: $fileName surpasses the max size for the form.");
                            }
                            break;
                            case UPLOAD_ERR_PARTIAL:
                            {
                                error_log("File: $fileName was only partially transferred.");
                            }
                            break;
                            case UPLOAD_ERR_CANT_WRITE:
                            {
                                error_log("File: $fileName - Cannot write to file area.");
                            }
                            break;
                            case UPLOAD_ERR_NO_TMP_DIR:
                            {
                                error_log("File: $fileName  - No temporary directory for file uploads.");
                            }
                            break;
                        } # end of switch ($errorCode)
                    }
                }
			}
        }
	}  // end foreach ($fileArray
	
	return $returnArray;
}

function testUpload()
{
	$maxFileSize = 10000000;
	$targetPage = "http://127.0.0.1/calfn8/toolbox/test.php";
    $uploadTable = <<<UPLOAD_TABLE
<script language="javascript" type="text/javascript" src="http://127.0.0.1/calfn8/js/jQuery/jquery.js" ></script>
<script language="javascript" type="text/javascript" src="../js/common.js" ></script>
<script language="javascript" type="text/javascript" src="js/upload.js" ></script>
<script language="javascript" type="text/javascript" src="http://127.0.0.1/calfn8/js/jQuery/ui/jquery.ui.core.js" ></script>
<script language="javascript" type="text/javascript" src="http://127.0.0.1/calfn8/js/jQuery/ui/jquery.ui.widget.js" ></script>
<script language="javascript" type="text/javascript" src="http://127.0.0.1/calfn8/js/jQuery/ui/jquery.ui.mouse.js" ></script>
<script language="javascript" type="text/javascript" src="http://127.0.0.1/calfn8/js/jQuery/ui/jquery.ui.draggable.js" ></script>
<script language="javascript" type="text/javascript" src="http://127.0.0.1/calfn8/js/jQuery/ui/jquery.ui.button.js" ></script>
<script language="javascript" type="text/javascript" src="http://127.0.0.1/calfn8/js/jQuery/ui/jquery.ui.position.js" ></script>
<script language="javascript" type="text/javascript" src="http://127.0.0.1/calfn8/js/jQuery/ui/jquery.ui.resizable.js" ></script>
<script language="javascript" type="text/javascript" src="http://127.0.0.1/calfn8/js/jQuery/ui/jquery.ui.dialog.js" ></script>

<script language="javascript" type="text/javascript" src="http://127.0.0.1/calfn8/js/farbtastic/farbtastic.js" ></script>
<script language="javascript" type="text/javascript" src="http://127.0.0.1/calfn8/js/jQuery/tools/jquery.tools.min.js" ></script>
<button id="showDialog" name="Upload..." onClick="javascript:UploadFiles('upload_file_dialog', '${targetPage}')">Upload...</button>
				<div id="upload_file_dialog" class="dialog hidden">
					<form id="test_form" class="ui-dialog">
					  <label>Test</label>
					</form>
				</div>
UPLOAD_TABLE;

    return $uploadTable;
}
?>