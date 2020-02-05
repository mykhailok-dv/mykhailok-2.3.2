<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Observer;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Customer\Controller\Account\LoginPost;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CustomerPredispatch implements ObserverInterface
{
    /**
     * @var \Mykhailok\SupportChat\Model\MessageAuthor
     */
    private $messageAuthor;
    /**
     * @var \Magento\Framework\Session\SessionManager
     */
    private $sessionManager;

    public function __construct(
        \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor,
        \Magento\Framework\Session\SessionManager $sessionManager
    ) {
        $this->messageAuthor = $messageAuthor;
        $this->sessionManager = $sessionManager;
    }

    public function execute(Observer $observer)
    {
        $userIsQuest = $this->messageAuthor->getUserType() === UserContextInterface::USER_TYPE_GUEST;
        $actionIsLoginPost = $observer->getData('controller_action') instanceof LoginPost;
        if ($userIsQuest && $actionIsLoginPost) {
            $this->messageAuthor->setQuestHash($this->sessionManager->getSessionId());
        }
    }
}
