<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Observer;

class CustomerLogin implements \Magento\Framework\Event\ObserverInterface
{
    /** @var \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory */
    private \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory;

    /** @var \Mykhailok\SupportChat\Model\ResourceModel\Chat $resourceModelChat */
    private \Mykhailok\SupportChat\Model\ResourceModel\Chat $resourceModelChat;

    /** @var \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor */
    private \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor;

    /** @var \Magento\Framework\Session\SessionManager $sessionManager */
    private \Magento\Framework\Session\SessionManager $sessionManager;

    /**
     * CustomerLogin constructor.
     * @param \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory
     * @param \Mykhailok\SupportChat\Model\ResourceModel\Chat $resourceModelChat
     * @param \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor
     * @param \Magento\Framework\Session\SessionManager $sessionManager
     */
    public function __construct(
        \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory,
        \Mykhailok\SupportChat\Model\ResourceModel\Chat $resourceModelChat,
        \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor,
        \Magento\Framework\Session\SessionManager $sessionManager
    ) {
        $this->chatCollectionFactory = $chatCollectionFactory;
        $this->resourceModelChat = $resourceModelChat;
        $this->messageAuthor = $messageAuthor;
        $this->sessionManager = $sessionManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        /** QuestHash was pass into @see CustomerPredispatch::execute() */
        $loggedCustomerChatHash = $this->sessionManager->getSessionId();
        $questCustomerChatHash = $this->messageAuthor->getQuestHash();

        /** @var \Mykhailok\SupportChat\Model\Chat $chatModel */
        $chatModel = $this->chatCollectionFactory->create()
            ->addHashFilter($questCustomerChatHash)
            ->getFirstItem();
        $chatModel->setHash($loggedCustomerChatHash);
        $this->resourceModelChat->save($chatModel);
    }
}
