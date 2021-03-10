<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Setup\UpgradeData;

class MigrateDataFromOldTable
{
    /** @var \Mykhailok\SupportChat\Model\ChatFactory $chatFactory */
    private \Mykhailok\SupportChat\Model\ChatFactory $chatFactory;

    /** @var \Mykhailok\SupportChat\Model\ChatMessageFactory $chatMessageFactory */
    private \Mykhailok\SupportChat\Model\ChatMessageFactory $chatMessageFactory;

    /** @var \Mykhailok\SupportChat\Model\ResourceModel\Chat $resourceModelChat */
    private \Mykhailok\SupportChat\Model\ResourceModel\Chat $resourceModelChat;

    /** @var \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage $resourceModelChatMessage */
    private \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage $resourceModelChatMessage;

    /** @var \Magento\Framework\App\ResourceConnection $resourceConnection */
    private \Magento\Framework\App\ResourceConnection $resourceConnection;

    /** @var \Psr\Log\LoggerInterface $logger */
    private \Psr\Log\LoggerInterface $logger;

    /**
     * MigrateDataFromOldTable constructor.
     * @param \Mykhailok\SupportChat\Model\ChatFactory $chatFactory
     * @param \Mykhailok\SupportChat\Model\ChatMessageFactory $chatMessageFactory
     * @param \Mykhailok\SupportChat\Model\ResourceModel\Chat $resourceModelChat
     * @param \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage $resourceModelChatMessage
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Mykhailok\SupportChat\Model\ChatFactory $chatFactory,
        \Mykhailok\SupportChat\Model\ChatMessageFactory $chatMessageFactory,
        \Mykhailok\SupportChat\Model\ResourceModel\Chat $resourceModelChat,
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage $resourceModelChatMessage,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->chatFactory = $chatFactory;
        $this->chatMessageFactory = $chatMessageFactory;
        $this->resourceModelChat = $resourceModelChat;
        $this->resourceModelChatMessage = $resourceModelChatMessage;
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger;
    }

    /**
     * @throws \Zend_Db_Statement_Exception
     * @throws \Exception
     */
    public function execute(): void
    {
        $tableName = $this->resourceConnection->getTableName('mykhailok_support_chat');

        // Select data from old table.
        $data = $this->selectChatMessageData($tableName);

        // Move data to new tables.
        $this->migrateMessages($data);
    }

    /**
     * @return bool
     */
    public function dropOldTable(): bool
    {
        $tableName = $this->resourceConnection->getTableName('mykhailok_support_chat');
        return $this->resourceConnection->getConnection()->dropTable($tableName);
    }

    /**
     * @param string $tableName
     * @return array
     * @throws \Zend_Db_Statement_Exception
     */
    private function selectChatMessageData(string $tableName): array
    {
        if (!$this->resourceConnection->getConnection()->isTableExists($tableName)) {
            return [];
        }

        $connection = $this->resourceConnection->getConnection();
        $oldestChatTableSelect = $connection->select()->from($tableName);

        return $connection->query($oldestChatTableSelect)->fetchAll();
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    private function migrateMessages(array $data): void
    {
        // Storages unique chats by chat hash. This column was duplicated in the previous version.
        $uniqueChats = [];

        foreach ($data as $message) {
            $chatHash = $message['chat_hash'];
            $websiteId = $message['website_id'];

            try {
                // Proceed chat entity.
                // Get $chatModel from an existing row or creating (migrating) a new one when it does not exist.
                if (isset($uniqueChats[$chatHash])) {
                    $chatModel = $uniqueChats[$chatHash];
                } else {
                    /** @var \Mykhailok\SupportChat\Model\Chat $chatModel */
                    $chatModel = $this->chatFactory->create();
                    $chatModel->setHash($chatHash)
                        ->setWebsiteId($websiteId);
                    $this->resourceModelChat->save($chatModel);
                    $uniqueChats[$chatHash] = $chatModel;
                }

                // Proceed message entity.
                /** @var \Mykhailok\SupportChat\Model\ChatMessage $chatMessageModel */
                $chatMessageModel = $this->chatMessageFactory->create();
                $chatMessageModel->setChatId($chatModel->getId())
                    ->setAuthorType($message['author_type'])
                    ->setAuthorId($message['author_id'])
                    ->setAuthorName($message['author_name'])
                    ->setMessage($message['message'])
                    ->setCreatedAt($message['created_at']);
                $this->resourceModelChatMessage->save($chatMessageModel);
            } catch (\Magento\Framework\Exception\AlreadyExistsException $exception) {
                $this->logger->error($exception->getMessage(), $exception->getTrace());
            }
        }
    }
}
