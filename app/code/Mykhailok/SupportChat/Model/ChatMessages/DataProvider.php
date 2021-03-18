<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model\ChatMessages;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /** @var array $loadedData */
    private array $loadedData = [];

    /** @var \Magento\Framework\App\RequestInterface $request */
    private \Magento\Framework\App\RequestInterface $request;

    /** @var \Magento\Backend\Model\Auth\Session $backendSession */
    private \Magento\Backend\Model\Auth\Session $backendSession;

    /**
     * DataProvider constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory
     * @param \Magento\Backend\Model\Auth\Session $backendSession
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Mykhailok\SupportChat\Model\ResourceModel\ChatMessage\CollectionFactory $chatMessageCollectionFactory,
        \Magento\Backend\Model\Auth\Session $backendSession,
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
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
        $this->request = $request;
        $this->collection = $chatMessageCollectionFactory->create();
        $this->backendSession = $backendSession;
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
        } else {
            $user = $this->backendSession->getUser();

            if ($user instanceof \Magento\User\Model\User) {
                $authorId = $user->getId();
                $authorType = \Magento\Authorization\Model\UserContextInterface::USER_TYPE_ADMIN;
                $authorName = $user->getName();
                $chatId = $this->request->getParam('chat_id');

                $this->loadedData[$chatId] = [
                    'author_id' => $authorId,
                    'author_type' => $authorType,
                    'author_name' => $authorName,
                    'chat_id' => $chatId,
                ];
            }
        }

        return $this->loadedData;
    }
}
