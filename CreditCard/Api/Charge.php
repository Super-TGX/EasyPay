<?php
//+----------------------------------------------------------------
//| Magento2 EasyPay 一个辅助类，用于创建 TransactionID
//| TransactionID 是一个由支付程序返回的一个唯一ID
//| 所有订单都基于此ID来查询支付状态
//+----------------------------------------------------------------
//| Author TGX <me@meapps.cn>
//+----------------------------------------------------------------
namespace EasyPay\CreditCard\Api;
class Charge{
	
	public $id = 0;
	
	// 构造函数
	public function __construct($data = array()){
		$this->id = 1;
	}
	
	// 创建
	public static function create($data = array()){
		return new Charge($data);
	}
	
}
?>