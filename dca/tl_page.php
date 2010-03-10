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
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_page']['palettes']['root'] .= ';{pdf_legend:hide},pdfAuthor,pdfFilename,pdfOrientation,pdfImageQuality,pdfLayout,pdfCache';
$GLOBALS['TL_DCA']['tl_page']['palettes']['regular'] .= ';{pdf_legend:hide},pdfAuthor,pdfFilename,pdfOrientation,pdfImageQuality,pdfLayout,pdfCache';
$GLOBALS['TL_DCA']['tl_page']['palettes']['pdf'] = '{title_legend},title,alias,type;{pdf_legend},pageTitle,pdfAuthor,pdfFilename,pdfOrientation,pdfImageQuality,pdfLayout,pdfCache;{protected_legend:hide},protected;{layout_legend:hide},includeLayout;{cache_legend:hide},includeCache;{chmod_legend:hide},includeChmod;{search_legend},noSearch;{expert_legend:hide},cssClass,hide,guests;{tabnav_legend:hide},tabindex,accesskey;{publish_legend},published,start,stop';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_page']['fields']['pdfAuthor'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_page']['pdfAuthor'],
	'exclude'			=> true,
	'inputType'			=> 'text',
	'eval'				=> array('maxlength'=>255, 'tl_class'=>'w50'),
);

$GLOBALS['TL_DCA']['tl_page']['fields']['pdfFilename'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_page']['pdfFilename'],
	'exclude'			=> true,
	'inputType'			=> 'text',
	'eval'				=> array('maxlength'=>255, 'tl_class'=>'w50'),
);

$GLOBALS['TL_DCA']['tl_page']['fields']['pdfOrientation'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_page']['pdfOrientation'],
	'exclude'			=> true,
	'inputType'			=> 'select',
	'options'			=> array('portrait', 'landscape'),
	'reference'			=> &$GLOBALS['TL_LANG']['tl_page'],
	'eval'				=> array('includeBlankOption'=>true, 'blankOptionLabel'=>&$GLOBALS['TL_LANG']['tl_page']['inherit'], 'tl_class'=>'w50'),
);

$GLOBALS['TL_DCA']['tl_page']['fields']['pdfImageQuality'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_page']['pdfImageQuality'],
	'exclude'			=> true,
	'inputType'			=> 'select',
	'options'			=> range(101, 1),
	'reference'			=> &$GLOBALS['TL_LANG']['tl_page'],
	'eval'				=> array('includeBlankOption'=>true, 'blankOptionLabel'=>&$GLOBALS['TL_LANG']['tl_page']['inherit'], 'tl_class'=>'w50'),
);

$GLOBALS['TL_DCA']['tl_page']['fields']['pdfLayout'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_page']['pdfLayout'],
	'exclude'			=> true,
	'inputType'			=> 'select',
	'foreignKey'		=> 'tl_layout.name',
	'eval'				=> array('includeBlankOption'=>true, 'blankOptionLabel'=>&$GLOBALS['TL_LANG']['tl_page']['inherit'], 'tl_class'=>'w50'),
);

$GLOBALS['TL_DCA']['tl_page']['fields']['pdfCache'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_page']['pdfCache'],
	'exclude'			=> true,
	'inputType'			=> 'select',
	'inputType'			=> 'select',
	'options'			=> array('-1', '0', 5, 15, 30, 60, 300, 900, 1800, 3600, 10800, 21600, 43200, 86400),
	'reference'			=> &$GLOBALS['TL_LANG']['CACHE'],
	'eval'				=> array('includeBlankOption'=>true, 'blankOptionLabel'=>&$GLOBALS['TL_LANG']['tl_page']['inherit'], 'tl_class'=>'w50'),
);

