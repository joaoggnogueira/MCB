<?PHP

define('ROOT_APP',"http://localhost/mcomputa/");
define('DATABASE_HOST',"localhost");
define('DATABASE_NAME',"ccomputacao");
define('DATABASE_USER',"root");
define('DATABASE_PASSWORD',"");
define('VIEW_CTRL', true);

function resource($path) {
    return constant('ROOT_APP') . $path;
}

function resource_script($path) {
    return constant('ROOT_APP')."js/" . $path;
}

function resource_css($path) {
    return constant('ROOT_APP')."styles/" . $path;
}

function resource_component($path,$data) {
    include ("./viewComponents/" . $path);
}

?>