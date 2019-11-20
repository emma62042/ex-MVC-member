<?php session_start();
require_once("lib/ini_view.php");
require_once("lib/DataAccess.php");
require_once("lib/Model.php");
require_once("lib/View.php");
require_once("lib/Controller.php");
//建立DataAccess物件(請根據你的需要修改引數值)
$dao=new DataAccess ("localhost:33060", "root", "root","center88_DB");
//$dao = $conn
//根據$_GET["action"]取值的不同調用不同的控制器子類
if(isset($_GET["action"])){
    $action=$_GET["action"];
    switch ($action)
    {
        case "searchlist":
            if(isset($_GET["input"]) && isset($_GET["page"])){
                $controller = new ListSearchController($dao, $page = $_GET["page"], $search = $_GET["input"]);
            }elseif(isset($_GET["page"])){
                $controller = new ListSearchController($dao, $page = $_GET["page"], $search = NULL);
            }elseif(isset($_GET["input"])){
                $controller = new ListSearchController($dao, $page = 1, $search = $_GET["input"]);
            }else{
                $controller = new ListSearchController($dao, $page = 1, $search = NULL);
            }
            break;
        case "post"://要會員
            $controller = new PostController($dao); 
            break;
        case "modify"://要會員
            $controller = new ModifyController($dao);
            break;
        case "delete"://要會員
            $controller = new DeleteController($dao);
            break;
        case "login":
            $controller = new LoginController($dao);
            break;
        case "signup":
            $controller = new SignupController($dao);
            break;
        case "modifyMyData"://修改會員資料
            $controller = new ModifyMyDataController($dao);
            break;
        case "modifyMyPwd"://修改會員密碼
            $controller = new ModifyMyPwdController($dao);
            break;
        case "listMyMsg"://我的留言
            if(isset($_GET["page"])){
                $controller = new ListMyMsgController($dao, $page = $_GET["page"]);
            }else{
                $controller = new ListMyMsgController($dao, $page = 1);
            }
            break;
        case "logout":
            session_destroy();
            //unset($_SESSION["login_id"]);
            header("Location:index.php");
            break;
        default:
            $controller = new ListSearchController($dao, $page = 1, $search = NULL);
            break; //預設為顯示留言
    }
}else{
    $controller = new ListSearchController($dao, $page = 1, $search = NULL);
}
?>
