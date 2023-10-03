/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'uiRegistry'
], function ($, wrapper, quote, registry) {
    'use strict';

    return function (setShippingInformationAction) {

        return wrapper.wrap(setShippingInformationAction, function (originalAction) {
            var shippingAddress = quote.shippingAddress();
            if (shippingAddress['extension_attributes'] === undefined) {
                shippingAddress['extension_attributes'] = {};
            }

            if (shippingAddress.customAttributes) {
                var attribute = shippingAddress.customAttributes.find(
                    function (element) {
                        return element.attribute_code === 'sub_district';
                    }
                );
                if (attribute) {
                    shippingAddress['extension_attributes']['sub_district'] = attribute.value;
                }
            }

            // pass execution to original action ('Magento_Checkout/js/action/set-shipping-information')
            return originalAction();

        });
    };
});
