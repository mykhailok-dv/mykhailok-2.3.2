<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model;

class ChatPriorityObserver
{
    private \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory;
    private \Psr\Log\LoggerInterface $logger;

    /**
     * ChatPriorityObserver constructor.
     * @param ResourceModel\Chat\CollectionFactory $chatCollectionFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->chatCollectionFactory = $chatCollectionFactory;
        $this->logger = $logger;
    }

    /**
     * @return bool
     */
    public function applyWaitingPriority(): bool
    {
        $readyToChangePriorityChatCollection = $this->getPotentialWaitingChats();
        $preparedIds = $readyToChangePriorityChatCollection->getAllIds();

        if (empty($preparedIds)) {
            return true;
        }

        $countRowToUpdate = count($preparedIds);
        $countRowToUpdated = 0;

        try {
            foreach ($preparedIds as $preparedId) {
                $countRowToUpdated += (int)$readyToChangePriorityChatCollection->getConnection()->update(
                    $readyToChangePriorityChatCollection->getResource()->getMainTable(),
                    ['priority' => \Mykhailok\SupportChat\Model\Chat::WAITING_PRIORITY],
                    ['id = ?' => $preparedId]
                );
            }

            return $countRowToUpdated === $countRowToUpdate;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
        }
    }

    /**
     * @return mixed
     */
    protected function getPotentialWaitingChats()
    {
        return $this->chatCollectionFactory->create()->addPotentialWaitingFilter();
    }
}
