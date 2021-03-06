<?php
/*
 * Copyright (C) 2005-2008 The Propel authors
 * Copyright (C) 2009 Igalia, S.L. <info@igalia.com>
 *
 * This file is part of PhpReport.
 * Based on code from the Propel project <http://propel.phpdb.org>
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

/*
 *  $Id: DBAdapter.php 1011 2008-03-20 11:36:27Z hans $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://propel.phpdb.org>.
 */

/**
 * DBAdapter</code> defines the interface for a Propel database adapter.
 *
 * <p>Support for new databases is added by subclassing
 * <code>DBAdapter</code> and implementing its abstract interface, and by
 * registering the new database adapter and corresponding Creole
 * driver in the private adapters map (array) in this class.</p>
 *
 * <p>The Propel database adapters exist to present a uniform
 * interface to database access across all available databases.  Once
 * the necessary adapters have been written and configured,
 * transparent swapping of databases is theoretically supported with
 * <i>zero code change</i> and minimal configuration file
 * modifications.</p>
 *
 * @author     Hans Lellelid <hans@xmpl.org> (Propel)
 * @author     Jon S. Stevens <jon@latchkey.com> (Torque)
 * @author     Brett McLaughlin <bmclaugh@algx.net> (Torque)
 * @author     Daniel Rall <dlr@finemaltcoding.com> (Torque)
 * @version    $Revision: 1011 $
 * @package    propel.adapter
 */
abstract class DBAdapter {

    const ID_METHOD_NONE = 0;
    const ID_METHOD_AUTOINCREMENT = 1;
    const ID_METHOD_SEQUENCE = 2;



    /**
     * Sets the character encoding using SQL standard SET NAMES statement.
     *
     * This method is invoked from the default initConnection() method and must
     * be overridden for an RDMBS which does _not_ support this SQL standard.
     *
     * @param      PDO   A PDO connection instance.
     * @param      string The charset encoding.
     * @see        initConnection()
     */
    public static function setCharset(PDO $con, $charset)
    {
        $con->exec("SET NAMES '" . $charset . "'");
    }

    /**
     * Returns the character used to indicate the beginning and end of
     * a piece of text used in a SQL statement (generally a single
     * quote).
     *
     * @return     string The text delimeter.
     */
    public static function getStringDelimiter()
    {
        return '\'';
    }

    /**
     * Checks if data is not NULL, so it can be inserted directly in database.
     * If it's NULL, then returns 'NULL', otherwise the same data.
     * @param      any type The value to check.
     * @return     any type The same value, or just 'NULL'.
     */
    public static function checkNull($data) {

        if (is_null($data))
            return "NULL";
        else
            return $data;

    }

    /**
     * Checks if bool is true or not, and returns the proper string.
     * @param      bool $bool The bool to check.
     * @return     bool The value of the boolean ("TRUE" or "FALSE").
     */
    public static function boolToString($bool) {

        if ($bool)
            return "TRUE";
        else return "FALSE";

    }


    /**
     * Quotes database objec identifiers (table names, col names, sequences, etc.).
     * @param      string $text The identifier to quote.
     * @return     string The quoted identifier.
     */
    public static function quoteIdentifier($text)
    {
        return '"' . $text . '"';
    }

    /**
     * Quotes a database table which could have space seperating it from an alias, both should be identified seperately
     * @param      string $table The table name to quo
     * @return     string The quoted table name
     **/
    public static function quoteIdentifierTable($table) {
        return implode(" ", array_map(array($this, "quoteIdentifier"), explode(" ", $table) ) );
    }

    /**
     * Returns the native ID method for this RDBMS.
     * @return     int one of DBAdapter:ID_METHOD_SEQUENCE, DBAdapter::ID_METHOD_AUTOINCREMENT.
     */
    protected static function getIdMethod()
    {
        return DBAdapter::ID_METHOD_AUTOINCREMENT;
    }

    /**
     * Whether this adapter uses an ID generation system that requires getting ID _before_ performing INSERT.
     * @return     boolean
     */
    public static function isGetIdBeforeInsert()
    {
        return ($this->getIdMethod() === DBAdapter::ID_METHOD_SEQUENCE);
    }

    /**
     * Whether this adapter uses an ID generation system that requires getting ID _before_ performing INSERT.
     * @return     boolean
     */
    public static function isGetIdAfterInsert()
    {
        return ($this->getIdMethod() === DBAdapter::ID_METHOD_AUTOINCREMENT);
    }

    /**
     * Gets the generated ID (either last ID for autoincrement or next sequence ID).
     * @return     mixed
     */
    public static function getId($con, $name)
    {
        return $con->lastInsertId($name);
    }

    /**
     * Returns timestamp formatter string for use in date() static function.
     * @return     string
     */
    public static function getTimestampFormatter()
    {
        return "Y-m-d H:i:s";
    }

    /**
     * Returns date formatter string for use in date() static function.
     * @return     string
     */
    public static function getDateFormatter()
    {
        return "Y-m-d";
    }

    /**
     * Returns date formatted as a string with the proper format string.
     * @return     string with the date formatted
     */
    public static function formatDate(DateTime $date = NULL)
    {
        if (is_null($date))
            return "NULL";
        else return "'" . date_format($date, DBPostgres::getDateFormatter()) . "'";
    }

    /**
     * Returns time formatter string for use in date() static function.
     * @return     string
     */
    public static function getTimeFormatter()
    {
        return "H:i:s";
    }

    /**
     * Should Column-Names get identifiers for inserts or updates.
     * By default false is returned -> backwards compability.
     *
     * it`s a workaround...!!!
     *
     * @todo       should be abstract
     * @return     boolean
     * @deprecated
     */
    public static function useQuoteIdentifier()
    {
        return false;
    }

}
