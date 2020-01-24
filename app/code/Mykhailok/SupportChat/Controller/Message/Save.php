<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Controller\Message;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json as JsonResult;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Mykhailok\SupportChat\Model\MessageUserDataProvider;
use Mykhailok\SupportChat\Service\RequestValidate;
use Mykhailok\SupportChat\Service\ResponseData;

class Save extends \Magento\Framework\App\Action\Action implements
    \Magento\Framework\App\Action\HttpPostActionInterface
{
    /**
     * @var \Mykhailok\SupportChat\Model\ChatMessageFactory
     */
    private $chatMessageFactory;

    /**
     * @var \Mykhailok\SupportChat\Model\MessageUserDataProviderFactory
     */
    private $messageUserDataProviderFactory;

    /**
     * @var \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage
     */
    private $chatMessageResource;

    /**
     * @var \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory
     */
    private $chatMessageCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var RequestValidate
     */
    private $requestValidate;

    /**
     * @var ResponseData
     */
    private $responseData;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        \Mykhailok\SupportChat\Model\ChatMessageFactory $chatMessageFactory,
        \Mykhailok\SupportChat\Model\MessageUserDataProviderFactory $messageUserDataProviderFactory,
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessageFactory $chatMessageResourceFactory,
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mykhailok\SupportChat\Service\RequestValidate $requestValidate,
        \Mykhailok\SupportChat\Service\ResponseData $responseData,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Psr\Log\LoggerInterface $logger,
        Context $context
    ) {
        parent::__construct($context);
        $this->chatMessageFactory = $chatMessageFactory;
        $this->messageUserDataProviderFactory = $messageUserDataProviderFactory;
        $this->chatMessageResource = $chatMessageResourceFactory->create();
        $this->chatMessageCollectionFactory = $chatMessageCollectionFactory;
        $this->storeManager = $storeManager;
        $this->requestValidate = $requestValidate;
        $this->responseData = $responseData;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     * https://mykhailokhrypko.local/support/message/index
     * @return \Magento\Framework\App\ResponseInterface|JsonResult|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $this->requestValidate->validate();

            /** @var MessageUserDataProvider $messageUserDataProviderFactory */
            $messageUserDataProviderFactory = $this->messageUserDataProviderFactory->create();
            $chatMessage = $messageUserDataProviderFactory->getChatMessageWithUserData();

            /** Save a new message. */
            $chatMessage->setMessage($this->getRequest()->getParam('message'));
            $this->chatMessageResource->save($chatMessage);

            /** @TODO: Method is temporary. Should by deleted when will be admin form functional. */
            $this->emulatorAdminAnswer($chatMessage->getChatHash());

            /** Load all unread messages. */
            $previousMessage = $chatMessage->getPreviousMessage();
            $unreadMessages = null;
            if ($previousMessage !== null) {
                $unreadMessages = $this->chatMessageCollectionFactory->create();
                if ($previousMessage->getId() === null) {
                    $unreadMessages->addItem($chatMessage);
                } else {
                    $fromDatetime = new \DateTime($previousMessage->getCreatedAt());
                    $unreadMessages->addCreatedAtRangeFilter($fromDatetime);
                }
            }
            $this->responseData->prepareResponseData($unreadMessages);
        } catch (LocalizedException $localizedException) {
            $this->messageManager->addErrorMessage($localizedException->getMessage());
            $result->setHttpResponseCode($localizedException->getCode());
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $this->messageManager->addErrorMessage(__('Unable to connect to the store support team.'));
        }

        return $result
            ->setData($this->responseData->getData());
    }

    /**
     * @param $chatHash
     * @return void
     */
    protected function emulatorAdminAnswer($chatHash): void
    {
        try {
            $chatMessage = $this->chatMessageFactory->create();
            /** @var \Mykhailok\SupportChat\Model\ChatMessage $chat */
            $chatMessage->setAuthorType(UserContextInterface::USER_TYPE_ADMIN)
                ->setAuthorId(1)
                ->setAuthorName('Admin')
                ->setMessage('Current time: ' . (new \DateTime())->format('Y-m-d H:i:s'))
                ->setWebsiteId((int)$this->storeManager->getWebsite()->getId())
                ->setChatHash($chatHash);
            $this->chatMessageResource->save($chatMessage);
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
        }
    }
}
