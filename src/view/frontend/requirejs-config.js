/**
 * Copyright © 2018 Rubic. All rights reserved.
 * See LICENSE.txt for license details.
 */
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping': {
                'Rubic_CleanCheckoutOnestep/js/mixin/shipping-mixin': true
            },
            'Magento_Checkout/js/view/payment': {
                'Rubic_CleanCheckoutOnestep/js/mixin/payment-mixin': true
            },
            'Magento_Checkout/js/view/payment/list': {
                'Rubic_CleanCheckoutOnestep/js/mixin/payment-list-mixin': true
            }
        }
    }
};