<?php
function getWidgetHeader($title)
{
	$returnData = '<div class="extensiontitle">' . $title . "</div>" . '<hr class="extensionline"/>';
	return $returnData;
}

function loadThemeOverrides()
{
	// Override to specific theme for JUI
//	setCssPath("JUI", "js/jQuery/themes/dark-hive");
}
?>
