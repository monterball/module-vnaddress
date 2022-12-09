<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Observer\Order\Address;

use Magento\Checkout\Api\Data\ShippingInformationInterface;

class LoadSubDistrict implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {

        $address = $observer->getAddress();

        if ($address->getData('sub_district')) {
            $extAttributes = $address->getExtensionAttributes();
            $extAttributes->setSubDistrict($address->getData('sub_district'));
            $address->setExtensionAttributes($extAttributes);
        }
    }
}
