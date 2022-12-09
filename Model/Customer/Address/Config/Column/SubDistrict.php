<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Model\Customer\Address\Config\Column;

use Eloab\VNAddress\Helper\Address;
use Eloab\VNAddress\Model\ResourceModel\Subdistrict\CollectionFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class SubDistrict extends Column
{
    /**
     * @var CollectionFactory
     **/
    protected $subdistrictCol;
    /** @var Address  */
    protected $addressHelper;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CollectionFactory $subdistrictCol,
        Address $addressHelper,
        array $components = [],
        array $data = []
    )   {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->subdistrictCol = $subdistrictCol;
        $this->addressHelper = $addressHelper;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $address = $objectManager->create('Magento\Customer\Model\Address')->load($item['entity_id']);
                if ($address->getData('sub_district')) {
                    $subdistrictName = $this->addressHelper->getSubDistrictName($address);
                    $item[$this->getData('name')] = $subdistrictName;
                }

            }
        }
        return $dataSource;
    }
}

