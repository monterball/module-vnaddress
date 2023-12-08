<?php
declare(strict_types=1);
/**
 * Copyright © Eloab, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eloab\VNAddress\Helper;

use Eloab\VNAddress\Model\ResourceModel\Subdistrict\CollectionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class AddressQuerySupporter extends AbstractHelper
{
    protected $subDistrictCollectionFactory;
    protected $districtCollectionFactory;
    protected $regionCollectionFactory;

    public function __construct(
        Context                                                               $context,
        CollectionFactory $subDistrictCollectionFactory,
        \Eloab\VNAddress\Model\ResourceModel\District\CollectionFactory    $districtCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory       $regionCollectionFactory
    ) {
        $this->districtCollectionFactory = $districtCollectionFactory;
        $this->subDistrictCollectionFactory = $subDistrictCollectionFactory;
        $this->regionCollectionFactory = $regionCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @param $name
     * @return \Magento\Framework\DataObject
     */
    public function getArea($name)
    {
        return $this->regionCollectionFactory->create()
            ->addFieldToFilter(
                'country_id',
                \Eloab\VNAddress\Setup\Patch\Data\AddAreaForVN::VN_COUNTRY_ID
            )
            ->addFieldToFilter('default_name', $name)->getFirstItem();
    }

    /**
     * @return \Magento\Directory\Model\ResourceModel\Region\Collection
     */
    public function getAreaList()
    {
        return $this->regionCollectionFactory->create()
            ->addFieldToFilter(
                'country_id',
                \Eloab\VNAddress\Setup\Patch\Data\AddAreaForVN::VN_COUNTRY_ID
            );
    }

    /**
     * @param $areaId
     * @return \Magento\Framework\DataObject
     */
    public function getAreaName($areaId)
    {
        return $this->regionCollectionFactory->create()
            ->addFieldToFilter('region_id', $areaId)
            ->getFirstItem()->getData('default_name');
    }

    /**
     * @param $name
     * @return \Magento\Framework\DataObject
     */
    public function getDistrict($name)
    {
        return $this->districtCollectionFactory->create()
            ->addFieldToFilter('default_name', $name)->getFirstItem();
    }

    /**
     * @param $districtId
     * @return array|mixed|string|null
     */
    public function getDistrictName($districtId)
    {
        if (!$districtId) {
            return '';
        }
        return $this->districtCollectionFactory->create()
            ->addFieldToFilter('district_id', $districtId)
            ->getFirstItem()->getData('default_name');
    }

    /**
     * @param $subDistrictId
     * @return array|mixed|string|null
     */
    public function getSubDistrictName($subDistrictId)
    {
        if (!$subDistrictId) {
            return '';
        }
        return $this->subDistrictCollectionFactory->create()
            ->addFieldToFilter('subdistrict_id', $subDistrictId)
            ->getFirstItem()->getData('default_name');
    }

    /**
     * @return \Eloab\VNAddress\Model\ResourceModel\District\Collection
     */
    public function getDistrictList()
    {
        return $this->districtCollectionFactory->create();
    }

    /**
     * @param $name
     * @param $districtId
     * @return \Magento\Framework\DataObject
     */
    public function getSubDistrict($name, $districtId)
    {
        return $this->subDistrictCollectionFactory->create()
            ->addFieldToFilter('district_id', $districtId)
            ->addFieldToFilter('default_name', $name)->getFirstItem();
    }

    /**
     * @param $districtId
     * @return \Eloab\VNAddress\Model\ResourceModel\Subdistrict\Collection
     */
    public function getSubDistrictList($districtId)
    {
        return $this->subDistrictCollectionFactory->create()->addFieldToFilter('district_id', $districtId);
    }
}
