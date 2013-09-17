<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *
 * PHP version 5
 * @copyright  terminal42 gmbh 2013
 * @author     Andreas Schempp <andreas.schempp@terminal42.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


class PagePDFHelper extends Frontend
{

    public function replaceTags($strTag)
    {
        $arrTag = explode('::', $strTag, 2);

        if ($arrTag[0] == 'link_pdf') {
            $strUrl = $this->Environment->request;
            $strUrl .= (strpos($strUrl, '?') === false ? '?' : '&');
            $strUrl .= 'pdf=' . $arrTag[1];

            return $strUrl;
        }

        return false;
    }
}
