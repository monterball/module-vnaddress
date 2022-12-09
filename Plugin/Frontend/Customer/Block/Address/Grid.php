<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Plugin\Frontend\Customer\Block\Address;

class Grid
{
    /** @var \Eloab\VNAddress\Helper\Address  */
    protected $addressHelper;

    public function __construct(
        \Eloab\VNAddress\Helper\Address $addressHelper
    ) {
        $this->addressHelper = $addressHelper;
    }

    /**
     * Get current additional customer addresses
     *
     * Return array of address interfaces if customer has additional addresses and false in other cases
     *
     * @return \Magento\Customer\Api\Data\AddressInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws NoSuchEntityException
     * @since 102.0.1
     */

    public function afterGetAdditionalAddresses(
        \Magento\Customer\Block\Address\Grid $subject,
        $result
    ) {
        if (count($result)) {
            foreach ($result as $key => $address) {
                $address = $this->addressHelper->resolveCityName($address);
                $result[$key] = $address;
            }
        }
        return $result;
    }
}
