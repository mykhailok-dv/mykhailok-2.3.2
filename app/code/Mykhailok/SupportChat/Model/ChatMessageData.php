<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model;

class ChatMessageData extends \Magento\Framework\Api\AbstractSimpleObject implements
    \Mykhailok\SupportChat\Api\Data\ChatMessageInterface
{
    public function getId(): int
    {
        return (int)$this->_get(self::ID);
    }

    public function setId(int $id): \Mykhailok\SupportChat\Api\Data\ChatMessageInterface
    {
        $this->setData(self::ID, $id);

        return $this;
    }

    public function getChatId(): int
    {
        return (int)$this->_get(self::CHAT_ID);
    }

    public function setChatId(int $chatId): \Mykhailok\SupportChat\Api\Data\ChatMessageInterface
    {
        $this->setData(self::CHAT_ID, $chatId);

        return $this;
    }

    public function getAuthorType(): int
    {
        return (int)$this->_get(self::AUTHOR_TYPE);
    }

    public function setAuthorType(int $authorType): \Mykhailok\SupportChat\Api\Data\ChatMessageInterface
    {
        $this->setData(self::AUTHOR_TYPE, $authorType);

        return $this;
    }

    public function getAuthorId(): int
    {
        return (int)$this->_get(self::AUTHOR_ID);
    }

    public function setAuthorId(int $authorId): \Mykhailok\SupportChat\Api\Data\ChatMessageInterface
    {
        $this->setData(self::AUTHOR_ID, $authorId);

        return $this;
    }

    public function getAuthorName(): string
    {
        return (string)$this->_get(self::AUTHOR_NAME);
    }

    public function setAuthorName(string $authorName): \Mykhailok\SupportChat\Api\Data\ChatMessageInterface
    {
        $this->setData(self::AUTHOR_NAME, $authorName);

        return $this;
    }

    public function getMessage(): string
    {
        return (string)$this->_get(self::MESSAGE);
    }

    public function setMessage(string $message): \Mykhailok\SupportChat\Api\Data\ChatMessageInterface
    {
        $this->setData(self::MESSAGE, $message);

        return $this;
    }

    public function getCreatedAt(): string
    {
        return (string)$this->_get(self::CREATED_AT);
    }

    public function setCreatedAt(string $createdAt = null): \Mykhailok\SupportChat\Api\Data\ChatMessageInterface
    {
        $createdAt = $createdAt ?: (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);

        $this->setData(self::CREATED_AT, $createdAt);

        return $this;
    }
}
