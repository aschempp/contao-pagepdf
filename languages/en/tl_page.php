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

