<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Plugin\Frontend\Checkout;

class LayoutProcessor
{
    public function __construct(
        \Magento\Payment\Model\Config $paymentModelConfig
    )
    {
        $this->paymentModelConfig = $paymentModelConfig;
    }

    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        $result
    ) {
        //Convert new field for district in shipping form
        $cityField = $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city'];
        $cityField['config']['elementTmpl'] = 'Eloab_VNAddress/form/element/district';
        $cityField['component'] = 'Eloab_VNAddress/js/form/element/district';
        $cityField['label'] = __('District');
        $cityField["dataScope"] = "shippingAddress.city";
        $cityField['sortOrder'] = 100;
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city'] = $cityField;

        //Convert new field for district in billing form
        $cityFields = $result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'];
        foreach ($cityFields as $paymentGroup => $groupConfig) {
            if (isset($groupConfig['component']) AND $groupConfig['component'] === 'Magento_Checkout/js/view/billing-address') {
                $cityFieldBilling = $result['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$paymentGroup]['children']['form-fields']['children']['city'];

                $cityFieldBilling['config']['elementTmpl'] = 'Eloab_VNAddress/form/element/district';
                $cityFieldBilling['component'] = 'Eloab_VNAddress/js/form/element/district';
                $cityFieldBilling['label'] = __('District');
                $cityFieldBilling["dataScope"] = $groupConfig['dataScopePrefix'] . ".city";
                $cityFieldBilling['sortOrder'] = 100;

                $result['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$paymentGroup]['children']['form-fields']['children']['city'] = $cityFieldBilling;

            }
        }


        //Add new field for subdistrict in shipping form
        $subDistrict = $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city'];
        $subDistrict['label'] = __('Ward');
        $subDistrict['sortOrder'] = 105;
        $subDistrict['config']['elementTmpl'] = 'Eloab_VNAddress/form/element/district';
        $subDistrict['component'] = 'Eloab_VNAddress/js/form/element/subdistrict';
        unset($subDistrict['validation']['max_text_length']);
        unset($subDistrict['validation']['min_text_length']);
        $subDistrict["options"] = [];
        $subDistrict["dataScope"] = "shippingAddress.custom_attributes.sub_district";
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['sub_district'] = $subDistrict;

        //Convert new field for district in billing form
        $subDistricts = $result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'];
        foreach ($subDistricts as $paymentGroup => $groupConfig) {
            if (isset($groupConfig['component']) AND $groupConfig['component'] === 'Magento_Checkout/js/view/billing-address') {
                $subDistrictFieldBilling = $result['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$paymentGroup]['children']['form-fields']['children']['city'];

                $subDistrictFieldBilling['label'] = __('Ward');
                $subDistrictFieldBilling['sortOrder'] = 105;
                $subDistrictFieldBilling['config']['elementTmpl'] = 'Eloab_VNAddress/form/element/district';
                $subDistrictFieldBilling['component'] = 'Eloab_VNAddress/js/form/element/subdistrict';
                unset($subDistrictFieldBilling['validation']['max_text_length']);
                unset($subDistrictFieldBilling['validation']['min_text_length']);
                $subDistrictFieldBilling["options"] = [];
                $subDistrictFieldBilling["dataScope"] = $groupConfig['dataScopePrefix'] . ".custom_attributes.sub_district";

                $result['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$paymentGroup]['children']['form-fields']['children']['sub_district'] = $subDistrictFieldBilling;

            }
        }

        return $result;
    }
}
