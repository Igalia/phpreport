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


/** File for DBConnectionErrorException
 *
 *  This file just contains {@link DBConnectionErrorException}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage Exception
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
include_once(PHPREPORT_ROOT . '/util/ConnectionErrorException.php');

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
