<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ChatMessage extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('mykhailok_support_chat', 'support_chat_message_id');
    }
}
