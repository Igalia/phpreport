<?php

/** File for CityHistoryVO
 *
 *  This file just contains {@link CityHistoryVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/vo/BaseHistoryVO.php');

/** VO for City Histories
 *
 *  This class just stores City History data.
 *
 *  @property int $id database internal identifier.
 *  @property int $userId database internal identifier of the associated User.
 *  @property DateTime $initDate beginning date of the history interval.
 *  @property DateTime $endDate end date (included) of the history interval.
 *  @property int $CityId database internal identifier of the associated City.
 */
class CityHistoryVO extends BaseHistoryVO
{

    /**#@+
     *  @ignore
     */
    protected $cityId = NULL;

    public function setCityId($cityId) {
        if (is_null($cityId))
        $this->cityId = $cityId;
    else
            $this->cityId = (int) $cityId;
    }

    public function getCityId() {
        return $this->cityId;
    }

    /**#@-*/

}
