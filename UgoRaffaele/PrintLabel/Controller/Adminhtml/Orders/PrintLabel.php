<?php
namespace UgoRaffaele\PrintLabel\Controller\Adminhtml\Orders;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\Information as StoreInformation;
use Magento\Framework\App\Response\Http\FileFactory;

class PrintLabel extends \Magento\Backend\App\Action {
	
	protected $request;
	
	protected $dateTime;
	
	protected $_storeInfo;
	
	protected $fileFactory;
 
	public function __construct(
		Context $context,
		Http $request,
		DateTime $dateTime,
		StoreInformation $storeInfo,
		FileFactory $fileFactory
	) {
		$this->request = $request;
		$this->dateTime = $dateTime;
		$this->_storeInfo = $storeInfo;
		$this->fileFactory = $fileFactory;
		parent::__construct($context);
	}
 
    public function execute() {
		
		$pdf = new \Zend_Pdf();
		$pdf->pages[] = $pdf->newPage(\Zend_Pdf_Page::SIZE_A4_LANDSCAPE);
		$page = $pdf->pages[0];
		$width = $page->getWidth();
		$height = $page->getHeight();
		$delta = 20.5;
		$deltaStore = 25;

		$style = new \Zend_Pdf_Style();

		/* Shipping Address */

		$font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_HELVETICA_BOLD);
		$style->setLineColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));
		$style->setFont($font, 19);
		$page->setStyle($style);

		$line = 1;

		$textWidth = $this->getTextWidth(__('Shipping Address'), $font, 19, 'UTF-8');
		$page->drawText(__('Shipping Address'), ($width / 4) - ($textWidth / 2), ($height - ($line * $delta + $line * 19)), 'UTF-8');

		$orderId = $this->getRequest()->getParam('id');
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$order = $objectManager->create('\Magento\Sales\Model\Order')->load($orderId);
		$customerId = $order->getCustomerId();

		$address = $order->getShippingAddress();
		$line++;

		$name = $address->getData('prefix') . " " . $address->getData('firstname') . " " . $address->getData('middlename') . " " . $address->getData('lastname') . " " . $address->getData('suffix');
		$textWidth = $this->getTextWidth($name, $font, 19, 'UTF-8');
		$page->drawText($name, ($width / 4) - ($textWidth / 2), ($height - ($line * $delta + $line * 19)), 'UTF-8');

		$line++;

		$company = ($address->getData('company') != "") ? "C/O " . $address->getData('company') : "";
		$textWidth = $this->getTextWidth($company, $font, 19, 'UTF-8');
		$page->drawText($company, ($width / 4) - ($textWidth / 2), ($height - ($line * $delta + $line * 19)), 'UTF-8');

		$line++;

		$street = $address->getData('street');
		$textWidth = $this->getTextWidth($street, $font, 19, 'UTF-8');
		$page->drawText($street, ($width / 4) - ($textWidth / 2), ($height - ($line * $delta + $line * 19)), 'UTF-8');

		$line++;

		$post = $address->getData('postcode') . " " . $address->getData('city') . " (" . $address->getData('region') . ")";
		$textWidth = $this->getTextWidth($post, $font, 19, 'UTF-8');
		$page->drawText($post, ($width / 4) - ($textWidth / 2), ($height - ($line * $delta + $line * 19)), 'UTF-8');

		$line++;

		$country = $address->getData('country_id');
		$textWidth = $this->getTextWidth($country, $font, 19, 'UTF-8');
		$page->drawText($country, ($width / 4) - ($textWidth / 2), ($height - ($line * $delta + $line * 19)), 'UTF-8');

		$line++;

		$telephone = "Tel: " . $address->getData('telephone');
		$textWidth = $this->getTextWidth($telephone, $font, 19, 'UTF-8');
		$page->drawText($telephone, ($width / 4) - ($textWidth / 2), ($height - ($line * $delta + $line * 19)), 'UTF-8');

		/* Sender Address */

		$font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_HELVETICA);
		$style->setLineColor(new \Zend_Pdf_Color_Rgb(24, 24, 24));
		$style->setFont($font, 18);
		$page->setStyle($style);

		$line++;

		$textWidth = $this->getTextWidth(__('Sender'), $font, 18, 'UTF-8');
		$page->drawText(__('Sender'), ($width / 4) - ($textWidth / 2), ($height - ($line * $deltaStore + $line * 18)), 'UTF-8');

		$store = $objectManager->create('\Magento\Store\Model\Store')->load($order->getStoreId());
		$storeAddress = $this->_storeInfo->getStoreInformationObject($store);
		$line++;

		$name = $storeAddress->getName();
		$textWidth = $this->getTextWidth($name, $font, 18, 'UTF-8');
		$page->drawText($name, ($width / 4) - ($textWidth / 2), ($height - ($line * $deltaStore + $line * 18)), 'UTF-8');

		$line++;

		$street = $storeAddress->getData('street_line1');
		$textWidth = $this->getTextWidth($street, $font, 18, 'UTF-8');
		$page->drawText($street, ($width / 4) - ($textWidth / 2), ($height - ($line * $deltaStore + $line * 18)), 'UTF-8');

		$line++;

		$post = $storeAddress->getData('postcode') . " " . $storeAddress->getData('city') . " (" . $storeAddress->getData('region_id') . ")";
		$textWidth = $this->getTextWidth($post, $font, 18, 'UTF-8');
		$page->drawText($post, ($width / 4) - ($textWidth / 2), ($height - ($line * $deltaStore + $line * 18)), 'UTF-8');

		$line++;

		$country = $storeAddress->getData('country_id');
		$textWidth = $this->getTextWidth($country, $font, 18, 'UTF-8');
		$page->drawText($country, ($width / 4) - ($textWidth / 2), ($height - ($line * $deltaStore + $line * 18)), 'UTF-8');

		$line++;

		$telephone = "Tel: " . $storeAddress->getPhone();
		$textWidth = $this->getTextWidth($telephone, $font, 18, 'UTF-8');
		$page->drawText($telephone, ($width / 4) - ($textWidth / 2), ($height - ($line * $deltaStore + $line * 18)), 'UTF-8');

		/* Print PDF Label */

		$this->fileFactory->create(
			sprintf('label-%s.pdf', $this->dateTime->date('Y-m-d_H-i-s')),
			$pdf->render(),
			\Magento\Framework\App\Filesystem\DirectoryList::TMP,
			'application/pdf'
		);
		
	}
	
	public static function getTextWidth($text, $font, $fontSize, $encoding = null) {

		if ($encoding == null) $encoding = 'UTF-8';

		/*
		if ($font instanceof \Zend_Pdf_Font) {
			if ($fontSize === null) throw new \Exception('The fontsize is unknown');
		} else {
			throw new \Exception('Invalid font passed');
		}
		*/

		$drawingText = iconv('', $encoding, $text);
		$characters = array();
		for ($i = 0; $i < strlen($drawingText); $i++) {
			$characters[] = ord($drawingText[$i]);
		}
		$glyphs = $font->glyphNumbersForCharacters($characters);
		$widths = $font->widthsForGlyphs($glyphs);

		$textWidth = ( array_sum($widths) / $font->getUnitsPerEm() ) * $fontSize;
		return $textWidth;

	}
	
}