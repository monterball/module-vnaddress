<?php
/**
 * Copyright © t All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Model;

use Magento\Framework\Model\AbstractModel;

class District extends AbstractModel
{

	const DISTRICT_ID = 'district_id';
	const DISTRICT_CODE = 'district_code';
	const DEFAULT_NAME = 'default_name';
    const REGION_ID = 'region_id';

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Eloab\VNAddress\Model\ResourceModel\District::class);
    }

    /**
     * @inheritDoc
     */
    public function getDistrictId()
    {
        return $this->getData(self::DISTRICT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setDistrictId($districtId)
    {
        return $this->setData(self::DISTRICT_ID, $districtId);
    }

    /**
     * @inheritDoc
     */
    public function getDistrictCode()
    {
        return $this->getData(self::DISTRICT_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setDistrictCode($districtCode)
    {
        return $this->setData(self::DISTRICT_CODE, $districtCode);
    }

    /**
     * @inheritDoc
     */
    public function getRegionId()
    {
        return $this->getData(self::REGION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setRegionId($regionId)
    {
        return $this->setData(self::REGION_ID, $regionId);
    }

    /**
     * @inheritDoc
     */
    public function getDefaultName()
    {
        return $this->getData(self::DEFAULT_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setDefaultName($defaultName)
    {
        return $this->setData(self::DEFAULT_NAME, $defaultName);
    }

    /**
     * Retrieve region name
     *
     * If name is not declared, then default_name is used
     *
     * @return string
     */
    public function getName()
    {
        $name = $this->getData('name');
        if ($name === null) {
            $name = $this->getData('default_name');
        }
        return $name;
    }
}
