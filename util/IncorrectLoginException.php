<?php

/** File for IncorrectLoginException
 *
 *  This file just contains {@link IncorrectLoginException}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage Exception
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

/** Exception for incorrect passwords
 *
 *  This is the exception thrown when a given password doesn't match the given login.<br/>This class extends {@link http://us2.php.net/manual/en/class.exception.php Exception}.
 *
 * @package PhpReport
 * @subpackage Exception
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class IncorrectLoginException extends Exception {

    /**#@+
     *  @ignore
     */

  public function __construct($message, $code = 0) {

    parent::__construct($message, $code);

    $this->message = "User and password " . $message . " don't match.";
  }

    /**#@-*/

}
