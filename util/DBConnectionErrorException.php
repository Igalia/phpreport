<?php

/** File for DBConnectionErrorException
 *
 *  This file just contains {@link DBConnectionErrorException}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage Exception
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
include_once('phpreport/util/ConnectionErrorException.php');

/** Exception for database connection errors
 *
 *  This is the exception thrown when an error happens while creating a new connection to database.
 *
 * @package PhpReport
 * @subpackage Exception
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class DBConnectionErrorException extends ConnectionErrorException {

    /**#@+
     *  @ignore
     */

  public function __construct($message, $code = 0) {

    parent::__construct($message, $code);

    $this->message = "A connection to the database with the following parameters could not be established:\n\t". $message;
  }

    /**#@-*/

}
