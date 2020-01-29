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
     * @var UserContextInterface
     */
    private $userContext;

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
     * @var \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollection
     */
    private $chatMessageCollection;

    /**
     * MessageAuthor constructor.
     * @param UserContextInterface $userContext
     * @param \Magento\Backend\Model\Auth\Session $backendSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Session\SessionManager $sessionManager
     * @param \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Authorization\Model\UserContextInterface $userContext,
        \Magento\Backend\Model\Auth\Session $backendSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Session\SessionManager $sessionManager,
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollection,
        array $data = []
    ) {
        parent::__construct($data);
        $this->userContext = $userContext;
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
     */
    private function _init(): void
    {
        $this->setType($this->userContext->getUserType() ?? UserContextInterface::USER_TYPE_GUEST);

        switch ($this->_data['type']) {
            case UserContextInterface::USER_TYPE_ADMIN:
                /** @var \Magento\User\Model\User $user */
                $user = $this->backendSession->getUser();
                if ($user instanceof User) {
                    $this->setId((int) $user->getId())
                        ->setName($user->getName())
                        ->setHash($this->request->getParam('chat_hash', null));
                }
                break;
            case UserContextInterface::USER_TYPE_CUSTOMER:
                $customer = $this->customerSession->getCustomer();
                $chatHash = $this->chatMessageCollection->create()
                    ->addAuthorIdFilter($this->_getData('id'))
                    ->addAuthorTypeFilter($this->_getData('type'))
                    ->setPageSize(1)
                    ->getFirstItem()
                    ->getData('chat_hash') ?? $this->sessionManager->getSessionId();

                $this->setId((int) $customer->getId())
                    ->setName($customer->getName())
                    ->setHash($chatHash);
                break;
            case UserContextInterface::USER_TYPE_GUEST:
            default:
                $this->setId(0)
                    ->setName('Quest #' . microtime(true))
                    ->setHash($this->sessionManager->getSessionId());
                break;
        }
    }
}
