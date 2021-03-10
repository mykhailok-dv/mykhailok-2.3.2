<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Observer\Grid;

class ChatCollection implements \Magento\Framework\Event\ObserverInterface
{
    /** @var \Magento\Framework\App\ResourceConnection $resourceConnection */
    private \Magento\Framework\App\ResourceConnection $resourceConnection;

    /** @var \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory */
    private \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory;

    /** @var \Magento\Framework\App\Request\Http $request */
    private \Magento\Framework\App\Request\Http $request;

    /**
     * ChatCollection constructor.
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->chatMessageCollectionFactory = $chatMessageCollectionFactory;
        $this->request = $request;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->isAllowJoinMessageTable($observer)) {
            return;
        }

        $chatMessageTableName = $this->resourceConnection->getTableName('my_chat_message');
        $gridCollectionSelect = $observer->getData('gridCollection')->getSelect();

        $chatMessageChildQuery = $this->chatMessageCollectionFactory->create()->getSelect();
        $chatMessageChildQuery
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns([
                'chat_id',
                'last_message_id' => new \Zend_Db_Expr("MAX(id)"),
                'count_of_messages' => new \Zend_Db_Expr("COUNT(id)")
            ])
            ->group('chat_id');

        $gridCollectionSelect
            ->join(
                ['cm' => $chatMessageTableName],
                'main_table.id = cm.chat_id'
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
                'author_id' => 'cm.author_type',
                'author_name' => 'cm.author_type',
                'message' => 'cm.message',
                'created_at' => 'cm.created_at',
                'message_count' => 'cm_child.count_of_messages',
            ]);
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return bool
     */
    protected function isAllowJoinMessageTable(\Magento\Framework\Event\Observer $observer): bool
    {
        $gridCollection = $observer->getData('gridCollection');

        $rules = [
            $gridCollection instanceof \Mykhailok\SupportChat\Model\ResourceModel\Chat\Grid\Collection,
            in_array($this->request->getFullActionName(), [
                'my_chat_chat_index',
                'mui_index_render',
            ]),
        ];

        if (in_array(false, $rules, true)) {
            return false;
        }

        return true;
    }
}
