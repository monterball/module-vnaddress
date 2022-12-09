<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eloab\VNAddress\Plugin\Adminhtml\Order;

use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Controller\Adminhtml\Order\AddressSave as MainAddressSave;
use Magento\Customer\Model\AddressFactory;

class AddressSave
{

    /** @var \Eloab\VNAddress\Helper\Address  */
    protected $addressHelper;

    /** @var AddressFactory */
    protected $addressFactory;

    public function __construct(
        \Eloab\VNAddress\Helper\Address $addressHelper,
        AddressFactory $addressFactory
    ) {
        $this->addressHelper = $addressHelper;
        $this->addressFactory = $addressFactory;
    }


    /**
     * @param MainAddressSave $subject
     */
    public function aroundExecute(
        MainAddressSave $subject,
        \Closure $proceed
    ) {
        $addressId = $subject->getRequest()->getParam('address_id');
        $subDistrictId = $subject->getRequest()->getParam('subdistrict');

        $returnResult = $proceed();
        /** @var \Magento\Customer\Model\Address $address */
        $this->addressHelper->saveSubdistrictCustomerAddress($addressId, $subDistrictId);
        return $returnResult;
    }

}
