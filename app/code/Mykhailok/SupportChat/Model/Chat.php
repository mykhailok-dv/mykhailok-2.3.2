<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model;

/**
 * @method string getHash()
 * @method $this setHash(string $chatHash)
 * @method int getWebsiteId()
 * @method $this setWebsiteId(int $websiteId)
 */
class Chat extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Mykhailok\SupportChat\Model\ResourceModel\Chat::class);
    }
}
