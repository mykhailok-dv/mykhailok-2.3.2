<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Controller\Message;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json as JsonResult;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\DB\Transaction;
use Mykhailok\SupportChat\Model\ChatLoad;

class Save extends \Magento\Framework\App\Action\Action implements
    \Magento\Framework\App\Action\HttpPostActionInterface
{
    use \Mykhailok\SupportChat\Controller\Message\Traits\RequestValidateTrait;

    /** @var \Mykhailok\SupportChat\Model\ChatFactory $chatFactory */
    private $chatFactory;

    /** @var \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory */
    private $chatCollectionFactory;

    /** @var \Magento\Framework\DB\TransactionFactory $transactionFactory */
    private $transactionFactory;

    /** @var \Magento\Store\Model\StoreManagerInterface $storeManager */
    private $storeManager;

    /** @var \Psr\Log\LoggerInterface $logger */
    private $logger;

    /** @var \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator */
    private $formKeyValidator;

    /** @var array $author */
    protected $author;

    /** @var \Mykhailok\SupportChat\Model\ChatLoadFactory */
    private $chatLoadFactory;

    /**
     * Save constructor.
     * @param \Mykhailok\SupportChat\Model\ChatFactory $chatFactory
     * @param \Mykhailok\SupportChat\Model\ChatLoadFactory $chatLoadFactory
     * @param \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $collectionFactory
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Psr\Log\LoggerInterface $logger
     * @param Context $context
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     */
    public function __construct(
        \Mykhailok\SupportChat\Model\ChatFactory $chatFactory,
        \Mykhailok\SupportChat\Model\ChatLoadFactory $chatLoadFactory,
        \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $collectionFactory,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Psr\Log\LoggerInterface $logger,
        Context $context
    ) {
        $this->chatFactory = $chatFactory;
        $this->chatLoadFactory = $chatLoadFactory;
        $this->chatCollectionFactory = $collectionFactory;
        $this->transactionFactory = $transactionFactory;
        $this->storeManager = $storeManager;
        $this->formKeyValidator = $formKeyValidator;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     * https://mykhailokhrypko.local/support/message/index
     * @return \Magento\Framework\App\ResponseInterface|JsonResult|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $this->requestValidate();

        if ($this->responseData) {
            /** @var ChatLoad $chatLoad */
            $chatLoad = $this->chatLoadFactory->create();
            try {
                /** @var Transaction $transaction */
                $transaction = $this->transactionFactory->create();
                /** Save a new message. */
                $chatLoad->setMessage($this->getRequest()->getParam('message'));
                $transaction->addObject($chatLoad);
                $transaction->save();
                /** @TODO: Method is temporary. Should by deleted when will be admin form functional. */
                $this->emulatorAdminAnswer($chatLoad->getChatHash());
            } catch (\Exception $exception) {
                $this->logger->critical($exception);
                $this->responseData['success'] = false;
            } finally {
                /** Load all unread messages. */
                $from_datetime = $chatLoad->getPreviousMessage()->getId()
                    ? new \DateTime($chatLoad->getPreviousMessage()->getCreatedAt())
                    : $chatLoad->getCreatedAt();

                $unread_messages = $this->chatCollectionFactory->create()
                    ->addCreatedAtRangeFilter($from_datetime);

                $this->setResponseMessages($unread_messages);
            }
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)
            ->setData($this->responseData);
    }

    /**
     * @param $chatHash
     * @return bool
     */
    protected function emulatorAdminAnswer($chatHash): bool
    {
        try {
            /** @var Transaction $transaction */
            $transaction = $this->transactionFactory->create();
            $chat = $this->chatFactory->create();
            /** @var \Mykhailok\SupportChat\Model\Chat $chat */
            $chat->setAuthorType(UserContextInterface::USER_TYPE_ADMIN)
                ->setAuthorId(1)
                ->setAuthorName('Admin')
                ->setMessage('Current time: ' . (new \DateTime())->format('Y-m-d H:i:s'))
                ->setWebsiteId((int)$this->storeManager->getWebsite()->getId())
                ->setChatHash($chatHash);
            $transaction->addObject($chat);
            $transaction->save();
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            return false;
        }
        return true;
    }
}
