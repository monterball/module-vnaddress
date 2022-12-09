<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Observer\Order\Address;

use Eloab\VNAddress\Helper\Address;

class RendererSubDistrict implements \Magento\Framework\Event\ObserverInterface
{
    /** @var Address */
    protected $addressHelper;

    /**
     * @param Address $addressHelper
     */
    public function __construct(
        Address $addressHelper
    ) {
        $this->addressHelper = $addressHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Zend_Db_Statement_Exception
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ): void {
        $address = $observer->getAddress();

        //Get and check the ward Id is exist or not
        if ($subdistrictId = $address->getData('sub_district')) {
            //Hidden this field
            $address->setData('sub_district', null);
            //Add the ward Name include on the District view
            $address->setData(
                'city',
                $this->getSubDistrictName($subdistrictId) . ', ' . $address->getData('city')
            );
        }
    }

    /**
     * @param $subdistrictId
     * @return string
     * @throws \Zend_Db_Statement_Exception
     */
    private function getSubDistrictName($subdistrictId): string
    {
        if ($subdistrict = $this->addressHelper->getSubDistrictNameById($subdistrictId)) {
            return $subdistrict['name'] ?? '';
        }
        return '';
    }
}
