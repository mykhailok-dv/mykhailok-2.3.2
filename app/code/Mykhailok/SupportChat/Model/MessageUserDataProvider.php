<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model;

class MessageUserDataProvider
{
    /** @var \Mykhailok\SupportChat\Model\Chat  */
    protected \Mykhailok\SupportChat\Model\Chat $chatModel;

    /** @var \Mykhailok\SupportChat\Model\ChatMessageFactory $chatMessageFactory */
    private \Mykhailok\SupportChat\Model\ChatMessageFactory $chatMessageFactory;

    /** @var \Mykhailok\SupportChat\Model\ResourceModel\Chat $resourceModelChat*/
    private \Mykhailok\SupportChat\Model\ResourceModel\Chat $resourceModelChat;

    /** @var \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory */
    private \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory;

    /** @var \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor */
    private \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor;

    /** @var \Magento\Store\Model\StoreManagerInterface $storeManager */
    private \Magento\Store\Model\StoreManagerInterface $storeManager;

    private \Magento\Framework\App\Response\RedirectInterface $redirect;

    public function __construct(
        \Mykhailok\SupportChat\Model\ChatMessageFactory $chatMessageFactory,
        \Mykhailok\SupportChat\Model\ResourceModel\Chat $resourceModelChat,
        \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory,
        \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Response\RedirectInterface $redirect
    ) {
        $this->chatMessageFactory = $chatMessageFactory;
        $this->resourceModelChat = $resourceModelChat;
        $this->chatCollectionFactory = $chatCollectionFactory;
        $this->messageAuthor = $messageAuthor;
        $this->storeManager = $storeManager;
        $this->redirect = $redirect;
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
     * @noinspection PhpFieldAssignmentTypeMismatchInspection
     */
    public function getChatWithUserData(): \Mykhailok\SupportChat\Model\Chat
    {
        if (!isset($this->chatModel)) {
            /** @var \Mykhailok\SupportChat\Model\ResourceModel\Chat\Collection $chatCollection */
            $chatCollection = $this->chatCollectionFactory->create();
            $chatCollection->addHashFilter($this->messageAuthor->getHash());

            $this->chatModel = $chatCollection->getFirstItem();
            if ($this->refererPageIsCheckout()) {
                $this->chatModel->setPriority(\Mykhailok\SupportChat\Model\Chat::IMMEDIATE_PRIORITY);
            }

            if (!$this->chatModel->getId()) {
                $this->chatModel
                    ->setHash($this->messageAuthor->getHash())
                    ->setWebsiteId((int)$this->storeManager->getWebsite()->getId());
            }

            if (!$this->chatModel->getIsActive()) {
                $this->chatModel->setIsActive(true);
            }

            $this->resourceModelChat->save($this->chatModel);
        }

        return $this->chatModel;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function refererPageIsCheckout(): bool
    {
        $refererUrl = $this->redirect->getRefererUrl();
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        $refererUrl = str_replace($baseUrl, '', $refererUrl);
        $refererUrl = trim($refererUrl, '/');
        $refererUrlParts = explode('?', $refererUrl);
        $clearRefererUrl = array_shift($refererUrlParts);
        unset($refererUrlParts, $refererUrl);
        $clearRefererUrlParts = explode('/', $clearRefererUrl);

        return $clearRefererUrlParts[0] === 'checkout';
    }
}
