<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Controller\Message;

class Save implements
    \Magento\Framework\App\ActionInterface,
    \Magento\Framework\App\Action\HttpPostActionInterface
{
    /** @var \Mykhailok\SupportChat\Model\MessageUserDataProviderFactory $messageUserDataProviderFactory */
    private \Mykhailok\SupportChat\Model\MessageUserDataProviderFactory $messageUserDataProviderFactory;

    /** @var \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage $resourceModelChatMessage */
    private \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage $resourceModelChatMessage;

    /** @var \Mykhailok\SupportChat\Service\RequestValidate $requestValidate */
    private \Mykhailok\SupportChat\Service\RequestValidate $requestValidate;

    /** @var \Magento\Framework\Message\ManagerInterface $messageManager */
    private \Magento\Framework\Message\ManagerInterface $messageManager;

    /** @var \Psr\Log\LoggerInterface $logger */
    private \Psr\Log\LoggerInterface $logger;

    /** @var \Magento\Framework\Controller\ResultFactory $resultFactory */
    private \Magento\Framework\Controller\ResultFactory $resultFactory;

    /** @var \Magento\Framework\App\RequestInterface $request */
    private \Magento\Framework\App\RequestInterface $request;

    /**
     * Save constructor.
     * @param \Mykhailok\SupportChat\Model\MessageUserDataProviderFactory $messageUserDataProviderFactory
     * @param \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage $resourceModelChatMessage
     * @param \Mykhailok\SupportChat\Service\RequestValidate $requestValidate
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Mykhailok\SupportChat\Model\MessageUserDataProviderFactory $messageUserDataProviderFactory,
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage $resourceModelChatMessage,
        \Mykhailok\SupportChat\Service\RequestValidate $requestValidate,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->messageUserDataProviderFactory = $messageUserDataProviderFactory;
        $this->resourceModelChatMessage = $resourceModelChatMessage;
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
