<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eloab\VNAddress\Block\Adminhtml\Order;

use Eloab\VNAddress\Helper\Address;
use Eloab\VNAddress\Helper\AddressQuerySupporter;
use Magento\Backend\Block\Template\Context;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Registry;

class RenderAddress extends \Magento\Backend\Block\Template
{
    /** @var string  */
    protected $_template = 'Eloab_VNAddress::renderAddressJs.phtml';
    /** @var Registry|null  */
    protected Registry|null $registry;
    /** @var Address  */
    protected Address $addressHelper;
    /** @var AddressQuerySupporter  */
    protected AddressQuerySupporter $addressQuerySupporterHelper;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Address $addressHelper
     * @param AddressQuerySupporter $addressQuerySupporterHelper
     * @param array $data
     * @param JsonHelper|null $jsonHelper
     * @param DirectoryHelper|null $directoryHelper
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Address $addressHelper,
        AddressQuerySupporter $addressQuerySupporterHelper,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    ) {
        $this->registry = $registry;
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
        $this->addressHelper = $addressHelper;
        $this->addressQuerySupporterHelper = $addressQuerySupporterHelper;
    }

    /**
     * @return mixed|null
     */
    public function getCurrentAddress()
    {
        return $this->registry->registry('order_address');
    }

    /**
     * @return array
     */
    public function getCurrentLocation() : array
    {
        $address = $this->getCurrentAddress();
        return [
            'area_id' => $address->getRegionId(),
            'area' => $address->getRegion(),
            'postcode' => $address->getPostCode(),
            'district' => $address->getCity(),
            'country_id' => $address->getCountryId(),
            'subdistrict_id' => $address->getSubDistrict(),
        ];
    }

    /**
     * @param $areaId
     * @return array
     */
    public function getDistrictList($areaId) : array
    {
        $districtListData = [];
        $districtList = $this->addressQuerySupporterHelper->getDistrictList();
        $districtList->addFieldToFilter('region_id', $areaId);
        foreach ($districtList as $district) {
            $districtListData[] = [
                'district' => $district->getDefaultName(),
                'district_id' => $district->getDistrictId()
            ];
        }
        return $districtListData;
    }

    /**
     * @param $districtName
     * @return array
     */
    public function getSubDistrictList($districtName) : array
    {
        $district = $this->getDistrict($districtName);
        $subDistrictListData = [];
        $subDistrictList = $this->addressQuerySupporterHelper
            ->getSubDistrictList($district->getData('district_id'));
        if ($subDistrictList->getSize()) {
            foreach ($subDistrictList as $subDistrict) {
                $subDistrictListData[] = [
                    'subdistrict' => $subDistrict->getDefaultName(),
                    'subdistrict_id' => $subDistrict->getSubdistrictId(),
                ];
            }
        }
        return $subDistrictListData;
    }

    /**
     * @param $districtName
     * @return \Magento\Framework\DataObject
     */
    public function getDistrict($districtName) : \Magento\Framework\DataObject
    {
        return $this->addressQuerySupporterHelper->getDistrict($districtName);
    }

    public function getUrlGetDistrictList()
    {
        return $this->getUrl('vnaddress/query/GetDistrictList');
    }

    public function getUrlGetSubDistrictList()
    {
        return $this->getUrl('vnaddress/query/GetSubDistrictList');
    }
}
