<?php
//! View 類
/*
 * 針對各個功能(list、post、delete)的各種View子類
 * 被Controller呼叫,完成不同功能的網頁顯示
 */
class SearchListView {  //顯示所有留言的子類上方加搜尋
    function searchBar($search = NULL) {?>
    	<form action="index.php" method="get">
    		<table cellpadding="10" width="600" border="1" align="center">
                <tr>
                    <td>
                    	<input type="hidden" name="action" value="searchlist">
						搜尋：<input type="text" name="input" size="41" style="font-size:20px" 
                        value="<?php echo $search ?>">
						<button type="submit">START</button>
					</td>
				</tr>
			</table>
		</form>
		<br/>
    <?php   
    }
    
    function viewMsgResult(&$notes) {
        foreach($notes as $value){?>
			<table class="cont_tb">
				<tr>
					<td colspan="2">
						#<?php echo $value["msg_id"] ?>
					</td>
				</tr>
				<tr>
					<td>留言標題：</td>
                    <td width="450">
                        <?php echo $value["msg_title"] ?>
                    </td>
                </tr>
                <tr>
					<td>留言內容：</td>
					<td width="450">
    					<?php
    					   $msg = str_replace("\n","<br/>",$value["msg"]);
                           echo $msg;
                        ?>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: right;">
						<?php echo $value["nickname"] . "&nbsp;發表於&nbsp;" . $value["time"]; ?>
					</td>
				</tr>
		  <?php if(isset($_SESSION["login_id"]) && $value["mb_id"] == $_SESSION["login_id"]){?>
    				<tr>
    					<td colspan="2" style="text-align: right;">
    						<button type="button" onclick="location.href='index.php?action=delete&id=<?php echo $value["msg_id"];?>'">刪除</button>
    						<button type="button" onclick="location.href='index.php?action=modify&id=<?php echo $value["msg_id"];?>'">修改</button>
    					</td>
    				</tr>
		  <?php }?>
			</table>
			<br/>            
			<?php 
        }
    }
    
    function viewPage($page_array, $search = NULL, $action = "searchlist") {?>
        <p>共<?php echo $page_array["data_rows"] ?>筆-在<?php $page_array["page"] ?>頁-共<?php echo $page_array["allpages"] ?>頁</p>
        <p><a href=index.php?action=<?php echo $action . $search ?>&page=1>首頁</a>-第
        <?php
        for($i = 1; $i <= $page_array["allpages"]; $i++){
            if($page_array["page"]-3 < $i && $i < $page_array["page"]+3){/*前2頁 後兩頁*/?>
            	<a href=index.php?action=<?php echo $action . $search ?>&page=<?php echo $i ?> ><?php echo $i ?></a>    
      <?php }
        }?>
		頁-<a href=index.php?action=<?php echo $action . $search ?>&page=<?php echo $page_array["allpages"] ?> >末頁</a>
        </p>
<?php
    }
}

class PostModifyView {//新增修改留言的子類
    function __construct(&$msg_array, $title) {
        if(isset($msg_array["success"]) && $msg_array["success"] == 1){?>
        	<h2><?php echo $title=="post" ? "新增" : "修改" ?>留言成功!!</h2>
  <?php     header("Refresh: 1; URL=index.php");
        }else{?>
            <h3><?php echo $title=="post" ? "新增" : "修改" ?>留言</h3>
        	<form id="postForm" action="index.php?action=<?php echo $title?>" method="post">
                <table  style="border:3px #000000 dashed;" cellpadding="10" border="1" align="center">
                   <tr>
                        <td>
    						<span style="color:red;">*</span>留言標題：
                        </td>
                        <td>
                            <input type="text" name="msg_title" size="38" style="font-size:20px" value="<?php echo $msg_array["msg_title"] ?>" required>
                            <br/>
                            <span style="color:red;"><?php echo $msg_array["errtitle"] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
    						<span style="color:red;">*</span>留言內容：
                        </td>
                        <td>
                            <textarea cols="45" rows="5" type="text" name="msg" style="font-size:16px" required><?php echo $msg_array["msg"] ?></textarea>
                            <br/>
                            <span style="color:red;"><?php echo $msg_array["errmsg"] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                        	<input type="hidden" name="msg_id" value="<?php echo $msg_array["id"]?>">
                            <button type="submit"><?php echo $title=="post" ? "新增" : "修改" ?>完成</button>
                        </td>
                    </tr>
                </table>
            </form>
    	<?php
        }
    }
}

class DeleteView {
    function __construct (&$value) {
        if(isset($value["success"]) && $value["success"] == 1){?>
        	<h2>刪除留言成功!!</h2>
  <?php     header("Refresh: 1; URL=index.php");
        }else{
            if(isset($_GET["id"])){
            ?>
                <form action="index.php?action=delete" method="post">
                	<table class="cont_tb">
        				<tr>
        					<td colspan="2">
        						#<?php echo $value["msg_id"]; ?>
        					</td>
        				</tr>
        				<tr>
        					<td>留言標題：</td>
                            <td width="450">
                                <?php echo $value["msg_title"]; ?>
                            </td>
                        </tr>
                        <tr>
        					<td>留言內容：</td>
        					<td width="450">
        					<?php
        					   $msg = str_replace("\n","<br/>",$value["msg"]);
                               echo $msg;
                            ?>
        					</td>
        				</tr>
        				<tr>
        					<td colspan="2" style="text-align: right;">
        					<?php echo $value["nickname"] . "&nbsp;發表於&nbsp;" . $value["time"] ?>
        					</td>
        				</tr>
        			</table>
        			<input type="hidden" name="msg_id" value="<?php echo $value["msg_id"]?>">
        			<button type="submit">確認刪除</button>
        		</form>
		<?php
            }
        }
    }
}

class LoginView {
    function __construct (&$member_array) {
        if($member_array["success"] == 0){?>
        		<h2>會員登入</h2>
        		<form action="index.php?action=login" method="post">
            		<table style="border:3px #000000 dashed;" cellpadding="10" width="400" border="1" align="center">
            			<tr>
            				<td>
                               	 帳號：
                            </td>
                            <td>
                                <input type="text" name="mb_id" style="font-size:20px" value="<?php echo $member_array["mb_id"] ?>" required>
                                <br/>
                                <span style="color:red;"><?php echo $member_array["errid"] ?></span>
                            </td>
            			</tr>
            			<tr>
            				<td>
                               	 密碼：
                            </td>
                            <td>
                                <input type="password" name="mb_pwd" style="font-size:20px" value="<?php echo $member_array["mb_pwd"] ?>" required>
                                <br/>
                                <span style="color:red;"><?php echo $member_array["errpwd"] ?></span>
                            </td>
            			</tr>
            		</table>
            		<br/>
            		<button type="submit">登入</button>
        		</form>
        		<br/>
        		<br/>
        		還沒有帳號嗎?&nbsp;&nbsp;<button onclick="location.href='index.php?action=signup'">註冊去→</button>
    	<?php 
        }elseif($member_array["success"] == 1){?>
            <h2>會員登入</h2>
        	登入成功!!&nbsp;&nbsp;將於3秒後跳轉至首頁
    		<?php 
    		header("Refresh: 2; URL=index.php");
        }
    }
}

class SignupView {
    function __construct (&$signup_array) {
        if(isset($signup_array["success"]) && $signup_array["success"] == 1){?>
            <h2>會員註冊</h2>
        	註冊成功!!&nbsp;&nbsp;將於3秒後跳轉至登入頁面
            <?php
            header("Refresh: 2; URL=index.php?action=login");
        }else{?>
        		<h2>會員註冊</h2>
        		<form id="signupForm" action="index.php?action=signup" method="post">
            		<table  style="border:3px #000000 dashed;" cellpadding="10" border="1" align="center">
            			<tr>
            				<td>
                               	 <span style="color:red;">*</span>帳號：
                            </td>
                            <td align="left">
                                <input id="set_id" type="text" name="set_id" style="font-size:20px" value="<?php echo $signup_array["set_id"] ?>">
                                <br/>
                                <span style="color:red;"><?php echo $signup_array["errid"] ?></span>
                            </td>
            			</tr>
            			<tr>
            				<td>
                               	 <span style="color:red;">*</span>密碼：
                            </td>
                            <td align="left">
                                <input type="password" name="set_pwd" style="font-size:20px" value="<?php echo $signup_array["set_pwd"] ?>" required>
                                <br/>
                                <span style="color:red;"><?php echo $signup_array["errpwd"] ?></span>
                            </td>
            			</tr>
            			<tr>
            				<td>
                               	 <span style="color:red;">*</span>確認密碼：
                            </td>
                            <td align="left">
                                <input type="password" name="check_pwd" style="font-size:20px" value="<?php echo $signup_array["check_pwd"] ?>" required>
                                <br/>
                                <span style="color:red;"><?php echo $signup_array["errckpwd"] ?></span>
                            </td>
            			</tr>
            			<tr>
            				<td>
                               	 您的暱稱：
                            </td>
                            <td align="left">
                                <input type="text" name="set_nickname" style="font-size:20px">
                                <br/>
                                	若未輸入則以帳號為暱稱
                            </td>
            			</tr>
            			<tr>
            				<td>
                               	 <span style="color:red;">*</span>email：
                            </td>
                            <td align="left">
                                <input type="email" name="set_email" style="font-size:20px" value="<?php echo $signup_array["set_email"] ?>" required>
                            </td>
            			</tr>
            		</table>
            		<br/>
            		<button type="submit">註冊</button>
        		</form>
    	<?php 
        }
    }
}

class ModifyMyDataView {
    function __construct(&$mb_array) {
        if(isset($mb_array["success"]) && $mb_array["success"] == 1){?>
        	<h2>修改成功!!</h2>
  <?php     header("Refresh: 1; URL=index.php");
        }else{?>
        	<h2>會員專區</h2>
            <form action="index.php?action=modifyMyData" method="post">
            	<table class="cont_tb">
    				<tr>
    					<td colspan="2">
    						修改會員資料
    					</td>
    				</tr>
    				<tr>
    					<td>
    						email：
    					</td>
                        <td width="450">
                            <input type="text" name="new_email" value="<?php echo $mb_array["email"] ?>">
                        </td>
                    </tr>
    				<tr>
    					<td colspan="2">
        					<button type="submit">確認修改</button>
    					</td>
    				</tr>
    			</table>
    		</form>
    		<?php
        }
    }
}

class ModifyMyPwdView {
    function __construct(&$mb_array) {
        if(isset($mb_array["success"]) && $mb_array["success"] == 1){?>
        	<h2>修改密碼成功!!</h2>
  <?php     header("Refresh: 1; URL=index.php");
        }else{?>
        	<h2>會員專區</h2>
            <form id="modifyMyPwdForm" action="index.php?action=modifyMyPwd" method="post">
            	<table class="cont_tb">
    				<tr>
    					<td colspan="2">
    						修改密碼
    					</td>
    				</tr>
    				<tr>
    					<td width="150">
    						<span style="color:red;">*</span>目前密碼：
    					</td>
                        <td>
                            <input type="password" name="old_mb_pwd" required>
                            <br/>
                            <span style="color:red;"><?php echo $mb_array["erroldpwd"] ?></span>	
                        </td>
                    </tr>
                    <tr>
    					<td width="150">
    						<span style="color:red;">*</span>新密碼：
    					</td>
                        <td>
                            <input type="password" name="new_mb_pwd" required>
                            <br/>
                            <span style="color:red;"><?php echo $mb_array["errpwd"] ?></span>
                        </td>
                    </tr>
                    <tr>
    					<td width="150">
    						<span style="color:red;">*</span>確認新密碼：
    					</td>
                        <td>
                            <input type="password" name="new_check_pwd" required>
                            <br/>
                            <span style="color:red;"><?php echo $mb_array["errckpwd"] ?></span>
                        </td>
                    </tr>
    				<tr>
    					<td colspan="2" >
        					<button type="submit">確認修改密碼</button>
    					</td>
    				</tr>
    			</table>
    		</form>
    		<?php
        }
    }
}
class ListMyMsgView extends SearchListView {
    function __construct () {?>
        	<h2>會員專區-我的留言</h2>
		<?php
    }
}
?>