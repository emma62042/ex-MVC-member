<?php

//! Controller
/*
 * 控制器將$_GET["action"]中不同的引數(list、post、delete)
 * 對應於完成該功能控制的相應子類
 */
class Controller {
    var $model;  // Model 物件
    var $view;   // View  物件
    //! 建構函式
    /*
     * 構造一個Model物件儲存於成員變數$this->model;
     */
    /*
     * & 表示引用(類似c的指標),傳送的是參數位址
     * 對此變數修改的話會改到原本的變數?
     * 對於不會修改的大型陣列使用這種方法比較省記憶體空間
     */
    function __construct (&$dao) { 
        $this->model = new Model($dao);//連接資料庫的model
        begin();
        sign();
        banner();
        sidebar();
        contentStart();
    }
    function __destruct() {
        contentEnd();
        viewend();
    }
}

//用於控制顯示留言列表的子類
class ListSearchController extends Controller {   //extends表示繼承
    function __construct (&$dao, $page, $search) { //建立model
        parent::__construct($dao);  //繼承其父類的建構函式然後執行(執行1號)
        //該行的含義可以簡單理解為:
        //將其父類的建構函式程式碼複製過來
        if($search == NULL){
            $page_array = $this->model->pageArray($page, "list");//列出全部的留言的page數目
            $notes = $this->model->listNote($page, $page_array["per"]);//抓出此頁留言
            $this->view = new SearchListView();
            $this->view->searchBar();
            $this->view->viewMsgResult($notes);
            $this->view->viewPage($page_array);
        }else{
            $page_array = $this->model->pageArray($page, "search", $search);
            $notes = $this->model->searchNote($page, $page_array["per"], $search);
            $this->view = new SearchListView();
            $this->view->searchBar($search);
            $this->view->viewMsgResult($notes);
            $this->view->viewPage($page_array, "&input=".$search);
        }
    }
}

//用於控制新增留言的子類
class PostController extends Controller {
    function __construct ($dao) {//連資料庫
        parent::__construct($dao);//建立model
        if(!isset($_SESSION["login_id"])){
            pleaseLogin();
        }else{
            if(isset($_POST["msg_title"]) && isset($_POST["msg"])){
                $msg_array = $this->model->postNote($_POST["msg_title"], $_POST["msg"]);//先收看看有沒有資料近來
            }else{
                $msg_array["id"] = "";
                $msg_array["msg_title"] = "";
                $msg_array["msg"] = "";
                $msg_array["errtitle"] = "";
                $msg_array["errmsg"] = "";
            }
            $this->view = new PostModifyView($msg_array, "post");
        }
    }
}

//用於控制修改留言的子類
class ModifyController extends Controller {
    function __construct (&$dao) {//連資料庫
        parent::__construct($dao);//建立model
        if(!isset($_SESSION["login_id"])){
            $this->view->pleaseLogin();
        }else{
            $msg_array = $this->model->modifyNote();//先收看看有沒有資料近來
            $this->view = new PostModifyView($msg_array, "modify");
        }
    }
}

class DeleteController extends Controller {
    function __construct (&$dao) {
        parent::__construct($dao);
        if(!isset($_SESSION["login_id"])){
            $this->view->pleaseLogin();
        }else{
            $value = $this->model->deleteNote();
            $this->view = new DeleteView($value);
        }
    }
}

class LoginController extends Controller {
    function __construct (&$dao) {
        parent::__construct($dao);
        $member_array = $this->model->loginNote();
        $this->view = new LoginView($member_array);
    }
}

class SignupController extends Controller {
    function __construct (&$dao) {
        parent::__construct($dao);
        $signup_array = $this->model->signupNote();
        $this->view = new SignupView($signup_array);
    }
}

//用於控制修改會員資料的子類
/*
 * parent::__construct : 建立model, 輸出sign、banner、sidebar的div;
 * 非會員, 輸出請登入畫面
 * 會員身分:
 * 1.建立modifyMyDataView obj
 * 2.透過model確認目前是按下確認修改前/後,回傳要輸出的資料陣列
 * 3.呼叫modifyMyData()輸出畫面
 */
class ModifyMyDataController extends Controller {
    function __construct (&$dao) {
        parent::__construct($dao);
        if(!isset($_SESSION["login_id"])){
            $this->view->pleaseLogin();
        }else{
            $md_array = $this->model->modifyMyDataNote();
            $this->view = new ModifyMyDataView($md_array);
        }
    }
}

//用於控制修改會員密碼的子類
/*
 * parent::__construct : 建立model, 輸出sign、banner、sidebar的div;
 * 非會員, 輸出請登入畫面
 * 會員身分:
 * 1.建立modifyMyPwdView obj
 * 2.透過model確認目前是按下確認修改密碼前/後,回傳要輸出的資料陣列
 * 3.呼叫modifyMyPwd()輸出畫面
 */
class ModifyMyPwdController extends Controller {
    function __construct (&$dao) {
        parent::__construct($dao);
        if(!isset($_SESSION["login_id"])){
            $this->view->pleaseLogin();
        }else{
            $mdpwd_array = $this->model->modifyMyPwdNote();
            $this->view = new ModifyMyPwdView($mdpwd_array);
        }
    }
}

//用於控制會員-我的留言的子類
/*
 * parent::__construct : 建立model, 輸出sign、banner、sidebar的div;
 * 非會員, 輸出請登入畫面
 * 會員身分:
 * 1.依照url get的page得到最底下頁數的輸出資料
 * 2.依照url get的page和每頁預設輸出筆數(per),從資料庫得到這頁的留言陣列
 * 3.建立listView obj
 * 4.輸出此頁留言
 * 5.輸出底下頁數連結
 */
class ListMyMsgController extends Controller {
    function __construct (&$dao, $page) {
        parent::__construct($dao);
        if(!isset($_SESSION["login_id"])){
            $this->view->pleaseLogin();
        }else{
            $page_array = $this->model->pageArray($page, "listMyMsg");
            $notes = $this->model->listMyMsgNote($page, $page_array["per"]);
            $this->view = new listMyMsgView();
            $this->view->viewMsgResult($notes);
            $this->view->viewPage($page_array, NULL ,"listMyMsg");
        }
    }
}

?>