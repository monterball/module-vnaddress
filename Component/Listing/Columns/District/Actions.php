<?php
/**
 * Copyright © Eloab DevTeam All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eloab\VNAddress\Component\Listing\Columns\District;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Actions extends Column
{
    /** @var UrlInterface  */
    protected UrlInterface $urlBuilder;

    /** @var ContextInterface */
    protected $context;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return mixed
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = [
                    'view' => [
                        'href' => $this->urlBuilder->getUrl(
                            'vnaddress/district/view',
                            ['district_id' => $item['district_id']]
                        ),
                        'label' => __('View'),
                        'hidden' => false,
                    ]
                ];
            }
        }
        return $dataSource;
    }
}
