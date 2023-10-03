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
     * @var \Eloab\VNAddress\Helper\Address
     */
    protected $addressHelper;


     /**
     * Constructor
     * @param \Eloab\VNAddress\Helper\Address $addressHelper
     **/

    public function __construct(
        \Eloab\VNAddress\Helper\Address $addressHelper
    )   {
        $this->addressHelper = $addressHelper;
    }


    public function getConfig()
    {
        //District
        $districtData = $this->addressHelper->getDistrictData();
        //Subdistrict
        $subdistrictData = $this->addressHelper->getSubDistrictData();

        $configArray['vnAddressDistrict'] = $this->prepareOnlyDistrict($districtData);
        $configArray['vnAddressSubdistrict'] = $this->prepareOnlySubdistrict($subdistrictData);

        return $configArray;
    }

    private function prepareOnlyDistrict($districtData)
    {
        $result = [];
        foreach ($districtData as $district) {
            $district['default_name'] = $district['name'];
            $result[$district['region_id']][] = $district;
        }
        return $result;
    }

    private function prepareOnlySubdistrict($subdistrictData)
    {
        $result = [];
        foreach ($subdistrictData as $subdistrict) {
            $subdistrict['default_name'] = $subdistrict['name'];
            $result[$subdistrict['district_id']][] = $subdistrict;
        }
        return $result;
    }
}
