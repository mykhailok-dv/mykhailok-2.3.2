<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model;

class MessageUserDataProvider
{
    protected ?\Mykhailok\SupportChat\Model\Chat $chatModel;

    private ChatFactory $chatFactory;
    private ChatMessageFactory $chatMessageFactory;
    private ResourceModel\Chat $resourceModelChat;
    private ResourceModel\Chat\CollectionFactory $chatCollectionFactory;
    private MessageAuthor $messageAuthor;
    private \Magento\Store\Model\StoreManagerInterface $storeManager;

    /**
     * MessageUserDataProvider constructor.
     * @param ChatFactory $chatFactory
     * @param ChatMessageFactory $chatMessageFactory
     * @param ResourceModel\Chat $resourceModelChat
     * @param ResourceModel\Chat\CollectionFactory $chatCollectionFactory
     * @param MessageAuthor $messageAuthor
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Mykhailok\SupportChat\Model\ChatFactory $chatFactory,
        \Mykhailok\SupportChat\Model\ChatMessageFactory $chatMessageFactory,
        \Mykhailok\SupportChat\Model\ResourceModel\Chat $resourceModelChat,
        \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory,
        \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->chatFactory = $chatFactory;
        $this->chatMessageFactory = $chatMessageFactory;
        $this->resourceModelChat = $resourceModelChat;
        $this->chatCollectionFactory = $chatCollectionFactory;
        $this->messageAuthor = $messageAuthor;
        $this->storeManager = $storeManager;
    }

    /**
     * @return \Mykhailok\SupportChat\Model\ChatMessage
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getChatMessageWithUserData(): \Mykhailok\SupportChat\Model\ChatMessage
    {
        $chatModel = $this->getChatWithUserData();

        /** @var \Mykhailok\SupportChat\Model\ChatMessage $chatMessage */
        $chatMessage = $this->chatMessageFactory->create();

        $chatMessage
            ->setChatId($chatModel->getId())
            ->setAuthorId($this->messageAuthor->getId())
            ->setAuthorType($this->messageAuthor->getType())
            ->setAuthorName($this->messageAuthor->getName());

        return $chatMessage;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getChatWithUserData(): \Mykhailok\SupportChat\Model\Chat
    {
        if (!isset($this->chatModel)) {
            /** @var \Mykhailok\SupportChat\Model\ResourceModel\Chat\Collection $chatCollection */
            $chatCollection = $this->chatCollectionFactory->create();
            $chatCollection->addHashFilter($this->messageAuthor->getHash());

            /** @var \Mykhailok\SupportChat\Model\Chat $chatModel */
            $chatModel = $chatCollection->getFirstItem();

            if ($chatModel->getId()) {
                $this->chatModel = $chatModel;
            } else {
                $this->chatModel = $chatModel
                    ->setHash($this->messageAuthor->getHash())
                    ->setWebsiteId((int)$this->storeManager->getWebsite()->getId());
                $this->resourceModelChat->save($chatModel);
            }
        }

        return $this->chatModel;
    }
}
