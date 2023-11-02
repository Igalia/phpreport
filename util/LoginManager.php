<?php
/*
 * Copyright (C) 2009-2019 Igalia, S.L. <info@igalia.com>
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


/** File for LoginManager
 *
 *  This file just contains {@link LoginManager}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage util
 * @author Jacobo Aragunde Perez <jaragunde@igalia.com>
 */
use JuliusPC\OpenIDConnect\Client;

require_once(PHPREPORT_ROOT . '/vendor/autoload.php');
require_once(PHPREPORT_ROOT . '/model/facade/UsersFacade.php');
require_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');
require_once(PHPREPORT_ROOT . '/model/vo/UserGroupVO.php');
require_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');

/** Login Manager
 *
 * Utility class containing two functions used by the controller
 * to manage login and authorization.
 */
class LoginManager
{
  public static function setupOidcClient()
  {
    $client = new Client(
      ConfigurationParametersManager::getParameter('OIDC_AUTHORITY'),
      ConfigurationParametersManager::getParameter('OIDC_CLIENT_ID'),
      ConfigurationParametersManager::getParameter('JWT_SECRET')
    );
    $client->setResponseTypes(array('code'));
    return $client;
  }

  /** Login utility function
   *
   * If invoked with the parameters $login and $pass, it will
   * perform the authentication over the model layer, get an
   * object with the user and save it into the session, under
   * $_SESSION['user']. If invoked without parameters, it will
   * check the existance of the session variable 'user' (which
   * is used to know if the user had already logged in).
   *
   * @param string $login The login name of the user.
   * @param string $password The password (clear) of the user.
   * @return boolean True if the user has (or already had) logged
   * in correctly.
   */
  public static function login( $login = NULL, $password = NULL )
  {

    session_start();

    // we are already logged in
    if ($login == NULL && $password == NULL && self::isLogged())
      return true;

    // if we receive the user and password, we try to log in
    try {
      if (strtolower(ConfigurationParametersManager::getParameter('USE_EXTERNAL_AUTHENTICATION')) === "true") {
        $oidc = self::setupOidcClient();

        $oidc->authenticate();
        $oidc_user = $oidc->requestUserInfo(ConfigurationParametersManager::getParameter('OIDC_USERNAME_PROPERTY'));
        $api_token = $oidc->getAccessToken();
        $refresh_token = $oidc->getRefreshToken();
        $id_token = $oidc->getIdToken();

        unset($_SESSION['api_token']);
        $_SESSION['api_token'] = $api_token;
        unset($_SESSION['id_token']);
        $_SESSION['id_token'] = $id_token;
        unset($_SESSION['refresh_token']);
        $_SESSION['refresh_token'] = $refresh_token;

        $user = UsersFacade::GetUserByLogin($oidc_user);
        if (!$user)
          throw new IncorrectLoginException("User not found");
      } else {
        $user = UsersFacade::Login($login, $password);
      }

      unset($_SESSION['user']);
      $_SESSION['user'] = $user;

      return true;
    } catch (IncorrectLoginException $exc) {
      return false;
    }
  }

  /** Logout utility function
   *
   * Removes all the data stored in the session and the session cookie.
   * @return void
   */
  public static function logout()
  {
    // Initialize the session.
    session_start();
    $id_token = $_SESSION['id_token'];
    // Unset all of the session variables.
    $_SESSION = array();

    // To kill the session, we also delete the session cookie.
    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(
        session_name(),
        '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
      );
    }

    // Finally, destroy the session.
    session_destroy();

    if (strtolower(ConfigurationParametersManager::getParameter('USE_EXTERNAL_AUTHENTICATION')) === "true") {
      $oidc = self::setupOidcClient();
      $oidc->signOut($id_token, null);
    }
  }

  /** Login check utility function
   *
   * This function checks whether the User is logged or not,
   * through User in the session, and returns that User in that
   * case. If it's invoked with the parameter <var>$sid</var>, it'll
   * check the User in that session.
   *
   * @param string $sid Session identifier (optional).
   * @return UserVO the User if it's logged in already, or a NULL
   * value otherwise.
   */
  public static function isLogged( $sid = NULL )
  {
    if ($sid)
      session_id($sid);

    if (!isset($_SESSION))
      session_start();

    if (empty($_SESSION['user']))
      return NULL;

    if (strtolower(ConfigurationParametersManager::getParameter('USE_EXTERNAL_AUTHENTICATION')) === "true") {
      if (!self::isTokenActive($_SESSION['api_token']) && !self::isTokenActive($_SESSION['refresh_token']))
        return NULL;
    }

    return $_SESSION['user'];

  }

  public static function isTokenActive( $token )
  {
    $oidc = self::setupOidcClient();
    $intro_token = $oidc->introspectToken(
      $token,
      null,
      ConfigurationParametersManager::getParameter('OIDC_CLIENT_ID'),
      ConfigurationParametersManager::getParameter('JWT_SECRET')
    );
    return $intro_token->active;
  }

  /**
   * @return void
   * @throws Exception
   */
  public static function refreshAccessToken()
  {
    //tokens from session, stored originally on login
    $current_refresh_token = $_SESSION['refresh_token'];
    $current_access_token = $_SESSION['api_token'];

    if ($current_access_token && $current_refresh_token) {
      //introspect the access token; if it's good, move along; if not, go next to introspect the refresh token and if valid, do a refresh and get new access_token and new refresh token
      if (!self::isTokenActive($current_access_token)) {
        //now check the refresh token
        if (self::isTokenActive($current_refresh_token)) {
          //fyi - the refreshToken() method brings back an object
          $refreshed_token = self::setupOidcClient()->refreshToken($current_refresh_token, false);
          unset($_SESSION['api_token']);
          $_SESSION['api_token'] = $refreshed_token->access_token;
          unset($_SESSION['refresh_token']);
          $_SESSION['refresh_token'] = $refreshed_token->refresh_token;
        } else {
          //both access and refresh tokens have expired
          throw new Exception('Error: Access and refresh tokens both expired');
        }
      }
    } else {
      //one or both tokens are null
      throw new Exception('Error: One or both tokens are null');
    }
  }

  /** Authorization utility function
   *
   * It checks the logged user against the permissions array. It
   * will retrieve the user groups and check the permissions of
   * each one to access the current url. If the parameter $sid
   * is passed, it will try to retrieve the data from the session
   * with that identifier.
   *
   * @param string $sid Session identifier (optional).
   * @return boolean true if the user belongs to a group able
   * to open the current url, false otherwise.
   */
  public static function isAllowed( $sid = NULL )
  {

    /* We include the file with the array of permissions */
    require(PHPREPORT_ROOT . '/config/permissions.php');

    if ($sid != NULL && !isset($_SESSION))
      session_id($sid);

    if (!isset($_SESSION))
      session_start();

    if (isset($_SESSION['user'])) {
      $user = $_SESSION['user'];
      $url = explode($urlHeader, $_SERVER["SCRIPT_NAME"]);

      foreach ($user->getGroups() as $group) {
        if (
          isset($permissions[$group->getName()]) &&
          in_array($url[1], $permissions[$group->getName()])
        )
          return true;
      }
    }
    return false;

  }


  /** Admin/Privileged user authorization utility function
   *
   * It checks the logged user against the extra permissions array. It
   * will retrieve the user groups and check the permissions of
   * each one to access the current url. If the parameter $sid
   * is passed, it will try to retrieve the data from the session
   * with that identifier.
   *
   * @param string $sid Session identifier (optional).
   * @return boolean true if the user belongs to a admin group for
   * the current url, false otherwise.
   */
  public static function hasExtraPermissions( $sid = NULL )
  {

    /* We include the file with the array of permissions */
    require(PHPREPORT_ROOT . '/config/permissions.php');

    if ($sid != NULL)
      session_id($sid);

    if (!isset($_SESSION))
      session_start();

    if (isset($_SESSION['user'])) {
      $user = $_SESSION['user'];

      foreach ($user->getGroups() as $group) {
        $url = explode($urlHeader, $_SERVER["SCRIPT_NAME"]);
        if (
          isset($extraPermissions[$group->getName()]) &&
          in_array($url[1], $extraPermissions[$group->getName()])
        )
          return true;
      }
    }
    return false;

  }
}
