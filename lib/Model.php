<?php

//! Model類
/*
 * 它的主要部分是對應於留言本各種資料操作的函式
 * 如:留言資料的顯示、插入、刪除等
 */
$key = "2BC725612ED4DE3B638732F73677DF275385EF3A08E1E78D34A28FED3FCC55C1D41B3E3711B31A9D2FBD6507F689EC4333C018463D871B0D9DBDE24F55";
class Model {
    
    var $dao; //DataAccess類的一個例項(物件)//conn
    //! 建構函式
    /*
     * 構造一個新的Model物件
     * @param $dao是一個DataAccess物件
     * 該引數以地址傳遞(&;$dao)的形式傳給Model
     * 並儲存在Model的成員變數$this->dao中
     * Model通過呼叫$this->dao的fetch方法執行所需的SQL語句
     */
    function __construct(&$dao) {
        $this->dao=$dao;
    }
    
    function listNote($page, $per) {    //獲取全部留言
        $start = ($page - 1) * $per; //每一頁開始的資料序號
        $notes = $this->dao->fetchRows("SELECT A.*, B.nickname
                                        FROM center88_board as A
                                        NATURAL JOIN center88_member as B
                                        ORDER BY time DESC 
                                        LIMIT ".$start.", ".$per);
        
        //執行dataAccess裡的function
        return $notes;
    }
    
    function searchNote($page, $per, $search) {    //獲取全部留言
        $start = ($page - 1) * $per; //每一頁開始的資料序號
        $notes = $this->dao->fetchRows("SELECT A.*, B.nickname
                                        FROM center88_board as A
                                        NATURAL JOIN center88_member as B
                                        WHERE msg_title LIKE '%" . $search . "%'
                                        ORDER BY time DESC
                                        LIMIT ".$start.", ".$per);
        //執行dataAccess裡的function
        return $notes;
    }
    
    function pageArray($page, $dowhat, $search = NULL) {
        $page_array["page"] = $page;
        $page_array["per"] = $per = 3;//每頁顯示筆數
        
        switch($dowhat){
            case "list":
                $page_array["data_rows"] = $this->dao->rowsNum("SELECT *
                                                                FROM center88_board
                                                                ORDER BY time DESC");
                break;
            case "search":
                $page_array["data_rows"] = $this->dao->rowsNum("SELECT *
                                                                FROM center88_board
                                                                WHERE msg_title LIKE '%" . $search . "%'
                                                                ORDER BY time DESC");
                break;
            case "listMyMsg":
                $page_array["data_rows"] = $this->dao->rowsNum("SELECT *
                                                                FROM center88_board
                                                                WHERE mb_id ='" . $_SESSION["login_id"] . "'
                                                                ORDER BY time DESC");
                break;
        }
        $page_array["allpages"] = ceil($page_array["data_rows"]/$per);
        return $page_array;
    }
    
    function postNote($msg_title, $msg) {
        $msg_array["id"] = "";
        $msg_array["msg_title"] = "";
        $msg_array["msg"] = "";
        $msg_array["errtitle"] = "";
        $msg_array["errmsg"] = "";
        
        if($msg_title != NULL && $msg != NULL ){//不允許add送空字串
            $sql = "INSERT INTO center88_board (msg_title, msg, mb_id)
                    VALUES ('" . $msg_title . "','" . $msg . "','" . $_SESSION["login_id"] . "')";
            if($this->dao->query($sql)){
                $msg_array["success"] = 1;
            }else{
                $msg_array["success"] = 0;
            }
        }
       return $msg_array;
    }
    
    function modifyNote() {
        $msg_array["id"] = "";
        $msg_array["msg_title"] = "";
        $msg_array["msg"] = "";
        $msg_array["errtitle"] = "";
        $msg_array["errmsg"] = "";
        
        if(isset($_GET["id"])){ //未修改前，輸出原本內容
            $msg_array["id"] = $_GET["id"];
            $sql = "SELECT *
                    FROM center88_board
                    WHERE msg_id = " . $msg_array["id"]. "
                    ORDER BY time DESC";
            $notes = $this->dao->fetchRows($sql);
            foreach($notes as $value){
                $msg_array["msg_title"] = isset($value["msg_title"]) ? $value["msg_title"] : "";
                $msg_array["msg"] = isset($value["msg"]) ? $value["msg"] : "";
            }
        }
        if(isset($_POST["msg_id"])){
            $time = date("Y-m-d H:i:s",time()+8*60*60); //GMT+8
            $id = $_POST["msg_id"];
            $msg = $_POST["msg"];
            $sql = "UPDATE center88_board
                    SET msg='" . $msg . "', time= '" . $time . "'
                    WHERE msg_id= '" . $id . "'";
            if($this->dao->query($sql)){
                $msg_array["success"] = 1;
            }else{
                $msg_array["success"] = 0;
            }
        }
        return $msg_array;
    }
    
    function deleteNote() {
        if(isset($_GET["id"])){ //未刪除前，輸出原本內容
            $sql = "SELECT A.*, B.nickname
                    FROM center88_board as A
                    NATURAL JOIN center88_member as B
                    WHERE msg_id = " . $_GET["id"] . "
                    ORDER BY time DESC";
            $notes = $this->dao->fetchRows($sql);
            foreach ($notes as $value){
                return $value;
            }
        }
        if(isset($_POST["msg_id"])){ //未修改前，輸出原本內容
            $sql = "DELETE FROM center88_board 
                    WHERE msg_id = '" . $_POST["msg_id"] . "'" ;
            if ($this->dao->query($sql)){
                $value["success"] = 1;
                return $value;
            }else{
                $value["success"] = 0;
                return $value;
            }
        }
    }
    
    function loginNote() {
        $member_array["success"] = 0;
        $member_array["mb_id"] = "";
        $member_array["mb_pwd"] = "";
        $member_array["errid"] = "";
        $member_array["errpwd"] = "";
        
        if(isset($_POST["mb_id"])){ //未修改前，輸出原本內容
            if($_POST["mb_id"] != NULL && $_POST["mb_pwd"] != NULL){
                $sql = "SELECT *
                        FROM center88_member
                        WHERE mb_id='" . $_POST["mb_id"] . "'";
                if($this->dao->rowsNum($sql) == 0){
                    $member_array["mb_id"] = $_POST["mb_id"];
                    $member_array["errid"] = "無此帳號!";
                    return $member_array;
                }
                $sql = "SELECT *
                        FROM center88_member
                        WHERE mb_id='" . $_POST["mb_id"] . "' and AES_DECRYPT(pwd, '" . $GLOBALS["key"] . "') ='" . $_POST["mb_pwd"] . "'";
                if($this->dao->rowsNum($sql) == 0){
                    $member_array["mb_id"] = $_POST["mb_id"];
                    $member_array["errpwd"] = "密碼錯誤!";
                    return $member_array;
                }else{
                    $member_array["success"] = 1;
                    $_SESSION["login_id"] = $_POST["mb_id"];
                }
            }
        }
        return $member_array;
    }
    
    function signupNote() {
        $signup_array["set_id"] = "";
        $signup_array["set_pwd"] = "";
        $signup_array["check_pwd"] = "";
        $signup_array["set_email"] = "";
        $signup_array["errid"] = "";
        $signup_array["errpwd"] = "";
        $signup_array["errckpwd"] = "";
        
        if (isset($_POST["set_id"])){
            if ($_POST["set_id"] != NULL && $_POST["set_pwd"] != NULL && $_POST["check_pwd"] != NULL && $_POST["set_email"] != NULL ){//不允許signup送空字串
                if($_POST["set_pwd"] != $_POST["check_pwd"]){
                    $signup_array["set_id"] = $_POST["set_id"];
                    $signup_array["set_email"] = $_POST["set_email"];
                    $signup_array["errckpwd"] = "密碼不相符";
                    return $signup_array;
                }
                $sql = "SELECT * 
                        FROM center88_member 
                        WHERE mb_id='" . $_POST["set_id"] . "'";
                if($this->dao->rowsNum($sql) > 0){
                    $signup_array["set_id"] = $_POST["set_id"];
                    $signup_array["set_email"] = $_POST["set_email"];
                    $signup_array["errid"] = "此帳號已被使用!";
                    return $signup_array;
                }
                if($_POST["set_nickname"] == NULL){
                    $set_nickname = $_POST["set_id"];
                }else{
                    $set_nickname = $_POST["set_nickname"];
                }
                
                $sql = "INSERT INTO center88_member
                        VALUES ('" . $_POST["set_id"] . "', AES_ENCRYPT('" . $_POST["set_pwd"] . "','" . $GLOBALS['key'] . "'),'" . $set_nickname . "','" . $_POST["set_email"] . "')";
                if($this->dao->query($sql)){
                    $signup_array["success"] = 1;
                }
            }
        }
        return $signup_array;
    }
    
    //修改會員資料
    /*
     * 建立md_array儲存要送到view的資料(email)
     * 預設 : 透過session["login_id"]從資料庫取出原先的email;
     * 以isset($_POST["new_email"])確認使用者輸入完資料送出
     * update資料庫,echo 成功/失敗訊息;
     */
    function modifyMyDataNote() {
        $md_array["email"] = "";
        if(isset($_POST["new_email"])){
            $sql = "UPDATE center88_member
                    SET email='" . $_POST["new_email"] . "'
                    WHERE mb_id= '" . $_SESSION["login_id"] . "'";
            if($this->dao->query($sql)){
                $md_array["success"] = 1;
            }else{
                $md_array["success"] = 0;
            }
        }else{
            $sql = "SELECT email
                    FROM center88_member
                    WHERE mb_id = '" . $_SESSION["login_id"] . "'";
            $notes = $this->dao->fetchRows($sql);
            foreach($notes as $value){
                $md_array["email"] = isset($value["email"]) ? $value["email"] : "";
            }
        }
        return $md_array;
    }
    //修改會員密碼
    /*
     * 建立mdpwd_array儲存要送到view的資料(password不能顯示出來,因此只有存err訊息欄)
     * 預設 : 所有欄位都是空的
     * 以isset($_POST["new_mb_pwd"])確認使用者輸入完資料送出
     * 1.檢查欄位是否為空 ? 輸出err,return : 進入2 ;
     * 2.從資料庫中取出 $_SESSION["login_id"]的密碼,進入3;
     * 3.比對舊密碼相符 ? 進入4 : 輸出erroldpwd,return;
     * 4.比對新密碼、確認新密碼相符 ? 進入5 : 輸出errckpwd,return;
     * 5.update資料庫,echo 成功/失敗訊息;
     */
    function modifyMyPwdNote() {
        $mdpwd_array["erroldpwd"] = "";
        $mdpwd_array["errpwd"] = "";
        $mdpwd_array["errckpwd"] = "";
        if(isset($_POST["new_mb_pwd"]))
        {
            if($_POST["old_mb_pwd"] != NULL && $_POST["new_mb_pwd"] != NULL && $_POST["new_check_pwd"] != NULL){//不允許signup送空字串
                $sql = "SELECT AES_DECRYPT(pwd, '" . $GLOBALS["key"] . "')pwd
                        FROM center88_member
                        WHERE mb_id='" . $_SESSION["login_id"] . "'";
                $notes = $this->dao->fetchRows($sql);
                foreach ($notes as $value) {
                    $pwd = isset($value["pwd"]) ? $value["pwd"] : "";
                }
                if($_POST["old_mb_pwd"] != $pwd){
                    $mdpwd_array["erroldpwd"] = "密碼錯誤";
                    return $mdpwd_array;
                }
                if($_POST["new_mb_pwd"] != $_POST["new_check_pwd"]){
                    $mdpwd_array["errckpwd"] = "密碼不相符";
                    return $mdpwd_array;
                }
                $sql = "UPDATE center88_member
                        SET pwd=AES_ENCRYPT('" . $_POST["new_mb_pwd"] . "','" . $GLOBALS['key'] . "')
                        WHERE mb_id= '" . $_SESSION["login_id"] . "'";
                if($this->dao->query($sql)){
                    $mdpwd_array["success"] = 1;
                }else{
                    $mdpwd_array["success"] = 0;
                }
            }
        }
        return $mdpwd_array;
    }
    //會員-我的留言
    /*
     * 使用listNote模板
     * 修改WHERE條件為member id == session["login_id"]
     */
    function listMyMsgNote($page, $per) {
        $start = ($page - 1) * $per; //每一頁開始的資料序號
        $notes = $this->dao->fetchRows("SELECT A.*, B.nickname
                                        FROM center88_board as A
                                        NATURAL JOIN center88_member as B
                                        WHERE mb_id ='" . $_SESSION["login_id"] . "'
                                        ORDER BY time DESC
                                        LIMIT ".$start.", ".$per);
        //執行dataAccess裡的function
        return $notes;
    }
}
?>