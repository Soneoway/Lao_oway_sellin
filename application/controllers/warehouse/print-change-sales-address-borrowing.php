<?php

$this->_helper->layout->disableLayout();
$id = $this->getRequest()->getParam('id');

if (isset($id) && $id) {
 
	$QChangeSalesOrder = new Application_Model_ChangeSalesOrder();

	$getData = $QChangeSalesOrder->getDataBorrowingByID($id);

	$address = '';

	switch ($getData['delivery']) {
		case '1':
			$address = 'คลังสินค้าเคอรี่ บางนา โลจิสติกส์ เซ็นเตอร์ <br/>เลขที่ 33/2 หมู่ 7 บางปลา บางพลี สมุทรปราการ 10540 ติดต่อคุณศริญญา เกตุเขียว 0657174931';
			break;
		case '2':
			$address = 'บจก.ไทย ออปโป้ เลขที่230 ถ.บางขุนเทียน <br/>-ชายทะเล แขวงแสมดำ เขตบางขุนเทียน กรุงเทพฯ 10150 ติดต่อคุณหนึ่ง  0835527645 หรือ คุณเอ๋ 0958250441';
			break;
		case '3':
			$address = $getData['address'];
			break;
		case '4':
			$address = 'บริษัท ไทย ออปโป้ จำกัด <br/>อาคาร AIA Capital Center รัชดา เลขที่ 89 ชั้น 31 ห้อง 5-7 ถนน รัชดาภิเษก เขตดินแดง กทม. 10400 <br/>โทรศัพท์ : 02- 013-1810 แฟ็กซ์: 02-013–1820';
			break;
	}

	$this->view->address = $address;

	$this->view->data = $getData;

 }
