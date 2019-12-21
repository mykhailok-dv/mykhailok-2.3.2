<?php
declare(strict_types=1);

namespace Mykhailok\ControllerDemo\Controller\Index;

use Magento\Framework\Controller\Result\Forward as ForwardResult;
use Magento\Framework\Controller\ResultFactory;

class Forward extends \Magento\Framework\App\Action\Action implements
    \Magento\Framework\App\Action\HttpGetActionInterface
{
    /**
     * @inheritDoc
     * https://mykhailokhrypko.local/forward/index/forward
     */
    public function execute()
    {
        $params = [
            'name' => 'Mykhailo',
            'lastname' => 'Khrypko',
            'repository_url' => 'https://github.com/mykhailok-dv/mykhailok-2.3.2',
        ];

        /** @var ForwardResult $response */
        $response = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);

        return $response
            ->setParams($params)
            ->forward('data');
    }
}
