<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Plugin\Frontend;

class Checkout
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
     * Constructor
     * @param \Eloab\VNAddress\Model\ResourceModel\District\CollectionFactory
     * @param \Eloab\VNAddress\Model\ResourceModel\Subdistrict\CollectionFactory
     * @param \Eloab\VNAddress\Helper\Address $addressHelper
     **/

    public function __construct(
        \Eloab\VNAddress\Model\ResourceModel\District\CollectionFactory $districtCol,
        \Eloab\VNAddress\Model\ResourceModel\Subdistrict\CollectionFactory $subdistrictCol,
        \Eloab\VNAddress\Helper\Address $addressHelper
    )   {
        $this->districtCol = $districtCol;
        $this->subdistrictCol = $subdistrictCol;
        $this->addressHelper = $addressHelper;
    }

    public function afterGetJsLayout(
        \Magento\Checkout\Block\Onepage $subject,
        $result
    ) {
        //Your plugin code
        $result = json_decode($result, true);
        $cityField = $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['region_id'];

        $cityField['config']['elementTmpl'] = 'Eloab_VNAddress/form/element/district';
        $cityField['component'] = 'Eloab_VNAddress/js/form/element/district';
        $cityField["imports"]["initialOptions"] = 'index = checkoutProvider:vnAddressDistrict';
        $cityField["imports"]["setOptions"] = "index = checkoutProvider:vnAddressDistrict";
        $cityField['label'] = __('District');
        $cityField['sortOrder'] = 100;
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city'] = $cityField;



        $subDistrict = $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['region_id'];
        $subDistrict['label'] = __('Ward');
        $subDistrict['sortOrder'] = 105;
        $subDistrict['config']['elementTmpl'] = 'Eloab_VNAddress/form/element/district';
        $subDistrict['component'] = 'Eloab_VNAddress/js/form/element/subdistrict';
        $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['sub_district'] = $subDistrict;
        if (isset($result['components']['checkoutProvider']['dictionaries'])) {
            $result['components']['checkoutProvider']['dictionaries']['district'] = $this->prepareOnlyDistrict();
            $result['components']['checkoutProvider']['dictionaries']['sub_district'] = $this->prepareOnlySubdistrict();
        }

        return json_encode($result);
    }

    private function prepareOnlyDistrict()
    {
        $districtData = $this->districtCol->create()->getData();
        $result = [];
        foreach ($districtData as $key => $district) {
            $districtName = $this->addressHelper->getDistrictNameById($district['district_id']);
            $result[$key]['value'] = $district['district_id'];
            $result[$key]['region_id'] = $district['region_id'];
            $result[$key]['label'] = $districtName;
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
            $result[$key]['value'] = $subdistrict['subdistrict_id'];
            $result[$key]['district'] = $subdistrict['district_id'];
            $result[$key]['label'] = $subdistrictName;
        }
        array_multisort($result, SORT_ASC, $subdistrictData);
        return $result;
    }
}
