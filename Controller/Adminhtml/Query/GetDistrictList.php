<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eloab\VNAddress\Controller\Adminhtml\Query;

use Eloab\VNAddress\Helper\Address;
use Eloab\VNAddress\Helper\AddressQuerySupporter;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Session\SessionManagerInterface;

class GetDistrictList extends Action implements HttpPostActionInterface
{
    /** @var SessionManagerInterface  */
    protected SessionManagerInterface $sessionManager;
    /** @var Address  */
    protected Address $addressHelper;
    /** @var AddressQuerySupporter  */
    protected AddressQuerySupporter $addressQuerySupporterHelper;

    /**
     * @param Context $context
     * @param SessionManagerInterface $sessionManager
     * @param Address $addressHelper
     * @param AddressQuerySupporter $addressQuerySupporterHelper
     */
    public function __construct(
        Context $context,
        SessionManagerInterface $sessionManager,
        Address $addressHelper,
        AddressQuerySupporter  $addressQuerySupporterHelper
    ) {
        parent::__construct($context);
        $this->sessionManager = $sessionManager;
        $this->addressHelper = $addressHelper;
        $this->addressQuerySupporterHelper = $addressQuerySupporterHelper;
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|ResponseInterface
     * @throws NotFoundException
     */
    public function execute()
    {
        // TODO: Implement execute method.
        $data = ['status' => 0, 'message' => __(
            "Cannot access function from external service.")];
        $areaId = $this->getRequest()->getParam('area_id');
        try {
            if (!empty($areaId)) {
                $districtList = $this->getDistrictList(str_replace('"', '', $areaId));
                if (count($districtList) > 0) {
                    $data = ['status' => 1, 'message' => 'Success',
                        'area_id' => $areaId,
                        'district_default' => $districtList[0]['district'],
                        'district_default_id' => $districtList[0]['district_id'],
                        'districts' => $districtList];
                } else {
                    //Null district
                    $data = ['status' => 1,
                        'message' => 'Success',
                        'area_id' => $areaId,
                        'district_default' => "",
                        'district_default_id' => "",
                        'districts' => $districtList];
                }
            } else {
                $data = ['status' => 0, 'message' => __("Please enter the area Id.")];
            }
            return $this->returnJsonData($data);
        } catch (\Magento\Framework\Exception\SessionException $sessionException) {
            $data = ['status' => 0, 'message' => $sessionException->getMessage()];
            return $this->returnJsonData($data);
        } catch (\Zend_Locale_Exception $locale_Exception) {
            $data = ['status' => 0, 'message' => $locale_Exception->getMessage()];
            return $this->returnJsonData($data);
        } catch (\Exception $exception) {
            $data = ['status' => 0, 'message' => $exception->getMessage()];
            return $this->returnJsonData($data);
        }
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
     * @param $data
     * @return \Magento\Framework\Controller\ResultInterface
     */
    protected function returnJsonData($data) : \Magento\Framework\Controller\ResultInterface
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($data);
        return $resultJson;
    }
}
