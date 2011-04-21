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


/** File for StoryVO
 *
 *  This file just contains {@link StoryVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/vo/BaseStoryVO.php');

/** VO for Stories
 *
 *  This class just stores Story data.
 *
 *  @property int $id database internal identifier.
 *  @property boolean $accepted acceptance flag.
 *  @property string $name name of this Iteration.
 *  @property int $iterationId database internal identifier of the associated Iteration.
 *  @property int $userId database internal identifier of the associated User (the one who leads the Story).
 *  @property int $storyId database internal identifier of the associated Story (next one).
 */
class StoryVO extends BaseStoryVO{

    /**#@+
     *  @ignore
     */
    protected $userId = NULL;

    public function setUserId($userId) {
        if (is_null($userId))
        $this->userId = $userId;
    else
            $this->userId = (int) $userId;
    }

    public function getUserId() {
        return $this->userId;
    }

    /**#@-*/

}
