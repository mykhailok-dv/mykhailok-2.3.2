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
