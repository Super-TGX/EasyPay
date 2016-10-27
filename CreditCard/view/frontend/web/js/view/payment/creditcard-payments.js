//+----------------------------------------------------------------
//| Easypay_Creditcard Magento JS component
//+----------------------------------------------------------------
//| @category    EasyPay
//| @package     easypay_creditcard
//| @author      TGX
//| @copyright   EasyPay (http://meapps.cn)
//+----------------------------------------------------------------
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'easypay_creditcard',
                component: 'easypay_creditcard/js/view/payment/method-renderer/creditcard-method'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);