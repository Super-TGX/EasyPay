<?php
//+----------------------------------------------------------------
//| easypay_creditcard Magento JS component
//+----------------------------------------------------------------
//| @category    EasyPay
//| @package     easypay_creditcard
//| @author      TGX
//| @copyright   EasyPay (http://meapps.cn)
//+----------------------------------------------------------------
namespace EasyPay\CreditCard\Model\Source;
class Cctype extends \Magento\Payment\Model\Source\Cctype {
	
    /**
     * @return array
     */
    public function getAllowedTypes() {
        return array('VI', 'MC', 'AE', 'DI', 'JCB', 'OT');
    }
	
}