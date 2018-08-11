CREATE TABLE IF NOT EXISTS `PREFIX_pk_bannercarousel` (
  `id_slide` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_shop` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `id_order` int(10) unsigned NOT NULL,
  `lang_iso` varchar(5) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `url` varchar(100) DEFAULT NULL,
  `target` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `image` varchar(100) DEFAULT NULL,
  `alt` varchar(100) DEFAULT NULL,
  `caption` varchar(300) DEFAULT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_slide`,`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;
INSERT INTO `PREFIX_pk_bannercarousel` (`id_slide`, `id_shop`, `id_lang`, `id_order`, `lang_iso`, `title`, `url`, `target`, `image`, `alt`, `caption`, `active`) VALUES
(1, SID, LID, 1, 'DEF_LANG_ISO', 'Banner-01', '#', 0, 'banner-01.jpg', '', '', 1),
(2, SID, LID, 1, 'DEF_LANG_ISO', 'Banner-02', '#', 0, 'banner-02.jpg', '', '', 1);
CREATE TABLE IF NOT EXISTS `PREFIX_pk_bannercarousel_options` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_shop` int(10) unsigned NOT NULL,
  `effect` varchar(300) DEFAULT NULL,
  `current` varchar(300) DEFAULT NULL,
  `slices` int(3) NOT NULL DEFAULT '15',
  `cols` int(3) NOT NULL DEFAULT '8',
  `rows` int(3) NOT NULL DEFAULT '4',
  `speed` int(4) NOT NULL DEFAULT '800',
  `pause` int(4) NOT NULL DEFAULT '4000',
  `manual` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `hover` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `buttons` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `control` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `thumbnail` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `random` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `start_slide` int(2) unsigned NOT NULL DEFAULT '0',
  `single` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `width` int(4) unsigned NOT NULL DEFAULT '0',
  `height` int(4) unsigned NOT NULL DEFAULT '0',
  `front` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`,`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;
INSERT INTO `PREFIX_pk_bannercarousel_options` (`id_shop`, `effect`, `current`, `slices`, `cols`, `rows`, `speed`, `pause`, `manual`, `hover`, `buttons`, `control`, `thumbnail`, `random`, `start_slide`, `single`, `width`, `height`, `front`) VALUES
(SID, 'sliceDown,sliceDownLeft,sliceUp,sliceUpLeft,sliceUpDown,sliceUpDownLeft,fold,slideInRight,slideInLeft,boxRandom,boxRain,boxRainReverse,boxRainGrow,boxRainGrowReverse,fade', '', 15, 8, 4, 800, 4000, 1, 1, 1, 0, 0, 1, 2, 0, 0, 0, 1);