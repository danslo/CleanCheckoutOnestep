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
            /**
             * Trims excess ':'.
             */
            getGroupTitle: function (group) {
                return this._super(group).slice(0, -1);
            }
        });
    };
});