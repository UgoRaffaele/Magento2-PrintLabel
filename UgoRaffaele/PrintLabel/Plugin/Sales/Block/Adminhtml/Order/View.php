<?php
namespace UgoRaffaele\PrintLabel\Plugin\Sales\Block\Adminhtml\Order;
use Magento\Framework\UrlInterface;
use Magento\Sales\Block\Adminhtml\Order\View as OrderView;

class View {

	private $_urlInterface = null;

	public function __construct(
		UrlInterface $urlInterface
	) {
		$this->_urlInterface = $urlInterface;
	}

	public function beforeSetLayout(OrderView $view) {
		
		$url = $this->_urlInterface->getUrl('printlabel/orders/printlabel', array('id' => $view->getOrderId()));

		$view->addButton(
			'print-label-button',
			[
				'label' => __('Print Label'),
				'class' => 'print-label-button',
				'onclick' => 'window.open(\'' . $url . '\')'
			]
		);
		
	}
	
}