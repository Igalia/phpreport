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


include_once(PHPREPORT_ROOT . '/util/DBAdapter.php');

/*
 *  $Id: DBPostgres.php 1011 2008-03-20 11:36:27Z hans $
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
 * This is used to connect to PostgresQL databases.
 *
 * <a href="http://www.pgsql.org">http://www.pgsql.org</a>
 *
 * @author     Hans Lellelid <hans@xmpl.org> (Propel)
 * @author     Hakan Tandogan <hakan42@gmx.de> (Torque)
 * @version    $Revision: 1011 $
 * @package    propel.adapter
 */
class DBPostgres extends DBAdapter {

    /**
     * This method is used to ignore case.
     *
     * @param      string $in The string to transform to upper case.
     * @return     string The upper case string.
     */
    public static function toUpperCase($in)
    {
        return "UPPER(" . $in . ")";
    }

    /**
     * This method is used to ignore case.
     *
     * @param      in The string whose case to ignore.
     * @return     The string in a case that can be ignored.
     */
    public static function ignoreCase($in)
    {
        return "UPPER(" . $in . ")";
    }

    /**
     * This method is used to ignore case in an ORDER BY clause.
     * Usually it is the same as ignoreCase, but some databases
     * (Interbase for example) does not use the same SQL in ORDER BY
     * and other clauses.
     *
     * @param      string $in The string whose case to ignore.
     * @return     string The string in a case that can be ignored.
     */
    public static function ignoreCaseInOrderBy($in)
    {
        return $this->ignoreCase($in);
    }

    /**
     * Returns SQL which concatenates the second string to the first.
     *
     * @param      string String to concatenate.
     * @param      string String to append.
     * @return     string
     */
    public static function concatString($s1, $s2)
    {
        return "($s1 || $s2)";
    }

    /**
     * Returns SQL which extracts a substring.
     *
     * @param      string String to extract from.
     * @param      int Offset to start from.
     * @param      int Number of characters to extract.
     * @return     string
     */
    public static function subString($s, $pos, $len)
    {
        return "substring($s from $pos" . ($len > -1 ? "for $len" : "") . ")";
    }

    /**
     * Returns SQL which calculates the length (in chars) of a string.
     *
     * @param      string String to calculate length of.
     * @return     string
     */
    public static function strLength($s)
    {
        return "char_length($s)";
    }

    /**
     * Returns string with special characters escaped.
     *
     * @param      string $str to escape.
     * @return     string $str escaped.
     */
    public static function escapeString($str)
    {
        return pg_escape_string($str);
    }

    /**
     * Checks if string is NULL, so it can be inserted in database without quotes.
     * In other case, it quotes it. It's also escaped.
     * @param      string $str The string to quote.
     * @return     string The quoted string, or just "NULL".
     */
    public static function checkStringNull($str) {

        if ($str == NULL)
            return "NULL";
        else return "'" . DBPostgres::escapeString($str) . "'";

    }

    /**
     * @see        DBAdapter::getIdMethod()
     */
    protected static function getIdMethod()
    {
        return DBAdapter::ID_METHOD_SEQUENCE;
    }

    /**
     * Gets ID for specified sequence name.
     */
    public static function getId($connect, $name)
    {
        $res = pg_query($connect, "SELECT CURRVAL('" . $name . "') AS seq" );
        $data = pg_fetch_assoc( $res );

            return $data['seq'];
    }

    /**
     * Returns timestamp formatter string for use in date() static function.
     * @return     string
     */
    public static function getTimestampFormatter()
    {
        return "Y-m-d H:i:s O";
    }

    /**
     * Returns timestamp formatter string for use in date() static function.
     * @return     string
     */
    public static function getTimeFormatter()
    {
        return "H:i:s O";
    }

    /**
     * @see        DBAdapter::applyLimit()
     */
    public static function applyLimit(&$sql, $offset, $limit)
    {
        if ( $limit > 0 ) {
            $sql .= " LIMIT ".$limit;
        }
        if ( $offset > 0 ) {
            $sql .= " OFFSET ".$offset;
        }
    }

    /**
     * @see        DBAdapter::random()
     */
    public static function random($seed=NULL)
    {
        return 'random()';
    }
}
