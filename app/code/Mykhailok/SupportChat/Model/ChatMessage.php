<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model;

/**
 * @method int getId()
 * @method $this setId(int $Id)
 * @method int getChatId()
 * @method $this setChatId(int $chatId)
 * @method int getAuthorType()
 * @method $this setAuthorType(int $authorType)
 * @method int getAuthorId()
 * @method $this setAuthorId(int $authorId)
 * @method string getAuthorName()
 * @method $this setAuthorName(string $authorName)
 * @method string getMessage()
 * @method $this setMessage(string $message)
 * @method \Datetime getCreatedAt()
 * @method $this setCreatedAt(\Datetime $datetime)
 */
class ChatMessage extends \Magento\Framework\Model\AbstractModel
{
    private \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\Collection $chatMessageCollection;
    private \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor;

    /**
     * ChatMessage constructor.
     * @param ResourceModel\ChatMessage\Collection $chatMessageCollection
     * @param MessageAuthor $messageAuthor
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\Collection $chatMessageCollection,
        \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->chatMessageCollection = $chatMessageCollection;
        $this->messageAuthor = $messageAuthor;
    }

    /**
     * @inheirtDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Mykhailok\SupportChat\Model\ResourceModel\ChatMessage::class);
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        parent::beforeSave();

        $this->validate();

        return $this;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate(): void
    {
        if (empty($this->getMessage())) {
            throw new \Magento\Framework\Exception\LocalizedException(__('You don\'t ask your question.'));
        }
    }
}
