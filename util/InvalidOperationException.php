<?php

/** File for InvalidOperationException
 *
 *  This file just contains {@link InvalidOperationException}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage Exception
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

/** Exception for invalid operations
 *
 *  This is the exception thrown when a functions is called and it's not valid for a given implementation.<br/>This class extends {@link http://us2.php.net/manual/en/class.exception.php Exception}.
 *
 * @package PhpReport
 * @subpackage Exception
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class InvalidOperationException extends Exception {

    /**#@+
     *  @ignore
     */

  public function __construct($message, $code = 0) {

    parent::__construct($message, $code);

  }

    /**#@-*/

}
