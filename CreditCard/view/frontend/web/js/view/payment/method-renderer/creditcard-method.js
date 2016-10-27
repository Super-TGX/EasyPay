//+----------------------------------------------------------------
//| easypay_creditcard Magento JS component
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
        'Magento_Payment/js/view/payment/cc-form',
        'jquery',
        'Magento_Payment/js/model/credit-card-validation/validator'
    ],
    function (Component, $) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'easypay_creditcard/payment/creditcard-form'
            },

            getCode: function() {
                return 'easypay_creditcard';
            },

            isActive: function() {
                return true;
            },

            validate: function() {
                var $form = $('#' + this.getCode() + '-form');
                return $form.validation() && $form.validation('isValid');
            }
        });
    }
);