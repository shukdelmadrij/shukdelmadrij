<?php

/**
 * @package     Extly.Library
 * @subpackage  lib_extly - Extly Framework
 *
 * @author      Prieco S.A. <support@extly.com>
 * @copyright   Copyright (C) 2007 - 2014 Prieco, S.A. All rights reserved.
 * @license     http://http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        http://www.extly.com http://support.extly.com
 */

namespace Cron;

// No direct access
defined('_JEXEC') or die('Restricted access');

use DateTime;

/**
 * Year field.  Allows: * , / -
 *
 * @author Michael Dowling <mtdowling@gmail.com>
 */
class YearField extends AbstractField
{
    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy(DateTime $date, $value)
    {
        return $this->isSatisfied($date->format('Y'), $value);
    }

    /**
     * {@inheritdoc}
     */
    public function increment(DateTime $date, $invert = false)
    {
        if ($invert) {
            $date->modify('-1 year');
            $date->setDate($date->format('Y'), 12, 31);
            $date->setTime(23, 59, 0);
        } else {
            $date->modify('+1 year');
            $date->setDate($date->format('Y'), 1, 1);
            $date->setTime(0, 0, 0);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value)
    {
        return (bool) preg_match('/[\*,\/\-0-9]+/', $value);
    }
}
