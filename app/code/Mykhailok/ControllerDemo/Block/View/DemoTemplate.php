<?php
declare(strict_types=1);

namespace Mykhailok\ControllerDemo\Block\View;

class DemoTemplate extends \Magento\Framework\View\Element\Template
{
    /**
     * @param array $params
     * @return bool
     */
    public function hasRequestParams(array $params): bool
    {
        $request = $this->getRequest();
        $exist = true;

        foreach ($params as $paramName) {
            if ($request->getParam($paramName) === null) {
                $exist = false;
                break;
            }
        }

        return $exist;
    }
}
