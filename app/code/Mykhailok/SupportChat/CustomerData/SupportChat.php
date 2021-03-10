<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\CustomerData;

class SupportChat implements \Magento\Customer\CustomerData\SectionSourceInterface
{
    /** @var array */
    private array $responseData = [];

    /** @var \Psr\Log\LoggerInterface $logger */
    private \Psr\Log\LoggerInterface $logger;

    /** @var \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory */
    private \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory;

    /** @var \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor */
    private \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor;

    /** @var \Magento\Framework\App\RequestInterface $request */
    private \Magento\Framework\App\RequestInterface $request;

    /** @var \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory */
    private \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory;

    /**
     * SupportChat constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory
     * @param \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory
     * @param \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory,
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory,
        \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->logger = $logger;
        $this->chatCollectionFactory = $chatCollectionFactory;
        $this->messageAuthor = $messageAuthor;
        $this->request = $request;
        $this->chatMessageCollectionFactory = $chatMessageCollectionFactory;
    }

    /**
     * @return array
     */
    public function getSectionData(): array
    {
        try {
            $limit = $this->request->getParam('limit');
            $limit = (1 <= $limit) && ($limit <= 100) ? $limit : 10;

            /** @var \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\Collection $chatCollection */
            $chatCollection = $this->chatCollectionFactory->create();
            $chatMessageCollection = $this->chatMessageCollectionFactory->create();
            $chatMessageCollection
                ->fetchMessagesByChatHash($this->messageAuthor->getHash())
                ->setPageSize($limit)
                ->setOrder(
                    $chatCollection->getResource()->getIdFieldName(),
                    \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\Collection::SORT_ORDER_DESC
                );
            $this->prepareResponseData($chatMessageCollection);
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
        }

        return $this->responseData;
    }

    /**
     * @param \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\Collection $chatCollection
     */
    private function prepareResponseData(
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\Collection $chatCollection
    ): void {
        /** @var \Mykhailok\SupportChat\Model\ChatMessage $chat */
        foreach ($chatCollection as $chat) {
            if ($chat->getId()) {
                $this->responseData['messages'][$chat->getId()] = [
                    'time' => $chat->getCreatedAt(),
                    'text' => $chat->getMessage(),
                    'authorName' => $chat->getAuthorName(),
                    'authorType' => (int)$chat->getAuthorType() ===
                    \Magento\Authorization\Model\UserContextInterface::USER_TYPE_ADMIN
                        ? 'USER_TYPE_ADMIN'
                        : 'USER_TYPE_USER',
                ];
            }
        }
    }
}
