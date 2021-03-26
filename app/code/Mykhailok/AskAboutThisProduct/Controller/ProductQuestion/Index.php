<?php
declare(strict_types=1);

namespace Mykhailok\AskAboutThisProduct\Controller\ProductQuestion;

class Index implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    public const ACTION_ROUTE = 'product-question/productQuestion/index';

    private \Magento\Framework\Data\Form\FormKey\Validator $validator;
    private \Magento\Framework\Message\ManagerInterface $messageManager;
    private \Magento\Framework\App\RequestInterface $request;
    private \Magento\Framework\Controller\ResultFactory $resultFactory;
    private \Mykhailok\AskAboutThisProduct\Model\EmailSender $emailSender;

    public function __construct(
        \Magento\Framework\Data\Form\FormKey\Validator $validator,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Mykhailok\AskAboutThisProduct\Model\EmailSender $emailSender
    ) {
        $this->validator = $validator;
        $this->messageManager = $messageManager;
        $this->request = $request;
        $this->resultFactory = $resultFactory;
        $this->emailSender = $emailSender;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        if (!$this->validator->validate($this->request)) {
            $this->messageManager->addErrorMessage(__('Something went wrong. Please, fill the form again.'));
        }

        try {
            $requestParam = $this->request->getParams();
            $this->emailSender->send($requestParam);
            $this->messageManager->addSuccessMessage(
                __('Your question sent. The manager provides an answer as soon as possible.')
            );
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e);
        }

        $result = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_RAW);
        return $result->setContents('Email sent');
    }
}
