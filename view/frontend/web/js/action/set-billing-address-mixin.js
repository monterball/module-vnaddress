/**
 * Copyright Â© Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/action/get-payment-information',
        'mage/utils/wrapper',
        'uiRegistry'
    ],
    function (
        $,
        quote,
        urlBuilder,
        storage,
        errorProcessor,
        customer,
        fullScreenLoader,
        getPaymentInformationAction,
        wrapper,
        registry
    ) {
        'use strict';

        return function (setBillingAddress) {

            return wrapper.wrap(setBillingAddress, function(originalAction, messageContainer) {
                var billingAddress = quote.billingAddress();
                if (billingAddress['extension_attributes'] === undefined) {
                    billingAddress['extension_attributes'] = {};
                }

                if (quote.paymentMethod()) {
                    var subdistrictBilling = registry.get('checkout.steps.billing-step.payment.payments-list.'+quote.paymentMethod().method +'-form.form-fields.sub_district');

                    if (subdistrictBilling) {
                        billingAddress['extension_attributes']['sub_district'] = subdistrictBilling.value();
                    }
                }

                return originalAction(messageContainer);
            });
        };


    }
);
