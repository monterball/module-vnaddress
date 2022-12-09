<?php
/**
 * Copyright © t All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Model\ResourceModel\Subdistrict;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'subdistrict_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Eloab\VNAddress\Model\Subdistrict::class,
            \Eloab\VNAddress\Model\ResourceModel\Subdistrict::class
        );
    }
}
