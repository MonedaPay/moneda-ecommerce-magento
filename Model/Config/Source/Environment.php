<?php
/**
 * Created by Qoliber
 *
 * @category    MonedaPay
 * @package     MonedaPay_MonedaPay
 * @author      qoliber <info@qoliber.com>
 */

declare(strict_types = 1);

namespace MonedaPay\MonedaPay\Model\Config\Source;

use MonedaPay\MonedaPayLib\Enum\Environment as Source;
use Magento\Framework\Data\OptionSourceInterface;

class Environment implements OptionSourceInterface
{
    /**
     * Get environment options as associative array
     *
     * @return mixed[] Array of environment options [key => value]
     */
    public function toArray(): array
    {
        return Source::toOptionArray();
    }

    /**
     * Get environment options as array for form field
     *
     * @return mixed[] Array of environment options [['value' => value, 'label' => label]]
     */
    public function toOptionArray(): array
    {
        $array = Source::toOptionArray();

        return array_map(
            fn ($key, $value) => ['value' => $value, 'label' => $key],
            array_keys($array),
            $array
        );
    }
}
