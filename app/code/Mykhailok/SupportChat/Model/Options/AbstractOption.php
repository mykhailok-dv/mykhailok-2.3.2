<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Model\Options;

class AbstractOption implements \Magento\Framework\Data\OptionSourceInterface
{
    /** @var array $options */
    private array $options;

    /**
     * AbstractOption constructor.
     * @param array $options
     */
    public function __construct(
        array $options
    ) {
        $this->options = [
            'options' => $options,
        ];
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        if (isset($this->options['options'])) {
            $options = [];
            foreach ($this->options['options'] as $optionValue => $optionLabel) {
                $options[] = [
                    'value' => $optionValue,
                    'label' => $optionLabel,
                ];
            }
            $this->options = $options;
        }

        return $this->options;
    }
}
