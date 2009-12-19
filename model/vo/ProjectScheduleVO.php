<?php

/** File for ProjectScheduleVO
 *
 *  This file just contains {@link ProjectScheduleVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

/** VO for Project Schedules
 *
 *  This class just stores Project Schedule data.
 *
 *  @property int $id database internal identifier.
 *  @property int $userId database internal identifier of the associated User.
 *  @property int $projectId database internal identifier of the associated Project.
 *  @property int $initWeek beginning week of this Project Schedule.
 *  @property int $initYear beginning year of this Project Schedule.
 *  @property int $endWeek end week (included) of this Project Schedule.
 *  @property int $endYear end year of this Project Schedule.
 *  @property double $weeklyLoad working hours scheduled per week.
 */
class ProjectScheduleVO {

    /**#@+
     *  @ignore
     */
    protected $id = NULL;
    protected $weeklyLoad = NULL;
    protected $initWeek = NULL;
    protected $initYear= NULL;
    protected $endWeek = NULL;
    protected $endYear = NULL;
    protected $userId = NULL;
    protected $projectId = NULL;

    public function setId($id) {
        if (is_null($id))
        $this->id = $id;
    else
            $this->id = (int) $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setWeeklyLoad($weeklyLoad) {
        if (is_null($weeklyLoad))
        $this->weeklyLoad = $weeklyLoad;
    else
            $this->weeklyLoad = (double) $weeklyLoad;
    }

    public function getWeeklyLoad() {
        return $this->weeklyLoad;
    }

    public function setInitWeek($initWeek) {
        if (is_null($initWeek))
        $this->initWeek = $initWeek;
    else
            $this->initWeek = (int) $initWeek;
    }

    public function getInitWeek() {
        return $this->initWeek;
    }

    public function setInitYear($initYear) {
        if (is_null($initYear))
        $this->initYear = $initYear;
    else
            $this->initYear = (int) $initYear;
    }

    public function getInitYear() {
        return $this->initYear;
    }

    public function setEndWeek($endWeek) {
        if (is_null($endWeek))
        $this->endWeek = $endWeek;
    else
            $this->endWeek = (int) $endWeek;
    }

    public function getEndWeek() {
        return $this->endWeek;
    }

    public function setEndYear($endYear) {
        if (is_null($endYear))
        $this->endYear = $endYear;
    else
            $this->endYear = (int) $endYear;
    }

    public function getEndYear() {
        return $this->endYear;
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

    public function setProjectId($projectId) {
        if (is_null($projectId))
        $this->projectId = $projectId;
    else
            $this->projectId = (int) $projectId;
    }

    public function getProjectId() {
        return $this->projectId;
    }

    /**#@-*/

}
