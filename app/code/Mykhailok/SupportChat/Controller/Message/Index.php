<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Controller\Message;

use Magento\Framework\Controller\Result\Json as JsonResult;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action implements
    \Magento\Framework\App\Action\HttpPostActionInterface
{
    /**
     * @inheritDoc
     * https://mykhailokhrypko.local/support/message/index
     */
    public function execute()
    {
        /** @var JsonResult $response */
        $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $array = [];
        foreach (range(0, random_int(0, 2)) as $key => $value) {
            $array[] = [
                'time' => time(),
                'message' => 'Message #' . ++$key . '. Says: ' . random_int(1, 100) . '. Good Luck!',
            ];
        }

        $response->setData([
            'success' => true,
            'messages' => $array,
        ]);

        return $response;
    }
}
