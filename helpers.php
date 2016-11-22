<?php
$baseDir = dirname(__FILE__);

include_once $baseDir . '/pageBuilder/theme.php';

function includeFunctionFile($functionPath)
{
	if (file_exists($functionPath))
	{
		include_once "${functionPath}";
	}
}

function displayHeader(& $page, $themePath, $headerPath, $displayPosition)
{
	if (file_exists($headerPath))
	{
		include_once "${headerPath}";
		
		if (function_exists("getPageHeader"))
		{
			getPageHeader($page, $themePath, $displayPosition, FIRST_CALL);
			
			// display any extension widgets in this area
			$extensionManager = ExtensionManager::getInstance();

			$extensionManager->displayExtensions($page, $displayPosition, HEADER_CONTENT_AREA);
			
			getPageHeader($page, $themePath, $displayPosition, SECOND_CALL);
		}
		else
		{
			$page->addBodyData("No Satisfaction");
		}
	}
	else
	{
		$page->addBodyData("File doesnt exist: " . $headerPath);
	}
}

function displayHeaderWidget(& $page, $themePath, $headerPath, $displayPosition)
{
	if (file_exists($headerPath))
	{
		include_once "${headerPath}";
		
		if (function_exists("getHeaderWidget"))
		{
			getHeaderWidget($page, $themePath, $displayPosition, FIRST_CALL);
			
			// display any extension widgets in this area
			$extensionManager = ExtensionManager::getInstance();
			
			$extensionManager->displayExtensions($page, $displayPosition, HEADER_WIDGET_AREA);
			
			getHeaderWidget($page, $themePath, $displayPosition, SECOND_CALL);
		}
		else
		{
			$page->addBodyData("No Satisfaction");
		}
	}
	else
	{
		$page->addBodyData("File doesnt exist: " . $headerPath);
	}
}

function displayBody(& $page, $themePath, $bodyPath, $displayPosition)
{
	if (file_exists($bodyPath))
	{
		include_once "${bodyPath}";
		
		if (function_exists("getPageBody"))
		{
			getPageBody($page, $themePath, $displayPosition, FIRST_CALL);
			
			// display any extension widgets in this area
			$extensionManager = ExtensionManager::getInstance();
			
			$extensionManager->displayExtensions($page, $displayPosition, BODY_CONTENT_AREA);
			
			getPageBody($page, $themePath, $displayPosition, SECOND_CALL);
		}
		else
		{
			$page->addBodyData("No Satisfaction");
		}
	}
	else
	{
		$page->addBodyData("File doesnt exist: " . $bodyPath);
	}
}

function displayBodyWidget(& $page, $themePath, $bodyPath, $displayPosition)
{
	if (file_exists($bodyPath))
	{
		include_once "${bodyPath}";
		
		if (function_exists("getBodyWidget"))
		{
			getBodyWidget($page, $themePath, $displayPosition, FIRST_CALL);
			
			// display any extension widgets in this area
			$extensionManager = ExtensionManager::getInstance();
			
			$extensionManager->displayExtensions($page, $displayPosition, BODY_WIDGET_AREA);
			
			getBodyWidget($page, $themePath, $displayPosition, SECOND_CALL);
		}
		else
		{
			$page->addBodyData("No Satisfaction");
		}
	}
	else
	{
		$page->addBodyData("File doesnt exist: " . $bodyPath);
	}
}

function displayFooter(& $page, $themePath, $footerPath, $displayPosition)
{
	if (file_exists($footerPath))
	{
		include_once "${footerPath}";
		
		if (function_exists("getPageFooter"))
		{
			getPageFooter($page, $themePath, $displayPosition, FIRST_CALL);
			
			// display any extension widgets in this area
			$extensionManager = ExtensionManager::getInstance();
			
			$extensionManager->displayExtensions($page, $displayPosition, FOOTER_CONTENT_AREA);
			
			getPageFooter($page, $themePath, $displayPosition, SECOND_CALL);
		}
		else
		{
			$page->addBodyData("No Satisfaction");
		}
	}
	else
	{
		$page->addBodyData("File doesnt exist: " . $footerPath);
	}
}

function displayFooterWidget(& $page, $themePath, $footerPath, $displayPosition)
{
	if (file_exists($footerPath))
	{
		include_once "${footerPath}";
		
		if (function_exists("getFooterWidget"))
		{
			getFooterWidget($page, $themePath, $displayPosition, FIRST_CALL);
			
			// display any extension widgets in this area
			$extensionManager = ExtensionManager::getInstance();
			
			$extensionManager->displayExtensions($page, $displayPosition, FOOTER_WIDGET_AREA);
			
			getFooterWidget($page, $themePath, $displayPosition, SECOND_CALL);
		}
		else
		{
			$page->addBodyData("No Satisfaction");
		}
	}
	else
	{
		$page->addBodyData("File doesnt exist: " . $footerPath);
	}
}
?>