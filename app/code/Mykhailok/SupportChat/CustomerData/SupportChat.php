<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\Data\Collection;

class SupportChat implements SectionSourceInterface
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
     * @var \Mykhailok\SupportChat\Service\ResponseData
     */
    private $responseData;

    /**
     * @var \Mykhailok\SupportChat\Model\MessageAuthor
     */
    private $messageAuthor;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatCollectionFactory,
        \Mykhailok\SupportChat\Service\ResponseData $responseData,
        \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->logger = $logger;
        $this->chatCollectionFactory = $chatCollectionFactory;
        $this->responseData = $responseData;
        $this->messageAuthor = $messageAuthor;
        $this->request = $request;
    }

    /**
     * @inheritDoc
     */
    public function getSectionData(): array
    {
        try {
            $limit = $this->request->getParam('limit');
            $limit = (1 <= $limit) && ($limit <= 100) ? $limit : 10;

            /** @var \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\Collection $chatCollection */
            $chatCollection = $this->chatCollectionFactory->create();
            $chatCollection
                ->addChatHashFilter($this->messageAuthor->getHash())
                ->setPageSize($limit)
                ->setOrder($chatCollection->getResource()->getIdFieldName(), Collection::SORT_ORDER_DESC);
            $this->responseData->prepareResponseData($chatCollection);

        } catch (\Exception $exception) {
            $this->logger->critical($exception);
        }

        return $this->responseData->getData();
    }
}
