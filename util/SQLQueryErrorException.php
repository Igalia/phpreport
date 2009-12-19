<?php

/** File for SQLQueryErrorException
 *
 *  This file just contains {@link SQLQueryErrorException}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage Exception
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/util/OperationErrorException.php');

/** Exception for SQL query errors
 *
 *  This is the exception thrown when a functions is called and it finds some kind of error while executing a SQL query.
 *
 * @package PhpReport
 * @subpackage Exception
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

class SQLQueryErrorException extends OperationErrorException {

    /**#@+
     *  @ignore
     */

  public function __construct($message, $code = 0) {

    parent::__construct($message, $code);

    $this->message =  $message;
  }

    /**#@-*/

}
