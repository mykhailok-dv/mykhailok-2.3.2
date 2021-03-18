<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model\ResourceModel\ChatMessage;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /** @var \Mykhailok\SupportChat\Model\ResourceModel\Chat $resourceModelChat */
    private \Mykhailok\SupportChat\Model\ResourceModel\Chat $resourceModelChat;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Mykhailok\SupportChat\Model\ResourceModel\Chat $resourceModelChat,
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
        $this->resourceModelChat = $resourceModelChat;
    }

    /** @var array  */
    private static array $authorTypes = [
        \Magento\Authorization\Model\UserContextInterface::USER_TYPE_ADMIN,
        \Magento\Authorization\Model\UserContextInterface::USER_TYPE_CUSTOMER,
        \Magento\Authorization\Model\UserContextInterface::USER_TYPE_GUEST,
    ];

    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(
            \Mykhailok\SupportChat\Model\ChatMessage::class,
            \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage::class
        );
    }

    /**
     * @param int $authorType
     * @return $this
     */
    public function addAuthorTypeFilter(int $authorType): self
    {
        /**
         * Filter will be applies when $authorType is const from UserContextInterface.
         * @see \Magento\Authorization\Model\UserContextInterface
         */
        if (in_array($authorType, self::$authorTypes, true)) {
            $this->addFieldToFilter('author_type', $authorType);
        }

        return $this;
    }

    /**
     * @param \DateTime $from
     * @param ?\DateTime $to
     * @return $this
     */
    public function addCreatedAtRangeFilter(\DateTime $from, \DateTime $to = null): self
    {
        return $this->addFieldToFilter('created_at', [
            'from' => $from,
            'to' => $to ?: new \DateTime(),
            'datetime' => true,
        ]);
    }

    /**
     * @param $authorId
     * @return $this
     */
    public function addAuthorIdFilter($authorId): self
    {
        return $this->addFieldToFilter('author_id', $authorId);
    }

    /**
     * @param $chatId
     * @return $this
     */
    public function addChatIdFilter($chatId): self
    {
        return $this->addFieldToFilter('chat_id', $chatId);
    }

    /**
     * @param int $messageId
     * @return $this
     */
    public function addMessageIdFilter(int $messageId): self
    {
        return $this->addFieldToFilter('id', $messageId);
    }

    /**
     * @param $chatHash
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function fetchMessagesByChatHash($chatHash): self
    {
        $this->getSelect()->joinLeft(
            ['my_chat' => $this->resourceModelChat->getMainTable()],
            'main_table.chat_id = my_chat.id',
            []
        );

        return $this->addFieldToFilter('my_chat.hash', $chatHash);
    }
}
