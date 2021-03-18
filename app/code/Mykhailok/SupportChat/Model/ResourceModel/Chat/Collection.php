<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model\ResourceModel\Chat;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /** @var \Magento\Store\Model\StoreManagerInterface $storeManager */
    private \Magento\Store\Model\StoreManagerInterface $storeManager;

    /** @var \Magento\Framework\App\State $state */
    private \Magento\Framework\App\State $state;

    /**
     * Collection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\State $state
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $state,
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
        $this->state = $state;
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
     * @return $this
     */
    public function addWebsiteFilter(int $websiteId): self
    {
        return $this->addFieldToFilter('website_id', $websiteId);
    }

    /**
     * @param string $chatHash
     * @return $this
     */
    public function addHashFilter(string $chatHash): self
    {
        return $this->addFieldToFilter('hash', $chatHash);
    }

    /**
     * @param int $chatId
     * @return $this
     */
    public function addChatIdFilter(int $chatId): self
    {
        return $this->addFieldToFilter('id', $chatId);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _renderFiltersBefore()
    {
        if ($this->state->getAreaCode() === \Magento\Framework\App\Area::AREA_WEBAPI_REST) {
            return;
        }

        $this->addWebsiteFilter((int)$this->storeManager->getWebsite()->getId());
    }
}
