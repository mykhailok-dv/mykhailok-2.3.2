<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Ui\Component\Listing\Column;

class BlockActions extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Url path
     */
    public const URL_PATH_DELETE = 'my_chat/chat/delete';
    public const URL_PATH_MARK_INACTIVE = 'my_chat/chat/toggleActive';
    public const URL_PATH_DETAILS = 'my_chat/chat/details';

    /** @var \Magento\Framework\UrlInterface $urlBuilder */
    private \Magento\Framework\UrlInterface $urlBuilder;

    /** @var \Magento\Framework\Escaper $escaper */
    private \Magento\Framework\Escaper $escaper;

    /**
     * BlockActions constructor.
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Escaper $escaper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Escaper $escaper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
        $this->urlBuilder = $urlBuilder;
        $this->escaper = $escaper;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['id'])) {
                    $authorName = $this->escaper->escapeHtmlAttr($item['author_name'] ?? '');
                    $item[$this->getData('name')] = [
                        'details' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_DETAILS,
                                [
                                    'chat_id' => $item['id'],
                                ]
                            ),
                            'label' => __('Open Chat'),
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    'id' => $item['id'],
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete chat with %1', $authorName),
                                'message' => __(
                                    'Are you sure you want to delete the chat with %1 customer?',
                                    $authorName
                                ),
                            ],
                            'post' => true,
                        ],
                        'inactive' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_MARK_INACTIVE,
                                [
                                    'id' => $item['id'],
                                ]
                            ),
                            'label' => $item['is_active']
                                ? __('Mark inactive')
                                : __('Mark active'),
                            'confirm' => [
                                'title' => $item['is_active']
                                    ? __('Mark chat with %1 as inactive', $authorName)
                                    : __('Mark chat with %1 as active', $authorName),
                            ],
                            'post' => true,
                        ],
                    ];
                }
            }
        }

        return $dataSource;
    }
}
