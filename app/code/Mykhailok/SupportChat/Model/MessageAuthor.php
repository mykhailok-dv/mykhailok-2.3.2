<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model;

/**
 * @method int getId()
 * @method $this setId(int $id)
 * @method string getName()
 * @method $this setName(string $name)
 * @method int getType()
 * @method $this setType(int $type)
 * @method string getHash()
 * @method $this setHash(string $chatHash)
 */
class MessageAuthor extends \Magento\Framework\DataObject
{
    private \Magento\Backend\Model\Auth\Session $backendSession;
    private \Magento\Customer\Model\Session $customerSession;
    private \Magento\Framework\App\RequestInterface $request;
    private \Magento\Framework\Session\SessionManager $sessionManager;
    private \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollection;

    /**
     * MessageAuthor constructor.
     * @param \Magento\Backend\Model\Auth\Session $backendSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Session\SessionManager $sessionManager
     * @param \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $backendSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Session\SessionManager $sessionManager,
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollection,
        array $data = []
    ) {
        parent::__construct($data);
        $this->backendSession = $backendSession;
        $this->customerSession = $customerSession;
        $this->request = $request;
        $this->sessionManager = $sessionManager;
        $this->chatMessageCollection = $chatMessageCollection;
    }

    /**
     * @inheritDoc
     */
    public function __call($method, $args)
    {
        if (strpos($method, 'set') !== 0
            && ($this->_getData('id') === null || $this->_getData('name') === null)
        ) {
            $this->_init();
        }

        return parent::__call($method, $args);
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function _init(): void
    {
        $this->setType($this->getUserType());

        switch ($this->_data['type']) {
            case \Magento\Authorization\Model\UserContextInterface::USER_TYPE_ADMIN:
                /** @var \Magento\User\Model\User $user */
                $user = $this->backendSession->getUser();
                if ($user instanceof \Magento\User\Model\User) {
                    $this->setId((int)$user->getId())
                        ->setName($user->getName())
                        ->setHash($this->request->getParam('chat_hash', null));
                }
                break;
            case \Magento\Authorization\Model\UserContextInterface::USER_TYPE_CUSTOMER:
                $customer = $this->customerSession->getCustomer();
                $chatHash = $this->chatMessageCollection->create()
                    ->addAuthorIdFilter((int)$this->_getData('id'))
                    ->addAuthorTypeFilter((int)$this->_getData('type'))
                    ->setPageSize(1)
                    ->getFirstItem()
                    ->getData('chat_hash') ?? $this->sessionManager->getSessionId();

                $this->setId((int) $customer->getId())
                    ->setName($customer->getName())
                    ->setHash($chatHash);
                break;
            case \Magento\Authorization\Model\UserContextInterface::USER_TYPE_GUEST:
            default:
                $this->setId(0)
                    ->setName('Quest #' . microtime(true))
                    ->setHash($this->sessionManager->getSessionId());
                break;
        }
    }

    /**
     * @param string $chatHash
     */
    public function setQuestHash(string $chatHash): void
    {
        $this->setData('questHash', $chatHash);
    }

    /**
     * @return string
     */
    public function getQuestHash(): string
    {
        return (string)$this->getData('questHash');
    }

    /**
     * @return int
     */
    public function getUserType(): int
    {
        if (($user = $this->backendSession->getUser()) && $user instanceof \Magento\User\Model\User) {
            return \Magento\Authorization\Model\UserContextInterface::USER_TYPE_ADMIN;
        }
        if ($user = $this->customerSession->getCustomerId()) {
            return \Magento\Authorization\Model\UserContextInterface::USER_TYPE_CUSTOMER;
        }

        return \Magento\Authorization\Model\UserContextInterface::USER_TYPE_GUEST;
    }
}
