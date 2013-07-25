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
 * @license    LGPL
 */


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_page']['pdfAuthor']			= array('Author', 'Changes the default value for the PDF Author that can be found in the PDF Properties.');
$GLOBALS['TL_LANG']['tl_page']['pdfFilename']		= array('PDF file name', 'Changes the default value for the PDF file name.');
$GLOBALS['TL_LANG']['tl_page']['pdfOrientation']	= array('Orientation', 'Select if PDF should be created in portrait or landscape mode.');
$GLOBALS['TL_LANG']['tl_page']['pdfImageQuality']	= array('Image quality', 'Can be used to decrease PDF files size. The smaller the value the smaller the PDF file size. A reasonable value is 75.');
$GLOBALS['TL_LANG']['tl_page']['pdfLayout']			= array('PDF Layout', 'You can select a different page layout for PDF generation.');
$GLOBALS['TL_LANG']['tl_page']['pdfCache']			= array('Force caching', 'Cache PDF even if page is protected. Make sure you configure system/tmp as inaccessable by web for security reason!');


/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_page']['pdf_legend']		= 'PDF settings';


/**
 * References
 */
$GLOBALS['TL_LANG']['tl_page']['inherit']			= 'Inherit';
$GLOBALS['TL_LANG']['tl_page']['portrait']			= 'Portrait';
$GLOBALS['TL_LANG']['tl_page']['landscape']			= 'Landscape';
$GLOBALS['TL_LANG']['tl_page'][101]					= 'Lossless compression';
$GLOBALS['TL_LANG']['tl_page'][75]					= '75 (recommended)';
$GLOBALS['TL_LANG']['tl_page']['enable']			= 'Enable';
$GLOBALS['TL_LANG']['tl_page']['disable']			= 'Disable';
$GLOBALS['TL_LANG']['CACHE']['-1']					= 'Until content is changed';

