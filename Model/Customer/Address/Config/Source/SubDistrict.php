<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Model\Customer\Address\Config\Source;

use Eloab\VNAddress\Helper\Address;
use Eloab\VNAddress\Model\ResourceModel\Subdistrict\CollectionFactory;

class SubDistrict extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /** @var Address  */
    protected $addressHelper;

    public function __construct(
        Address $addressHelper
    )   {
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
            $this->_options[] = ['value' => '', 'label' => ''];
            $subdistrictData = $this->addressHelper->getSubDistrictData();
            foreach ($subdistrictData as $key => $subdistrict) {
                $this->_options[] = ['value' => $subdistrict['subdistrict_id'], 'label' => $subdistrict['default_name']];
            }
        }
        return $this->_options;
    }
}

