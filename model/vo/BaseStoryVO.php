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


/** File for BaseStoryVO
 *
 *  This file just contains {@link BaseStoryVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

/** Base VO for Stories
 *
 *  This class just stores the Base Story data, which will be extended by
 *  specific classes.
 *
 *  @property int $id database internal identifier.
 *  @property boolean $accepted acceptance flag.
 *  @property string $name name of this Iteration.
 *  @property int $iterationId database internal identifier of the associated Iteration.
 *  @property int $storyId database internal identifier of the associated BaseStory (next one).
 */
abstract class BaseStoryVO {

    /**#@+
     *  @ignore
     */
    protected $id = NULL;
    protected $accepted = NULL;
    protected $name = NULL;
    protected $iterationId = NULL;
    protected $storyId = NULL;

    public function setId($id) {
    if (is_null($id))
        $this->id = $id;
    else
            $this->id = (int) $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setName($name) {
        $this->name = (string) $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setAccepted($accepted) {
        $this->accepted = (boolean) $accepted;
    }

    public function getAccepted() {
        return $this->accepted;
    }

    public function setIterationId($iterationId) {
        if (is_null($iterationId))
        $this->iterationId = $iterationId;
    else
            $this->iterationId = (int) $iterationId;
    }

    public function getIterationId() {
        return $this->iterationId;
    }

    public function setStoryId($storyId) {
        if (is_null($storyId))
        $this->storyId = $storyId;
    else
            $this->storyId = (int) $storyId;
    }

    public function getStoryId() {
        return $this->storyId;
    }

    /**#@-*/

}
