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


/** File for ExtraHourVO
 *
 *  This file just contains {@link ExtraHourVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

/** VO for Extra Hours
 *
 *  This class just stores Extra Hour data.
 *
 *  @property int $id database internal identifier.
 *  @property int $userId database internal identifier of the associated User.
 *  @property DateTime $date date of the Extra Hour.
 *  @property double $hours number of extra hours.
 */
class ExtraHourVO {
    protected $id = NULL;
    protected $userId = NULL;
    protected $date = NULL;
    protected $hours = NULL;

    public function setId($id) {
        if (is_null($id))
        $this->id = $id;
    else
            $this->id = (int) $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setUserId($userId) {
        if (is_null($userId))
        $this->userId = $userId;
    else
            $this->userId = (int) $userId;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function setDate(DateTime $date = NULL) {
        $this->date = $date;
    }

    public function getDate() {
        return $this->date;
    }

    public function setHours($hours) {
        if (is_null($hours))
        $this->hours = $hours;
    else
            $this->hours = (double) $hours;
    }

    public function getHours() {
        return $this->hours;
    }
}
