<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Plugin\Frontend\Checkout;

class DirectoryDataProcessor
{
    /**
     * @var \Eloab\VNAddress\Helper\Address
     */
    protected $addressHelper;

    /**
     * @var \Magento\Framework\Locale\Resolver
     */
    protected $resolver;

    protected $locale;

    protected $subDistrictOptions;

    /**
     * @param \Eloab\VNAddress\Helper\Address $addressHelper
     * @param \Magento\Framework\Locale\Resolver $resolver
     */
    public function __construct(
        \Eloab\VNAddress\Helper\Address $addressHelper,
        \Magento\Framework\Locale\Resolver $resolver
    )
    {
        $this->addressHelper = $addressHelper;
        $this->resolver = $resolver;
    }

    public function afterProcess(
        \Magento\Checkout\Block\Checkout\DirectoryDataProcessor $subject,
                                                                $result
    )
    {
        //Prepare data
        if (!$this->locale) {
            $this->locale = $this->resolver->getLocale();
        }
        $onlyDistrict = $this->prepareOnlyDistrict();
        $onlySubDistrict = $this->prepareOnlySubdistrict();


        if (isset($result['components']['checkoutProvider']['dictionaries'])) {
            $result['components']['checkoutProvider']['dictionaries']['district'] = $onlyDistrict;
            $result['components']['checkoutProvider']['dictionaries']['sub_district'] = $onlySubDistrict;
        }

        if (isset($result['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['city'])) {
            $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['city']['options'] = $onlyDistrict;
        }
        //Convert new field for district in billing form
        $cityFields = $result['components']['checkout']['children']['steps']['children']['billing-step']['children']
        ['payment']['children']['payments-list']['children'];
        foreach ($cityFields as $paymentGroup => $groupConfig) {
            if (isset($groupConfig['component']) AND $groupConfig['component'] === 'Magento_Checkout/js/view/billing-address') {
                if (isset($result['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['children']['payments-list']['children'][$paymentGroup]['children']['form-fields']['children']['city'])) {
                    $result['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['children']['payments-list']['children'][$paymentGroup]['children']['form-fields']['children']['city']['options'] =
                        $onlyDistrict;
                }
            }
        }

        $result['components']['checkoutProvider']['customAttributes']['sub_district'] = $this->subDistrictOptions;
        if (isset($result['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['sub_district'])) {
            $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['sub_district']['options'] = $onlySubDistrict;
        }

        //Convert new field for district in billing form
        $subDistrictFields = $result['components']['checkout']['children']['steps']['children']['billing-step']['children']
        ['payment']['children']['payments-list']['children'];
        foreach ($subDistrictFields as $paymentGroup => $groupConfig) {
            if (isset($groupConfig['component']) AND $groupConfig['component'] === 'Magento_Checkout/js/view/billing-address') {
                if (isset($result['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['children']['payments-list']['children'][$paymentGroup]['children']['form-fields']['children']['sub_district'])) {
                    $result['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['children']['payments-list']['children'][$paymentGroup]['children']['form-fields']['children']['sub_district']['options'] =
                        $onlySubDistrict;
                }
            }
        }



        return $result;
    }

    private function prepareOnlyDistrict()
    {
        $districtData = $this->addressHelper->getDistrictData();
        $result = [];

        foreach ($districtData as $key => $district) {
            $result[$key]['label'] = $district['name'];
            $result[$key]['value'] = $district['district_id'];
            $result[$key]['region_id'] = $district['region_id'];
        }

        return $result;
    }

    private function prepareOnlySubdistrict()
    {
        $subdistrictData = $this->addressHelper->getSubDistrictData();
        $result = [];
        $this->subDistrictOptions = [];
        foreach ($subdistrictData as $key => $subdistrict) {
            $result[$key]['label'] = $subdistrict['name'];
            $result[$key]['value'] = $subdistrict['subdistrict_id'];
            $result[$key]['district_id'] = $subdistrict['district_id'];

            //Set Subdistrict Options
            $this->subDistrictOptions[] = [
                'value' => $result[$key]['value'],
                'label' => $result[$key]['label']
            ];
        }

        return $result;
    }
}
