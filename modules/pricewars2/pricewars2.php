<?php
/* 
 * Stworzono przez SEIGI http://pl.seigi.eu/
 * Wszelkie prawa zastrzeżone.
 * Zabrania się modyfikacji używania i udostępniania kodu bez zgody lub odpowiedniej licencji.
 * Utworzono  : Dec 29, 2015
 * Author     : SEIGI - Grzegorz Zawadzki <kontakt@seigi.eu>
 */

if(!extension_loaded('ionCube Loader')){
	Context::getContext()->controller->errors[] = 'pricewars2 - Please Install IonCube Loader extension / Prosimy zainstalować rozszeżenie IoncubeLoader';
	class pricewars2 extends Module {
		public function __construct()
		{
			$this->name = 'pricewars2';
			$this->version = 'x.x.x';
			$this->author = 'SEIGI Grzegorz Zawadzki';
			$this->need_instance = 1;
			parent::__construct();
			$this->displayName = $this->l( 'IONCUBE PROBLEM - pricewars2' );
			$this->description = $this->l( 'Please Install IonCube Loader extension / Prosimy zainstalować rozszeżenie IoncubeLoader.' );
		}
		public function install() { return false; }
		public function uninstall() { return false; }
		public function getErrors(){ return array('Please Install IonCube Loader extension / Prosimy zainstalować rozszeżenie IoncubeLoader'); }
	}

} else {
	require_once(dirname(__FILE__). '/pricewars2.inc.php');
}