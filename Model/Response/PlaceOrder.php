<?php
/**
 * Created by Qoliber
 *
 * @category    MonedaPay
 * @package     MonedaPay_MonedaPay
 * @author      qoliber <info@qoliber.com>
 */

declare(strict_types = 1);

namespace MonedaPay\MonedaPay\Model\Response;

use MonedaPay\MonedaPay\Api\Response\PlaceOrderInterface as Response;

class PlaceOrder implements Response
{
    /** @var string */
    private string $paymentUrl = '';

    /** @var bool */
    private bool $status = false;

    /**
     * Get Payment Url
     * @return string
     */
    public function getPaymentUrl(): string
    {
        return $this->paymentUrl;
    }

    /**
     * Set Payment Url
     *
     * @param string $paymentUrl
     * @return void
     */
    public function setPaymentUrl(string $paymentUrl): void
    {
        $this->paymentUrl = $paymentUrl;
    }

    /**
     * Get Status
     *
     * @return bool
     */
    public function getStatus(): bool
    {
        return $this->status;
    }

    /**
     * Set Status
     *
     * @param bool $status
     * @return void
     */
    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }
}
