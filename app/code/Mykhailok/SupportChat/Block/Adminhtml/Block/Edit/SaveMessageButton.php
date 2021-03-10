<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Block\Adminhtml\Block\Edit;

class SaveMessageButton implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    public function getButtonData(): array
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'message_form.message_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    [
                                        'back' => 'continue'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'class_name' => \Magento\Ui\Component\Control\Container::DEFAULT_CONTROL,
        ];
    }
}
