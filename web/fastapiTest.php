<?php
define('PHPREPORT_ROOT', __DIR__ . '/../');

/* We check authentication and authorization */
require_once(PHPREPORT_ROOT . '/web/auth.php');
include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');
$user = $_SESSION['user'];
$api_token = $_SESSION['api_token'];

/* Include the generic header and sidebar*/
define('PAGE_TITLE', "PhpReport - FastAPI Test");
include("include/header.php");
echo "<script>\n";
echo "let api_token ='$api_token';  \n";
echo "</script>\n";
?>

<h1>Test page<h1>
<div id="content"></div>
<script>
fetch('http://localhost:8555/projects/', { method: 'GET', headers: { Authorization: 'Bearer ' + api_token } })
.then(response => response.json())
.then(function (data) {
    data.forEach(project => {
        document.getElementById('content').innerHTML += project.description + '<br>'
    })
})
</script>
<?php
/* Include the footer to close the header */
include("include/footer.php");
?>