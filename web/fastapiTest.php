
<?php
define('PHPREPORT_ROOT', __DIR__ . '/../');

/* We check authentication and authorization */
require_once(PHPREPORT_ROOT . '/web/auth.php');
include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');
$user = $_SESSION['user'];
/* Include the generic header and sidebar*/
define('PAGE_TITLE', "PhpReport - FastAPI Test");
include("include/header.php");
?>

<h1>Test page<h1>
<div id="content"></div>
<script>
fetch('http://localhost:8555/projects/', { method: 'GET'})
.then(response => response.json())
.then(function(data){
    data.forEach(project => {
        document.getElementById('content').innerHTML += project.description + '<br>'
    })
})
</script>
<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
