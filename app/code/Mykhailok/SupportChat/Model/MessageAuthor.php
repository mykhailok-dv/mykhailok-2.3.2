<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model;

use Magento\Authorization\Model\UserContextInterface;
use Magento\User\Model\User;

/**
 * Class MessageAuthor
 * Class returned an user fields: id and name which depends of user context.
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
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $backendSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Framework\Session\SessionManager
     */
    private $sessionManager;

    /**
     * @var ResourceModel\ChatMessage\CollectionFactory
     */
    private $chatMessageCollection;

    /**
     * MessageAuthor constructor.
     * @param \Magento\Backend\Model\Auth\Session $backendSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Session\SessionManager $sessionManager
     * @param ResourceModel\ChatMessage\Collection $chatMessageCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $backendSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Session\SessionManager $sessionManager,
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\Collection $chatMessageCollection,
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
        if ($this->_getData('id') === null || $this->_getData('name') === null) {
            $this->_init();
        }

        return parent::__call($method, $args);
    }

    /**
     * @return void
     */
    private function _init(): void
    {
        $this->setType($this->getUserType());

        switch ($this->_data['type']) {
            case UserContextInterface::USER_TYPE_ADMIN:
                /** @var \Magento\User\Model\User $user */
                $user = $this->backendSession->getUser();
                if ($user instanceof User) {
                    $this->_data['id'] = (int)$user->getId();
                    $this->_data['name'] = $user->getName();
                    $this->_data['hash'] = $this->request->getParam('chat_hash', null);
                }
                break;
            case UserContextInterface::USER_TYPE_CUSTOMER:
                $customer = $this->customerSession->getCustomer();
                $this->_data['id'] = (int)$customer->getId();
                $this->_data['name'] = $customer->getName();
                $this->_data['hash'] = $this->chatMessageCollection
                    ->addAuthorIdFilter($this->_getData('id'))
                    ->addAuthorTypeFilter((int)$this->_getData('type'))
                    ->setPageSize(1)
                    ->getLastItem()
                    ->getData('chatHash');
                break;
            case UserContextInterface::USER_TYPE_GUEST:
            default:
                $this->_data['id'] = 0;
                $this->_data['name'] = 'Quest #' . microtime(true);
                $this->_data['hash'] = $this->sessionManager->getSessionId();
                break;
        }
    }

    public function setQuestHash(string $chatHash): void
    {
        $this->setData('questHash', $chatHash);
    }

    public function getQuestHash(): string
    {
        return (string)$this->getData('questHash');
    }

    public function getUserType(): int
    {
        if (($user = $this->backendSession->getUser()) && $user instanceof User) {
            return UserContextInterface::USER_TYPE_ADMIN;
        }
        if ($user = $this->customerSession->getCustomerId()) {
            return UserContextInterface::USER_TYPE_CUSTOMER;
        }

        return UserContextInterface::USER_TYPE_GUEST;
    }
}
