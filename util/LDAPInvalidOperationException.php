<?php
/*
 * Copyright (C) 2009 Igalia, S.L. <info@igalia.com>
 *
 * This file is part of PhpReport.
 *
 * PhpReport is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PhpReport is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhpReport.  If not, see <http://www.gnu.org/licenses/>.
 */


/** File for LDAPInvalidOperationException
 *
 *  This file just contains {@link LDAPInvalidOperationException}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage Exception
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
include_once(PHPREPORT_ROOT . '/util/InvalidOperationException.php');

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
