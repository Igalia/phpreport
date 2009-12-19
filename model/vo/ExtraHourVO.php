<?php

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
