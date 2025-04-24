<?php
/**
 * Created by Qoliber
 *
 * @category    Ari10
 * @package     Ari10_MonedaPay
 * @author      qoliber <info@qoliber.com>
 */

declare(strict_types = 1);

namespace Ari10\MonedaPay\Model\Methods;

use Magento\Payment\Model\Method\AbstractMethod;

class MonedaPay extends AbstractMethod
{
    /** @var string */
    public const CODE = 'moneda_pay';

    /** @var string */
    protected $_code = self::CODE;

    /**
     * is Available
     *
     * @param \Magento\Quote\Api\Data\CartInterface|null $quote
     * @return bool
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null): bool
    {
        return parent::isAvailable($quote);
    }

    /**
     * Is Active
     *
     * @param $storeId
     * @return bool
     */
    public function isActive($storeId = null): bool
    {
        return (bool)(int)$this->getConfigData('active', $storeId);
    }

    /**
     * Get Code
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->_code;
    }
}
