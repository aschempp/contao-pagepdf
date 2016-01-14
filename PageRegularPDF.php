<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *
 * PHP version 5
 * @copyright  terminal42 gmbh 2009-2013
 * @author     Andreas Schempp <andreas.schempp@terminal42.ch>
 * @author     Kamil Kuźmiński <kamil.kuzminski@terminal42.ch>
 * @license    LGPL
 */



class PageRegularPDF extends PageRegular
{

	/**
	 * URL to convert PDFs
	 */
	private $apiURL = 'http://convert.html2pdf.seven49.net/';

	/**
	 * Force PDF print
	 * @var boolean
	 */
	protected $blnPdf = false;


	/**
	 * Generate a regular page
	 * @param object
	 * @param boolean
	 */
	public function generate($objPage, $blnCheckRequest=false)
	{
		if ($this->Input->get('pdf') != 'page' && !$this->blnPdf)
		{
			parent::generate($objPage, $blnCheckRequest);
			return;
		}

		$objPage = $this->inheritPDFData($objPage);

		if ($objPage->pdfLayout)
		{
			$objPage->layout = $objPage->pdfLayout;
		}

		if (intval($objPage->pdfCache) > 0)
		{
			$objPage->cache = $objPage->pdfCache;
		}

		$strBuffer = null;

		// Disable gzip compression
		$GLOBALS['TL_CONFIG']['enableGZip'] = false;

		// Remove index.php fragment
		$strUrl = preg_replace('@^(index.php/)?([^\?]+)(\?.*)?@i', '$2', $this->Environment->request);

		// Add $_GET variables if alias usage is disabled
		if ($GLOBALS['TL_CONFIG']['disableAlias'])
		{
			$arrChunks = array();

			foreach (array_keys($_GET) as $key)
			{
				if ($key == 'id' || $key == 'articles' || $key == 'items' || $key == 'events')
				{
					$arrChunks[] = $key . '=' . $this->Input->get($key);
				}
			}

			$strUrl .= '?' . implode('&', $arrChunks);
		}

		// Rebuild URL to eliminate duplicate parameters
		else
		{
			$strUrl = (strlen($objPage->alias) ? $objPage->alias : $objPage->id);

			foreach (array_keys($_GET) as $key)
			{
				if ($key == 'day' || $key == 'page' || $key == 'id' || $key == 'file')
				{
					continue;
				}

				$strUrl .= '/' . $key . '/' . $this->Input->get($key);
			}

			$strUrl .= $GLOBALS['TL_CONFIG']['urlSuffix'];
		}

		// Create a unique key. Append .pdf if this is only the print view
		$strCacheFile = 'system/tmp/' . md5($this->Environment->base . $strUrl) . ($blnPDF ? '' : '.pdf');

		// Continue if the cache file does not exist
		if (file_exists(TL_ROOT . '/' . $strCacheFile))
		{
			$expire = null;

			// Include file
			ob_start();
			require_once(TL_ROOT . '/' . $strCacheFile);

			// File has not expired
			if ($expire >= time())
			{
				// Read buffer
				$strBuffer = base64_decode(ob_get_contents());
				ob_end_clean();
			}
			elseif ($objPage->pdfCache == '-1')
			{
				$latest = $this->Database->prepare("SELECT tstamp AS article_tstamp, (SELECT tstamp FROM tl_content WHERE tl_content.pid=tl_article.id ORDER BY tstamp DESC LIMIT 0,1) AS content_tstamp FROM tl_article WHERE pid=? ORDER BY tstamp DESC")->limit(1)->execute($objPage->id);
				$latest = $latest->article_tstamp > $objPage->tstamp ? ($latest->content_tstamp > $latest->article_tstamp ? $latest->content_tstamp : $latest->article_tstamp) : ($objPage->tstamp > $latest->content_tstamp ? $objPage->tstamp : $latest->content_tstamp);

				if ($latest <= $expire)
				{
					// Read buffer
					$strBuffer = base64_decode(ob_get_contents());
					ob_end_clean();
				}
			}
		}

		if (!$strBuffer)
		{
			// Get the template
			$strBuffer = $this->generateOriginal($objPage, $blnCheckRequest);

			// Write buffer to temporary file
			$strFile = (version_compare(VERSION, '3.0', '<') ? 'system/html/' : 'assets/pdf/') . uniqid() . '.html';
			$objFile = new File($strFile);
			$objFile->write($this->replaceInsertTags($strBuffer));
			$objFile->close();


			// Generate encoded url
			$arrURL = array();
			$arrParams = array
			(
				'UrlToRender'		=> $this->Environment->base.$strFile,
				'Orientation'		=> ($objPage->pdfOrientation ? $objPage->pdfOrientation : 'portrait'),
				'Title'				=> (strlen($objPage->pageTitle) ? $objPage->pageTitle : $objPage->title),
				'Author'			=> $objPage->pdfAuthor,
				'FileName'			=> $objPage->pdfFilename,
				'ImageQuality'		=> ($objPage->pdfImageQuality ? $objPage->pdfImageQuality : 75),
				'FooterText'		=> 'blank',
			);

			foreach( $arrParams as $k => $v )
			{
				$arrURL[] = $k . '=' . urlencode($v);
			}

			// Retrieve PDF
			$objRequest = new Request();
			$objRequest->send($this->apiURL . '?' . implode('&', $arrURL));

			// IMPORTANT! Drop temporary file, otherwise protected pages could be visible to anyone
			$objFile->delete();

			if ($objRequest->code < 200 || $objRequest->code > 299)
			{
				die('html2pdf Error: ' . $objRequest->code . ' ' . $objRequest->error);
			}

			$strBuffer = $objRequest->response;

			// Cache page if it is not protected
			if (empty($_POST) && !BE_USER_LOGGED_IN && ((!FE_USER_LOGGED_IN && intval($objPage->cache) > 0 && !$objPage->protected) || strlen($objPage->pdfCache)))
			{
				// Do not cache empty requests
				if (strlen($this->Environment->request) && $this->Environment->request != 'index.php')
				{
					$intCache = intval($objPage->cache) + time();

					// Create cache file
					$objFile = new File($strCacheFile);
					$objFile->write('<?php $expire = ' . $intCache . '; $content = "application/pdf"; ?>' . base64_encode($strBuffer));
					$objFile->close();
				}
			}
		}

		// Push PDF to screen
		header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
		header('Pragma: public');
		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Content-Length: '.strlen($strBuffer));
		header('Content-Type: application/pdf');
		header('Content-Disposition: attachement; filename="'.($objPage->pdfFilename ? $objPage->pdfFilename : $objPage->alias).'.pdf";');

		echo $strBuffer;

		exit;
	}


	/**
	 * Generate a regular page
	 *
	 * @param \PageModel $objPage
	 * @param boolean    $blnCheckRequest
	 */
	private function generateOriginal($objPage, $blnCheckRequest=false)
	{
		$GLOBALS['TL_KEYWORDS'] = '';
		$GLOBALS['TL_LANGUAGE'] = $objPage->language;

		\System::loadLanguageFile('default');

		// Static URLs
		$this->setStaticUrls();

		// Get the page layout
		$objLayout = $this->getPageLayout($objPage);

		// HOOK: modify the page or layout object (see #4736)
		if (isset($GLOBALS['TL_HOOKS']['getPageLayout']) && is_array($GLOBALS['TL_HOOKS']['getPageLayout']))
		{
			foreach ($GLOBALS['TL_HOOKS']['getPageLayout'] as $callback)
			{
				$this->import($callback[0]);
				$this->{$callback[0]}->{$callback[1]}($objPage, $objLayout, $this);
			}
		}

		/** @var \ThemeModel $objTheme */
		$objTheme = $objLayout->getRelated('pid');

		// Set the layout template and template group
		$objPage->template = $objLayout->template ?: 'fe_page';
		$objPage->templateGroup = $objTheme->templates;

		// Store the output format
		list($strFormat, $strVariant) = explode('_', $objLayout->doctype);
		$objPage->outputFormat = $strFormat;
		$objPage->outputVariant = $strVariant;

		// Initialize the template
		$this->createTemplate($objPage, $objLayout);

		// Initialize modules and sections
		$arrCustomSections = array();
		$arrSections = array('header', 'left', 'right', 'main', 'footer');
		$arrModules = deserialize($objLayout->modules);

		$arrModuleIds = array();

		// Filter the disabled modules
		foreach ($arrModules as $module)
		{
			if ($module['enable'])
			{
				$arrModuleIds[] = $module['mod'];
			}
		}

		// Get all modules in a single DB query
		$objModules = \ModuleModel::findMultipleByIds($arrModuleIds);

		if ($objModules !== null || $arrModules[0]['mod'] == 0) // see #4137
		{
			$arrMapper = array();

			// Create a mapper array in case a module is included more than once (see #4849)
			if ($objModules !== null)
			{
				while ($objModules->next())
				{
					$arrMapper[$objModules->id] = $objModules->current();
				}
			}

			foreach ($arrModules as $arrModule)
			{
				// Disabled module
				if (!$arrModule['enable'])
				{
					continue;
				}

				// Replace the module ID with the module model
				if ($arrModule['mod'] > 0 && isset($arrMapper[$arrModule['mod']]))
				{
					$arrModule['mod'] = $arrMapper[$arrModule['mod']];
				}

				// Generate the modules
				if (in_array($arrModule['col'], $arrSections))
				{
					// Filter active sections (see #3273)
					if ($arrModule['col'] == 'header' && $objLayout->rows != '2rwh' && $objLayout->rows != '3rw')
					{
						continue;
					}
					if ($arrModule['col'] == 'left' && $objLayout->cols != '2cll' && $objLayout->cols != '3cl')
					{
						continue;
					}
					if ($arrModule['col'] == 'right' && $objLayout->cols != '2clr' && $objLayout->cols != '3cl')
					{
						continue;
					}
					if ($arrModule['col'] == 'footer' && $objLayout->rows != '2rwf' && $objLayout->rows != '3rw')
					{
						continue;
					}

					$this->Template->{$arrModule['col']} .= $this->getFrontendModule($arrModule['mod'], $arrModule['col']);
				}
				else
				{
					$arrCustomSections[$arrModule['col']] .= $this->getFrontendModule($arrModule['mod'], $arrModule['col']);
				}
			}
		}

		$this->Template->sections = $arrCustomSections;

		// Mark RTL languages (see #7171)
		if ($GLOBALS['TL_LANG']['MSC']['textDirection'] == 'rtl')
		{
			$this->Template->isRTL = true;
		}

		// HOOK: modify the page or layout object
		if (isset($GLOBALS['TL_HOOKS']['generatePage']) && is_array($GLOBALS['TL_HOOKS']['generatePage']))
		{
			foreach ($GLOBALS['TL_HOOKS']['generatePage'] as $callback)
			{
				$this->import($callback[0]);
				$this->{$callback[0]}->{$callback[1]}($objPage, $objLayout, $this);
			}
		}

		// Set the page title and description AFTER the modules have been generated
		$this->Template->mainTitle = $objPage->rootPageTitle;
		$this->Template->pageTitle = $objPage->pageTitle ?: $objPage->title;

		// Meta robots tag
		$this->Template->robots = $objPage->robots ?: 'index,follow';

		// Remove shy-entities (see #2709)
		$this->Template->mainTitle = str_replace('[-]', '', $this->Template->mainTitle);
		$this->Template->pageTitle = str_replace('[-]', '', $this->Template->pageTitle);

		// Fall back to the default title tag
		if ($objLayout->titleTag == '')
		{
			$objLayout->titleTag = '{{page::pageTitle}} - {{page::rootPageTitle}}';
		}

		// Assign the title and description
		$this->Template->title = strip_insert_tags($this->replaceInsertTags($objLayout->titleTag)); // see #7097
		$this->Template->description = str_replace(array("\n", "\r", '"'), array(' ' , '', ''), $objPage->description);

		// Body onload and body classes
		$this->Template->onload = trim($objLayout->onload);
		$this->Template->class = trim($objLayout->cssClass . ' ' . $objPage->cssClass);

		// Execute AFTER the modules have been generated and create footer scripts first
		$this->createFooterScripts($objLayout);
		$this->createHeaderScripts($objPage, $objLayout);

		// Print the template to the screen
		$buffer = $this->Template->parse();

		// Replace insert tags and then re-replace the request_token tag in case a form element has been loaded via insert tag
		$buffer = $this->replaceInsertTags($buffer, false);
		$buffer = str_replace(array('{{request_token}}', '[{]', '[}]'), array(REQUEST_TOKEN, '{{', '}}'), $buffer);
		$buffer = $this->replaceDynamicScriptTags($buffer); // see #4203

		return $buffer;
	}


	protected function inheritPDFData($objPage)
	{
		$objParentPage = $objPage;

		while( $objParentPage->pid > 0 && (!strlen($objPage->pdfAuthor) || !strlen($objPage->pdfFilename) || !strlen($objPage->pdfOrientation) || !strlen($objPage->pdfImageQuality) || !strlen($objPage->pdfCache) || !$objPage->pdfLayout) )
		{
			$objParentPage = $this->Database->prepare("SELECT * FROM tl_page WHERE id=?")->limit(1)->execute($objParentPage->pid);

			if (!strlen($objPage->pdfAuthor)) $objPage->pdfAuthor = $objParentPage->pdfAuthor;
			if (!strlen($objPage->pdfFilename)) $objPage->pdfFilename = $objParentPage->pdfFilename;
			if (!strlen($objPage->pdfOrientation)) $objPage->pdfOrientation = $objParentPage->pdfOrientation;
			if (!strlen($objPage->pdfImageQuality)) $objPage->pdfImageQuality = $objParentPage->pdfImageQuality;
			if (!strlen($objPage->pdfCache)) $objPage->pdfCache = $objParentPage->pdfCache;
			if (!$objPage->pdfLayout) $objPage->pdfLayout = $objParentPage->pdfLayout;
		}

		return $objPage;
	}
}

