<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\Collection;

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
class ChatMessage extends AbstractModel
{
    /**
     * @var Collection
     */
    private $chatMessageCollection;

    /**
     * @var MessageAuthor
     */
    private $messageAuthor;

    public function __construct(
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\Collection $chatMessageCollection,
        \Mykhailok\SupportChat\Model\MessageAuthor $messageAuthor,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->chatMessageCollection = $chatMessageCollection;
        $this->messageAuthor = $messageAuthor;
    }
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Mykhailok\SupportChat\Model\ResourceModel\ChatMessage::class);
    }

    /**
     * @var self
     */
    private $lastMessage;

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        parent::beforeSave();

        $this->validate();

        return $this;
    }

    /**
     * @throws LocalizedException
     */
    public function validate(): void
    {
        if (empty($this->getMessage())) {
            throw new LocalizedException(__('You don\'t ask your question.'));
        }
    }

    /**
     * @return $this|null
     */
    public function getPreviousMessage(): ?self
    {
        if ($this->lastMessage === null) {
            $this->loadPreviousMessage();
        }

        return $this->lastMessage;
    }

    /**
     * @return void
     */
    private function loadPreviousMessage(): void
    {
        if ($this->messageAuthor->getType() === UserContextInterface::USER_TYPE_GUEST) {
            $this->chatMessageCollection
                ->addChatHashFilter($this->getChatHash());
        }

        $this->chatMessageCollection
            ->addAuthorIdFilter($this->messageAuthor->getId())
            ->setPageSize(1)
            ->addOrder('created_at');

        $this->lastMessage = $this->chatMessageCollection
            ->getLastItem();
    }
}
