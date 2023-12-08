<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Model\ResourceModel\District;

use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'district_id';

    /**
     * Table name
     *
     * @var string
     */
    protected $_nameTable;

    /**
     * @var string
     */
    protected string $localeCode = "en_US";

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Eloab\VNAddress\Model\District::class,
            \Eloab\VNAddress\Model\ResourceModel\District::class
        );
        $this->_nameTable = $this->getTable('directory_country_region_district_name');
    }

    /**
     * @param string $localeCode
     * @return $this
     */
    public function setLocale(string $localeCode = 'en_US') : Collection
    {
        $this->localeCode = $localeCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocale() : string
    {
        return $this->localeCode;
    }

    /**
     * Initialize select
     *
     * @return void
     */
    public function _initSelect()
    {
        parent::_initSelect();

        $this->_select->joinLeft(
            ['name_table' => $this->_nameTable],
            'name_table.district_id = main_table.district_id AND name_table.locale = "'
            . $this->localeCode . '"',
            ['name']
        );
        $this->addFilterToMap('name', 'name_table.name');
        $this->addFilterToMap('district_id', 'name_table.district_id');
    }
}
