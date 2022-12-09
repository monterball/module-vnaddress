<?php
/**
 * Copyright © Eloab, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Eloab\VNAddress\Helper;

use Eloab\VNAddress\Model\DistrictFactory;
use Eloab\VNAddress\Model\ResourceModel\District\CollectionFactory;
use Eloab\VNAddress\Model\SubdistrictFactory;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Model\Address\Mapper;
use Magento\Customer\Model\AddressFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Sales\Model\Order\Address as SalesAddress;

class Address extends \Magento\Framework\App\Helper\AbstractHelper
{
    /** @var DistrictFactory  */
    protected $districtFactory;
    /** @var SubdistrictFactory  */
    protected $subdistrictFactory;
    /** @var AddressFactory  */
    protected $addressFactory;
    /** @var CollectionFactory  */
    protected $districtCollection;
    /** @var \Eloab\VNAddress\Model\ResourceModel\Subdistrict\CollectionFactory  */
    protected $subdistrictCollection;
    /** @var \Magento\Sales\Model\Order\AddressFactory  */
    protected $saleAddressFactory;
    /** @var \Magento\Store\Api\Data\StoreInterface  */
    protected $store;
    /** @var \Magento\Framework\App\ResourceConnection  */
    protected $resource;
    /** @var \Magento\Customer\Model\Address\Config */
    protected $addressConfig;
    /** @var Mapper */
    protected $addressMapper;

    public function __construct(
        Context $context,
        DistrictFactory $districtFactory,
        SubdistrictFactory $subdistrictFactory,
        AddressFactory $addressFactory,
        CollectionFactory $districtCollection,
        \Eloab\VNAddress\Model\ResourceModel\Subdistrict\CollectionFactory $subdistrictCollection,
        \Magento\Sales\Model\Order\AddressFactory $saleAddressFactory,
        \Magento\Store\Api\Data\StoreInterface $store,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Customer\Model\Address\Config $addressConfig,
        Mapper $addressMapper
    ) {
        $this->districtFactory = $districtFactory;
        $this->subdistrictFactory = $subdistrictFactory;
        $this->addressFactory = $addressFactory;
        $this->districtCollection = $districtCollection;
        $this->subdistrictCollection = $subdistrictCollection;
        $this->saleAddressFactory = $saleAddressFactory;
        $this->store = $store;
        $this->resource = $resource;
        parent::__construct($context);
        $this->addressConfig = $addressConfig;
        $this->addressMapper = $addressMapper;
    }

    /**
     * @param $address
     * @return mixed
     */
    public function resolveCityName($address)
    {
        if ($address->getCity() && is_numeric($address->getCity())) {
            $district = $this->districtFactory->create()->load($address->getCity());
            if ($district) {
                $address->setCity($district->getName());
                $address->setData('city_id', $district->getId());
            }
        }
        return $address;
    }

    /**
     * @param $address
     * @return string
     */
    public function getSubDistrictName($address)
    {
        if ($subDistrict = $this->getSubDistrictObj($address)) {
            $subDistrict = $this->getSubDistrictNameById($subDistrict->getId());
            return $subDistrict['name'] ?? '';
        }
        return '';
    }

    /**
     * @param $address
     * @return \Eloab\VNAddress\Model\Subdistrict|null
     */
    public function getSubDistrictObj($address)
    {
        if ($address->getId()) {
            $customerAddresses = $this->addressFactory->create()->load($address->getId());
            if ($customerAddresses->getData('sub_district')) {
                $subDistrict = $this->subdistrictFactory->create()->load(
                    $customerAddresses->getData('sub_district')
                );
                return $subDistrict;
            }
        }
        return null;
    }

    /**
     * @return \Eloab\VNAddress\Model\ResourceModel\District\Collection
     */
    public function getDistrictCol()
    {
        $districtCollection = $this->districtCollection->create()->load();
        $districtCollection->addOrder('default_name', 'ASC');
        return $districtCollection;
    }

    /**
     * @return array
     */
    public function getDistrictData()
    {
        $districts = $this->getDistrictCol()->getData();
        $newDistricts = [];
        if (count($districts) > 0) {
            foreach ($districts as $district) {
                $district['name'] = $this->getDistrictName($district['district_id']);
                $newDistricts[] = $district;
            }
        }
        return $newDistricts;
    }

    /**
     * @param $id
     * @return string
     */
    public function getDistrictName($id)
    {
        $district = $this->districtFactory->create()->load($id);
        return $district->getName();
    }

    /**
     * @return \Eloab\VNAddress\Model\ResourceModel\Subdistrict\Collection
     */
    public function getSubDistrictCol()
    {
        $subdistrictCollection = $this->subdistrictCollection->create()->load();
        $subdistrictCollection->addOrder('default_name', 'ASC');
        return $subdistrictCollection;
    }

    /**
     * @return array
     */
    public function getSubDistrictData()
    {
        $subdistricts = $this->getSubDistrictCol()->getData();
        $newSubDistricts = [];
        if (count($subdistricts) > 0) {
            foreach ($subdistricts as $subdistrict) {
                $subdistrictObj = $this->getSubDistrictNameById($subdistrict['subdistrict_id']);
                if ($subdistrictObj) {
                    $subdistrict['name'] = $subdistrictObj['name'];
                } else {
                    $subdistrict['name'] = $subdistrict['default_name'];
                }
                $newSubDistricts[] = $subdistrict;
            }
        }
        return $newSubDistricts;
    }

    /**
     * @param $addressId
     * @param $subdistrictId
     * @return void
     * @throws \Exception
     */
    public function saveSubdistrictCustomerAddress($addressId, $subdistrictId)
    {
        if ($addressId) {
            /** @var SalesAddress $customerAddresses */
            $customerAddresses = $this->saleAddressFactory->create()->load($addressId);
            $customerAddresses->setData('sub_district', $subdistrictId);
            $customerAddresses->save();
        }
    }

    /**
     * @param $districtName
     * @return string
     */
    public function convertVNtoENDistrict($districtName)
    {
        $isCity = substr_count($districtName, 'Thành phố ');
        $isTown = substr_count($districtName, 'Thị xã ');
        $isDistrict1 = substr_count($districtName, 'Huyện ');
        $isDistrict2 = substr_count($districtName, 'Quận ');
        $isProvince = substr_count($districtName, 'Tỉnh ');
        if ($isCity > 0) {
            $enName = str_replace('Thành phố ', '', $districtName);
            $enName = $this->vnToEN($enName);
            $enName = $enName . ' City';
        } elseif ($isProvince > 0) {
            $enName = str_replace('Tỉnh ', '', $districtName);
            $enName = $this->vnToEN($enName);
            $enName = $enName . ' Province';
        } elseif ($isTown > 0) {
            $enName = str_replace('Thị xã ', '', $districtName);
            $enName = $this->vnToEN($enName);
            $enName = $enName . ' Town';
        } elseif ($isDistrict1 > 0) {
            $enName = str_replace('Huyện ', '', $districtName);
            $enName = $this->vnToEN($enName);
            $enName = $enName . ' District';
        } elseif ($isDistrict2 > 0) {
            $enName = str_replace('Quận ', '', $districtName);
            $firstChar = substr($enName, 0, 1);
            $enName = $this->vnToEN($enName);
            if (is_numeric($firstChar)) {
                $enName = 'District ' . $enName;
            } else {
                $enName = $enName . ' District';
            }
        } else {
            $enName = $districtName;
        }
        return $enName;
    }

    /**
     * @param $subdistrictName
     * @return string
     */
    public function convertVNtoENSubDistrict($subdistrictName)
    {
        $isTown = substr_count($subdistrictName, 'Thị trấn ');
        $enName = str_replace(['Xã ', 'Thị trấn ','Phường '], '', $subdistrictName);
        $enName = $this->vnToEN($enName);
        $firstChar = substr($enName, 0, 1);
        if ($isTown > 0) {
            $enName = $enName . ' Town';
        } elseif (is_numeric($firstChar)) {
            $enName = 'Ward ' . $enName;
        } else {
            $enName = $enName . ' Ward';
        }

        return $enName;
    }

    /**
     * Convert string VN to EN
     * @param $str
     * @return array|string|string[]
     */
    public function vnToEN($str)
    {
        $unicode = [
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd'=>'đ',
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i'=>'í|ì|ỉ|ĩ|ị',
            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
            'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D'=>'Đ',
            'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
            'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        ];
        foreach ($unicode as $nonUnicode=>$uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        return $str;
    }

    /**
     * District Name by ID district
     * @param $id
     * @return array|mixed
     * @throws \Zend_Db_Statement_Exception
     */
    public function getDistrictNameById($id)
    {
        $adapter = $this->resource->getConnection();
        $tableName = $adapter->getTableName('directory_country_region_district_name');
        // add for All Page
        $select = $adapter->select()->from(
            ['mainTbl' => $tableName],
            ['*']
        )->where(
            'district_id = ?',
            $id
        );

        $data = [];
        $query = $adapter->query($select);
        while ($row = $query->fetch()) {
            array_push($data, $row);
        }

        if (count($data) > 0) {
            foreach ($data as $district) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $store = $objectManager->get('Magento\Framework\Locale\Resolver');
                $locale =  $store->getLocale();
                if (!empty($district['locale']) && $district['locale'] == $locale) {
                    return $district;
                }
            }
        }

        return $data;
    }

    /**
     * Ward Name by ID district
     * @param $id
     * @return array|mixed
     * @throws \Zend_Db_Statement_Exception
     */
    public function getSubDistrictNameById($id)
    {
        $adapter = $this->resource->getConnection();
        $tableName = $adapter->getTableName('directory_country_region_district_subdistrict_name');
        // add for All Page
        $select = $adapter->select()->from(
            ['mainTbl' => $tableName],
            ['*']
        )->where(
            'subdistrict_id = ?',
            $id
        );

        $data = [];
        $query = $adapter->query($select);
        while ($row = $query->fetch()) {
            array_push($data, $row);
        }

        if (count($data) > 0) {
            foreach ($data as $subdistrict) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $store = $objectManager->get('Magento\Framework\Locale\Resolver');
                $locale =  $store->getLocale();
                if (!empty($subdistrict['locale']) && $subdistrict['locale'] == $locale) {
                    return $subdistrict;
                }
            }
        }

        return $data;
    }

    /**
     * Render an address as HTML and return the result
     *
     * @param AddressInterface $address
     * @param string $type
     * @return string
     */
    public function getAddressRender(AddressInterface $address, string $type = 'html') : string
    {
        $builtOutputAddressData = $this->addressMapper->toFlatArray($address);
        $addressArray = [];
        if (!empty($valueSDistrict = $builtOutputAddressData['sub_district'])) {
            foreach ($this->addressMapper->toFlatArray($address) as $key => $value) {
                if ($key == 'sub_district') {
                    continue;
                } elseif ($key == 'city') {
                    $districtName = $this->getDistrictNameById($value)['name'];
                    $subDistrict = $this->getSubDistrictNameById($valueSDistrict);

                    $addressArray[$key] = $subDistrict['name'] . ', ' . $districtName;
                } else {
                    $addressArray[$key] = $value;
                }
            }
        } else {
            $addressArray = $builtOutputAddressData;
        }
        return $this->addressConfig
            ->getFormatByCode($type)
            ->getRenderer()
            ->renderArray($addressArray);
    }
}
