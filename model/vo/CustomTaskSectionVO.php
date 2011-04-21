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


/** File for CustomTaskSectionVO
 *
 *  This file just contains {@link CustomTaskSectionVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/vo/BaseTaskSectionVO.php');

/** Custom VO for Task Sections
 *
 *  This class just stores Task Section and additional data.
 *
 *  @property int $id database internal identifier.
 *  @property int $risk risk level.
 *  @property string $name name of this Task Section.
 *  @property double $estHours estimated number of hours this Task Section will last.
 *  @property double $spent working hours spent in this Task Section.
 *  @property double $toDo pending working hours in this Task Section.
 *  @property int $sectionId database internal identifier of the associated Section.
 *  @property UserVO $developer developer of this Task Section.
 *  @property UserVO $reviewer reviewer of this Task Section.
 */
class CustomTaskSectionVO extends BaseTaskSectionVO {

    /**#@+
     *  @ignore
     */
    protected $spent = NULL;
    protected $toDo = NULL;
    protected $developer = NULL;
    protected $reviewer = NULL;

    public function setToDo($toDo) {
    if (is_null($toDo))
        $this->toDo = $toDo;
    else
            $this->toDo = (double) $toDo;
    }

    public function getToDo() {
        return $this->toDo;
    }

    public function setSpent($spent) {
    if (is_null($spent))
        $this->spent = $spent;
    else
            $this->spent = (double) $spent;
    }

    public function getSpent() {
        return $this->spent;
    }

    public function setDeveloper(UserVO $developer) {
        $this->developer = $developer;
    }

    public function getDeveloper() {
        return $this->developer;
    }

    public function setReviewer(UserVO $reviewer) {
        $this->reviewer = $reviewer;
    }

    public function getReviewer() {
        return $this->reviewer;
    }

    /**#@-*/

}
