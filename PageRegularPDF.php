<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 * Copyright (C) 2005-2009 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at http://www.gnu.org/licenses/.
 *
 * PHP version 5
 * @copyright  Andreas Schempp 2009
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 * @version    $Id$
 */



class PageRegularPDF extends PageRegular
{
	
	/**
	 * URL to convert PDFs
	 */
	private $apiURL = 'http://convert.html2pdf.seven49.net/';
	

	/**
	 * Generate a regular page
	 * @param object
	 */
	public function generate(Database_Result $objPage, $blnPDF=false)
	{
		if ($this->Input->get('pdf') != 'page' && !$blnPDF)
		{
			return parent::generate($objPage);
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
			ob_start();
			parent::generate($objPage);
			$strBuffer = ob_get_contents();
			ob_end_clean();
			
			// Write buffer to temporary file
			$strFile = 'system/html/' . uniqid();
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
	
	
	protected function inheritPDFData($objPage)
	{
		$objParentPage = $objPage;

		while( $objParentPage->numRows && $objParentPage->pid > 0 && (!strlen($objPage->pdfAuthor) || !strlen($objPage->pdfFilename) || !strlen($objPage->pdfOrientation) || !strlen($objPage->pdfImageQuality) || !strlen($objPage->pdfCache) || !$objPage->pdfLayout) )
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

