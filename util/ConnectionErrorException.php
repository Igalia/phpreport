<?php

/** File for ConnectionErrorException
 *
 *  This file just contains {@link ConnectionErrorException}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage Exception
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

/** Exception for connection errors
 *
 *  This is the exception thrown when an error happens while creating a new connection.<br/>This class extends {@link http://us2.php.net/manual/en/class.exception.php Exception}.
 *
 * @package PhpReport
 * @subpackage Exception
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class ConnectionErrorException extends Exception {

    /**#@+
     *  @ignore
     */

  public function __construct($message, $code = 0) {

    parent::__construct($message, $code);

  }

    /**#@-*/

}
