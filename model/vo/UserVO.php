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
