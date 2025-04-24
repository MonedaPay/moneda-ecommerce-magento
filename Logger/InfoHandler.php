<?php
/**
 * Created by Qoliber
 *
 * @category    Ari10
 * @package     Ari10_MonedaPay
 * @author      qoliber <info@qoliber.com>
 */

declare(strict_types = 1);

namespace Ari10\MonedaPay\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger as MonologLogger;

class InfoHandler extends Base
{
    /** @var int  */
    protected $loggerType = MonologLogger::INFO;

    /** @var string  */
    protected $fileName = '/var/log/ari10_moneda_pay_info.log';


    /**
     * @param array $record
     * @return bool
     */
    public function isHandling(array $record): bool
    {
        return $record['level'] == $this->level;
    }
}
