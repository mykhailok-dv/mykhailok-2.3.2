<?php
declare(strict_types=1);

namespace Mykhailok\ControllerDemo\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Data extends \Magento\Framework\App\Action\Action implements
    \Magento\Framework\App\Action\HttpGetActionInterface
{
    public function __construct(
        Context $context,
        \Magento\Framework\Event\ManagerInterface $manager
    ) {
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     * https://mykhailokhrypko.local/forward/index/data
     */
    public function execute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
