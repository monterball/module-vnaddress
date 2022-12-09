<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Plugin\Frontend\Customer\Block\Account\Dashboard;

use Magento\Customer\Helper\Session\CurrentCustomerAddress;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;

class Address
{

    /** @var \Eloab\VNAddress\Helper\Address  */
    protected $addressHelper;

    /** @var \Magento\Customer\Helper\Session\CurrentCustomerAddress */
    protected $currentCustomerAddress;

    /**
     * @param \Eloab\VNAddress\Helper\Address $addressHelper
     * @param CurrentCustomerAddress $currentCustomerAddress
     */
    public function __construct(
        \Eloab\VNAddress\Helper\Address $addressHelper,
        \Magento\Customer\Helper\Session\CurrentCustomerAddress $currentCustomerAddress
    ) {
        $this->addressHelper = $addressHelper;
        $this->currentCustomerAddress = $currentCustomerAddress;
    }

    /**
     * HTML for Shipping Address
     *
     * @param \Magento\Customer\Block\Account\Dashboard\Address $subject
     * @param callable $proceed
     * @return Phrase|string
     */
    public function aroundGetPrimaryShippingAddressHtml(
        \Magento\Customer\Block\Account\Dashboard\Address $subject,
        callable $proceed
    )
    {
        try {
            $address = $this->currentCustomerAddress->getDefaultShippingAddress();
        } catch (NoSuchEntityException $e) {
            return $proceed();
        }

        if ($address) {
            return $this->addressHelper->getAddressRender($address);
        } else {
            return __('You have not set a default shipping address.');
        }
    }

    /**
     * HTML for Billing Address
     *
     * @param \Magento\Customer\Block\Account\Dashboard\Address $subject
     * @param callable $proceed
     * @return Phrase|string
     */
    public function aroundGetPrimaryBillingAddressHtml(
        \Magento\Customer\Block\Account\Dashboard\Address $subject,
        callable $proceed
    )
    {
        try {
            $address = $this->currentCustomerAddress->getDefaultBillingAddress();
        } catch (NoSuchEntityException $e) {
            return $proceed();
        }

        if ($address) {
            return $this->addressHelper->getAddressRender($address);
        } else {
            return $subject->escapeHtml(__('You have not set a default billing address.'));
        }
    }
}
