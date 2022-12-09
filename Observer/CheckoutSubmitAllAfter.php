<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Observer;

use Magento\Checkout\Api\Data\ShippingInformationInterface;

class CheckoutSubmitAllAfter implements \Magento\Framework\Event\ObserverInterface
{

    private $logger;

    /**
     * @param Context $context
     * @param ShippingInformationInterface $addressInformation
     * @param array $data
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Model\AddressFactory $customerAddressFactory
    ) {
        $this->logger = $logger;
        $this->customerAddressFactory = $customerAddressFactory;
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

        $order = $observer->getOrder();

        $shippingAddress = $order->getShippingAddress();
        $billingAddress = $order->getBillingAddress();

        $customerBillingAddressId = $shippingAddress->getData('customer_address_id');
        $customerShippingAddressId = $billingAddress->getData('customer_address_id');
        $subDistrictShipping = $shippingAddress->getData('sub_district');
        $subDistrictBilling = $shippingAddress->getData('sub_district');

        if ($customerShippingAddressId && $subDistrictShipping && is_numeric($subDistrictShipping)) {

            $customerShippingAddress = $this->customerAddressFactory->create()->load($customerShippingAddressId);
            $customerShippingAddress->setData('sub_district', $subDistrictShipping)->save();
        }

        if ($customerBillingAddressId != $customerShippingAddressId && $subDistrictBilling && is_numeric($subDistrictBilling)) {
            $customerBillingAddress = $this->customerAddressFactory->create()->load($customerBillingAddressId);
            $customerBillingAddress->setData('sub_district', $subDistrictBilling)->save();
        }

        $this->logger->debug($shippingAddress->getData('sub_district'));

    }
}
