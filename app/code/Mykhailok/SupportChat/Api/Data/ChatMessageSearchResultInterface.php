<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Api\Data;

interface ChatMessageSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \Mykhailok\SupportChat\Api\Data\ChatMessageInterface[]
     */
    public function getItems();

    /**
     * @param \Mykhailok\SupportChat\Api\Data\ChatMessageInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
