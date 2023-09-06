<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Model\Element\UiComponent\DataProvider\District;

/**
 * Class DataProvider
 *
 * @api
 */
class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * Returns search criteria with VN regions
     *
     * @return \Magento\Framework\Api\Search\SearchCriteria
     */
    public function getSearchCriteria()
    {
        $regionId = $this->request->getParam('region_id');
        if ($regionId) {
            //create our filter for the district request
            $filter = $this->filterBuilder
                ->setField('region_id')
                ->setValue('')
                ->setConditionType('eq')
                ->create();

            $this->addFilter($filter);
        }

        if (!$this->searchCriteria) {
            $this->searchCriteria = $this->searchCriteriaBuilder->create();
            $this->searchCriteria->setRequestName($this->name);
        }


        return $this->searchCriteria;
    }

}
