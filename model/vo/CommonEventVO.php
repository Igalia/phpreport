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


/** File for CommonEventVO
 *
 *  This file just contains {@link CommonEventVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

/** VO for Common Events
 *
 *  This class just stores Common Event data.
 *
 *  @property int $id database internal identifier.
 *  @property int $cityId database internal identifier of the associated City.
 *  @property DateTime $date date of the Common Event.
 */
class CommonEventVO {

    /**#@+
     *  @ignore
     */
    protected $id = NULL;
    protected $cityId = NULL;
    protected $date = NULL;

    public function setId($id) {
        if (is_null($id))
        $this->id = $id;
    else
            $this->id = (int) $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setCityId($cityId) {
        if (is_null($cityId))
        $this->cityId = $cityId;
    else
            $this->cityId = (int) $cityId;
    }

    public function getCityId() {
        return $this->cityId;
    }

    public function setDate(DateTime $date = NULL) {
        $this->date = $date;
    }

    public function getDate() {
        return $this->date;
    }

    /**#@-*/

}
