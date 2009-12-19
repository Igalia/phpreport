<?php

/** File for CommonEventVO
 *
 *  This file just contains {@link CommonEventVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

/** VO for Common Events
 *
 *  This class just stores Common Event data.
 *
 *  @property int $id database internal identifier.
 *  @property int $cityId database internal identifier of the associated City.
 *  @property DateTime $date date of the Common Event.
 */
class CommonEventVO {

    /**#@+
     *  @ignore
     */
    protected $id = NULL;
    protected $cityId = NULL;
    protected $date = NULL;

    public function setId($id) {
        if (is_null($id))
        $this->id = $id;
    else
            $this->id = (int) $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setCityId($cityId) {
        if (is_null($cityId))
        $this->cityId = $cityId;
    else
            $this->cityId = (int) $cityId;
    }

    public function getCityId() {
        return $this->cityId;
    }

    public function setDate(DateTime $date = NULL) {
        $this->date = $date;
    }

    public function getDate() {
        return $this->date;
    }

    /**#@-*/

}
