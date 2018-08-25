/**
 * Copyright © 2018 Rubic. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'ko',
], function (ko) {
    'use strict';

    return function (target) {
        return target.extend({
            isVisible: ko.observable(true)
        });
    };
});