<?php
/** @noinspection PhpMissingFieldTypeInspection */
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model\ResourceModel\Chat\Grid;

class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    protected $_eventPrefix = 'my_chat_dataprovider_searchresult';
    protected $_eventObject = 'gridCollection';
}
