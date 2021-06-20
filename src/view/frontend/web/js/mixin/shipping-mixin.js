/**
 * Copyright © 2018 Rubic. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'ko',
    'jquery',
    'Rubic_CleanCheckoutOnestep/js/model/address-validator',
    'Magento_Checkout/js/action/set-shipping-information',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/checkout-data',
    'uiRegistry'
], function (
    ko,
    $,
    addressValidator,
    setShippingInformationAction,
    stepNavigator,
    checkoutData,
    registry
) {
    'use strict';

    return function (target) {
        return target.extend({
            defaults: {
                template: 'Rubic_CleanCheckoutOnestep/address'
            },
            /**
             * @inheritDoc
             */
            initialize: function () {
                this._super();
                this.visible(true);
                this.visible.subscribe(function(newValue) {
                    if (!newValue) {
                        this.visible(true);
                    }
                }.bind(this));
            },
            /**
             * @inheritDoc
             */
            setShippingInformation: function () {
                debugger;
                if (
                    this.validateShippingInformation()
                    &&
                    addressValidator.validateBillingInformation(this.isFormInline, this.source)
                ) {
                    registry.async('checkoutProvider')(function (checkoutProvider) {
                        var shippingAddressData = checkoutData.getShippingAddressFromData();

                        if (shippingAddressData) {
                            checkoutProvider.set(
                                'shippingAddress',
                                $.extend(true, {}, checkoutProvider.get('shippingAddress'), shippingAddressData)
                            );
                        }
                    });

                    return setShippingInformationAction();
                }
            }

        });
    };
});
