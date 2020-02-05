<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\CustomerData;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Customer\CustomerData\SectionSourceInterface;
use Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\Collection;
use Mykhailok\SupportChat\Model\ChatMessage;

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
     * @var \Mykhailok\SupportChat\Model\MessageAuthor
     */
    private $messageAuthor;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var array
     */
    private $responseData = [];

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatCollectionFactory,
        \Mykhailok\SupportChat\Model\MessageAuthor\Interceptor $messageAuthor,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->logger = $logger;
        $this->chatCollectionFactory = $chatCollectionFactory;
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
            $this->prepareResponseData($chatCollection);

        } catch (\Exception $exception) {
            $this->logger->critical($exception);
        }

        return $this->responseData;
    }

    /**
     * @param Collection $chatCollection
     * @return void
     */
    private function prepareResponseData(Collection $chatCollection = null): void
    {
        if ($chatCollection === null) {
            $this->responseData['messages'] = [];
        } else {
            /** @var ChatMessage $chat */
            foreach ($chatCollection as $chat) {
                if ($chat->getSupportChatMessageId()) {
                    $this->responseData['messages'][$chat->getSupportChatMessageId()] = [
                        'time' => $chat->getCreatedAt(),
                        'text' => $chat->getMessage(),
                        'authorName' => $chat->getAuthorName(),
                        'authorType' => (int)$chat->getAuthorType() === UserContextInterface::USER_TYPE_ADMIN
                            ? 'USER_TYPE_ADMIN'
                            : 'USER_TYPE_USER',
                    ];
                }
            }
        }
    }
}
