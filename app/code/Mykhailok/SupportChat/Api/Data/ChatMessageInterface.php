<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Api\Data;

interface ChatMessageInterface
{
    public const ID = 'id';
    public const CHAT_ID = 'chat_id';
    public const AUTHOR_TYPE = 'author_type';
    public const AUTHOR_ID = 'author_id';
    public const AUTHOR_NAME = 'author_name';
    public const MESSAGE = 'message';
    public const CREATED_AT = 'created_at';

    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id): self;

    /**
     * @return int
     */
    public function getChatId(): int;

    /**
     * @param int $chatId
     * @return $this
     */
    public function setChatId(int $chatId): self;

    /**
     * @return int
     */
    public function getAuthorType(): int;

    /**
     * @param int $authorType
     * @return $this
     */
    public function setAuthorType(int $authorType): self;

    /**
     * @return int
     */
    public function getAuthorId(): int;

    /**
     * @param int $authorId
     * @return $this
     */
    public function setAuthorId(int $authorId): self;

    /**
     * @return string
     */
    public function getAuthorName(): string;

    /**
     * @param string $authorName
     * @return $this
     */
    public function setAuthorName(string $authorName): self;

    /**
     * @return string
     */
    public function getMessage(): string;

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): self;

    /**
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * @param string|null $createdAt
     * @return mixed
     */
    public function setCreatedAt(string $createdAt = null): self;
}
