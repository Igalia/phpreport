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


/** File for CustomProjectVO
 *
 *  This file just contains {@link CustomProjectVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/vo/ProjectVO.php');

/** VO for Custom Projects
 *
 *  This class just stores Project detailed and derived data.
 *
 *  @property int $id database internal identifier.
 *  @property boolean $activation says if the Project is active or not.
 *  @property double $invoice invoice of this Project.
 *  @property double $estHours number of working hours we have estimated this Project will require.
 *  @property DateTime $init beginning date of this Project.
 *  @property DateTime $_end end date (included) of this Project.
 *  @property int $areaId database internal identifier of the associated Area.
 *  @property string $description description of this Project.
 *  @property double $movedHours hours this Project has moved from the estimation.
 *  @property double $workedHours hours of work spent on this Project.
 *  @property double $totalCost total cost of the hours spent by employees on this Project.
 *  @property string $type type of this Project.
 *  @property string $schedType type of scheduling this Project has.
 */
class CustomProjectVO extends ProjectVO {

    /**#@+
     *  @ignore
     */
    protected $workedHours = NULL;
    protected $totalCost = NULL;

    public function setTotalCost($totalCost) {
        if (is_null($totalCost))
            $this->totalCost = $totalCost;
        else
            $this->totalCost = (double) $totalCost;
    }

    public function getTotalCost() {
        return $this->totalCost;
    }

    public function setWorkedHours($workedHours) {
        if (is_null($workedHours))
            $this->workedHours = $workedHours;
        else
            $this->workedHours = (double) $workedHours;
    }

    public function getWorkedHours() {
        return $this->workedHours;
    }

    /**#@-*/

}
