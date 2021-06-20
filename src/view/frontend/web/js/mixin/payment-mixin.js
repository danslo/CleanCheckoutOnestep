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
            initialize: function () {
                this._super();
                this.isVisible(true);
                this.isVisible.subscribe(function(newValue) {
                    if (!newValue) {
                        this.isVisible(true);
                    }
                }.bind(this));
            }
        });
    };
});
