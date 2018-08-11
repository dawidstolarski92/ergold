<?php

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../init.php');
//set_include_path(get_include_path() . PATH_SEPARATOR . 'libraries');
//include('Zend/Pdf.php');

$module_instance = Module::getInstanceByName('psinpost');

if (!Tools::isSubmit('token') || (Tools::isSubmit('token')) && Tools::getValue('token') != sha1(_COOKIE_KEY_.$module_instance->name)) exit;

if (Tools::isSubmit('printLabel')) {
	if(Tools::isSubmit('bulk')) {
		$ids = explode(',', Tools::getValue('id_label'));
		$pdf = new Zend_Pdf();
		foreach($ids as $id) {
			$pdf_file_contents = $module_instance->getLabelPdf($id);
			$pdf1 = Zend_Pdf::parse($pdf_file_contents);
			foreach ($pdf1->pages as $page) {
  				$pdf->pages[] = clone $page;
			}
		}
	
		ob_end_clean();
		header('Content-type: application/pdf');
		header('Content-Disposition: attachment; filename="inpost_labels.pdf"');
		echo $pdf->render();
	}
	else {
		$id_label = Tools::getValue('id_label');
		$pdf_file_contents = $module_instance->getLabelPdf($id_label);
	
		ob_end_clean();
		header('Content-type: application/pdf');
		header('Content-Disposition: attachment; filename="inpost_label_'. sprintf("%07d", $id_label) .'.pdf"');
		echo $pdf_file_contents;
		exit;		
	}
}

if (Tools::isSubmit('printSlip')) {
	if(Tools::isSubmit('bulk')) {
		$ids = explode(',', Tools::getValue('ids'));
		$pdf = new Zend_Pdf();
		foreach($ids as $id) {
       		$order = new Order((int)$id);
        	if(!Validate::isLoadedObject($order)) {
            	throw new PrestaShopException('Can\'t load Order object');
        	}
        	$order_invoice_collection = $order->getInvoicesCollection();
	        $pdfs = new PDF($order_invoice_collection, PDF::TEMPLATE_DELIVERY_SLIP, Context::getContext()->smarty);
    	    $pdf_file_contents = $pdfs->render(false);

			$pdf1 = Zend_Pdf::parse($pdf_file_contents);
			foreach ($pdf1->pages as $page) {
  				$pdf->pages[] = clone $page;
			}
		}
	
		ob_end_clean();
		header('Content-type: application/pdf');
		header('Content-Disposition: attachment; filename="inpost_slips.pdf"');
		echo $pdf->render();
	}
}
