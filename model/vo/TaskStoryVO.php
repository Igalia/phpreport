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


/** File for TaskStoryVO
 *
 *  This file just contains {@link TaskStoryVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/vo/BaseTaskStoryVO.php');

/** VO for Task Stories
 *
 *  This class just stores Task Story data.
 *
 *  @property int $id database internal identifier.
 *  @property int $risk risk level.
 *  @property string $name name of this Task Story.
 *  @property DateTime $init date this Task began at.
 *  @property DateTime $end date this Task ended at.
 *  @property double $estHours estimated number of hours this Task Story will last.
 *  @property double $toDo pending working hours in this Task Story.
 *  @property DateTime $estEnd estimated end date of this Task Story.
 *  @property int $storyId database internal identifier of the associated Story.
 *  @property int $userId database internal identifier of the associated User.
 *  @property int $taskSectionId database internal identifier of the associated TaskSection.
 */
class TaskStoryVO extends BaseTaskStoryVO{

    /**#@+
     *  @ignore
     */
    protected $userId = NULL;
    protected $taskSectionId = NULL;

    public function setUserId($userId) {
        if (is_null($userId))
        $this->userId = $userId;
    else
            $this->userId = (int) $userId;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function setTaskSectionId($taskSectionId) {
        if (is_null($taskSectionId))
        $this->taskSectionId = $taskSectionId;
    else
            $this->taskSectionId = (int) $taskSectionId;
    }

    public function getTaskSectionId() {
        return $this->taskSectionId;
    }

    /**#@-*/

}
