<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

/**
 * @method int getSupportChatMessageId()
 * @method int getAuthorType()
 * @method $this setAuthorType(int $authorType)
 * @method int getAuthorId()
 * @method $this setAuthorId(int $authorId)
 * @method string getAuthorName()
 * @method $this setAuthorName(string $authorName)
 * @method string getMessage()
 * @method $this setMessage(string $message)
 * @method int getWebsiteId()
 * @method $this setWebsiteId(int $websiteId)
 * @method string getChatHash()
 * @method $this setChatHash(string $chatHash)
 * @method \Datetime getCreatedAt()
 * @method $this setCreatedAt(\Datetime $datetime)
 */
class Chat extends AbstractModel
{

    protected function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\Chat::class);
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        parent::beforeSave();

        $this->validate();

        return $this;
    }

    public function validate(): void
    {
        if (empty($this->getMessage())) {
            throw new LocalizedException(__('You don\'t ask your question.'));
        }
    }
}
