/**
 * Copyright © 2018 Rubic. All rights reserved.
 * See LICENSE.txt for license details.
 */
define(['jquery'], function ($) {
    'use strict';

    var agreementsConfig = window.checkoutConfig.checkoutAgreements;

    return function (target) {
        /** Override default place order action and add agreement_ids to request */
        return function (paymentData) {
            debugger;
            var agreementForm,
                agreementData,
                agreementIds;

            if (!agreementsConfig.isEnabled) {
                return;
            }

            agreementForm = $('#summary div[data-role=checkout-agreements] input');
            agreementData = agreementForm.serializeArray();
            agreementIds = [];

            agreementData.forEach(function (item) {
                agreementIds.push(item.value);
            });

            if (paymentData['extension_attributes'] === undefined) {
                paymentData['extension_attributes'] = {};
            }

            paymentData['extension_attributes']['agreement_ids'] = agreementIds;
        };
    };
});
