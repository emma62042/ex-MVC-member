<?php
class DataAccess {
    //var表示變數
    var $conn; //用於儲存資料庫連線
    
    var $query_id; //用於儲存查詢源
    //! 建構函式.
    /*
     * 建立一個新的DataAccess物件
     * @param $host 資料庫伺服器名稱
     * @param $user 資料庫伺服器使用者名稱
     * @param $pass 密碼
     * @param $db   資料庫名稱
     * __construct = new物件後立即執行的function, 預設函數的意思
     */
    function __construct($host,$user,$pass,$db) {
        $this->conn=mysqli_connect($host,$user,$pass,$db); //連線資料庫伺服器
        
    }
    //! 執行SQL語句
    /*
     * 執行SQL語句,獲取一個查詢源並存儲在資料成員$query中
     * @param $sql  被執行的SQL語句字串
     * @return void
     */
    function query($sql) {
        $this->query_id = mysqli_query($this->conn, $sql); // Perform query here
        if ($this->query_id) return true;
        else return false;
    }
    //! 獲取結果集
    /*
     * 以陣列形式返回查詢結果的所有記錄
     * @return mixed
     */
    function fetchRows($sql) {
        $this->query($sql);
        return $this->query_id;
    }
    
    function rowsNum($sql) {
        $this->query($sql);
        $data_rows = mysqli_num_rows($this->query_id);
        return $data_rows;
    }
}
?>
