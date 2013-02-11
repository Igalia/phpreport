<?php
/*
 * Copyright (C) 2011 Igalia, S.L. <info@igalia.com>
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
 *
 *
 * This file is based on code from WordPress project: www.wordpress.org
 */

/** Web installation wizard: creation of the config.php file, and setup of the
 * database based on the contents of that file.
 *
 * The permissions for the config directory must allow writing files in order to
 * create config.php in this page.
 *
 * This file is based on code from WordPress project: www.wordpress.org
 *
 * @filesource
 * @package PhpReport
 * @subpackage install
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */

define('ABSPATH', __DIR__.'/../config/');

define('SQLPATH', __DIR__.'/../sql/');



/* These are the sql files that must be executed to prepare DB.
 *
 * IMPORTANT: they must be ordered for their proper execution.
 */
$sqlfiles[] = SQLPATH . "schema.sql";
$sqlfiles[] = SQLPATH . "uniqueConstraints.sql";
$sqlfiles[] = SQLPATH . "otherConstraints.sql";
$sqlfiles[] = SQLPATH . "initialData.sql";
$sqlfiles[] = SQLPATH . "update/all.sql";

function parse_psql_dump($url,$nowhost,$nowport,$nowdatabase,$nowuser,$nowpass){
    $link = @pg_connect("host=$nowhost port=$nowport user=$nowuser dbname=$nowdatabase password=$nowpass");
    if (!$link) {
        return 0;
    }

    $file_content = file($url);
    $string = "";
    foreach($file_content as $sql_line){
        $string = $string . $sql_line;
        if(trim($string) != "" && strstr($string, "--") === false){
            if (strstr($string, "\\.") != false)
            {
                @pg_put_line($link, $string);
                @pg_end_copy($link);
                $string = "";
            } elseif (strstr($string, ";") != false)
            {
                pg_query($link, $string);
                $string = "";
            }
        } else $string = "";
    }

    return 1;
}

$error = NULL;

if (isset($_GET['step']))
    $step = $_GET['step'];
else
    $step = 0;

if (!file_exists(ABSPATH . 'config.template') && ($step < 3))
    $error = '<p>Sorry, I need a <code>config.template</code> file to work from. Please re-upload this file from your PhpReport installation.</p>';
else
    $configFile = file(ABSPATH . 'config.template');

if ( !is_writable(ABSPATH) && ($step != 3) )
    $error = "<p>Sorry, I can't write to the directory. You'll have to either change the permissions on your PhpReport directory or create your <code>config.php</code> manually.</p>";

// Check if config.php has been created
if (file_exists(ABSPATH . 'config.php') && ($step < 3))
    $error = "<p>The file <code>config.php</code> already exists. If you need to reset any of the configuration items in this file, you can do it <a href='setup-config.php?step=4'>through this form</a>, or you may try <a href='setup-config.php?step=3'>preparing DB now</a>.</p>";

if (!is_null($error) && ($step < 3))
{
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>PhpReport &rsaquo; Error</title>
    <link rel="stylesheet" href="css/install.css" type="text/css" />
</head>
<body><h1 id="logo"><img alt="Igalia" src="images/phpreport-logo.png" /></h1>
    <p><?php echo $error; ?></p>
</body>
</html>

<?php

die();

}


/**
 * Display setup config.php file header.
 *
 */
function display_header() {
    header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PhpReport &rsaquo; Setup Configuration File</title>
<link rel="stylesheet" href="css/install.css" type="text/css" />

</head>
<body>
<h1 id="logo"><img alt="Igalia" src="images/phpreport-logo.png" /></h1>
<?php
}//end function display_header();

switch($step) {
    case 0:
        display_header();
?>

<p>Welcome to PhpReport!<br><br>Before getting started, we need some information on the database. You will need to choose the following items before proceeding:</p>
<ol>
    <li>Database name</li>
    <li>Database username</li>
    <li>Database password</li>
    <li>Database host</li>
    <li>Database port</li>
</ol>
<p>If you&#8217;re all ready&hellip;</p>

<p class="step"><a href="setup-config.php?step=1" class="button">Go on!</a></p>
<?php
    break;

    case 4:

    unlink(ABSPATH . 'config.php');

    case 1:
        display_header();
    ?>
<form method="post" action="setup-config.php?step=2">
    <p>Below you should enter your database configuration details. You should ensure these are the proper ones before continuing. </p>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="dbname">Database Name</label></th>
            <td><input name="dbname" id="dbname" type="text" size="25" value="phpreport" /></td>
            <td>The name of the database you want to prepare in order to run PhpReport in. </td>
        </tr>
        <tr>
            <th scope="row"><label for="uname">User Name</label></th>
            <td><input name="uname" id="uname" type="text" size="25" value="phpreport" /></td>
            <td>Your PostgreSQL username</td>
        </tr>
        <tr>
            <th scope="row"><label for="pwd">Password</label></th>
            <td><input name="pwd" id="pwd" type="text" size="25" value="phpreport" /></td>
            <td>...and PostgreSQL password.</td>
        </tr>
        <tr>
            <th scope="row"><label for="dbhost">Database Host</label></th>
            <td><input name="dbhost" id="dbhost" type="text" size="25" value="localhost" /></td>
            <td>I guess it's 'localhost'.</td>
        </tr>
        <tr>
            <th scope="row"><label for="dbport">Database Port</label></th>
            <td><input name="dbport" id="dbport" type="text" size="25" value="5432" /></td>
            <td>Now I guess it's 5432 ;-).</td>
        </tr>
    </table>
    <p class="step"><input name="submit" type="submit" value="Submit" class="button" /></p>
</form>
<?php
    break;

    case 2:
    $dbname  = trim($_POST['dbname']);
    $uname   = trim($_POST['uname']);
    $passwrd = trim($_POST['pwd']);
    $dbhost  = trim($_POST['dbhost']);
    $dbport  = trim($_POST['dbport']);
    if (empty($prefix)) $prefix = 'wp_';

    $handle = fopen(ABSPATH . 'config.php', 'w');

    foreach ($configFile as $line_num => $line) {
        if (strstr($line, "_DBC_DBHOST")) {
            fwrite($handle, str_replace("_DBC_DBHOST", $dbhost, $line));
        }
        else if (strstr($line, "_DBC_DBPORT")) {
            fwrite($handle, str_replace("_DBC_DBPORT", $dbport, $line));
        }
        else if (strstr($line, "_DBC_DBUSER")) {
            fwrite($handle, str_replace("_DBC_DBUSER", $uname, $line));
        }
        else if (strstr($line, "_DBC_DBPASS")) {
            fwrite($handle, str_replace("_DBC_DBPASS", $passwrd, $line));
        }
        else if (strstr($line, "_DBC_DBNAME")) {
            fwrite($handle, str_replace("_DBC_DBNAME", $dbname, $line));
        }
        else {
            fwrite($handle, $line);
        }
    }
    fclose($handle);
    chmod(ABSPATH . 'config.php', 0666);

    display_header();
?>

<p>Okay, now it&#8217;s time to prepare the database with the data you have input:</p>

<p class="step"><a href="setup-config.php?step=3" class="button">Prepare our very own Database</a></p>

<?php
    break;

    case 3:

    require_once(ABSPATH . 'config.php');

    display_header();

    $error = FALSE;

    foreach((array)$sqlfiles as $file)
        if (!parse_psql_dump($file,DB_HOST,DB_PORT,DB_NAME,DB_USER,DB_PASSWORD))
            $error = TRUE;

    if (!$error)
    {

?>

<p>Well done! You have made it through this part of the installation, and now PhpReport has its own working database properly configured.</p>

<?php

    }
    else
    {

?>

<p>It seems there has been some error when attempting to connect the database. Make sure SQL files have not been deleted or changed and that you made no mistakes on data input, and try again:</p>
<p class="step"><a href="setup-config.php?step=4" class="button">Input data</a></p><p class="step"><a href="setup-config.php?step=3" class="button">Run scripts</a></p>

<?php

    }

    break;
}
?>
</body>
</html>
