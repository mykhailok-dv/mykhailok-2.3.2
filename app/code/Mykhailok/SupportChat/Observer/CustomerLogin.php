<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Observer;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\DB\Transaction;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mykhailok\SupportChat\Model\ChatMessage;
use Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\Collection;

class CustomerLogin implements ObserverInterface
{
    /**
     * @var \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory
     */
    private $chatMessageCollectionFactory;

    /**
     * @var \Mykhailok\SupportChat\Model\MessageAuthor
     */
    private $messageAuthor;

    /**
     * @var \Magento\Framework\DB\TransactionFactory
     */
    private $transactionFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory,
        \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->chatMessageCollectionFactory = $chatMessageCollectionFactory;
        $this->messageAuthor = $messageAuthor;
        $this->transactionFactory = $transactionFactory;
        $this->logger = $logger;
    }

    public function execute(Observer $observer): void
    {
        /** QuestHash was pass into @see CustomerPredispatch::execute() */
        $this->messageAuthor->getId();
        $chatHash[] = $this->chatMessageCollectionFactory->create()
                ->addAuthorIdFilter($this->messageAuthor->getId())
                ->addAuthorTypeFilter($this->messageAuthor->getType())
                ->setPageSize(1)
                ->getFirstItem()
                ->getData('chat_hash');
        $chatHash[] = $this->messageAuthor->getQuestHash();

        /** $chatMessageCollection contain messages from quest chat. */
        $chatMessageCollection = $this->chatMessageCollectionFactory->create();
        $chatMessageCollection
            ->addChatHashFilter(['in' => $chatHash]);

        $this->updateQuestMessages($chatMessageCollection);
    }

    private function updateQuestMessages(Collection $chatMessageCollection): void
    {
        if (count($chatMessageCollection)) {
            /** @var Transaction $transaction */
            $transaction = $this->transactionFactory->create();

            /**
             * Update author_id and author_type fields of every current quest message.
             * @var ChatMessage $chatMessage
             */
            foreach ($chatMessageCollection as $chatMessage) {
                $chatMessage->setChatHash($this->messageAuthor->getHash());
                /** Messages by quest should be updated from author_type, author_id, author_name fields also. */
                if ((int)$chatMessage->getAuthorType() === UserContextInterface::USER_TYPE_GUEST) {
                    $chatMessage
                        ->setAuthorType(UserContextInterface::USER_TYPE_CUSTOMER)
                        ->setAuthorId((int)$this->messageAuthor->getId())
                        ->setAuthorName($this->messageAuthor->getName());
                }
                $transaction->addObject($chatMessage);
            }
            /** Save updated data. */
            try {
                $transaction->save();
            } catch (\Exception $exception) {
                $this->logger->error($exception->getMessage(), $exception->getTrace());
            }
        }
    }
}
