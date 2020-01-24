<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Service;

use Magento\Authorization\Model\UserContextInterface;
use Mykhailok\SupportChat\Model\ChatMessage;
use Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\Collection;

class ResponseData
{
    /**
     * @var array
     */
    private $responseData = [];
    /**
     * @param Collection|null $chatCollection
     * @return self
     */
    public function prepareResponseData(Collection $chatCollection = null): self
    {
        if ($chatCollection === null) {
            $this->responseData['messages'] = [];
        } else {
            /** @var ChatMessage $chat */
            foreach ($chatCollection as $chat) {
                if ($chat->getSupportChatMessageId()) {
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

        return $this;
    }

    public function getData(): array
    {
        return $this->responseData;
    }
}
