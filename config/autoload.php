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
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'PagePDF' => 'system/modules/pagepdf/PagePDF.php',
	'PagePDFHelper' => 'system/modules/pagepdf/PagePDFHelper.php',
	'PageRegularPDF' => 'system/modules/pagepdf/PageRegularPDF.php',
));
