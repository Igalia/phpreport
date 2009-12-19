<?php

/** File for OperationErrorException
 *
 *  This file just contains {@link OperationErrorException}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage Exception
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

/** Exception for operation errors
 *
 *  This is the exception thrown when a functions is called and it finds some kind of error.<br/>This class extends {@link http://us2.php.net/manual/en/class.exception.php Exception}.
 *
 * @package PhpReport
 * @subpackage Exception
 * @author Jorge López Fernández <jlopez@igalia.com>
 */


class OperationErrorException extends Exception {

    /**#@+
     *  @ignore
     */

  public function __construct($message, $code = 0) {

    parent::__construct($message, $code);

  }

    /**#@-*/

}
