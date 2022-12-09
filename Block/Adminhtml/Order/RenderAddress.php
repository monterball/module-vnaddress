<?php

namespace Eloab\VNAddress\Block\Adminhtml\Order;


use Magento\Backend\Block\Template\Context;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Registry;
use Eloab\VNAddress\Helper\Address;
use Eloab\VNAddress\Helper\AddressQuerySupporter;

class RenderAddress extends \Magento\Backend\Block\Template
{
    protected $_template = 'Eloab_VNAddress::renderAddressJs.phtml';

    /** @var Registry  */
    protected $registry = null;
    /** @var Address  */
    protected $addressHelper;
    /** @var AddressQuerySupporter  */
    protected $addressQuerySupporterHelper;

    public function __construct(Context $context, Registry $registry, Address $addressHelper, AddressQuerySupporter $addressQuerySupporterHelper, array $data = [], ?JsonHelper $jsonHelper = null, ?DirectoryHelper $directoryHelper = null)
    {
        $this->registry = $registry;
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
        $this->addressHelper = $addressHelper;
        $this->addressQuerySupporterHelper = $addressQuerySupporterHelper;
    }

    public function getCurrentAddress()
    {
        return $this->registry->registry('order_address');
    }

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

    public function getSubDistrictList($districtName) : array
    {
        $district = $this->getDistrict($districtName);
        $subDistrictListData = [];
        $subDistrictList = $this->addressQuerySupporterHelper->getSubDistrictList($district->getData('district_id'));
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

    public function getDistrict($districtName) {
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
