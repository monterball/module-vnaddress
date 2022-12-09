<?php
/**
 * Copyright © t All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Model\ResourceModel\Region;


use Magento\Directory\Model\AllowedCountries;
use Magento\Framework\App\ObjectManager;

class Collection extends \Magento\Directory\Model\ResourceModel\Region\Collection
{
    const VN_LOCALE = 'vi_VN';
    const VN_ISO3_CODE = 'VNM';
    const VN_CODE = 'VN';

    /**
     * @var AllowedCountries
     */
    private $allowedCountriesReader;

    /**
     * Define main, country, locale region name tables
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magento\Directory\Model\Region::class, \Magento\Directory\Model\ResourceModel\Region::class);

        $this->_countryTable = $this->getTable('directory_country');
        $this->_regionNameTable = $this->getTable('directory_country_region_name');

        $this->addOrder('name', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
        $this->addOrder('default_name', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
    }

    /**
     * Initialize select object
     *
     * @return $this
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(['main_table' => $this->getMainTable()]);

        $this->addBindParam(':region_locale', self::VN_LOCALE);
        $this->getSelect()->joinLeft(
            ['rname' => $this->_regionNameTable],
            "main_table.region_id = rname.region_id AND rname.locale = '".self::VN_LOCALE."'",
            ['name']
        );

        $this->addCountryCodeFilter(self::VN_ISO3_CODE);

        return $this;
    }

    /**
     * Return Allowed Countries reader
     *
     * @return \Magento\Directory\Model\AllowedCountries
     * @deprecated 100.1.4
     */
    private function getAllowedCountriesReader()
    {
        if (!$this->allowedCountriesReader) {
            $this->allowedCountriesReader = ObjectManager::getInstance()->get(AllowedCountries::class);
        }

        return $this->allowedCountriesReader;
    }
}
