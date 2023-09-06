<?php

namespace Eloab\VNAddress\Block\Adminhtml\Region;


use Magento\Backend\Block\Template\Context;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Registry;
use Eloab\VNAddress\Helper\Address;
use Eloab\VNAddress\Helper\AddressQuerySupporter;
use Magento\Directory\Model\RegionFactory;

class View extends \Magento\Backend\Block\Template
{
    /** @var string  */
    protected $_template = 'Eloab_VNAddress::region/view.phtml';

    /** @var Registry  */
    protected $registry = null;

    /** @var Address  */
    protected Address $addressHelper;

    /** @var AddressQuerySupporter  */
    protected AddressQuerySupporter $addressQuerySupporterHelper;

    /** @var RegionFactory */
    protected RegionFactory $regionFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Address $addressHelper
     * @param AddressQuerySupporter $addressQuerySupporterHelper
     * @param RegionFactory $regionFactory
     * @param array $data
     * @param JsonHelper|null $jsonHelper
     * @param DirectoryHelper|null $directoryHelper
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Address $addressHelper,
        AddressQuerySupporter $addressQuerySupporterHelper,
        RegionFactory $regionFactory,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    ) {
        $this->registry = $registry;
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
        $this->regionFactory = $regionFactory;
        $this->addressHelper = $addressHelper;
        $this->addressQuerySupporterHelper = $addressQuerySupporterHelper;
    }

    /**
     * Return region ID
     * @return mixed|null
     */
    protected function getRegionId()
    {
        return $this->registry->registry('region_id');
    }

    /**
     * Return region data
     * @return \Magento\Directory\Model\Region|null
     */
    public function getRegion()
    {
        try {
            $regionId = $this->getRegionId();
            return $this->regionFactory->create()->load($regionId);
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @param $id
     * @param string $locale
     * @return string|null
     * @throws \Zend_Db_Statement_Exception
     */
    public function getRegionName($id, string $locale = 'en_US')
    {
        $regionName = $this->addressHelper->getRegionNameById($id, $locale);
        if (!empty($regionName)) {
            return $regionName;
        }
        return null;
    }
}
