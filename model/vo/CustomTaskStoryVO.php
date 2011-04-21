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


/** File for CustomTaskStoryVO
 *
 *  This file just contains {@link CustomTaskStoryVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/vo/BaseTaskStoryVO.php');

/** Custom VO for Task Stories
 *
 *  This class just stores Task Story and additional data.
 *
 *  @property int $id database internal identifier.
 *  @property int $risk risk level.
 *  @property string $name name of this Task Story.
 *  @property DateTime $init date this Iteration began at.
 *  @property DateTime $end date this Iteration ended at.
 *  @property DateTime $estEnd estimated end date of this Task Story.
 *  @property double $estHours estimated number of hours this Task Story will last.
 *  @property double $spent working hours spent in this Task Story.
 *  @property double $toDo pending working hours in this Task Story.
 *  @property int $storyId database internal identifier of the associated Story.
 *  @property TaskSectionVO $taskSection Task Section that contains this working.
 *  @property UserVO $developer developer of this Task Story.
 *  @property UserVO $reviewer reviewer of this Task Story.
 */
class CustomTaskStoryVO extends BaseTaskStoryVO {

    /**#@+
     *  @ignore
     */
    protected $spent = NULL;
    protected $taskSection = NULL;
    protected $developer = NULL;
    protected $reviewer = NULL;

    public function setSpent($spent) {
    if (is_null($spent))
        $this->spent = $spent;
    else
            $this->spent = (double) $spent;
    }

    public function getSpent() {
        return $this->spent;
    }

    public function setTaskSection(TaskSectionVO $taskSection) {
        $this->taskSection = $taskSection;
    }

    public function getTaskSection() {
        return $this->taskSection;
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
