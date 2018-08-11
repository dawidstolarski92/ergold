CREATE TABLE IF NOT EXISTS `PREFIX_pk_theme_settings` (
`id_setting` int(10) unsigned NOT NULL AUTO_INCREMENT,
`id_shop` int(10) unsigned NOT NULL,
`name` VARCHAR(50),
`value` VARCHAR(9999),
`type` VARCHAR(99),
PRIMARY KEY (`id_setting`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_pk_theme_settings_hooks` (
`id_setting` int(10) unsigned NOT NULL AUTO_INCREMENT,
`id_shop` int(10) unsigned NOT NULL,
`hook` VARCHAR(99),
`module` VARCHAR(99),
`ordr` int(10) UNSIGNED NOT NULL DEFAULT 0,
`value` INT(2),
PRIMARY KEY (`id_setting`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_pk_product_extratabs` (
`id_pet` int(10) unsigned NOT NULL AUTO_INCREMENT,
`id_product` INT(10) UNSIGNED NOT NULL,
`shop_id` INT(2) UNSIGNED NOT NULL,
`lang_id` INT(2) UNSIGNED NOT NULL,
`video` VARCHAR(100) NOT NULL,
`custom_tab_name` VARCHAR(100) NOT NULL,
`custom_tab` TEXT NOT NULL,
PRIMARY KEY (`id_pet`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;