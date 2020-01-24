<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model;

/**
 * Class MessageUserDataProvider
 * Provide method getChatMessageWithUserData();
 */
class MessageUserDataProvider
{
    /**
     * @var \Mykhailok\SupportChat\Model\ChatMessageFactory
     */
    private $chatMessageFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var MessageAuthor
     */
    private $messageAuthor;

    /**
     * MessageUserDataProvider constructor.
     * @param \Mykhailok\SupportChat\Model\ChatMessageFactory $chatMessageFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor
     */
    public function __construct(
        \Mykhailok\SupportChat\Model\ChatMessageFactory $chatMessageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor
    ) {
        $this->chatMessageFactory = $chatMessageFactory;
        $this->storeManager = $storeManager;
        $this->messageAuthor = $messageAuthor;
    }

    /**
     * @return ChatMessage
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getChatMessageWithUserData(): ChatMessage
    {
        /** @var ChatMessage $chatMessage */
        $chatMessage = $this->chatMessageFactory->create();

        $chatMessage->setAuthorId($this->messageAuthor->getId())
            ->setAuthorType($this->messageAuthor->getType())
            ->setAuthorName($this->messageAuthor->getName())
            ->setWebsiteId((int)$this->storeManager->getWebsite()->getId())
            ->setChatHash($this->messageAuthor->getHash());

        return $chatMessage;
    }
}
