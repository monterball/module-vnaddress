<?php

namespace Eloab\VNAddress\Controller\Adminhtml\Query;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Session\SessionManagerInterface;
use Eloab\VNAddress\Helper\Address;
use Eloab\VNAddress\Helper\AddressQuerySupporter;

class GetSubDistrictList extends Action implements HttpPostActionInterface
{
    /** @var SessionManagerInterface  */
    protected $sessionManager;
    /** @var Address  */
    protected $addressHelper;
    /** @var AddressQuerySupporter  */
    protected $addressQuerySupporterHelper;

    public function __construct(Context $context,
                                SessionManagerInterface $sessionManager,
                                Address $addressHelper,
                                AddressQuerySupporter  $addressQuerySupporterHelper)
    {
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
        $data = ['status' => 0, 'message' => __("Cannot access function from external service.")];
        $districtId = $this->getRequest()->getParam('district_id');
        try {
            if (!empty($districtId)) {
                $subDistrictList = $this->getSubDistrictList(trim(str_replace('"','',$districtId)));
                if (count($subDistrictList) > 0) {
                    $data = ['status' => 1, 'message' => 'Success',
                        'district_id' => trim(str_replace('"','',$districtId)),
                        'default_subdistrict' => $subDistrictList[0]['subdistrict_id'], 'subdistricts' => $subDistrictList];
                } else {
                    $data = ['status' => 1, 'message' => 'Success',
                        'district_id' => trim(str_replace('"','',$districtId)),
                        'default_subdistrict' => "", 'subdistricts' => $subDistrictList];
                }
            } else {
                $data = ['status' => 0, 'message' => __("Please enter the district id.")];
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

    public function getSubDistrictList($districtId) : array
    {
        $subDistrictListData = [];
        $subDistrictList = $this->addressQuerySupporterHelper->getSubDistrictList($districtId);
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

    /**
     * @param $data
     * @return \Magento\Framework\Controller\ResultInterface
     */
    protected function returnJsonData($data)
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($data);
        return $resultJson;
    }
}
