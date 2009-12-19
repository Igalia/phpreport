<?php

/** File for LDAPInvalidOperationException
 *
 *  This file just contains {@link LDAPInvalidOperationException}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage Exception
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
include_once('phpreport/util/InvalidOperationException.php');

/** Exception for LDAP invalid operations
 *
 *  This is the exception thrown when a function is called and it's not valid for LDAP implementation.
 *
 * @package PhpReport
 * @subpackage Exception
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class LDAPInvalidOperationException extends InvalidOperationException {

    /**#@+
     *  @ignore
     */

  public function __construct($message, $code = 0) {

    parent::__construct($message, $code);

    $this->message = "The following operation is not valid when working over LDAP:\n\t". $message;
  }

    /**#@-*/

}
