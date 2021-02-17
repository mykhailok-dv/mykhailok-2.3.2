<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\CustomerData;

class SupportChat implements \Magento\Customer\CustomerData\SectionSourceInterface
{
    private \Psr\Log\LoggerInterface $logger;
    private \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory;
    private \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor;
    private \Magento\Framework\App\RequestInterface $request;
    private array $responseData = [];
    private \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory;

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
     * @param ?\Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\Collection $chatCollection
     */
    private function prepareResponseData(
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\Collection $chatCollection = null
    ): void {
        if ($chatCollection === null) {
            $this->responseData['messages'] = [];
        } else {
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
}
