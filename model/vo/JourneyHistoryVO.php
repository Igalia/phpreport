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


/** File for JourneyHistoryVO
 *
 *  This file just contains {@link JourneyHistoryVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/vo/BaseHistoryVO.php');

/** VO for Journey Histories
 *
 *  This class just stores Journey History data.
 *
 *  @property int $id database internal identifier.
 *  @property int $userId database internal identifier of the associated User.
 *  @property DateTime $initDate beginning date of the history interval.
 *  @property DateTime $endDate end date (included) of the history interval.
 *  @property double $journey journey of the User.
 */
class JourneyHistoryVO extends BaseHistoryVO
{

    /**#@+
     *  @ignore
     */
    protected $journey = NULL;

    public function setJourney($journey) {
        if (is_null($journey))
        $this->journey = $journey;
    else
            $this->journey = (double) $journey;
    }

    public function getJourney() {
        return $this->journey;
    }

    /**#@-*/

}
