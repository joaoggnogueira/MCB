<?PHP

define('ROOT_APP',"http://localhost/mcomputa/");
define('DATABASE_HOST',"localhost");
define('DATABASE_NAME',"ccomputacao");
define('DATABASE_USER',"root");
define('DATABASE_PASSWORD',"");
define('VIEW_CTRL', true);
define('ENABLE_ENADE', true);

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

function check_if_are_admin($id,$email){
    return (($id == "103615207932272137622") && ($email == "joaogabriel.sveen@gmail.com"));
}

?>