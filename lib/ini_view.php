<?php 
function begin() {?>
<!DOCTYPE html><!-- html 5 文件類型聲明  -->
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>center88留言板</title>
        <link rel=stylesheet type="text/css" href="css/board.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/localization/messages_zh_TW.js"></script>
		<script>
		$().ready(function(){
            $("#postForm").validate();
            $("#modifyMyPwdForm").validate();
            $("#signupForm").validate({
            	//debug:true,
            	rules:{
                	set_id:{  
                        required:true,  
                        remote:{                         //自带远程验证存在的方法  
                            url:"tel.php",  				//太神奇啦??? 
                            type:"post",
                            data:{
                            	set_id:function(){
                                	return $("#set_id").val();
                            	}
							}
						}  
					}  
        		},
        		messages:{
    				set_id:{
    					remote:"帳號已被使用!"
    				}
    			}
            });
        });
        	
        </script>
        <style>
        .error{
        	color:red;
        }
        </style> 
    </head>

    <body>
        <div class="container">
<?php 
}

function sign() {
    if(empty($_SESSION["login_id"])){?>
        <div class="sign">
        	<a href="index.php?action=login">會員登入</a>
    	</div>
    	<?php 
    }else{?>
        <div class="sign">
        	<span>歡迎<?php echo $_SESSION["login_id"] ; ?></span>&nbsp;&nbsp;
       		<a href="index.php?action=logout">登出→</a>
        </div>
<?php }
}

function banner() {?>
	<div class="banner">
		<p><a href="index.php?action=list">center88留言板</a></p>
	</div>
<?php    
}

function sidebar() {
    if(isset($_SESSION["login_id"])){?>
	<div class="sidebar">
        <table class="bar_tb">
  			<tr>
                <td style="border-style:none;color:white;">
					---會員專區---
                </td>
            </tr>
            <tr>
                <td>
                    <a href="index.php?action=post">新增留言</a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href="index.php?action=modifyMyData">修改資料</a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href="index.php?action=modifyMyPwd">修改密碼</a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href="index.php?action=listMyMsg">我的留言</a>
                </td>
            </tr>
            <tr>
                <td>
                    <a href="index.php?action=searchlist">回首頁</a>
                </td>
            </tr>
        </table>
    </div>
<?php 
    }
}

function pleaseLogin() {?>
    <h2>請先登入!</h2>
    <button onclick="location.href='index.php?action=login'">登入</button>
<?php     
}

function contentStart() {
	if(isset($_SESSION["login_id"])){?>
    	<div class="login_content">
    <?php 
	}else{?>
	  	<div class="content">
	<?php  
	}
}

function contentEnd() {?>
    </div>
<?php     
}

function viewend() {?>
		</div>
	</body>
</html>
<?php 
}
?>
