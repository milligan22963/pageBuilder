<?php

function getPageHeader(& $page, $themePath, $displayPosition, $callOrder)
{
	$headerData = null;
	
	if ($displayPosition == DISPLAY_PRE)
	{
		if ($callOrder == FIRST_CALL)
		{
			$headerData = <<<HEADER_DATA
	<div id="header">
	  <div class="headertitle">
HEADER_DATA;
		}
		elseif ($callOrder == SECOND_CALL)
		{
			$systemObject = getSystemObject();
		
			$titleText = $systemObject->getConfigurationData(SITE_TITLE);
			$headerData = <<<HEADER_DATA
	  $titleText
	  </div>
HEADER_DATA;
		}
	}
	else
	{
		if ($callOrder == FIRST_CALL)
		{
			$loginInstance = UserSession::getInstance();
		
			if ($loginInstance->isLoggedIn() == true)
			{
				$displayText = "Welcome " . $loginInstance->getUserName();
			}
			else
			{
				$displayText = "Please login...";
			}
			$headerData = <<<HEADER_DATA
		<div class="headerdescr">
		$displayText
HEADER_DATA;
		}
		elseif ($callOrder == SECOND_CALL)
		{
			$headerData = <<<HEADER_DATA
		</div>
		</div>
HEADER_DATA;
		}
	}
  if ($headerData != null)
  {
  	$page->addBodyData($headerData);
  }
}

?>
