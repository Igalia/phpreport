<?php

/** File for UnknownParameterException
 *
 *  This file just contains {@link UnknownParameterException}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage Exception
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

/** Exception for unknown parameter exceptions
 *
 *  This is the exception thrown by {@link ConfigurationParametersManager} when we request the value of a parameter that doesn't exist on {@link config.php}.
 *  <br/>This class extends {@link http://us2.php.net/manual/en/class.exception.php Exception}.
 *
 * @package PhpReport
 * @subpackage Exception
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

class UnknownParameterException extends Exception {

    /**#@+
     *  @ignore
     */

  public function __construct($message, $code = 0) {

    parent::__construct($message, $code);

    $this->message = "The parameter " . $message . " doesn't exist";
  }

    /**#@-*/

}
