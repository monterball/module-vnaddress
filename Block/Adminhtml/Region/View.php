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

class View extends \Magento\Backend\Block\Template
{
    protected $_template = 'Eloab_VNAddress::region/view.phtml';

    /** @var Registry  */
    protected $registry = null;
    /** @var Address  */
    protected $addressHelper;
    /** @var AddressQuerySupporter  */
    protected $addressQuerySupporterHelper;

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
}
