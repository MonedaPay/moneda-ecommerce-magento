<?php
/**
 * Created by Qoliber
 *
 * @category    Ari10
 * @package     Ari10_MonedaPay
 * @author      qoliber <info@qoliber.com>
 */

declare(strict_types = 1);

namespace Ari10\MonedaPay\Model;

readonly class Encryption extends \Ari10\MonedaPayLib\Service\Encryption
{
    /**
     * @param \Ari10\MonedaPay\Model\Config $configuration Module configuration service
     */
    public function __construct(
        private Config $configuration
    ) {
        parent::__construct($this->configuration);
    }

    /**
     * Generate hash
     *
     * @throws \Ari10\MonedaPayLib\Exception\ConfigurationException|\Magento\Framework\Exception\NoSuchEntityException
     */
    public function generate(string $message): string
    {
        $hmacReplacement = $this->configuration->getHmacReplacement();

        return $hmacReplacement ?? parent::generate($message);
    }
}
