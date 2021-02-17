<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Observer;

class CustomerPredispatch implements \Magento\Framework\Event\ObserverInterface
{
    private \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor;
    private \Magento\Framework\Session\SessionManager $sessionManager;

    /**
     * CustomerPredispatch constructor.
     * @param \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor
     * @param \Magento\Framework\Session\SessionManager $sessionManager
     */
    public function __construct(
        \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor,
        \Magento\Framework\Session\SessionManager $sessionManager
    ) {
        $this->messageAuthor = $messageAuthor;
        $this->sessionManager = $sessionManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $userIsQuest =
            $this->messageAuthor->getUserType() === \Magento\Authorization\Model\UserContextInterface::USER_TYPE_GUEST;
        $actionIsLoginPost =
            $observer->getData('controller_action') instanceof \Magento\Customer\Controller\Account\LoginPost;

        if ($userIsQuest && $actionIsLoginPost) {
            $this->messageAuthor->setQuestHash($this->sessionManager->getSessionId());
        }
    }
}
