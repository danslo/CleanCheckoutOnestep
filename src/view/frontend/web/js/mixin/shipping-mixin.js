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
            visible: ko.observable(true)
        });
    };
});