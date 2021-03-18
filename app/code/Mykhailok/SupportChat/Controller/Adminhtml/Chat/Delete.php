<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Controller\Adminhtml\Chat;

class Delete extends \Magento\Framework\App\Action\Action
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

                $this->transactionFactory->create()
                    ->addObject($chatModel)
                    ->delete();
            }

            !empty($chatId)
                ? $this->messageManager->addSuccessMessage(__('%1 chat(s) have been deleted.', 1))
                : $this->messageManager->addErrorMessage(__('Please, select chat which should be deleted'));
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e);
        }

        return $this->resultFactory->create(
            \Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT
        )->setPath('*/*/');
    }
}
