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

readonly class Encryption extends \MonedaPay\MonedaPayLib\Service\Encryption
{
    /**
     * @param \MonedaPay\MonedaPay\Model\Config $configuration Module configuration service
     */
    public function __construct(
        private Config $configuration
    ) {
        parent::__construct($this->configuration);
    }

    /**
     * Generate hash
     *
     * @throws \MonedaPay\MonedaPayLib\Exception\ConfigurationException|\Magento\Framework\Exception\NoSuchEntityException
     */
    public function generate(string $message): string
    {
        $hmacReplacement = $this->configuration->getHmacReplacement();

        return $hmacReplacement ?? parent::generate($message);
    }
}
