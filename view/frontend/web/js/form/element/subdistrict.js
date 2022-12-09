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
                subdistrictOptions: '${ $.parentName }.city:indexedOptions',
                update: '${ $.parentName }.city:value',
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

            this.districtHasChildren = ko.computed(function() {
                if (this.options().length > 0) {
                    $('.field[name="shippingAddress.custom_attributes.sub_district"]').css('display','block');
                } else {
                    $('.field[name="shippingAddress.custom_attributes.sub_district"]').css('display','none');
                }
                return this.options().length > 0
            }, this)
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

            option = _.isObject(this.subdistrictOptions) && this.subdistrictOptions[value];

            if (!option) {
                return;
            }

            options = ko.observableArray([]);
            this.allOptions.forEach(function(option) {
                if (option.district_id == value) {
                    options.push(option);
                }
            });

            if (options().length > 0) {
                isRegionRequired = true;
            } else {
                isRegionRequired = false;
            }

            this.required(isRegionRequired);
            this.validation['required-entry'] = isRegionRequired;

            this.observe('options caption')
                .setOptions(options());
            registry.get(this.customName, function (input) {
                input.required(isRegionRequired);
                input.validation['required-entry'] = isRegionRequired;
            }.bind(this));
        }
    });
});
