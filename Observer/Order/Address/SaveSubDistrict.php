<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Observer\Order\Address;

use Magento\Quote\Api\CartRepositoryInterface;

class SaveSubDistrict implements \Magento\Framework\Event\ObserverInterface
{
    protected $quoteRepository;
    protected $addressFactory;
    protected $districtFactory;

    public function __construct(
        \Magento\Quote\Model\Quote\AddressFactory $addressFactory,
        CartRepositoryInterface $quoteRepository,
        \Eloab\VNAddress\Model\DistrictFactory $districtFactory
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->addressFactory = $addressFactory;
        $this->districtFactory = $districtFactory;
    }

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
            $address = $observer->getAddress();

            $order = $address->getOrder();
            $quoteId = $order->getQuoteId();
            $quote = $this->quoteRepository->get($quoteId);
            $currentQuoteAddress = $quote->getShippingAddress();

            $quoteAddress = $this->addressFactory->create()->load($currentQuoteAddress->getId());
            if ($quoteAddress->getData('sub_district')) {
                $address->setData('sub_district', $quoteAddress->getData('sub_district'));
            }

            // Save city/district as the city/district name
            $district = $address->getData('city');
            $districtObj = null;
            if (is_numeric($district)) {
                $districtObj = $this->districtFactory->create()->load($district);

            }
            if ($districtObj) {
                $address->setData('city', $districtObj->getName());
            }
        }
    }
}
