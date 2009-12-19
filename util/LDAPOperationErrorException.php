<?php

/** File for LDAPOperationErrorException
 *
 *  This file just contains {@link LDAPOperationErrorException}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage Exception
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/util/OperationErrorException.php');

/** Exception for LDAP operation errors
 *
 *  This is the exception thrown when a functions is called and it finds some kind of error while working with LDAP.
 *
 * @package PhpReport
 * @subpackage Exception
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

class LDAPOperationErrorException extends OperationErrorException {

    /**#@+
     *  @ignore
     */

  public function __construct($message, $code = 0) {

    parent::__construct($message, $code);

    $this->message = "The operation " . $message . " could not be executed";
  }

    /**#@-*/

}
