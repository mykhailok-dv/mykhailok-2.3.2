<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Controller\Adminhtml\Message;

class Save implements
    \Magento\Framework\App\ActionInterface,
    \Magento\Framework\App\Action\HttpPostActionInterface
{
    /** @var \Magento\Framework\App\RequestInterface $request */
    private \Magento\Framework\App\RequestInterface $request;

    /** @var \Magento\Framework\Controller\ResultFactory */
    private \Magento\Framework\Controller\ResultFactory $resultFactory;

    /** @var \Magento\Framework\Message\ManagerInterface $messageManager */
    private \Magento\Framework\Message\ManagerInterface $messageManager;

    /** @var \Mykhailok\SupportChat\Service\RequestValidate $requestValidate */
    private \Mykhailok\SupportChat\Service\RequestValidate $requestValidate;

    /** @var \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage $resourceModelChatMessage */
    private \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage $resourceModelChatMessage;

    /** @var \Mykhailok\SupportChat\Model\ChatMessageFactory $chatMessageFactory */
    private \Mykhailok\SupportChat\Model\ChatMessageFactory $chatMessageFactory;

    /** @var \Magento\Framework\App\Response\RedirectInterface $redirect */
    private \Magento\Framework\App\Response\RedirectInterface $redirect;

    /**
     * Save constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Mykhailok\SupportChat\Service\RequestValidate $requestValidate
     * @param \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage $resourceModelChatMessage
     * @param \Mykhailok\SupportChat\Model\ChatMessageFactory $chatMessageFactory
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Mykhailok\SupportChat\Service\RequestValidate $requestValidate,
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage $resourceModelChatMessage,
        \Mykhailok\SupportChat\Model\ChatMessageFactory $chatMessageFactory,
        \Magento\Framework\App\Response\RedirectInterface $redirect
    ) {
        $this->request = $request;
        $this->resultFactory = $resultFactory;
        $this->messageManager = $messageManager;
        $this->requestValidate = $requestValidate;
        $this->resourceModelChatMessage = $resourceModelChatMessage;
        $this->chatMessageFactory = $chatMessageFactory;
        $this->redirect = $redirect;
    }

    public function execute()
    {
        $result = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);

        try {
            $this->requestValidate->validate(true, false, true);

            $chatMessage = $this->chatMessageFactory->create();
            $chatMessage->setData($this->request->getParams());

            $this->resourceModelChatMessage->save($chatMessage);
            $this->messageManager->addSuccessMessage(__('You added answer'));
        } catch (\Magento\Framework\Exception\LocalizedException $localizedException) {
            $this->messageManager->addErrorMessage($localizedException->getMessage());
            $result->setHttpResponseCode($localizedException->getCode());
        }

        return $result->setPath($this->redirect->getRefererUrl());
    }
}
