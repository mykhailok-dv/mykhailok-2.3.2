<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Controller\Adminhtml\Chat;

class Details implements \Magento\Framework\App\ActionInterface
{
    public const ADMIN_RESOURCE = 'Mykhailok_SupportChat::chat-listing';

    /** @var \Magento\Framework\Controller\ResultFactory $resultFactory */
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

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        return $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
    }
}
