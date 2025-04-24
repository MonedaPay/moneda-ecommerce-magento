<?php
/**
 * Created by Qoliber
 *
 * @category    Ari10
 * @package     Ari10_MonedaPay
 * @author      qoliber <info@qoliber.com>
 */

declare(strict_types = 1);

namespace Ari10\MonedaPay\Api\Response;

interface PlaceOrderInterface
{
    /**
     * Get Payment Url
     *
     * @return string
     */
    public function getPaymentUrl(): string;

    /**
     * Set Payment Url
     *
     * @param string $paymentUrl
     * @return void
     */
    public function setPaymentUrl(string $paymentUrl): void;

    /**
     * Get Status
     *
     * @return bool
     */
    public function getStatus(): bool;

    /**
     * Set Status
     *
     * @param bool $status
     * @return void
     */
    public function setStatus(bool $status): void;
}
