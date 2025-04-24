<?php
/**
 * Created by Qoliber
 *
 * @category    Ari10
 * @package     Ari10_MonedaPay
 * @author      qoliber <info@qoliber.com>
 */

declare(strict_types = 1);

namespace Ari10\MonedaPay\Controller\Payment;

use Ari10\MonedaPay\Logger\Logger;
use Exception;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Model\OrderRepository;

class CancelOrder implements HttpGetActionInterface, CsrfAwareActionInterface
{
    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Ari10\MonedaPay\Logger\Logger $logger
     */
    public function __construct(
        private CheckoutSession $checkoutSession,
        private OrderRepository $orderRepository,
        private ResultFactory   $resultFactory,
        private Logger          $logger
    ) {
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute(): ResultInterface
    {
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->checkoutSession->getLastRealOrderId()) {
            return $redirect->setPath('checkout/cart');
        }
        $order = $this->checkoutSession->getLastRealOrder();
        $this->checkoutSession->restoreQuote();

        try {
            $this->orderRepository->delete($order);
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage(), $exception->getTrace());
        }

        return $redirect->setPath('checkout/cart');
    }

    /**
     * Create Csrf Validation
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\Request\InvalidRequestException|null
     */
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException {
        return null;
    }

    /**
     * Validate for Csrf
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
