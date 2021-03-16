<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model\Repository;

class FrontendChatMessageRepository extends AbstractChatMessageRepository
{
    /** @var \Magento\Framework\Api\FilterBuilder $filterBuilder */
    protected \Magento\Framework\Api\FilterBuilder $filterBuilder;

    /** @var \Magento\Framework\Session\SessionManager $sessionManager */
    protected \Magento\Framework\Session\SessionManager $sessionManager;

    /** @var \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory */
    protected \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory;

    /** @var \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder */
    protected \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder;

    /** @var \Magento\Framework\EntityManager\HydratorInterface $hydrator */
    protected \Magento\Framework\EntityManager\HydratorInterface $hydrator;

    /** @var \Mykhailok\SupportChat\Model\MessageUserDataProviderFactory $messageUserDataProviderFactory */
    protected \Mykhailok\SupportChat\Model\MessageUserDataProviderFactory $messageUserDataProviderFactory;

    public function __construct(
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor,
        \Magento\Framework\EntityManager\EntityManager $entityManager,
        \Mykhailok\SupportChat\Service\RequestValidate $messageValidator,
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory,
        \Mykhailok\SupportChat\Api\Data\ChatMessageInterfaceFactory $chatMessageDataFactory,
        \Mykhailok\SupportChat\Api\Data\ChatMessageSearchResultInterfaceFactory $chatMessageSearchResultFactory,
        \Mykhailok\SupportChat\Model\MessageUserDataProviderFactory $messageUserDataProviderFactory,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Session\SessionManager $sessionManager,
        \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Magento\Framework\EntityManager\HydratorInterface $hydrator
    ) {
        parent::__construct(
            $collectionProcessor,
            $entityManager,
            $messageValidator,
            $chatMessageCollectionFactory,
            $chatMessageDataFactory,
            $chatMessageSearchResultFactory
        );
        $this->messageUserDataProviderFactory = $messageUserDataProviderFactory;
        $this->filterBuilder = $filterBuilder;
        $this->sessionManager = $sessionManager;
        $this->chatCollectionFactory = $chatCollectionFactory;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->hydrator = $hydrator;
    }

    /**
     * @param \Mykhailok\SupportChat\Api\Data\ChatMessageInterface $chatMessageData
     * @return \Mykhailok\SupportChat\Api\Data\ChatMessageInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Mykhailok\SupportChat\Api\Data\ChatMessageInterface $chatMessageData
    ): \Mykhailok\SupportChat\Api\Data\ChatMessageInterface
    {
        $chatMessageModel = $this->messageUserDataProviderFactory->create()
            ->getChatMessageWithUserData();
        $chatMessageData
            ->setChatId((int)$chatMessageModel->getChatId())
            ->setAuthorId((int)$chatMessageModel->getAuthorId())
            ->setAuthorType((int)$chatMessageModel->getAuthorType())
            ->setAuthorName((string)$chatMessageModel->getAuthorName())
            ->setCreatedAt();

        return parent::save($chatMessageData);
    }

    public function get(int $id): \Mykhailok\SupportChat\Api\Data\ChatMessageInterface
    {
        $chatModel = $this->getChatModel();
        $chatMessageCollection = $this->chatMessageCollectionFactory->create();
        $chatMessageData = $this->chatMessageDataFactory->create();

        $chatMessage = $chatMessageCollection
            ->addMessageIdFilter($id)
            ->addChatIdFilter($chatModel->getId())
            ->getFirstItem();

        /** @var \Mykhailok\SupportChat\Api\Data\ChatMessageInterface $chatMessageData */
        $chatMessageData = $this->hydrator->hydrate($chatMessageData, $chatMessage->getData());

        return $chatMessageData;
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     * @return \Mykhailok\SupportChat\Api\Data\ChatMessageSearchResultInterface
     */
    public function getList(
        ?\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ): \Mykhailok\SupportChat\Api\Data\ChatMessageSearchResultInterface
    {
        $this->addCustomerChatIdFilterToSearchCriteria($searchCriteria);

        return parent::getList($searchCriteria);
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     */
    protected function addCustomerChatIdFilterToSearchCriteria(
        ?\Magento\Framework\Api\SearchCriteriaInterface &$searchCriteria
    ): void
    {
        $chatModel = $this->getChatModel();
            /** @var \Magento\Framework\Api\Filter $filter */
            $chatIdFilter = $this->filterBuilder->create()
                ->setField('chat_id')
                ->setValue($chatModel->getId())
                ->setConditionType('eq');

            $filterGroups = $searchCriteria->getFilterGroups();
            foreach ($filterGroups as &$filterGroup) {
                $existingFilters = $filterGroup->getFilters();
                $updatedFilters = array_merge($existingFilters, [$chatIdFilter]);
                $filterGroup->setFilters($updatedFilters);
            }

            if (empty($filterGroups)) {
                $filterGroups[] = $this->filterGroupBuilder->addFilter($chatIdFilter)->create();
            }

            $searchCriteria->setFilterGroups($filterGroups);
    }

    /**
     * @return \Mykhailok\SupportChat\Model\Chat|null
     */
    protected function getChatModel(): ?\Mykhailok\SupportChat\Model\Chat
    {
        $chatHash = $this->sessionManager->getSessionId();

        /** @var \Mykhailok\SupportChat\Model\Chat $chatModel */
        $chatModel = $this->chatCollectionFactory->create()
            ->addHashFilter($chatHash)
            ->getFirstItem();

        return $chatModel;
    }
}
