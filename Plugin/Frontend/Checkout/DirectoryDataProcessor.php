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
     * @var \Eloab\VNAddress\Model\ResourceModel\District\CollectionFactory
     **/
    protected $districtCol;

    /**
     * @var \Eloab\VNAddress\Model\ResourceModel\Subdistrict\CollectionFactory
     **/
    protected $subdistrictCol;

    /**
     * @var \Eloab\VNAddress\Helper\Address
     */
    protected $addressHelper;

    /**
     * @param \Eloab\VNAddress\Model\ResourceModel\District\CollectionFactory $districtCol
     * @param \Eloab\VNAddress\Model\ResourceModel\Subdistrict\CollectionFactory $subdistrictCol
     * @param \Eloab\VNAddress\Helper\Address $addressHelper
     */
    public function __construct(
        \Eloab\VNAddress\Model\ResourceModel\District\CollectionFactory    $districtCol,
        \Eloab\VNAddress\Model\ResourceModel\Subdistrict\CollectionFactory $subdistrictCol,
        \Eloab\VNAddress\Helper\Address $addressHelper
    )
    {
        $this->districtCol = $districtCol;
        $this->subdistrictCol = $subdistrictCol;
        $this->addressHelper = $addressHelper;
    }

    public function afterProcess(
        \Magento\Checkout\Block\Checkout\DirectoryDataProcessor $subject,
                                                                $result
    )
    {
        if (isset($result['components']['checkoutProvider']['dictionaries'])) {
            $result['components']['checkoutProvider']['dictionaries']['district'] = $this->prepareOnlyDistrict();
            $result['components']['checkoutProvider']['dictionaries']['sub_district'] = $this->prepareOnlySubdistrict();
        }

        if (isset($result['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['city'])) {
            $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['city']['options'] = $this->prepareOnlyDistrict();
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
                        $this->prepareOnlyDistrict();
                }
            }
        }

        $result['components']['checkoutProvider']['customAttributes']['sub_district'] = $this->getSubdistrictOptions();
        if (isset($result['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['sub_district'])) {
            $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['sub_district']['options'] = $this->prepareOnlySubdistrict();
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
                        $this->prepareOnlySubdistrict();
                }
            }
        }



        return $result;
    }

    private function prepareOnlyDistrict()
    {
        $districtData = $this->districtCol->create()->getData();
        $result = [];

        foreach ($districtData as $key => $district) {
            $districtName = $this->addressHelper->getDistrictNameById($district['district_id']);
            $result[$key]['label'] = $districtName['name'];
            $result[$key]['value'] = $district['district_id'];
            $result[$key]['region_id'] = $district['region_id'];
        }
        array_multisort($result, SORT_ASC, $districtData);

        return $result;
    }

    private function prepareOnlySubdistrict()
    {
        $subdistrictData = $this->subdistrictCol->create()->getData();
        $result = [];
        foreach ($subdistrictData as $key => $subdistrict) {
            $subdistrictName = $this->addressHelper->getSubDistrictNameById($subdistrict['subdistrict_id']);
            $result[$key]['label'] = $subdistrictName['name'];
            $result[$key]['value'] = $subdistrict['subdistrict_id'];
            $result[$key]['district_id'] = $subdistrict['district_id'];
        }
        array_multisort($result, SORT_ASC, $subdistrictData);
        return $result;
    }

    private function getSubdistrictOptions()
    {
        $options = [];
        $subdistrictData = $this->subdistrictCol->create()->getData();
        foreach ($subdistrictData as $key => $subdistrict) {
            $subdistrictName = $this->addressHelper->getSubDistrictNameById($subdistrict['subdistrict_id']);
            $options[] = [
                'value' => $subdistrict['subdistrict_id'],
                'label' => $subdistrictName
            ];
        }
        return $options;
    }
}
