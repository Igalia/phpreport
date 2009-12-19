<?php

/** File for CityVO
 *
 *  This file just contains {@link CityVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

/** VO for Cities
 *
 *  This class just stores City data.
 *
 *  @property int $id database internal identifier.
 *  @property string $name name of the City.
 */
class CityVO {

    /**#@+
     *  @ignore
     */
    protected $id = NULL;
    protected $name = NULL;

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

    /**#@-*/

}
