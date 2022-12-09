<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class VNAddressConfigProvider implements ConfigProviderInterface
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


    public function getConfig()
    {
        $configDistrict = [];
        $configSubdistrict = [];

        $districtData = $this->districtCol->create()->getData();
        $subdistrictData = $this->subdistrictCol->create()->getData();


        foreach ($subdistrictData as $subdistrict) {
            $configSubdistrict[$subdistrict['district_id']][] = $subdistrict;
        }

        foreach ($districtData as $district) {
            $aDistrictData = $district;
            if (!empty($configSubdistrict[$district['district_id']])) {
                $aDistrictData['subdistricts'] = $configSubdistrict[$district['district_id']];
            }
            $configDistrict[$district['region_id']][] = $aDistrictData;
        }

        $configArray['vnAddressData'] = $configDistrict;
        $configArray['vnAddressDistrict'] = $this->prepareOnlyDistrict($districtData);
        $configArray['vnAddressSubdistrict'] = $this->prepareOnlySubdistrict($subdistrictData);

        return $configArray;
    }

    private function prepareOnlyDistrict($districtData)
    {
        $result = [];
        foreach ($districtData as $district) {
            $districtName = $this->addressHelper->getDistrictNameById($district['district_id']);
            $district['default_name'] = $districtName['name'];
            $result[$district['region_id']][] = $district;
        }
        return $result;
    }

    private function prepareOnlySubdistrict($subdistrictData)
    {
        $result = [];
        foreach ($subdistrictData as $subdistrict) {
            $subdistrictName = $this->addressHelper->getSubDistrictNameById($subdistrict['subdistrict_id']);
            $subdistrict['default_name'] = $subdistrictName['name'];
            $result[$subdistrict['district_id']][] = $subdistrict;
        }
        return $result;
    }
}
