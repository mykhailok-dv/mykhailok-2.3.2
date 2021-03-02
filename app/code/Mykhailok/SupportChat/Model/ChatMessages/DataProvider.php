<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model\ChatMessages;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    private array $loadedData = [];

    public function __construct(
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
        $this->collection = $chatMessageCollectionFactory->create();
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData(): array
    {
        if (!isset($this->loadedData)) {
            $items = $this->collection->getItems();

            /** @var \Mykhailok\SupportChat\Model\ChatMessage $item */
            foreach ($items as $item) {
                $this->loadedData[$item->getId()] = $item->getData();
            }
        }

        return $this->loadedData;
    }
}
