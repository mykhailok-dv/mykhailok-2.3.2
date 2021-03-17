<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Controller\Adminhtml\Chat;

class MassDelete extends \Magento\Framework\App\Action\Action
    implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Mykhailok_SupportChat::chat_delete';

    /** @var \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory */
    private \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory;

    /** @var \Magento\Ui\Component\MassAction\Filter $filter */
    private \Magento\Ui\Component\MassAction\Filter $filter;

    /** @var \Magento\Framework\DB\TransactionFactory $transactionFactory */
    private \Magento\Framework\DB\TransactionFactory $transactionFactory;

    /**
     * MassDelete constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Framework\DB\TransactionFactory $transactionFactory
    ) {
        parent::__construct($context);
        $this->chatCollectionFactory = $chatCollectionFactory;
        $this->filter = $filter;
        $this->transactionFactory = $transactionFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute(): \Magento\Backend\Model\View\Result\Redirect
    {
        /** @var \Magento\Framework\DB\Transaction $transaction */
        $transaction = $this->transactionFactory->create();
        try {
            $collection = $this->filter->getCollection($this->chatCollectionFactory->create());

            foreach ($collection as $item) {
                $transaction->addObject($item);
            }

            $transaction->delete();
            $collectionSize = $collection->count();
            $this->messageManager->addSuccessMessage(__('%1 preference(s) have been deleted.', $collectionSize));
        } catch (\Exception|\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addExceptionMessage($e);
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
