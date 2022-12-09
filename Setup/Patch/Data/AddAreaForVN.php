<?php
/**
 * Copyright © Eloab, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Eloab\VNAddress\Setup\Patch\Data;

use Magento\Directory\Setup\DataInstaller;
use Magento\Directory\Setup\DataInstallerFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Csv as CsvReader;
use Eloab\VNAddress\Helper\Address;

/**
 * Adds VN States
 */
class AddAreaForVN implements DataPatchInterface
{

    const VN_COUNTRY_ID = 'VN';
    const VN_ADDRESS_DATA_FILE_PATH = '/import/VN-address.csv';

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var CsvReader
     */
    private $csvReader;

    /**
     * @var []
     */
    private $regions;

    /**
     * @var []
     */
    private $vnAddress;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var DataInstallerFactory
     */
    private $dataInstallerFactory;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    private $data;

    /**
     * @var Address
     */
    protected $address;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param DataInstallerFactory $dataInstallerFactory
     * @param DirectoryList $directoryList
     * @param CsvReader $csvReader
     * @param \Magento\Directory\Helper\Data $data
     * @param ResourceConnection $resourceConnection
     * @param Address $address
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        DataInstallerFactory $dataInstallerFactory,
        DirectoryList $directoryList,
        CsvReader $csvReader,
        \Magento\Directory\Helper\Data $data,
        ResourceConnection $resourceConnection,
        Address $address
    ) {
        $this->directoryList = $directoryList;
        $this->csvReader = $csvReader;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->dataInstallerFactory = $dataInstallerFactory;
        $this->data = $data;
        $this->resourceConnection = $resourceConnection;
        $this->address = $address;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $this->addCountryRegions($this->moduleDataSetup->getConnection(), $this->getDataForArea());
    }

    /**
     * Australian states data.
     *
     * @return array
     */
    private function getDataForArea()
    {
        $VNAddressArr = $this->getVNAddress(); //from file
        $formatAddress = [];
        $firstRow = 0;
        foreach ($VNAddressArr as $vnAddress) {
            if ($firstRow == 0) {
                $firstRow++;
                continue;
            }
            $id = self::VN_COUNTRY_ID . $vnAddress[1];
            if (in_array($id, array_column($formatAddress, 1))) {
                continue;
            } else {
                $isCity = substr_count($vnAddress[0],'Thành phố ');
                $isProvince = substr_count($vnAddress[0],'Tỉnh ');
                if ($isCity > 0) {
                    $enName = str_replace('Thành phố ', '',$vnAddress[0]);
                    $enName = $this->address->vnToEN($enName);
                    $enName = $enName . ' City';
                } elseif ($isProvince > 0) {
                    $enName = str_replace('Tỉnh ', '',$vnAddress[0]);
                    $enName = $this->address->vnToEN($enName);
                    $enName = $enName . ' Province';
                } else {
                    $enName = $vnAddress[0];
                }

                $formatAddress[] = [self::VN_COUNTRY_ID, $id, $enName, $vnAddress[0]];
            }
        }
        return $formatAddress;
    }

    /**
     * Read Hong Kong Address from CSV
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
     * Add country-region data.
     *
     * @param AdapterInterface $adapter
     * @param array $data
     */
    private function addCountryRegions(AdapterInterface $adapter, array $data)
    {
        /**
         * Fill table directory/country_region
         * Fill table directory/country_region_name for en_US, vi_VN locale
         */
        foreach ($data as $row) {
            $bind = ['country_id' => $row[0], 'code' => $row[1], 'default_name' => $row[2]];
            $adapter->insert($this->resourceConnection->getTableName('directory_country_region'), $bind);
            $regionId = $adapter->lastInsertId($this->resourceConnection->getTableName('directory_country_region'));

            //Install
            $bind = ['locale' => 'en_US', 'region_id' => $regionId, 'name' => $row[2]];
            $adapter->insert($this->resourceConnection->getTableName('directory_country_region_name'), $bind);
            $bind = ['locale' => 'vi_VN', 'region_id' => $regionId, 'name' => $row[3]];
            $adapter->insert($this->resourceConnection->getTableName('directory_country_region_name'), $bind);
        }
        /**
         * Upgrade core_config_data general/region/state_required field.
         */
        $countries = $this->data->getCountryCollection()->getCountriesWithRequiredStates();
        $adapter->update(
            $this->resourceConnection->getTableName('core_config_data'),
            [
                'value' => implode(',', array_keys($countries))
            ],
            [
                'scope="default"',
                'scope_id=0',
                'path=?' => \Magento\Directory\Helper\Data::XML_PATH_STATES_REQUIRED
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
