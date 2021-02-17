<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model\ResourceModel\Chat;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    private \Magento\Store\Model\StoreManagerInterface $storeManager;
    private \Mykhailok\SupportChat\Model\ResourceModel\Chat $chatResourceModel;

    /**
     * Collection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Mykhailok\SupportChat\Model\ResourceModel\Chat $chatResourceModel
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mykhailok\SupportChat\Model\ResourceModel\Chat $chatResourceModel,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->storeManager = $storeManager;
        $this->chatResourceModel = $chatResourceModel;
    }

    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(
            \Mykhailok\SupportChat\Model\Chat::class,
            \Mykhailok\SupportChat\Model\ResourceModel\Chat::class
        );
    }

    /**
     * @param int $websiteId
     * @return \Mykhailok\SupportChat\Model\ResourceModel\Chat\Collection
     */
    public function addWebsiteFilter(int $websiteId): self
    {
        return $this->addFieldToFilter('website_id', $websiteId);
    }

    /**
     * @param $chatHash
     * @return $this
     */
    public function addHashFilter($chatHash): self
    {
        return $this->addFieldToFilter('hash', $chatHash);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _renderFiltersBefore()
    {
        $this->addWebsiteFilter((int)$this->storeManager->getWebsite()->getId());
    }
}
