<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Observer\Quote\Address;

use Magento\Checkout\Api\Data\ShippingInformationInterface;

class SaveSubDistrict implements \Magento\Framework\Event\ObserverInterface
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

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $state = $objectManager->get('Magento\Framework\App\State');
        if ($state->getAreaCode() == 'frontend' || $state->getAreaCode() == 'webapi_rest') {
            $address = $observer->getQuoteAddress();
            $extAttributes = $address->getExtensionAttributes();

            if ($extAttributes->getSubDistrict()) {
                $address->setData('sub_district',$extAttributes->getSubDistrict());
            }
        }
    }
}
