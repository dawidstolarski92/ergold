<?php
/**
 * LICENCE
 * 
 * ALL RIGHTS RESERVED.
 * YOU ARE NOT ALLOWED TO COPY/EDIT/SHARE/WHATEVER.
 * 
 * IN CASE OF ANY PROBLEM CONTACT AUTHOR.
 * 
 *  @author    Tomasz Dacka (kontakt@tomaszdacka.pl)
 *  @copyright PrestaHelp.com
 *  @license   ALL RIGHTS RESERVED
 */

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'paczkomatyinpost` (
    `id_cart` int(11) NOT NULL,
	`status` varchar(30),
	`dispatch_order_id` varchar(255),
	`status_date` datetime,
	`packcode` varchar(30),
	`paid` tinyint(1) DEFAULT 0,
	`calculated_charge` double,
	`customer_delivering_code` char(6),
	`receiver_email` varchar(255),
	`receiver_mobile` varchar(255),
	`receiver_machine` char(10),
	`receiver_machine_cod` char(10),
	`packtype` char(1),
	`self_send` tinyint(1),
	`sender_machine` char(10),
	`reference_number` varchar(40),
	`insurance` double,
	`cod` tinyint(1),
	`cod_value` double,
	`date_add` datetime,
	`date_upd` datetime,
    PRIMARY KEY  (`id_cart`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

foreach ($sql as $query)
	if (Db::getInstance()->execute($query) == false)
		return false;