<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Controller\Adminhtml\Chat;

class Details implements \Magento\Framework\App\ActionInterface
{
    public const ADMIN_RESOURCE = 'Mykhailok_SupportChat::chat-listing';
    private \Magento\Framework\Controller\ResultFactory $resultFactory;

    /**
     * Details constructor.
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     */
    public function __construct(
        \Magento\Framework\Controller\ResultFactory $resultFactory
    ) {
        $this->resultFactory = $resultFactory;
    }

    public function execute()
    {
        return $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
    }
}
