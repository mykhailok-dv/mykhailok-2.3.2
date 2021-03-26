<?php
declare(strict_types=1);

namespace Mykhailok\AskAboutThisProduct\Block\Product\View\Question;

class QuestionLayoutProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    private \Magento\Framework\Registry $registry;
    private \Magento\Framework\UrlInterface $url;
    private \Mykhailok\AskAboutThisProduct\Model\ScopeConfig $questionScopeConfig;

    /**
     * QuestionLayoutProcessor constructor.
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     * @param \Mykhailok\AskAboutThisProduct\Model\ScopeConfig $questionScopeConfig
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $url,
        \Mykhailok\AskAboutThisProduct\Model\ScopeConfig $questionScopeConfig
    ) {
        $this->registry = $registry;
        $this->url = $url;
        $this->questionScopeConfig = $questionScopeConfig;
    }

    public function process($jsLayout): array
    {
        $questionFormComponentArgs = [
            'qProductId' => $this->registry->registry('current_product')->getId(),
            'isVisible' => (bool)$this->questionScopeConfig->isVisible(),
            'action' => $this->url->getUrl(
                \Mykhailok\AskAboutThisProduct\Controller\ProductQuestion\Index::ACTION_ROUTE
            ),
        ];

        return array_merge_recursive($jsLayout, [
            'components' => [
                'mykhailok-question-form' => $questionFormComponentArgs,
            ],
        ]);
    }
}
