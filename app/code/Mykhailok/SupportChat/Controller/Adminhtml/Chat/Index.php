<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Controller\Adminhtml\Chat;

class Index implements \Magento\Framework\App\ActionInterface,
    \Magento\Framework\App\Action\HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Mykhailok_SupportChat::chat-listing';

    /** @var \Magento\Framework\Controller\ResultFactory $resultFactory */
    private \Magento\Framework\Controller\ResultFactory $resultFactory;

    /**
     * Index constructor.
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     */
    public function __construct(
        \Magento\Framework\Controller\ResultFactory $resultFactory
    ) {
        $this->resultFactory = $resultFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        return $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
    }
}
