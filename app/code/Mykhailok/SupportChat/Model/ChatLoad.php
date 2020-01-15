<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Customer\Model\Visitor;
use Magento\User\Model\User;
use Mykhailok\SupportChat\Model\ResourceModel\Chat\Collection;

class ChatLoad extends Chat
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Authorization\Model\UserContextInterface
     */
    private $userContext;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var ResourceModel\Chat\CollectionFactory
     */
    private $chatCollectionFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session $backendSession
     */
    private $backendSession;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Visitor\CollectionFactory
     */
    private $customerVisitorFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var array|null
     */
    private $author;

    /**
     * @var \Mykhailok\SupportChat\Model\Chat
     */
    private $lastMessage;

    /**
     * ChatLoad constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Authorization\Model\UserContextInterface $userContext
     * @param \Magento\Framework\App\RequestInterface $request
     * @param ResourceModel\Chat\CollectionFactory $chatCollectionFactory
     * @param \Magento\Backend\Model\Auth\Session $backendSession
     * @param \Magento\Customer\Model\ResourceModel\Visitor\CollectionFactory $customerVisitorFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Authorization\Model\UserContextInterface $userContext,
        \Magento\Framework\App\RequestInterface $request,
        \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory,
        \Magento\Backend\Model\Auth\Session $backendSession,
        \Magento\Customer\Model\ResourceModel\Visitor\CollectionFactory $customerVisitorFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->userContext = $userContext;
        $this->request = $request;
        $this->chatCollectionFactory = $chatCollectionFactory;
        $this->backendSession = $backendSession;
        $this->customerVisitorFactory = $customerVisitorFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return $this|Chat
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        parent::beforeSave();

        $this->setAuthorId((int)$this->getAuthor('id'))
            ->setAuthorType($this->getAuthor('type'))
            ->setAuthorName($this->getAuthor('name'))
            ->setWebsiteId((int)$this->storeManager->getWebsite()->getId())
            ->_setChatHash();

        if ($this->getPreviousMessage()->getId() === null) {
            $this->setCreatedAt(new \DateTime());
        }

        return $this;
    }

    /**
     * @return void
     */
    protected function _setChatHash(): void
    {
        $chatHash = null;
        $userIsAdmin = $this->userContext->getUserType() === UserContextInterface::USER_TYPE_ADMIN;

        $chatHash = $userIsAdmin
            /** For admin chat_hash should be take from client message. */
            ? $this->request->getParam('chat_hash', null)
            /** For customers chat_hash should be take from last message or makes if it's new. */
            : $this->getPreviousMessage()->getChatHash();

        /**
         * $chatHash will be null when the user is new.
         * For new client chat_hash will be equal session_id.
         */
        $chatHash = $chatHash ?? $this->customerSession->getSessionId();

        $this->setChatHash($chatHash);
    }

    /**
     * @return \Mykhailok\SupportChat\Model\Chat
     */
    public function getPreviousMessage(): \Mykhailok\SupportChat\Model\Chat
    {
        if ($this->lastMessage === null) {
            /** @var Collection $chatCollection */
            $chatCollection = $this->chatCollectionFactory->create()
                ->addAuthorIdFilter($this->getAuthor('id'))
                ->addAuthorTypeFilter($this->getAuthor('type'))
                ->setPageSize(1)
                ->addOrder('created_at');
            $this->lastMessage = $chatCollection->getLastItem();
        }
        return $this->lastMessage;
    }

    /**
     * @param string $fieldName
     * @return int|string|array|null
     */
    protected function getAuthor($fieldName = null)
    {
        if (!$this->author) {
            $author = null;
            $authorType = $this->userContext->getUserType() ?? UserContextInterface::USER_TYPE_GUEST;

            switch ($authorType) {
                case UserContextInterface::USER_TYPE_ADMIN:
                    /** @var \Magento\User\Model\User $user */
                    $user = $this->backendSession->getUser();
                    $author = $user instanceof User ?: [
                        'id' => $user->getId(),
                        'name' => $user->getName(),
                    ];
                    break;
                case UserContextInterface::USER_TYPE_CUSTOMER:
                    $customer = $this->customerSession->getCustomer();
                    $author = [
                        'id' => $customer->getId(),
                        'name' => $customer->getName(),
                    ];
                    break;
                case UserContextInterface::USER_TYPE_GUEST:
                default:
                    /** @var Visitor $customerVisitor */
                    $customerVisitor = $this->customerVisitorFactory->create()
                        ->addFieldToFilter('session_id', ['eq' => $this->customerSession->getSessionId()])
                        ->getLastItem();
                    $author = [
                        'id' => $customerVisitor->getId() ?? 0,
                        'name' => 'Customer Visitor #' . $customerVisitor->getId() ?? '',
                    ];
                    break;
            }

            $this->author = $author + ['type' => $authorType];
        }

        return $fieldName
            ? $this->author[$fieldName] ?? null
            : $this->author;
    }
}
