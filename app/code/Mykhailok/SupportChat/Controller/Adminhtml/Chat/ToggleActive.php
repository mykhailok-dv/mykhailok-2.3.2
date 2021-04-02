<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Controller\Adminhtml\Chat;

class ToggleActive extends \Magento\Framework\App\Action\Action
    implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Mykhailok_SupportChat::chat_delete';

    /** @var \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory */
    private \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory;

    /** @var \Magento\Framework\DB\TransactionFactory $transactionFactory */
    private \Magento\Framework\DB\TransactionFactory $transactionFactory;

    /**
     * MassDelete constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory,
        \Magento\Framework\DB\TransactionFactory $transactionFactory
    ) {
        parent::__construct($context);
        $this->chatCollectionFactory = $chatCollectionFactory;
        $this->transactionFactory = $transactionFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute(): \Magento\Backend\Model\View\Result\Redirect
    {
        try {
            $chatId = (int)$this->_request->getParam('id');

            if (!empty($chatId)) {
                /** @var \Mykhailok\SupportChat\Model\Chat $chatModel */
                $chatModel = $this->chatCollectionFactory->create()
                    ->addChatIdFilter($chatId)
                    ->getFirstItem();
                $chatModel->setIsActive(false);

                $this->transactionFactory->create()
                    ->addObject($chatModel)
                    ->save();
            }

            !empty($chatId)
                ? $this->messageManager->addSuccessMessage(__('The chat was marked as inactive.', 1))
                : $this->messageManager->addErrorMessage(
                    __('The chat was marked as inactive. Please, select the chat which should be marked as inactive.')
                );
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e);
        }

        return $this->resultFactory->create(
            \Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT
        )->setPath('*/*/');
    }
}
