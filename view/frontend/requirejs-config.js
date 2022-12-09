var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-shipping-information': {
                'Eloab_VNAddress/js/action/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/action/set-billing-address': {
                'Eloab_VNAddress/js/action/set-billing-address-mixin': true
            }
        }
    },
    map: {
        '*': {
            'Magento_Checkout/js/view/billing-address':
                'Eloab_VNAddress/js/view/billing-address',
            'Magento_Checkout/js/model/new-customer-address' :
                'Eloab_VNAddress/js/model/new-customer-address',
            'Magento_Checkout/js/view/shipping-address/address-renderer/default':
                'Eloab_VNAddress/js/view/shipping-address/address-renderer/default',
            'Magento_Checkout/js/view/shipping-information/address-renderer/default':
                'Eloab_VNAddress/js/view/shipping-information/address-renderer/default',
            'Eloab_VNAddress/js/form/element/district':
                'Eloab_VNAddress/js/form/element/district',
        }
    }
};
