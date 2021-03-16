<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model\Repository;

abstract class AbstractChatMessageRepository implements \Mykhailok\SupportChat\Api\ChatMessageRepositoryInterface
{
    /** @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor */
    protected \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor;

    /** @var \Magento\Framework\EntityManager\EntityManager $entityManager */
    protected \Magento\Framework\EntityManager\EntityManager $entityManager;

    /** @var \Mykhailok\SupportChat\Service\RequestValidate $messageValidator */
    protected \Mykhailok\SupportChat\Service\RequestValidate $messageValidator;

    /** @var \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory */
    protected \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory;

    /** @var \Mykhailok\SupportChat\Api\Data\ChatMessageInterfaceFactory $chatMessageDataFactory */
    protected \Mykhailok\SupportChat\Api\Data\ChatMessageInterfaceFactory $chatMessageDataFactory;

    /** @var \Mykhailok\SupportChat\Api\Data\ChatMessageSearchResultInterfaceFactory $chatMessageSearchResultFactory */
    protected \Mykhailok\SupportChat\Api\Data\ChatMessageSearchResultInterfaceFactory $chatMessageSearchResultFactory;

    /**
     * ChatMessageRepository constructor.
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
     * @param \Magento\Framework\EntityManager\EntityManager $entityManager
     * @param \Mykhailok\SupportChat\Service\RequestValidate $messageValidator
     * @param \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory
     * @param \Mykhailok\SupportChat\Api\Data\ChatMessageInterfaceFactory $chatMessageDataFactory
     * @param \Mykhailok\SupportChat\Api\Data\ChatMessageSearchResultInterfaceFactory $chatMessageSearchResultFactory
     */
    public function __construct(
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor,
        \Magento\Framework\EntityManager\EntityManager $entityManager,
        \Mykhailok\SupportChat\Service\RequestValidate $messageValidator,
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory,
        \Mykhailok\SupportChat\Api\Data\ChatMessageInterfaceFactory $chatMessageDataFactory,
        \Mykhailok\SupportChat\Api\Data\ChatMessageSearchResultInterfaceFactory $chatMessageSearchResultFactory
    ) {
        $this->collectionProcessor = $collectionProcessor;
        $this->entityManager = $entityManager;
        $this->messageValidator = $messageValidator;
        $this->chatMessageCollectionFactory = $chatMessageCollectionFactory;
        $this->chatMessageDataFactory = $chatMessageDataFactory;
        $this->chatMessageSearchResultFactory = $chatMessageSearchResultFactory;
    }

    /**
     * @param \Mykhailok\SupportChat\Api\Data\ChatMessageInterface $chatMessageData
     * @return \Mykhailok\SupportChat\Api\Data\ChatMessageInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(
        \Mykhailok\SupportChat\Api\Data\ChatMessageInterface $chatMessageData
    ): \Mykhailok\SupportChat\Api\Data\ChatMessageInterface
    {
        try {
            $this->entityManager->save($chatMessageData);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__($exception->getMessage()));
        }

        return $chatMessageData;
    }

    /**
     * @param int $id
     * @return \Mykhailok\SupportChat\Api\Data\ChatMessageInterface
     * @throws \Magento\Framework\Exception\IntegrationException
     */
    public function get(
        int $id
    ): \Mykhailok\SupportChat\Api\Data\ChatMessageInterface
    {
        throw new \Magento\Framework\Exception\IntegrationException(__('This method should be overridden.'));
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     * @return \Mykhailok\SupportChat\Api\Data\ChatMessageSearchResultInterface
     */
    public function getList(
        ?\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ): \Mykhailok\SupportChat\Api\Data\ChatMessageSearchResultInterface
    {
        $chatMessageCollection = $this->chatMessageCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $chatMessageCollection);
        $chatMessages = [];

        /** @var \Mykhailok\SupportChat\Model\ChatMessage $chatMessage */
        foreach ($chatMessageCollection as $chatMessage) {
            $data = $chatMessage->getData();
            $chatMessages[] = $this->chatMessageDataFactory->create(['data' => $data]);
        }

        /** @var \Mykhailok\SupportChat\Api\Data\ChatMessageSearchResultInterface $searchResults */
        $searchResults = $this->chatMessageSearchResultFactory->create();
        $searchResults->setTotalCount($chatMessageCollection->getSize());
        $searchResults->setItems($chatMessages);

        return $searchResults;
    }

    /**
     * @param \Mykhailok\SupportChat\Api\Data\ChatMessageInterface $chatMessage
     * @return bool
     */
    public function delete(
        \Mykhailok\SupportChat\Api\Data\ChatMessageInterface $chatMessage
    ): bool
    {
        try {
            $this->entityManager->delete($chatMessage);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteById(
        int $id
    ): bool
    {
        return $this->delete($this->get($id));
    }
}
