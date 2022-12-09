<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Model\Customer\Address\Config\Selector;

use Eloab\VNAddress\Helper\Address;
use Eloab\VNAddress\Model\ResourceModel\Subdistrict\CollectionFactory;

class SubDistrict extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var CollectionFactory
     **/
    protected $subdistrictCol;
    /** @var Address  */
    protected $addressHelper;

    public function __construct(
        CollectionFactory $subdistrictCol,
        Address $addressHelper
    )   {
        $this->subdistrictCol = $subdistrictCol;
        $this->addressHelper = $addressHelper;
    }

    /**
     * getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options[] = ['value' => '', 'label' => 'Please select the ward.'];
            $subdistrictData = $this->subdistrictCol->create()->getData();
            foreach ($subdistrictData as $key => $subdistrict) {
                $districtName = $this->addressHelper->getDistrictName($subdistrict['district_id']);
                $this->_options[] = ['value' => $subdistrict['subdistrict_id'], 'label' => $districtName . ' - ' .$subdistrict['default_name']];
            }
        }
        return $this->_options;
    }
}

