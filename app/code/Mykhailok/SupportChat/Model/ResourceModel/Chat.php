<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model\ResourceModel;

class Chat extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('my_chat', 'id');
    }
}
