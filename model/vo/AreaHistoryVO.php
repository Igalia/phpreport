<?php

/** File for AreaHistoryVO
 *
 *  This file just contains {@link AreaHistoryVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/vo/BaseHistoryVO.php');

/** VO for Area Histories
 *
 *  This class just stores Area History data.
 *
 *  @property int $id database internal identifier.
 *  @property int $userId database internal identifier of the associated User.
 *  @property DateTime $initDate beginning date of the history interval.
 *  @property DateTime $endDate end date (included) of the history interval.
 *  @property int $areaId database internal identifier of the associated Area.
 */
class AreaHistoryVO extends BaseHistoryVO
{

    /**#@+
     *  @ignore
     */
    protected $areaId = NULL;

    public function setAreaId($areaId) {
        if (is_null($areaId))
        $this->areaId = $areaId;
    else
            $this->areaId = (int) $areaId;
    }

    public function getAreaId() {
        return $this->areaId;
    }

    /**#@-*/

}
