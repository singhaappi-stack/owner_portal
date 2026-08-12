<?php
session_start();

error_reporting(0);
ini_set('display_errors', 0);

// Determine base path for redirects dynamically
$base_dir = str_replace('template/template.php', '', $_SERVER['SCRIPT_NAME']);

// Handle Logout
if (isset($_REQUEST['template']) && $_REQUEST['template'] === 'logout') {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header("Location: " . $base_dir . "login");
    exit();
}

// Handle Login POST
$error = '';
if (isset($_REQUEST['template']) && $_REQUEST['template'] === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($email === 'owner@example.com' && $password === 'password') {
        $_SESSION['owner'] = [
            "name" => "山田 太郎",
            "email" => $email
        ];
        header("Location: " . $base_dir . "dashboard");
        exit();
    } else {
        $error = 'メールアドレスまたはパスワードが正しくありません。';
    }
}

// Access control: if not logged in and not on login page, redirect to login
if (!isset($_SESSION['owner']) && (!isset($_REQUEST['template']) || $_REQUEST['template'] !== 'login')) {
    header("Location: " . $base_dir . "login");
    exit();
}

// If logged in and trying to access login page, redirect to dashboard
if (isset($_SESSION['owner']) && isset($_REQUEST['template']) && $_REQUEST['template'] === 'login') {
    header("Location: " . $base_dir . "dashboard");
    exit();
}

include "../include/templateHeader.php";
?>

<?php
switch ($_REQUEST['template']){
    case 'login':
        include "./login.php";
        break;
        
    default:
?>
<div class="">
    <div id="sidebar">
        <?php include "./sidebar.html"; ?>
    </div>
    <div id="content_wrapper">
        <?php
            if ($_REQUEST['folder']){
                if (file_exists ("./" .$_REQUEST['folder'] . "/" . $_REQUEST['template'] .".php")){
                    include "./" .$_REQUEST['folder'] . "/" . $_REQUEST['template'] .".php";
                } else {
                    include "./" .$_REQUEST['folder'] . "/" . $_REQUEST['template'] .".html";
                }
            } else {
                include "./" . $_REQUEST['template'] .".html";
            }
        ?>
    </div>
</div>
<?php
        break;
}
include "../include/templateFooter.php";
?>