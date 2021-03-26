<?php
declare(strict_types=1);

namespace Mykhailok\AskAboutThisProduct\Model;

class EmailSender
{
    private \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder;
    private \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation;
    private \Magento\Store\Model\StoreManagerInterface $storeManager;
    private \Magento\Catalog\Api\ProductRepositoryInterface $productRepository;
    private \Psr\Log\LoggerInterface $logger;
    private \Mykhailok\AskAboutThisProduct\Model\ScopeConfig $emailScopeConfig;

    public function __construct(
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Psr\Log\LoggerInterface $logger,
        \Mykhailok\AskAboutThisProduct\Model\ScopeConfig $emailScopeConfig
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
        $this->emailScopeConfig = $emailScopeConfig;
    }

    /**
     * @param array $data
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Exception
     */
    public function send(array $data): void
    {
        $this->inlineTranslation->suspend();

        try {
            $product = $this->productRepository->getById($data['product_id']);
            $data['productSku'] = $product->getSku();
            $data['productName'] = $product->getName();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            $this->logger->info($exception->getMessage(), $exception->getTrace());
            throw new \Exception($exception->getMessage());
        }

        try {
            /** @noinspection PhpUnhandledExceptionInspection */
            $transport = $this->transportBuilder
                ->setTemplateIdentifier('my_ask_about_this_product_product_question')
                ->setTemplateOptions([
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $storeId = $this->storeManager->getStore()->getId(),
                ])
                ->setTemplateVars($data)
                ->setFromByScope('support')
                ->addTo($this->emailScopeConfig->getRecipientEmail(), $this->emailScopeConfig->getRecipientName())
                ->getTransport();

            $transport->sendMessage();
        } catch (\Magento\Framework\Exception\MailException $exception) {
            $this->logger->info($exception->getMessage(), $exception->getTrace());
        } finally {
            $this->inlineTranslation->resume();
        }
    }
}
