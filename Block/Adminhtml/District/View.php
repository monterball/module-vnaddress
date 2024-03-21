<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eloab\VNAddress\Block\Adminhtml\District;


use Magento\Backend\Block\Template\Context;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Registry;
use Eloab\VNAddress\Helper\Address;
use Eloab\VNAddress\Helper\AddressQuerySupporter;
use Eloab\VNAddress\Model\DistrictFactory;

class View extends \Magento\Backend\Block\Template
{
    /** @var string  */
    protected $_template = 'Eloab_VNAddress::district/view.phtml';

    /** @var Registry  */
    protected $registry = null;

    /** @var Address  */
    protected Address $addressHelper;

    /** @var AddressQuerySupporter  */
    protected AddressQuerySupporter $addressQuerySupporterHelper;

    /** @var DistrictFactory */
    protected DistrictFactory $districtFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Address $addressHelper
     * @param AddressQuerySupporter $addressQuerySupporterHelper
     * @param DistrictFactory $districtFactory
     * @param array $data
     * @param JsonHelper|null $jsonHelper
     * @param DirectoryHelper|null $directoryHelper
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Address $addressHelper,
        AddressQuerySupporter $addressQuerySupporterHelper,
        DistrictFactory $districtFactory,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    ) {
        $this->registry = $registry;
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
        $this->districtFactory = $districtFactory;
        $this->addressHelper = $addressHelper;
        $this->addressQuerySupporterHelper = $addressQuerySupporterHelper;
    }

    /**
     * Return district ID
     * @return mixed|null
     */
    protected function getDistrictId()
    {
        return $this->registry->registry('district_id');
    }

    /**
     * Return district data
     * @return \Eloab\VNAddress\Model\District|null
     */
    public function getDistrict()
    {
        try {
            $districtId = $this->getDistrictId();
            return $this->districtFactory->create()->load($districtId);
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
    public function getDistrictName($id, string $locale = 'en_US')
    {
        $districtName = $this->addressHelper->getDistrictNameById($id, $locale);
        if (!empty($districtName)) {
            return $districtName;
        }
        return null;
    }
}
