<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Observer;

class CustomerLogin implements \Magento\Framework\Event\ObserverInterface
{
    private \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory;
    private \Mykhailok\SupportChat\Model\ResourceModel\Chat $resourceModelChat;
    private \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor;
    private \Magento\Framework\Session\SessionManager $sessionManager;
    private \Psr\Log\LoggerInterface $logger;

    /**
     * CustomerLogin constructor.
     * @param \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory
     * @param \Mykhailok\SupportChat\Model\ResourceModel\Chat $resourceModelChat
     * @param \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor
     * @param \Magento\Framework\Session\SessionManager $sessionManager
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory,
        \Mykhailok\SupportChat\Model\ResourceModel\Chat $resourceModelChat,
        \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor,
        \Magento\Framework\Session\SessionManager $sessionManager,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->chatCollectionFactory = $chatCollectionFactory;
        $this->resourceModelChat = $resourceModelChat;
        $this->messageAuthor = $messageAuthor;
        $this->sessionManager = $sessionManager;
        $this->logger = $logger;
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

        $chatModel = $this->chatCollectionFactory->create()
            ->addHashFilter($questCustomerChatHash)
            ->getFirstItem();

        if ($chatModel instanceof \Mykhailok\SupportChat\Model\Chat) {
            $chatModel->setHash($loggedCustomerChatHash);
            $this->resourceModelChat->save($chatModel);
        }
    }
}
