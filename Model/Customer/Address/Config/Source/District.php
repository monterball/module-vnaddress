<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Model\Customer\Address\Config\Source;

use Eloab\VNAddress\Helper\Address;

class District extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
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
            $districtData = $this->addressHelper->getDistrictData();
            foreach ($districtData as $key => $district) {
                $this->_options[] = ['value' => $district['district_id'], 'label' => $district['default_name']];
            }
        }
        return $this->_options;
    }
}

