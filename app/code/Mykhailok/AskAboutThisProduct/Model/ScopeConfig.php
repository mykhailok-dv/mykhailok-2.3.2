<?php
declare(strict_types=1);

namespace Mykhailok\AskAboutThisProduct\Model;

class ScopeConfig
{
    public const XML_PATH_PRODUCT_SUPPORT_RECIPIENT_EMAIL = 'trans_email/product_manager_support/recipient_email';
    public const XML_PATH_PRODUCT_SUPPORT_RECIPIENT_NAME = 'trans_email/product_manager_support/recipient_name';
    public const XML_PATH_PRODUCT_SUPPORT_FORM_IS_VISIBLE = 'catalog/review/my_question_is_visible';

    private \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    /**
     * ScopeConfig constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return string|null
     */
    public function getRecipientEmail(): ?string
    {
        return ($value = $this->scopeConfig->getValue(self::XML_PATH_PRODUCT_SUPPORT_RECIPIENT_EMAIL))
            ? (string)$value
            : null;
    }

    /**
     * @return string|null
     */
    public function getRecipientName(): ?string
    {
        return ($value = $this->scopeConfig->getValue(self::XML_PATH_PRODUCT_SUPPORT_RECIPIENT_NAME))
            ? (string)$value
            : null;
    }

    /**
     * @return bool
     */
    public function isVisible(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_PRODUCT_SUPPORT_FORM_IS_VISIBLE);
    }
}
