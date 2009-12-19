<?php

/** File for SQLIncorrectTypeException
 *
 *  This file just contains {@link SQLIncorrectTypeException}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage Exception
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/util/IncorrectTypeException.php');

/** Exception for SQL query incorrect types
 *
 *  This is the exception thrown when an invalid value is passed to a SQL query.
 *
 * @package PhpReport
 * @subpackage Exception
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

class SQLIncorrectTypeException extends IncorrectTypeException {

    /**#@+
     *  @ignore
     */

  public function __construct($message, $code = 0) {

    parent::__construct($message, $code);

    $this->message = "The value " . $message . " is not valid";
  }

    /**#@-*/

}
