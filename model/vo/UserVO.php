<?php

/** File for UserVO
 *
 *  This file just contains {@link UserVO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage VO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

/** VO for Users
 *
 *  This class just stores User data.
 *
 *  @property int $id database internal identifier.
 *  @property string $login login of the User.
 *  @property string $password password of the User.
 *  @property array $groups array of objects {@link UserGroupVO} this User is associated to.
 */
class UserVO {

    /**#@+
     *  @ignore
     */
    protected $id = NULL;
    protected $login = NULL;
    protected $password = NULL;
    protected $groups = array ();

    public function setId($id) {
        if (is_null($id))
        $this->id = $id;
    else
            $this->id = (int) $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setLogin($login) {
        $this->login = (string) $login;
    }

    public function getLogin() {
        return $this->login;
    }

    public function setPassword($password) {
        $this->password = (string) $password;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setGroups(array $groups) {
        $this->groups = $groups;
    }

    public function getGroups() {
        return $this->groups;
    }

    /**#@-*/
}
