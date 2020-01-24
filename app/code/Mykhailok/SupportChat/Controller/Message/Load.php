<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Controller\Message;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\Collection as ChatCollection;

class Load extends \Magento\Framework\App\Action\Action implements
    \Magento\Framework\App\Action\HttpGetActionInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory
     */
    private $chatCollectionFactory;

    /**
     * @var \Mykhailok\SupportChat\Service\RequestValidate
     */
    private $requestValidate;

    /**
     * @var \Mykhailok\SupportChat\Service\ResponseData
     */
    private $responseData;

    /**
     * @var \Mykhailok\SupportChat\Model\MessageAuthor
     */
    private $messageAuthor;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatCollectionFactory,
        \Mykhailok\SupportChat\Service\RequestValidate $requestValidate,
        \Mykhailok\SupportChat\Service\ResponseData $responseData,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor,
        Context $context
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->chatCollectionFactory = $chatCollectionFactory;
        $this->requestValidate = $requestValidate;
        $this->responseData = $responseData;
        $this->messageManager = $messageManager;
        $this->messageAuthor = $messageAuthor;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $this->requestValidate->validate(false);

            $limit = $this->getRequest()->getParam('limit');
            $limit = (1 <= $limit) && ($limit <= 100) ? $limit : 10;

            /** @var ChatCollection $chatCollection */
            $chatCollection = $this->chatCollectionFactory->create()
                ->addChatHashFilter($this->messageAuthor->getHash())
                ->setPageSize($limit);
            $this->responseData->prepareResponseData($chatCollection);

        } catch (LocalizedException $localizedException) {
            $this->messageManager->addErrorMessage($localizedException->getMessage());
            $result->setHttpResponseCode($localizedException->getCode());
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
        }

        return $result
            ->setData($this->responseData->getData());
    }
}
