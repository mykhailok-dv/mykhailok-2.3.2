<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model;

/**
 * @method string getHash()
 * @method $this setHash(string $chatHash)
 * @method int getWebsiteId()
 * @method $this setWebsiteId(int $websiteId)
 * @method int getPriority()
 * @method $this setPriority(int $priority)
 * @method bool getIsActive()
 * @method $this setIsActive(bool $isActive)
 */
class Chat extends \Magento\Framework\Model\AbstractModel
{
    public const REGULAR_PRIORITY = 0;
    public const WAITING_PRIORITY = 1;
    public const IMMEDIATE_PRIORITY = 2;

    public const IS_NOT_ACTIVE = 0;
    public const IS_ACTIVE = 1;

    protected function _construct()
    {
        $this->_init(\Mykhailok\SupportChat\Model\ResourceModel\Chat::class);
    }
}
