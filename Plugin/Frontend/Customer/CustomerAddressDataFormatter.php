<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Plugin\Frontend\Customer;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Model\Address\Config as AddressConfig;
use Magento\Customer\Model\Address\CustomAttributesProcessor;
use Magento\Customer\Model\Address\Mapper;

class CustomerAddressDataFormatter
{
    /** @var \Eloab\VNAddress\Helper\Address  */
    protected $addressHelper;

    /**
     * @var \Magento\Customer\Model\Address\Config
     */
    protected $addressConfig;

    /**
     * @var CustomAttributesProcessor
     */
    private $customAttributesProcessor;

    /**
     * @var Mapper
     */
    protected $addressMapper;

    public function __construct(
        \Eloab\VNAddress\Helper\Address $addressHelper,
        \Magento\Customer\Model\Address\Config $addressConfig,
        Mapper $addressMapper,
        CustomAttributesProcessor $customAttributesProcessor
    ) {
        $this->addressHelper = $addressHelper;
        $this->addressConfig = $addressConfig;
        $this->addressMapper = $addressMapper;
        $this->customAttributesProcessor = $customAttributesProcessor;
    }

    public function aroundPrepareAddress(
        \Magento\Customer\Model\Address\CustomerAddressDataFormatter $subject,
        callable $proceed,
        AddressInterface $customerAddress
    ): array {
        try {
            $resultAddress = [
                'id' => $customerAddress->getId(),
                'customer_id' => $customerAddress->getCustomerId(),
                'company' => $customerAddress->getCompany(),
                'prefix' => $customerAddress->getPrefix(),
                'firstname' => $customerAddress->getFirstname(),
                'lastname' => $customerAddress->getLastname(),
                'middlename' => $customerAddress->getMiddlename(),
                'suffix' => $customerAddress->getSuffix(),
                'street' => $customerAddress->getStreet(),
                'city' => $customerAddress->getCity(),
                'region' => [
                    'region' => $customerAddress->getRegion()->getRegion(),
                    'region_code' => $customerAddress->getRegion()->getRegionCode(),
                    'region_id' => $customerAddress->getRegion()->getRegionId(),
                ],
                'region_id' => $customerAddress->getRegionId(),
                'postcode' => $customerAddress->getPostcode(),
                'country_id' => $customerAddress->getCountryId(),
                'telephone' => $customerAddress->getTelephone(),
                'fax' => $customerAddress->getFax(),
                'default_billing' => $customerAddress->isDefaultBilling(),
                'default_shipping' => $customerAddress->isDefaultShipping(),
                'inline' => $this->addressHelper->getAddressRender(
                    $customerAddress,
                    AddressConfig::DEFAULT_ADDRESS_FORMAT
                ),
                'custom_attributes' => [],
                'extension_attributes' => $customerAddress->getExtensionAttributes(),
                'vat_id' => $customerAddress->getVatId()
            ];

            if ($customerAddress->getCustomAttributes()) {
                $customerAddress = $customerAddress->__toArray();
                $resultAddress['custom_attributes'] = $this->customAttributesProcessor->filterNotVisibleAttributes(
                    $customerAddress['custom_attributes']
                );
            }

            return $resultAddress;
        } catch (\Exception $exception) {
            return $proceed();
        }
    }
}
