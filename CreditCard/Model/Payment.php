<?php
//+----------------------------------------------------------------
//| Magento2 信用卡支付程序演示
//+----------------------------------------------------------------
//| Author TGX <me@meapps.cn>
//+----------------------------------------------------------------
namespace EasyPay\CreditCard\Model;

class Payment extends \Magento\Payment\Model\Method\Cc {
	
	const CODE = 'easypay_creditcard';	// 支付方式 code	
	protected $_code = self::CODE; 
	protected $_isGateway = true;
	protected $_canCapture = true;
	protected $_canCapturePartial = true;
	protected $_canRefund = true;
	protected $_canRefundInvoicePartial = true; 
	protected $_easypayApi = false; 
	protected $_minAmount = null;
	protected $_maxAmount = null;
	// 支持的货币
	protected $_supportedCurrencyCodes = array('USD');
	
	/**
	 * \Magento\Framework\Event\ManagerInterface $eventManager
	 * \Magento\Payment\Helper\Data $paymentData
	 * \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	 * \Magento\Framework\Logger $logger
	 * \Magento\Framework\Module\ModuleListInterface $moduleList
	 * \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
	 * \Magento\Centinel\Model\Service $centinelService
	 * \EasyPay\CreditCard\Api $easypayApi	// 自定义的一个类，用于处理支付方式使用到的数据，比如用一张数据表存储信息
	 */
	public function __construct(
		\Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Directory\Model\CountryFactory $countryFactory,
		\EasyPay\CreditCard\Api\EasyPayApi $easypayApi,
		array $data = array()
	) {
		parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $moduleList,
            $localeDate,
            null,
            null,
            $data
        );
 
		$this->_easypayApi = $easypayApi;
		// $this->_stripeApi->setApiKey(
				// $this->getConfigData('api_key')
		// );
		$this->_minAmount = $this->getConfigData('min_order_total');
		$this->_maxAmount = $this->getConfigData('max_order_total');
	}
	
	/**
	 * 支付捕获方法
	 * *
	 * @param \Magento\Framework\Object $payment
	 * @param float $amount
	 * @return $this
	 * @throws \Magento\Framework\Model\Exception
	*/
	public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount) {
		/** @var Magento\Sales\Model\Order $order */
		$order = $payment->getOrder();
 
		/** @var Magento\Sales\Model\Order\Address $billing */
		$billing = $order->getBillingAddress();
 
		try {
			$charge = \EasyPay\CreditCard\Api\Charge::create(array(
				'amount' => $amount * 100,
				'currency' => strtolower($order->getBaseCurrencyCode()),
				'description' => sprintf('#%s, %s', $order->getIncrementId(), $order->getCustomerEmail()),
				'card' => array(
					'number' => $payment->getCcNumber(),
					'number' => $payment->getCcNumber(),
					'exp_month' => sprintf('%02d',$payment->getCcExpMonth()),
					'exp_year' => $payment->getCcExpYear(),
					'cvc' => $payment->getCcCid(),
					'name' => $billing->getName(),
					'address_line1' => $billing->getStreet(1),
					'address_line2' => $billing->getStreet(2),
					'address_zip' => $billing->getPostcode(),
					'address_state' => $billing->getRegion(),
					'address_country' => $billing->getCountry(),
				),
			));
 
			$payment->setTransactionId($charge->id)->setIsTransactionClosed(0);
		} catch (\Exception $e) {
			$this->debugData($e->getMessage());
			$this->_logger->logException(__('Payment capturing error.'));
			throw new \Magento\Framework\Model\Exception(__('Payment capturing error.'));
		}
		return $this;
	}
	
	/**
	 * 退款处理
	 *
	 * @param \Magento\Framework\Object $payment
	 * @param float $amount
	 * @return $this
	 * @throws \Magento\Framework\Model\Exception
	*/
	public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount){
		$transactionId = $payment->getParentTransactionId(); 
		try {
			\Stripe\Charge::retrieve($transactionId)->refund();
		} catch (\Exception $e) {
			$this->debugData($e->getMessage());
			$this->_logger->logException(__('Payment refunding error.'));
			throw new \Magento\Framework\Model\Exception(__('Payment refunding error.'));
		}
 
		$payment->setTransactionId($transactionId . '-' . \Magento\Sales\Model\Order\Payment\Transaction::TYPE_REFUND)
				->setParentTransactionId($transactionId)
				->setIsTransactionClosed(1)
				->setShouldCloseParentTransaction(1);
		return $this;
	}
 
	/**
     * Determine method availability based on quote amount and config data
     *
     * @param \Magento\Quote\Api\Data\CartInterface|null $quote
     * @return bool
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        if ($quote && (
            $quote->getBaseGrandTotal() < $this->_minAmount
            || ($this->_maxAmount && $quote->getBaseGrandTotal() > $this->_maxAmount))
        ) {
            return false;
        }
        if (!$this->getConfigData('api_key')) {
            return false;
        }
        return parent::isAvailable($quote);
    }
	
    /**
     * Availability for currency
     *
     * @param string $currencyCode
     * @return bool
     */
    public function canUseForCurrency($currencyCode)
    {
        if (!in_array($currencyCode, $this->_supportedCurrencyCodes)) {
            return false;
        }
        return true;
    }
	
}
?>