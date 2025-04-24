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

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Checkout\Model\Session\SuccessValidator;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;

class Status implements HttpGetActionInterface, CsrfAwareActionInterface
{
    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Magento\Checkout\Model\Session\SuccessValidator $validator
     */
    public function __construct(
        private CheckoutSession  $checkoutSession,
        private ResultFactory    $resultFactory,
        private PageFactory      $pageFactory,
        private SuccessValidator $validator
    ) {
    }

    public function execute()
    {
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if (!$this->validator->isValid()) {
            return $redirect->setPath('checkout/cart');
        }

        $orderId = $this->checkoutSession->getLastRealOrderId();
        $this->checkoutSession->clearQuote();

        /** @var \Magento\Framework\View\Result\Page $page */
        $page = $this->pageFactory->create();
        $page->getLayout()->getBlock('moneda_pay.payment.status')
                          ?->setRealOrderId($orderId);

        return $page;
    }

    /**
     * Create Validation Exception
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
     * Validate Csrf
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
