-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the TYPOlight *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************

-- 
-- Table `tl_page`
-- 

CREATE TABLE `tl_page` (
  `pdfAuthor` varchar(255) NOT NULL default '',
  `pdfFilename` varchar(255) NOT NULL default '',
  `pdfOrientation` varchar(9) NOT NULL default '',
  `pdfImageQuality` varchar(3) NOT NULL default '',
  `pdfLayout` int(10) unsigned NOT NULL default '0',
  `pdfCache` varchar(7) NOT NULL default '',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

