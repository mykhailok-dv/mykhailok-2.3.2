<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Plugin\Model\ResourceModel\ChatMessage;

use Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\Collection as ChatMessageCollection;

class Collection
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /** Plugin will be pass before
     * @see \Magento\Framework\Data\Collection\AbstractDb::load()
     * @param ChatMessageCollection $collection
     * @param bool $printQuery
     * @param bool $logQuery
     * @return array|bool[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeLoad(ChatMessageCollection $collection, $printQuery = false, $logQuery = false): array
    {
        $collection->addWebsiteFilter((int)$this->storeManager->getWebsite()->getId());

        return [$printQuery, $logQuery];
    }
}
