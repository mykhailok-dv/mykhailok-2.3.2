<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Controller\Adminhtml\Chat;

class Delete implements \Magento\Framework\App\ActionInterface
{
    private \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory;
    private \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory;
    private \Magento\Ui\Component\MassAction\Filter $filter;
    private \Magento\Framework\DB\TransactionFactory $transactionFactory;
    private \Magento\Framework\Controller\ResultFactory $resultFactory;
    private \Magento\Framework\Message\ManagerInterface $messageManager;

    public function __construct(
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory,
        \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->chatMessageCollectionFactory = $chatMessageCollectionFactory;
        $this->chatCollectionFactory = $chatCollectionFactory;
        $this->filter = $filter;
        $this->transactionFactory = $transactionFactory;
        $this->resultFactory = $resultFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
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
