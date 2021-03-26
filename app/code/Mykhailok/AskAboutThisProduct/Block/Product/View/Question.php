<?php
declare(strict_types=1);

namespace Mykhailok\AskAboutThisProduct\Block\Product\View;

class Question extends \Magento\Framework\View\Element\Template
{
    private array $layoutProcessors;
    private string $processedJsLayout = '';
    private \Magento\Framework\Serialize\SerializerInterface $serializer;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        array $data = [],
        array $layoutProcessors = []
    ) {
        parent::__construct($context, $data);
        $this->layoutProcessors = $layoutProcessors;
        $this->serializer = $serializer;
    }

    /**
     * @inheritdoc
     * @noinspection PhpUndefinedVariableInspection
     */
    public function getJsLayout(): string
    {
        if (empty($this->processedJsLayout) && !empty($this->layoutProcessors)) {
            /** @var \Magento\Checkout\Block\Checkout\LayoutProcessorInterface $processor */
            foreach ($this->layoutProcessors as $processor) {
                $jsLayout = $processor->process($this->jsLayout);
            }

            $this->processedJsLayout = (string)$this->serializer->serialize($jsLayout);
        }

        return $this->processedJsLayout;
    }
}
