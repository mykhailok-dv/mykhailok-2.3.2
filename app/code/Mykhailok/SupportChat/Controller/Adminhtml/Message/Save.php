<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Controller\Adminhtml\Message;

class Save extends \Magento\Backend\App\Action implements
    \Magento\Framework\App\ActionInterface,
    \Magento\Framework\App\Action\HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Mykhailok_SupportChat::chat_chatting';

    /** @var \Mykhailok\SupportChat\Service\RequestValidate $requestValidate */
    private \Mykhailok\SupportChat\Service\RequestValidate $requestValidate;

    /** @var \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage $resourceModelChatMessage */
    private \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage $resourceModelChatMessage;

    /** @var \Mykhailok\SupportChat\Model\ChatMessageFactory $chatMessageFactory */
    private \Mykhailok\SupportChat\Model\ChatMessageFactory $chatMessageFactory;

    /**
     * Save constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Mykhailok\SupportChat\Service\RequestValidate $requestValidate
     * @param \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage $resourceModelChatMessage
     * @param \Mykhailok\SupportChat\Model\ChatMessageFactory $chatMessageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mykhailok\SupportChat\Service\RequestValidate $requestValidate,
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage $resourceModelChatMessage,
        \Mykhailok\SupportChat\Model\ChatMessageFactory $chatMessageFactory
    ) {
        parent::__construct($context);
        $this->requestValidate = $requestValidate;
        $this->resourceModelChatMessage = $resourceModelChatMessage;
        $this->chatMessageFactory = $chatMessageFactory;
    }

    public function execute()
    {
        $result = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);

        try {
            $this->requestValidate->validate(true, false, true);

            $chatMessage = $this->chatMessageFactory->create();
            $chatMessage->setData($this->_request->getParams());

            $this->resourceModelChatMessage->save($chatMessage);
            $this->messageManager->addSuccessMessage(__('You added answer'));
        } catch (\Magento\Framework\Exception\LocalizedException $localizedException) {
            $this->messageManager->addErrorMessage($localizedException->getMessage());
            $result->setHttpResponseCode($localizedException->getCode());
        }

        return $result->setPath($this->_redirect->getRefererUrl());
    }
}
