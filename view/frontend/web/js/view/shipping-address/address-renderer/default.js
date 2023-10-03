/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'underscore',
    "mage/url",
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-address/form-popup-state',
    'Magento_Checkout/js/checkout-data',
    'Magento_Customer/js/customer-data',
    'domReady!'
], function ($, ko, Component, _, urlBuilder, selectShippingAddressAction, quote, formPopUpState, checkoutData, customerData) {
    'use strict';

    var countryData = customerData.get('directory-data');
    var regionDistrictData = window.checkoutConfig.vnAddressDistrict,
        districtSubdistrictData = window.checkoutConfig.vnAddressSubdistrict;

    return Component.extend({
        defaults: {
            template: 'Eloab_VNAddress/shipping-address/address-renderer/default'
        },

        /** @inheritdoc */
        initObservable: function () {
            var self = this;
            this._super();
            this.isSelected = ko.computed(function () {
                var isSelected = false,
                    shippingAddress = quote.shippingAddress();

                if (shippingAddress) {
                    isSelected = shippingAddress.getKey() == this.address().getKey();
                }

                return isSelected;
            }, this);

            return this;
        },

        /**
         * @param {String} countryId
         * @return {String}
         */
        getCountryName: function (countryId) {
            return countryData()[countryId] != undefined ? countryData()[countryId].name : '';
        },

        /**
         * @param address
         * @return {String}
         */
        getDistrictName: function (address) {
            var districtId = address.city,
                regionId = address.regionId;
            if (regionId === undefined) {
                var shippingAddress = quote.shippingAddress();
                regionId = shippingAddress.regionId;
            }
            if (!isNaN(districtId)) {
                var regionDistrictOptions = _.find(regionDistrictData, function(obj, key) {
                    return key == regionId;
                });
                var district = _.find(regionDistrictOptions, function(obj, key){
                    return districtId == obj.district_id;
                })

                if (district) {
                    var subdistrictName = '';
                    if (address.customAttributes) {
                        let subdistrictValue = _.find(address.customAttributes, function(obj, key) {
                            return obj.attribute_code && obj.attribute_code === 'sub_district';
                        });
                        let districtObject = _.find(districtSubdistrictData, function(obj, key) {
                            return key == districtId;
                        });
                        let subdistrict = _.find(districtObject, function(obj, key) {
                            return subdistrictValue.value == obj.subdistrict_id;
                        });
                        subdistrictName = subdistrict.default_name + ', ';
                    }
                    return subdistrictName + district.default_name;
                }
            }
            return districtId;

        },

        /**
         * Get customer attribute label
         *
         * @param {*} attribute
         * @returns {*}
         */
        getCustomAttributeLabel: function (attribute) {
            var label;

            //exclude subdistrict
            if (attribute.attribute_code == 'sub_district') {
                return null;
            }

            if (typeof attribute === 'string') {
                return attribute;
            }

            if (attribute.label) {
                return attribute.label;
            }

            if (_.isArray(attribute.value)) {
                label = _.map(attribute.value, function (value) {
                    return this.getCustomAttributeOptionLabel(attribute['attribute_code'], value) || value;
                }, this).join(', ');
            } else if (typeof attribute.value === 'object') {
                label = _.map(Object.values(attribute.value)).join(', ');
            } else {
                label = this.getCustomAttributeOptionLabel(attribute['attribute_code'], attribute.value);
            }

            return label || attribute.value;
        },

        /**
         * Get option label for given attribute code and option ID
         *
         * @param {String} attributeCode
         * @param {String} value
         * @returns {String|null}
         */
        getCustomAttributeOptionLabel: function (attributeCode, value) {
            var option,
                label,
                options = this.source.get('customAttributes') || {};

            if (options[attributeCode]) {
                option = _.findWhere(options[attributeCode], {
                    value: value
                });

                if (option) {
                    label = option.label;
                }
            } else if (value.file !== null) {
                label = value.file;
            }

            return label;
        },

        /** Set selected customer shipping address  */
        selectAddress: function () {
            selectShippingAddressAction(this.address());
            checkoutData.setSelectedShippingAddress(this.address().getKey());
        },

        /**
         * Edit address.
         */
        editAddress: function () {
            formPopUpState.isVisible(true);
            this.showPopup();

        },

        /**
         * Show popup.
         */
        showPopup: function () {
            $('[data-open-modal="opc-new-shipping-address"]').trigger('click');
        },
    });
});
