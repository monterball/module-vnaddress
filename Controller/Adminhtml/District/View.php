<?php
/**
 * View controller for District data
 * @package Eloab_VNAddress
 * @author Bao Le
 * @date 2022
 */
namespace Eloab\VNAddress\Controller\Adminhtml\District;

use Eloab\VNAddress\Helper\Address;
use Eloab\VNAddress\Helper\AddressQuerySupporter;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class View extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Eloab_VNAddress::VNAddress_View_District';

    /** @var bool|PageFactory  */
    protected $resultPageFactory = false;

    /** @var Registry  */
    protected $registry = null;

    /** @var Address  */
    protected $addressHelper;

    /** @var AddressQuerySupporter  */
    protected $addressQuerySupporterHelper;


    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param Address $addressHelper
     * @param AddressQuerySupporter $addressQuerySupporterHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        Address $addressHelper,
        AddressQuerySupporter $addressQuerySupporterHelper
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->addressHelper = $addressHelper;
        $this->addressQuerySupporterHelper = $addressQuerySupporterHelper;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }


    /**
     * @return ResultInterface|Page
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend((__("District Information")));

        $params = $this->getRequest()->getParams();
        try {
            //Set region ID and render the data information in block
            $this->registry->register('district_id', $params['district_id']);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            if (isset($params['region_id'])) {
                $this->registry->register('region_id', $params['region_id']);
                return $this->resultRedirectFactory->create()->setPath(
                    'vnaddress/region/view',
                    ['region_id' => $params['region_id']]);
            }
            return $this->resultRedirectFactory->create()->setPath('vnaddress/manage/index');
        }
        return $resultPage;
    }
}
