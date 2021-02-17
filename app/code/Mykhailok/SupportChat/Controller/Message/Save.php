<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Controller\Message;

class Save implements
    \Magento\Framework\App\ActionInterface,
    \Magento\Framework\App\Action\HttpPostActionInterface
{
    private \Mykhailok\SupportChat\Model\ChatMessageFactory $chatMessageFactory;
    private \Mykhailok\SupportChat\Model\MessageUserDataProviderFactory $messageUserDataProviderFactory;
    private \Mykhailok\SupportChat\Model\ResourceModel\ChatMessageFactory $chatMessageResourceFactory;
    private \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage $resourceModelChatMessage;
    private \Magento\Store\Model\StoreManagerInterface $storeManager;
    private \Mykhailok\SupportChat\Service\RequestValidate $requestValidate;
    private \Magento\Framework\Message\ManagerInterface $messageManager;
    private \Psr\Log\LoggerInterface $logger;
    private \Magento\Framework\Controller\ResultFactory $resultFactory;
    private \Magento\Framework\App\RequestInterface $request;

    public function __construct(
        \Mykhailok\SupportChat\Model\ChatMessageFactory $chatMessageFactory,
        \Mykhailok\SupportChat\Model\MessageUserDataProviderFactory $messageUserDataProviderFactory,
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessageFactory $chatMessageResourceFactory,
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage $resourceModelChatMessage,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mykhailok\SupportChat\Service\RequestValidate $requestValidate,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->chatMessageFactory = $chatMessageFactory;
        $this->messageUserDataProviderFactory = $messageUserDataProviderFactory;
        $this->chatMessageResourceFactory = $chatMessageResourceFactory;
        $this->resourceModelChatMessage = $resourceModelChatMessage;
        $this->storeManager = $storeManager;
        $this->requestValidate = $requestValidate;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
        $this->resultFactory = $resultFactory;
        $this->request = $request;
    }

    /**
     * @inheritDoc
     * https://mykhailokhrypko.local/support/message/index
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        $result = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        try {
            $this->requestValidate->validate();

            /** @var \Mykhailok\SupportChat\Model\MessageUserDataProvider $messageUserDataProviderFactory */
            $messageUserDataProviderFactory = $this->messageUserDataProviderFactory->create();
            $chatMessageModel = $messageUserDataProviderFactory->getChatMessageWithUserData();

            /** Save a new message. */
            $chatMessageModel->setMessage($this->request->getParam('message'));
            $this->resourceModelChatMessage->save($chatMessageModel);

        } catch (\Magento\Framework\Exception\LocalizedException $localizedException) {
            $this->messageManager->addErrorMessage($localizedException->getMessage());
            $result->setHttpResponseCode($localizedException->getCode());
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $this->messageManager->addErrorMessage(__('Unable to connect to the store support team.'));
            $result->setHttpResponseCode(400);
        }

        return $result->setData(['messages' => []]);
    }
}
