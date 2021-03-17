<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model\Repository;

class ChatMessageRepository extends \Mykhailok\SupportChat\Model\Repository\AbstractChatMessageRepository
{
    /**
     * @param int $id
     * @return \Mykhailok\SupportChat\Api\Data\ChatMessageInterface
     */
    public function get(
        int $id
    ): \Mykhailok\SupportChat\Api\Data\ChatMessageInterface {
        $chatMessage = $this->chatMessageDataFactory->create();

        return $this->entityManager->load($chatMessage, $id);
    }
}
