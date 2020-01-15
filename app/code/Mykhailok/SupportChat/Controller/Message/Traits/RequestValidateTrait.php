<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Controller\Message\Traits;

use Magento\Authorization\Model\UserContextInterface;
use Mykhailok\SupportChat\Model\Chat;
use Mykhailok\SupportChat\Model\ResourceModel\Chat\Collection as ChatCollection;

trait RequestValidateTrait
{
    /**
     * @var array $responseData
     */
    private $responseData;

    /**
     * Do validate a request data and add errors to @return void
     * @see $responseData.
     */
    protected function requestValidate(): void
    {
        try {
            if (!$this->getRequest()->isSecure()) {
                throw new \RuntimeException(__('You haven\'t secure connection. Please, reload page.')->render());
            }
            if (!$this->getRequest()->isAjax()) {
                throw new \RuntimeException(__('You aren\'t using ajax connection.')->render());
            }
            if (!$this->formKeyValidator->validate($this->getRequest())) {
                throw new \RuntimeException(__('You have wrong form. Please, reload page.')->render());
            }
        } catch (\Exception $exception) {
            $this->responseData['messages'][] = [
                'time' => time(),
                'text' => $exception->getMessage(),
                'authorName' => 'System',
                'authorType' => 'system',
            ];
        } finally {
            $this->responseData['success'] = true;
        }
    }

    /**
     * @param ChatCollection $chatCollection
     * @return void
     */
    protected function setResponseMessages(ChatCollection $chatCollection): void
    {
        if ($chatCollection->count()) {
            $this->responseData['success'] = true;
        } else {
            $this->responseData = [
                'success' => false,
                'messages' => [],
            ];
        }

        /** @var Chat $chat */
        foreach ($chatCollection as $chat) {
            $this->responseData['messages'][$chat->getSupportChatMessageId()] = [
                'time' => $chat->getCreatedAt(),
                'text' => $chat->getMessage(),
                'authorName' => $chat->getAuthorName(),
                'authorType' => (int)$chat->getAuthorType() === UserContextInterface::USER_TYPE_ADMIN
                    ? 'USER_TYPE_ADMIN'
                    : 'USER_TYPE_USER',
            ];
        }
    }
}