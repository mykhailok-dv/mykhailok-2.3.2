<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Service;

class RequestValidate
{
    private \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator;
    private \Magento\Framework\App\RequestInterface $request;

    /**
     * RequestValidate constructor.
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->formKeyValidator = $formKeyValidator;
        $this->request = $request;
    }

    /**
     * @param bool $checkFormKey
     * @param bool $checkAjax
     * @param bool $checkSecure
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate($checkFormKey = true, $checkAjax = true, $checkSecure = true): void
    {
        if ($checkSecure && !$this->request->isSecure()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('You haven\'t secure connection. Please, reload page.'),
                null,
                400
            );
        }
        if ($checkFormKey && !$this->formKeyValidator->validate($this->request)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('You have wrong form. Please, reload page.'),
                null,
                400
            );
        }
        if ($checkAjax && !$this->request->isAjax()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'You aren\'t using ajax connection.',
                    null,
                    400
                )
            );
        }
    }
}
