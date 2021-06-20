/**
 * Copyright © 2018 Rubic. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'uiComponent',
    'uiRegistry',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data'
], function (Component, uiRegistry, quote, checkoutData) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Rubic_CleanCheckoutOnestep/place-order'
        },

        /**
         * Checks if place order action is allowed.
         *
         * @returns {boolean}
         */
        isPlaceOrderActionAllowed: function () {
            if (!quote.shippingMethod()) {
                return false;
            }
            let paymentMethodComponent = this.getPaymentMethodComponent();
            if (paymentMethodComponent && typeof paymentMethodComponent.isPlaceOrderActionAllowed !== 'undefined') {
                return paymentMethodComponent.isPlaceOrderActionAllowed();
            }
            return true;
        },

        /**
         * Returns the payment method component for selected shipping method.
         *
         * @returns {*|Object}
         */
        getPaymentMethodComponent: function () {
            let paymentMethodCode = checkoutData.getSelectedPaymentMethod();
            let paymentsList = uiRegistry.get('checkout.steps.billing-step.payment.payments-list');
            return paymentMethodCode ? paymentsList.getChild(paymentMethodCode) : null;
        },

        /**
         * @returns {boolean}
         */
        placeOrder: function () {
            debugger;
            let shipping = uiRegistry.get('checkout.steps.shipping-step.shippingAddress');
            if (shipping.validateShippingInformation()) {
                var done = shipping.setShippingInformation()
                if (done) {
                    done.done(function () {
                        this.getPaymentMethodComponent().placeOrder();
                    }.bind(this))
                }
            }
        }
    });
});
