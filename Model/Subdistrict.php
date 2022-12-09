<?php
/**
 * Copyright © t All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Model;

use Magento\Framework\Model\AbstractModel;

class Subdistrict extends AbstractModel
{

    const SUBDISTRICT_ID = 'subdistrict_id';
    const SUBDISTRICT_CODE = 'subdistrict_code';
    const DISTRICT_ID = 'district_id';
    const DEFAULT_NAME = 'default_name';
    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Eloab\VNAddress\Model\ResourceModel\Subdistrict::class);
    }

    /**
     * @inheritDoc
     */
    public function getSubdistrictId()
    {
        return $this->getData(self::SUBDISTRICT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSubdistrictId($subdistrictId)
    {
        return $this->setData(self::SUBDISTRICT_ID, $subdistrictId);
    }

    /**
     * @inheritDoc
     */
    public function getSubdistrictCode()
    {
        return $this->getData(self::SUBDISTRICT_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setSubdistrictCode($subdistrictCode)
    {
        return $this->setData(self::SUBDISTRICT_CODE, $subdistrictCode);
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
