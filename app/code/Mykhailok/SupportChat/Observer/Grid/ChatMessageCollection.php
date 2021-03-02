<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Observer\Grid;

class ChatMessageCollection implements \Magento\Framework\Event\ObserverInterface
{
    private \Magento\Framework\App\ResourceConnection $resourceConnection;
    private \Magento\Framework\App\Request\Http $request;

    /**
     * ChatMessageCollection constructor.
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->request = $request;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $gridCollection = $observer->getData('gridCollection');
        $chatId = $this->request->getParam('chat_id');

        if ($gridCollection instanceof \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult) {
            $gridCollection->addFieldToFilter('chat_id', $chatId);
        }
    }
}
