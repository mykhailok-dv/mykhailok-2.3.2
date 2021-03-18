<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Observer\Grid;

class ChatMessageCollection implements \Magento\Framework\Event\ObserverInterface
{
    /** @var \Magento\Framework\App\Request\Http $request */
    private \Magento\Framework\App\Request\Http $request;

    /**
     * ChatMessageCollection constructor.
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->request = $request;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult $gridCollection */
        $gridCollection = $observer->getData('gridCollection');
        $chatId = $this->request->getParam('chat_id');
        $gridCollection->addFieldToFilter('chat_id', $chatId);
    }
}
