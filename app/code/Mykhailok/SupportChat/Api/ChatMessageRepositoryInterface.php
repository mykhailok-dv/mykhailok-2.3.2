<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Api;

interface ChatMessageRepositoryInterface
{
    /**
     * @param \Mykhailok\SupportChat\Api\Data\ChatMessageInterface $chatMessageData
     * @return \Mykhailok\SupportChat\Api\Data\ChatMessageInterface
     */
    public function save(
        \Mykhailok\SupportChat\Api\Data\ChatMessageInterface $chatMessageData
    ): \Mykhailok\SupportChat\Api\Data\ChatMessageInterface;

    /**
     * @param int $id
     * @return \Mykhailok\SupportChat\Api\Data\ChatMessageInterface
     */
    public function get(
        int $id
    ): \Mykhailok\SupportChat\Api\Data\ChatMessageInterface;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     * @return \Mykhailok\SupportChat\Api\Data\ChatMessageSearchResultInterface
     */
    public function getList(
        ?\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ): \Mykhailok\SupportChat\Api\Data\ChatMessageSearchResultInterface;

    /**
     * @param Data\ChatMessageInterface $chatMessage
     * @return bool
     */
    public function delete(
        \Mykhailok\SupportChat\Api\Data\ChatMessageInterface $chatMessage
    ): bool;

    /**
     * @param int $id
     * @return bool
     */
    public function deleteById(
        int $id
    ): bool;
}
