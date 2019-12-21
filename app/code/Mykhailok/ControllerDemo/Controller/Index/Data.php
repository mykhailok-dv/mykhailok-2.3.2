<?php
declare(strict_types=1);

namespace Mykhailok\ControllerDemo\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class Data extends \Magento\Framework\App\Action\Action implements
    \Magento\Framework\App\Action\HttpGetActionInterface
{
    /**
     * @inheritDoc
     * https://mykhailokhrypko.local/forward/index/data
     */
    public function execute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
