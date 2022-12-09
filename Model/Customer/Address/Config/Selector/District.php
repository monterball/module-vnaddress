<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Model\Customer\Address\Config\Selector;

use Magento\Directory\Model\Region;
use Eloab\VNAddress\Model\ResourceModel\District\CollectionFactory;

class District extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /** @var CollectionFactory  */
    protected $districtCol;
    /** @var Region */
    protected $region;

    public function __construct(
        CollectionFactory $districtCol,
        Region $region
    )   {
        $this->districtCol = $districtCol;
        $this->region = $region;
    }

    /**
     * getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $districtData = $this->districtCol->create()->getData();
            foreach ($districtData as $key => $district) {
                $regionName = $this->region->load($district['region_id'])->getName();
                $this->_options[] = ['value' => $district['district_id'], 'label' => $regionName . ' - ' .$district['default_name']];
            }
        }
        return $this->_options;
    }
}

