<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\Grid;

class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    protected $_eventPrefix = 'my_chatmessage_dataprovider_searchresult';
    protected $_eventObject = 'gridCollection';
}
