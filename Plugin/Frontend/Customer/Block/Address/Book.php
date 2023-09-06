<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Plugin\Frontend\Customer\Block\Address;

use Magento\Customer\Model\Address\Mapper;

class Book
{
    /** @var \Eloab\VNAddress\Helper\Address  */
    protected $addressHelper;

    /**
     * @var \Magento\Customer\Model\Address\Config
     */
    protected $addressConfig;

    /**
     * @var Mapper
     */
    protected $addressMapper;

    public function __construct(
        \Eloab\VNAddress\Helper\Address $addressHelper,
        \Magento\Customer\Model\Address\Config $addressConfig,
        Mapper $addressMapper
    ) {
        $this->addressHelper = $addressHelper;
        $this->addressConfig = $addressConfig;
        $this->addressMapper = $addressMapper;
    }

    /**
     * Get current additional customer addresses
     *
     * Return array of address interfaces if customer has additional addresses and false in other cases
     *
     * @return \Magento\Customer\Api\Data\AddressInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws NoSuchEntityException
     * @since 102.0.1
     */

    public function afterGetAddressById(
        \Magento\Customer\Block\Address\Book $subject,
        $result
    ) {
        if (!empty($result)) {
            $result = $this->addressHelper->resolveCityName($result);
        }
        return $result;
    }

    /**
     * Render an address as HTML and return the result
     * @param \Magento\Customer\Block\Address\Book $subject
     * @param callable $proceed
     * @param \Magento\Customer\Api\Data\AddressInterface|null $address
     * @return string
     */
    public function aroundGetAddressHtml(
        \Magento\Customer\Block\Address\Book $subject,
        callable $proceed,
        \Magento\Customer\Api\Data\AddressInterface $address = null
    ): string {
        if ($address !== null) {
            /** @var \Magento\Customer\Block\Address\Renderer\RendererInterface $renderer */
            $renderer = $this->addressConfig->getFormatByCode('html')->getRenderer();
            try {
                if (!empty($valueSDistrict = $this->addressMapper->toFlatArray($address)['sub_district'])) {
                    $addressArray = [];
                    foreach ($this->addressMapper->toFlatArray($address) as $key => $value) {
                        if ($key == 'sub_district') {
                            $addressArray[$key] = '';
                        } elseif ($key == 'city') {
                            $addressArray[$key] = $this->addressHelper->getSubDistrictNameById($valueSDistrict)
                                . ', ' . $value;
                        } else {
                            $addressArray[$key] = $value;
                        }
                    }
                    return $renderer->renderArray($addressArray);
                }
            } catch (\Exception $exception) {
                return $proceed();
            }

            return $proceed();
        }
        return '';
    }
}
