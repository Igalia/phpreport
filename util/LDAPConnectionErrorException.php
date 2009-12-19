<?php

/** File for LDAPConnectionErrorException
 *
 *  This file just contains {@link LDAPConnectionErrorException}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage Exception
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
include_once('phpreport/util/ConnectionErrorException.php');

/** Exception for LDAP connection errors
 *
 *  This is the exception thrown when an error happens while creating a new connection to LDAP.
 *
 * @package PhpReport
 * @subpackage Exception
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class LDAPConnectionErrorException extends ConnectionErrorException {

    /**#@+
     *  @ignore
     */

  public function __construct($message, $code = 0) {

    parent::__construct($message, $code);

    $this->message = "A connection to the LDAP with the following parameters could not be established:\n\t". $message;
  }

    /**#@-*/

}
