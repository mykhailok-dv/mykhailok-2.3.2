<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Controller\Adminhtml\Chat;

class Details extends \Magento\Backend\App\Action implements
    \Magento\Framework\App\ActionInterface,
    \Magento\Framework\App\Action\HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Mykhailok_SupportChat::chat_reading';

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        return $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
    }
}
