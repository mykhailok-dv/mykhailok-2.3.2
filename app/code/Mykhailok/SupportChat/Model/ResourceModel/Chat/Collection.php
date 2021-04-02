<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model\ResourceModel\Chat;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /* The timeout when the chat priority will be increased to the Immediate. */
    public const CRITICAL_CHAT_ANSWER_TIME = 1800;

    private \Magento\Store\Model\StoreManagerInterface $storeManager;

    private \Magento\Framework\App\State $state;
    private \Magento\Framework\App\ResourceConnection $resourceConnection;
    private \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory;

    /**
     * Collection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\State $state
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory
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
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory,
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
        $this->resourceConnection = $resourceConnection;
        $this->chatMessageCollectionFactory = $chatMessageCollectionFactory;
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
     * @param int $priority
     * @return $this
     */
    public function addPriorityFilter(int $priority): self
    {
        return $this->addFieldToFilter('priority', $priority);
    }

    /**
     * @param bool $isActive
     * @return $this
     */
    public function addIsActiveFilter(bool $isActive): self
    {
        return $this->addFieldToFilter('is_active', $isActive);
    }

    /**
     * @return $this
     */
    public function addPotentialWaitingFilter(): self
    {
        $criticalChatLiveTimeout = time() - self::CRITICAL_CHAT_ANSWER_TIME;
        $criticalDateTime = (new \DateTime())
            ->setTimestamp($criticalChatLiveTimeout)
            ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);

        $this->addChatWithLatestMessageFilter($this, [
            sprintf('`cm`.`created_at` < \'%s\'', $criticalDateTime),
            sprintf('`cm`.`author_type` != %s', \Magento\Authorization\Model\UserContextInterface::USER_TYPE_ADMIN),
            sprintf('`main_table`.`priority` = %s', \Mykhailok\SupportChat\Model\Chat::REGULAR_PRIORITY),
        ]);

        return $this;
    }

    public function addChatWithLatestMessageFilter(
        \Magento\Framework\Data\CollectionDataSourceInterface $chatCollection = null,
        array $conditions = []
    ): \Magento\Framework\Data\CollectionDataSourceInterface {
        if ($chatCollection === null) {
            $chatCollection = $this;
        }
        $conditionString = '';
        foreach ($conditions as $condition) {
            $conditionString .= ' AND ' . $condition;
        }

        $chatMessageTableName = $this->resourceConnection->getTableName('my_chat_message');

        $chatMessageChildQuery = $this->chatMessageCollectionFactory->create()->getSelect();
        $chatMessageChildQuery
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns([
                'chat_id',
                'last_message_id' => new \Zend_Db_Expr("MAX(id)"),
                'count_of_messages' => new \Zend_Db_Expr("COUNT(id)")
            ])
            ->group('chat_id');

        $chatCollection->getSelect()
            ->join(
                ['cm' => $chatMessageTableName],
                'main_table.id = cm.chat_id' . $conditionString
            )->join(
                ['cm_child' => $chatMessageChildQuery],
                'cm_child.last_message_id = cm.id'
            )->reset(
                \Zend_Db_Select::COLUMNS
            )->columns([
                'id' => 'main_table.id',
                'hash' => 'main_table.hash',
                'website_id' => 'main_table.website_id',
                'author_type' => 'cm.author_type',
                'author_id' => 'cm.author_id',
                'author_name' => 'cm.author_name',
                'message' => 'cm.message',
                'priority' => 'main_table.priority',
                'is_active' => 'main_table.is_active',
                'created_at' => 'cm.created_at',
                'message_count' => 'cm_child.count_of_messages',
            ]);

        return $chatCollection;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _renderFiltersBefore(): void
    {
        if ($this->state->getAreaCode() === \Magento\Framework\App\Area::AREA_WEBAPI_REST) {
            return;
        }

        $this->addWebsiteFilter((int)$this->storeManager->getWebsite()->getId());
    }
}
