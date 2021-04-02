<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Observer\Grid;

class ChatCollection implements \Magento\Framework\Event\ObserverInterface
{
    private \Magento\Framework\App\ResourceConnection $resourceConnection;

    private \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory;

    private \Magento\Framework\App\Request\Http $request;

    private \Mykhailok\SupportChat\Model\ResourceModel\Chat\Collection $chatCollection;

    /**
     * ChatCollection constructor.
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory,
        \Mykhailok\SupportChat\Model\ResourceModel\Chat\Collection $chatCollection,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->chatMessageCollectionFactory = $chatMessageCollectionFactory;
        $this->request = $request;
        $this->chatCollection = $chatCollection;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->isAllowJoinMessageTable($observer)) {
            return;
        }

        /** @var \Mykhailok\SupportChat\Model\ResourceModel\Chat\Grid\Collection $gridCollection */
        $gridCollection = $observer->getData('gridCollection');
        $this->chatCollection->addChatWithLatestMessageFilter($gridCollection);
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
