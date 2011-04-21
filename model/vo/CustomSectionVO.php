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


/** File for CustomSectionVO
 *
 *  This file just contains {@link CustomSectionVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */


include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/BaseSectionVO.php');

/** Custom VO for Sections
 *
 *  This class just stores Section and additional data.
 *
 *  @property int $id database internal identifier.
 *  @property boolean $accepted acceptance flag.
 *  @property string $name name of this Module.
 *  @property string $text text with information about this Section.
 *  @property array $developers developers of this Section.
 *  @property UserVO $reviewer reviewer of this Section.
 *  @property double $estHours estimated working hours of this Section.
 *  @property double $spent working hours spent in this Section.
 *  @property double $done per-1 work done.
 *  @property double $overrun per-1 variation of real work versus estimated work.
 *  @property double $toDo pending working hours in this Section.
 *  @property int $moduleId database internal identifier of the associated Module.
 */
class CustomSectionVO extends BaseSectionVO{

    /**#@+
     *  @ignore
     */
    protected $developers = NULL;
    protected $reviewer = NULL;
    protected $estHours = NULL;
    protected $spent = NULL;
    protected $done = NULL;
    protected $overrun = NULL;
    protected $toDo = NULL;

    public function setEstHours($estHours) {
        $this->estHours = (double) $estHours;
    }

    public function getEstHours() {
        return $this->estHours;
    }

    public function setSpent($spent) {
        $this->spent = (double) $spent;
    }

    public function getSpent() {
        return $this->spent;
    }

    public function setDone($done) {
        $this->done = (double) $done;
    }

    public function getDone() {
        return $this->done;
    }

    public function setOverrun($overrun) {
        $this->overrun = (double) $overrun;
    }

    public function getOverrun() {
        return $this->overrun;
    }

    public function setToDo($toDo) {
        $this->toDo = (double) $toDo;
    }

    public function getToDo() {
        return $this->toDo;
    }

    public function setDevelopers($developers) {
        if (is_null($developers))
        $this->developers = $developers;
    else
            $this->developers = (array) $developers;
    }

    public function getDevelopers() {
        return $this->developers;
    }

    public function setReviewer(UserVO $reviewer) {
        $this->reviewer = $reviewer;
    }

    public function getReviewer() {
        return $this->reviewer;
    }

    /**#@-*/

}
