<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Controller\Message;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Mykhailok\SupportChat\Model\ResourceModel\Chat\Collection as ChatCollection;

class Load extends \Magento\Framework\App\Action\Action implements
    \Magento\Framework\App\Action\HttpPostActionInterface
{
    use \Mykhailok\SupportChat\Controller\Message\Traits\RequestValidateTrait;

    /**
     * @var \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory
     */
    private $chatCollectionFactory;

    /**
     * @var \Mykhailok\SupportChat\Model\ChatLoadFactory
     */
    private $chatLoadFactory;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    private $formKeyValidator;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        \Mykhailok\SupportChat\Model\ResourceModel\Chat\CollectionFactory $chatCollectionFactory,
        \Mykhailok\SupportChat\Model\ChatLoadFactory $chatLoadFactory,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Psr\Log\LoggerInterface $logger,
        Context $context
    ) {
        parent::__construct($context);
        $this->chatCollectionFactory = $chatCollectionFactory;
        $this->chatLoadFactory = $chatLoadFactory;
        $this->formKeyValidator = $formKeyValidator;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $this->requestValidate();

        if ($this->responseData) {
            try {
                $chatHash = $this->chatLoadFactory->create()
                    ->getPreviousMessage()
                    ->getChatHash();
                $limit = $this->getRequest()->getParam('limit');
                $limit = (1 <= $limit) && ($limit <= 100) ? $limit : 10;

                /** @var ChatCollection $chatCollection */
                $chatCollection = $this->chatCollectionFactory->create()
                    ->addChatHashFilter($chatHash)
                    ->setPageSize($limit);
                $this->setResponseMessages($chatCollection);
            } catch (\Exception $exception) {
                $this->logger->critical($exception);
                $this->responseData['messages'] = false;
            }
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)
            ->setData($this->responseData);
    }
}
