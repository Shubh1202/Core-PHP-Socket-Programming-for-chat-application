<?php require("config.php"); ?>

<?php 

if(!empty($_POST["btn-submit"])){ 
    $phone = (isset($_SESSION["USER_PHONE"])) ? $_SESSION["USER_PHONE"] : $_POST["phone"];
    $name = $_POST["name"];
    $table = "users";

    if(!empty($phone)){
        $array = array("phone"=>$phone);
        $res = insertDataExist($table, $phone, true);
        if($res["status"]=="success"){
            insertData($table, $array);
            $_SESSION["USER_PHONE"] = $phone;
            header("location: index.php");
            die();
        }
    }

    if(!empty($name)){
        $array = array("name"=>$name);
        $res = insertDataExist($table, $phone, true);
        if($res["status"]=="error"){
            updateData($table, $array);
            $_SESSION["USER_NAME"] = $name;
            header("location: index.php");
            die();
        }
    }    

    $res = insertDataExist($table, $phone);
    if($res["status"]=="success"){
        $_SESSION["USER_ID"] = $res["data"]["id"];
        $_SESSION["USER_PHONE"] = $res["data"]["phone"];
        $_SESSION["USER_NAME"] = $res["data"]["name"];
        header("location: index.php");
        die();
    }
}

if(isset($_SESSION["USER_PHONE"]) && isset($_SESSION["USER_NAME"])){
    $phone = $_SESSION["USER_PHONE"];
    $id = $_SESSION["USER_ID"];
    $query = "SELECT * FROM `messages` WHERE `from_uxid`='$id'";
    $sql = mysqli_query($con, $query) or write_error("1102", "Data could not be fetch", "index.php");
    $res = mysqli_fetch_all($sql, MYSQLI_ASSOC);
    foreach($res as $value){
        $name = (trim($value["from_uxid"])==trim($id)) ? "You: " : "Admin: ";
        $name = "<b>$name</b>";
        $msg = $value["msg"];
        $pdata .= "<p>$name $msg</p>";
    }
}

if(isset($_GET["prx"]) && $_GET["prx"]=="disconnect"){
    session_destroy();
    session_unset();
    unset($_SESSION["USER_PHONE"]);
    header("location: index.php");
}


?>

<style>
p{
    margin:0;
    padding:0;
    box-sizing:border-box;
}
.logout, .user-info{
    margin:10px 0;
}
</style>

<?php if(!isset($_SESSION["USER_PHONE"]) || !isset($_SESSION["USER_NAME"]) ){ ?>

    <form method="post" action="" autocomplete="off">
        <?php if(!isset($_SESSION["USER_PHONE"])){ ?>
            <input type="tel" name="phone" value="" required maxlength="10" minlength="10" placeholder="Phone number..." autocomplete="off">
            <button type="submit" name="btn-submit" value="submit">Login</button>
        <?php } ?>

        <?php if(!isset($_SESSION["USER_NAME"]) && isset($_SESSION["USER_PHONE"])){ ?>
            <input type="text" name="name" value="" required placeholder="Your name.." autocomplete="off">
            <button type="submit" name="btn-submit" value="submit">Continue</button>
        <?php } ?>
        
    </form>
<?php } ?>


<?php if(isset($_SESSION["USER_PHONE"]) && isset($_SESSION["USER_NAME"])){ ?>
    <div class="user-info"><b>Welcome: </b><?php echo ucwords($_SESSION["USER_NAME"]); ?></div>
    <form method="post" action="" onSubmit="sendMsg()" autocomplete="off">
        <input type="text" name="msg" value="" required placeholder="Type here..." id="msg" focus="true" autocomplete="off">
        <button type="submit" name="btn-msg" value="submit">Send</button>
    </form>
    <div class="logout">
        <a href="?prx=disconnect">End chat</a>    
    </div>
    <div id="messages">
        <?php echo $pdata;?>
    </div>
    <script>
        async function sendMsg(){
            this.event.preventDefault();
            let msgBox = document.getElementById("msg");
            let msgData = msgBox.value;
            let dataObj = {prx:msgData};
            let messageData = "<p>Hllo</p>";
            let url = "http://localhost/chat/chat.php";

            let response = await fetch(url, {
                method:"POST",
                header: {
                    "Content-Type":"application/json",
                },
                body: JSON.stringify(dataObj),
            });
            let res = response.json();
            msgBox.value="";
            msgBox.focus();
            document.getElementById("messages").append(msgData+"\n");
        }
    </script>
<?php } ?>