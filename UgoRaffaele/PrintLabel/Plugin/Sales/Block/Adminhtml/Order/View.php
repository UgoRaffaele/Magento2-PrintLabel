<?php
namespace UgoRaffaele\PrintLabel\Plugin\Sales\Block\Adminhtml\Order;
use Magento\Framework\UrlInterface;
use Magento\Sales\Block\Adminhtml\Order\View as OrderView;

class View {

	protected $urlInterface;

	public function __construct(
		UrlInterface $urlInterface
	) {
		$this->urlInterface = $urlInterface;
	}

	public function beforeSetLayout(OrderView $view) {
		
		$url = $this->urlInterface->getUrl('printlabel/orders/printlabel', array('id' => $view->getOrderId()));

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