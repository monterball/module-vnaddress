<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Model\Customer\Address\Attribute\Source;

class SubDistrict extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var \Eloab\VNAddress\Model\ResourceModel\Subdistrict\CollectionFactory
     **/
    protected $subdistrictCol;

    public function __construct(
        \Eloab\VNAddress\Model\ResourceModel\Subdistrict\CollectionFactory $subdistrictCol
    )   {
        $this->subdistrictCol = $subdistrictCol;
    }

    /**
     * getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $subdistrictData = $this->subdistrictCol->create()->getData();
            foreach ($subdistrictData as $key => $subdistrict) {
                $this->_options[] = ['value' => $subdistrict['subdistrict_id'], 'label' => $subdistrict['default_name']];
            }
        }
        return $this->_options;
    }
}

