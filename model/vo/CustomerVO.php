<?php

/** File for CustomerVO
 *
 *  This file just contains {@link CustomerVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

/** VO for Customers
 *
 *  This class just stores Customer data.
 *
 *  @property int $id database internal identifier.
 *  @property int $sectorId database internal identifier of the associated Sector.
 *  @property string $name name of the Customer.
 *  @property string $type type of the Customer.
 *  @property string $url URL of the Customer.
 */
class CustomerVO {
    protected $id = NULL;
    protected $sectorId = NULL;
    protected $name = NULL;
    protected $type = NULL;
    protected $url = NULL;

    public function setId($id) {
        if (is_null($id))
        $this->id = $id;
    else
            $this->id = (int) $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setSectorId($sectorId) {
        if (is_null($sectorId))
        $this->sectorId = $sectorId;
    else
            $this->sectorId = (int) $sectorId;
    }

    public function getSectorId() {
        return $this->sectorId;
    }

    public function setName($name) {
        $this->name = (string) $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setType($type) {
        $this->type = (string) $type;
    }

    public function getType() {
        return $this->type;
    }

    public function setUrl($url) {
        $this->url = (string) $url;
    }

    public function getUrl() {
        return $this->url;
    }
}
