<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model\ResourceModel\Chat;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(
            \Mykhailok\SupportChat\Model\Chat::class,
            \Mykhailok\SupportChat\Model\ResourceModel\Chat::class
            );
    }

    /**
     * @param int $authorType
     * @return $this
     */
    public function addAuthorTypeFilter(int $authorType)
    {
        $userContextReflectionClass = new \ReflectionClass(\Magento\Authorization\Model\UserContextInterface::class);
        $authorTypes = $userContextReflectionClass->getConstants();

        /**
         * Filter will be applies when $authorType is const from UserContextInterface.
         * @see \Magento\Authorization\Model\UserContextInterface
         */
        if (isset(array_flip($authorTypes)[$authorType])) {
            return $this->addFieldToFilter('author_type', $authorType);
        } else {
            return $this;
        }
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @return $this
     */
    public function addCreatedAtRangeFilter(\DateTime $from, \DateTime $to = null)
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
    public function addWebsiteFilter(int $websiteId)
    {
        return $this->addFieldToFilter('website_id', $websiteId);
    }

    /**
     * @param $authorId
     * @return $this
     */
    public function addAuthorIdFilter($authorId)
    {
        return $this->addFieldToFilter('author_id', $authorId);
    }

    /**
     * @param $chatHash
     * @return $this
     */
    public function addChatHashFilter($chatHash)
    {
        return $this->addFieldToFilter('chat_hash', $chatHash);
    }
}
