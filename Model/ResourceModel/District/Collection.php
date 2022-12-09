<?php
/**
 * Copyright © t All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Model\ResourceModel\District;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'district_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Eloab\VNAddress\Model\District::class,
            \Eloab\VNAddress\Model\ResourceModel\District::class
        );
    }
}
