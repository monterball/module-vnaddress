<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Model\Element\UiComponent\DataProvider\Ward;

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
//        //create our filter for VN region
//        $filter = $this->filterBuilder
//            ->setField('country_id')
//            ->setValue('VN')
//            ->setConditionType('eq')
//            ->create();
//
//        $this->addFilter($filter);

        if (!$this->searchCriteria) {
            $this->searchCriteria = $this->searchCriteriaBuilder->create();
            $this->searchCriteria->setRequestName($this->name);
        }


        return $this->searchCriteria;
    }

}
