<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model\ResourceModel\ChatMessage;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Authorization\Model\UserContextInterface;

class Collection extends AbstractCollection
{
    private static $authorTypes = [
        UserContextInterface::USER_TYPE_ADMIN,
        UserContextInterface::USER_TYPE_CUSTOMER,
        UserContextInterface::USER_TYPE_GUEST,
    ];

    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(
            \Mykhailok\SupportChat\Model\ChatMessage::class,
            \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage::class
        );
    }

    /**
     * @param int $authorType
     * @return $this
     */
    public function addAuthorTypeFilter(int $authorType): self
    {
        /**
         * Filter will be applies when $authorType is const from UserContextInterface.
         * @see \Magento\Authorization\Model\UserContextInterface
         */
        if (in_array($authorType, self::$authorTypes, true)) {
            $this->addFieldToFilter('author_type', $authorType);
        }

        return $this;
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @throws \Exception
     * @return $this
     */
    public function addCreatedAtRangeFilter(\DateTime $from, \DateTime $to = null): self
    {
        return $this->addFieldToFilter('created_at', [
            'from' => $from,
            'to' => $to ?? new \DateTime(),
            'datetime' => true,
        ]);
    }

    /**
     * @param int $websiteId
     * @return $this
     */
    public function addWebsiteFilter(int $websiteId): self
    {
        return $this->addFieldToFilter('website_id', $websiteId);
    }

    /**
     * @param $authorId
     * @return $this
     */
    public function addAuthorIdFilter($authorId): self
    {
        return $this->addFieldToFilter('author_id', $authorId);
    }

    /**
     * @param $chatHash
     * @return $this
     */
    public function addChatHashFilter($chatHash): self
    {
        return $this->addFieldToFilter('chat_hash', $chatHash);
    }
}
