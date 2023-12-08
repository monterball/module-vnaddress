<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);
namespace Eloab\VNAddress\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Csv as CsvReader;
use Eloab\VNAddress\Helper\Address;

/**
 * Class Add VN Districts
 * @package Eloab\VNAddress\Setup\Patch
 */
class AddDistrictSubdistrictForVN implements DataPatchInterface
{

    const VN_COUNTRY_ID = 'VN';
    const VN_ADDRESS_DATA_FILE_PATH = '/import/VN-address.csv';

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
    * @var CsvReader
    */
    private $csvReader;

    /**
     * @var Address
     */
    protected $address;

    /**
    * @var []
    */
    private $regions;

    /**
    * @var []
    */
    private $vnAddress;

    /**
     * PatchInitial constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param DirectoryList $directoryList
     * @param CsvReader $csvReader
     * @param Address $address
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        DirectoryList $directoryList,
        CsvReader $csvReader,
        Address $address
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->directoryList = $directoryList;
        $this->csvReader = $csvReader;
        $this->address = $address;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->addDistrictData();
        $this->addSubdistrictData();
    }

    /**
     * Add District Data
     * @return void
     *
     */
    private function addDistrictData()
    {
        $regionDistrictTable = $this->moduleDataSetup->getTable('directory_region_district');
        $districtNameTable = $this->moduleDataSetup->getTable('directory_country_region_district_name');
        $connection = $this->moduleDataSetup->getConnection();
        //from file
        $VNAddressArr = $this->getVNAddress();
        //from region table
        $VNRegions = $this->getVNRegions();

        $districtCodeInserted = [];
        $firstRow = 0;

        foreach ($VNAddressArr as $vnAddress) {
            if ($firstRow == 0) {
                $firstRow++;
                continue;
            }
            //Get information from file
            $regionCode = self::VN_COUNTRY_ID . $vnAddress[1];

            //If not in database
            if (empty($VNRegions[$regionCode])) {
                continue;
            }
            $regionId = $VNRegions[$regionCode]['region_id'];

            $districtName = trim($vnAddress[2]);
            $districtNameEn = $this->address->convertVNtoENDistrict($districtName);
            $districtCode = self::VN_COUNTRY_ID . trim($vnAddress[3]);

            if (empty($districtName) || in_array($districtCode, $districtCodeInserted)) {
                // make sure don't duplicate district
                continue;
            }

            try {
                $connection->insert($regionDistrictTable, [
                    'region_id' => $regionId,
                    'district_code' => $districtCode,
                    'default_name' => $districtNameEn
                ]);
                //Store to make sure don't duplicate district
                $districtCodeInserted[] = $districtCode;
                $districtId = $connection->lastInsertId($regionDistrictTable);
                //Eng
                $connection->insert($districtNameTable, [
                    'locale' => \Magento\Framework\AppInterface::DISTRO_LOCALE_CODE,
                    'district_id' => $districtId,
                    'name' => $districtNameEn
                ]);
                //Viet
                $connection->insert($districtNameTable, [
                    'locale' => 'vi_VN',
                    'district_id' => $districtId,
                    'name' => $districtName
                ]);

            } catch (\Exception $e) {
                //@todo: Log something
            }
        }

    }

    /**
     * Add District Data
     * @return void
     *
     */
    private function addSubdistrictData()
    {
        $districtSubdistrictTable = $this->moduleDataSetup->getTable('directory_district_subdistrict');
        $subdistrictNameTable = $this->moduleDataSetup->getTable('directory_country_region_district_subdistrict_name');
        $connection = $this->moduleDataSetup->getConnection();
        //from district table
        $VNDistricts = $this->getVNDistricts();
        //from file
        $VNAddressArr = $this->getVNAddress();
        $subdistrictInserted = [];

        $firstRow = 0;
        foreach ($VNAddressArr as $vnAddress) {

            if ($firstRow == 0) {
                $firstRow++;
                continue;
            }

            $districtCode = self::VN_COUNTRY_ID . trim($vnAddress[3]);

            if (empty($VNDistricts[$districtCode])) {
                continue;
            }

            $districtId = $VNDistricts[$districtCode]['district_id'];
            $subDistrictName = trim($vnAddress[4]);
            $subDistrictNameEn = $this->address->convertVNtoENSubDistrict($subDistrictName);
            $subdistrictCode = self::VN_COUNTRY_ID . trim($vnAddress[5]);

            if (empty($subDistrictName) || in_array($subdistrictCode, $subdistrictInserted)) {
                // make sure don't duplicate subdistrict
                continue;
            }

            try {
                $connection->insert($districtSubdistrictTable, [
                    'district_id' => $districtId,
                    'subdistrict_code' => $subdistrictCode,
                    'default_name' => $subDistrictNameEn
                ]);
                $subdistricId = $connection->lastInsertId($districtSubdistrictTable);
                $connection->insert($subdistrictNameTable, [
                    'locale' => \Magento\Framework\AppInterface::DISTRO_LOCALE_CODE,
                    'subdistrict_id' => $subdistricId,
                    'name' => $subDistrictNameEn
                ]);
                $connection->insert($subdistrictNameTable, [
                    'locale' => 'vi_VN',
                    'subdistrict_id' => $subdistricId,
                    'name' => $subDistrictName
                ]);

            } catch (\Exception $e) {
                //@todo: Log something
            }
        }

    }

    /**
     * @return array
     */
    private function getVNDistricts()
    {
        $districtTable = $this->moduleDataSetup->getTable('directory_region_district');
        $connection = $this->moduleDataSetup->getConnection();

        $selectVNDistricts = $connection->select()
            ->from($districtTable)
            ->where('region_id IN (?)', $this->getVNRegionIds());
        $VNDistricts = $connection->fetchAll($selectVNDistricts);

        $result = [];
        foreach ($VNDistricts as $VNDistrict) {
            $result[$VNDistrict['district_code']] = [
                'default_name' => $VNDistrict['default_name'],
                'district_id' => $VNDistrict['district_id'],
            ];
        }
        return $result;
    }

    /**
     * Retrive and prepare Viet Nam region(area)
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @return array
     */
    private function getVNRegions()
    {
        if (empty($this->regions)) {
            $regionTable = $this->moduleDataSetup->getTable('directory_country_region');
            $connection = $this->moduleDataSetup->getConnection();

            $selectVNRegions = $connection->select()
                ->from($regionTable)
                ->where('country_id = ?', self::VN_COUNTRY_ID);
            $VNRegions = $connection->fetchAll($selectVNRegions);
            $result = [];
            foreach ($VNRegions as $VNRegion) {
                $result[$VNRegion['code']] = [
                    'default_name' => $VNRegion['default_name'],
                    'region_id' => $VNRegion['region_id']
                ];
            }
            $this->regions = $result;
        }

        return $this->regions;
    }

    /**
     * Retrive and prepare Viet Nam region(area)
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @return array
     */
    private function getVNRegionIds()
    {
        if (empty($this->regions)) {
            $regionTable = $this->moduleDataSetup->getTable('directory_country_region');
            $connection = $this->moduleDataSetup->getConnection();

            $selectVNRegions = $connection->select()
                ->from($regionTable)
                ->where('country_id = ?', self::VN_COUNTRY_ID);
            $VNRegions = $connection->fetchAll($selectVNRegions);
            $result = [];
            foreach ($VNRegions as $VNRegion) {
                $result[] = $VNRegion['region_id'];
            }
            $this->regions = $result;
        }

        return $this->regions;
    }

    /**
     * Read Viet Nam Address from CSV
     * @return array
     */
    private function getVNAddress()
    {
        if (empty($this->vnAddress)) {
            $vnAddressFile = $this->directoryList->getRoot() . "/app/code/Eloab/VNAddress/data/" . self::VN_ADDRESS_DATA_FILE_PATH;
            $vnAddressArr = $this->csvReader->getData($vnAddressFile);
            $this->vnAddress = $vnAddressArr;
        }

        return $this->vnAddress;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [
            AddAreaForVN::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
