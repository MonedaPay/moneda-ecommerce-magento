<?php
/**
 * Created by Qoliber
 *
 * @category    MonedaPay
 * @package     MonedaPay_MonedaPay
 * @author      qoliber <info@qoliber.com>
 */

declare(strict_types = 1);

namespace MonedaPay\MonedaPay\Model;

use MonedaPay\MonedaPayLib\Enum\EcommerceType;
use MonedaPay\MonedaPayLib\Enum\Environment;
use MonedaPay\MonedaPayLib\Model\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config implements ConfigInterface
{
    /** @var string */
    const XML_PATH_MONEDA_PAY = 'payment/moneda_pay/';

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    /**
     * Get Merchant ID
     *
     * @param $storeId
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMerchantId($storeId = null): ?string
    {
        return $this->getConfigValue('merchant_id', $storeId);
    }

    /**
     * Get Config Value
     *
     * @param $field
     * @param $storeId
     * @param string $pathPrefix
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfigValue(
        $field,
        $storeId = null,
        string $pathPrefix = self::XML_PATH_MONEDA_PAY
    ) {
        if (null === $storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        return $this->scopeConfig->getValue(
            $pathPrefix . $field,
            ScopeInterface::SCOPE_STORES,
            $storeId
        );
    }

    /**
     * Get Shop ID
     *
     * @param $storeId
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getShopId($storeId = null): ?string
    {
        return $this->getConfigValue('shop_id', $storeId);
    }

    /**
     * Get Environment
     *
     * @param $storeId
     * @return \MonedaPay\MonedaPayLib\Enum\Environment|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getEnvironment($storeId = null): ?Environment
    {
        $env = $this->getConfigValue('environment', $storeId);

        return Environment::from($env);
    }

    /**
     * Get Order Status
     *
     * @param $storeId
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOrderStatus($storeId = null): mixed
    {
        return $this->getConfigValue('order_status', $storeId);
    }

    /**
     * Get Sort Order
     *
     * @param $storeId
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSortOrder($storeId = null): int
    {
        return (int)$this->getConfigValue('sort_order', $storeId);
    }

    /**
     * Is Debug Mode
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isDebugMode(): bool
    {
        return (bool)$this->getConfigValue('debug');
    }

    /**
     * Get Hmac Replacement
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getHmacReplacement(): ?string
    {
        if (!$this->isDebugMode()) {
            return null;
        }

        return $this->getConfigValue('hmac_replacement');
    }

    /**
     * Get Ecommerce Type
     *
     * @return \MonedaPay\MonedaPayLib\Enum\EcommerceType|null
     */
    public function getEcommerceType(): ?EcommerceType
    {
        return EcommerceType::MAGENTO;
    }

    /**
     * Get Api Secret
     *
     * @param $storeId
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getApiSecret($storeId = null): ?string
    {
        return $this->getConfigValue('private_key', $storeId);
    }

    /**
     * Get pi Key
     *
     * @return string|null
     */
    public function getApiKey(): ?string
    {
        return null;
    }

    /**
     * Get Secure Url of the store
     *
     * @param $storeId
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseEcommerceUrl($storeId = null): ?string
    {
        return $this->getConfigValue(
            'base_url',
            $storeId,
            'web/secure/'
        );
    }
}
