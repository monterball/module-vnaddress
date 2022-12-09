/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'ko',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'domReady!'
], function ($,ko, _, registry, Select) {
    'use strict';

    return Select.extend({
        defaults: {
            skipValidation: false,
            imports: {
                districtOptions: '${ $.parentName }.region_id:indexedOptions',
                update: '${ $.parentName }.region_id:value',
                allOptions: ko.observableArray([])
            }
        },

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            this._super();

            if (!this.allOptions) {
                this.allOptions = this.options();
            }
            return this;
        },

        /**
         * Method called every time country selector's value gets changed.
         * Updates all validations and requirements for certain country.
         * @param {String} value - Selected country ID.
         */
        update: function (value) {
            var isRegionRequired,
                option,
                options;
            if (!this.allOptions) {
                this.allOptions = this.options();
            }
            if (!value) {
                return;
            }

            option = _.isObject(this.districtOptions) && this.districtOptions[value];


            if (!option) {
                return;
            }

            isRegionRequired = true;

            if (!isRegionRequired) {
                this.error(false);
            }

            this.required(isRegionRequired);
            this.validation['required-entry'] = isRegionRequired;
            options = ko.observableArray([]);
            this.allOptions.forEach(function(option) {
                if (option.region_id == value) {
                    options.push(option);
                }
            });
            this.observe('options caption')
                .setOptions(options());
            registry.get(this.customName, function (input) {
                input.required(isRegionRequired);
                input.validation['required-entry'] = isRegionRequired;
                input.validation['validate-not-number-first'] = !this.options().length;
            }.bind(this));
        }
    });
});
