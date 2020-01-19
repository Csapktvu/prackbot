<?php
define('BOT_TOKEN', '***TOKEN***');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');
function CsapktvuWebhook($method, $parameters) {
    if (!is_string($method)) {
        error_log("Method name must be a string\n");
        return false;
    }

    if (!$parameters) {
        $parameters = array();
    } else if (!is_array($parameters)) {
        error_log("Parameters must be an array\n");
        return false;
    }

    $parameters["method"] = $method;

    header("Content-Type: application/json");
    echo json_encode($parameters);
    return true;
}

function exec_curl_request($handle) {
    $response = curl_exec($handle);

    if ($response === false) {
        $errno = curl_errno($handle);
        $error = curl_error($handle);
        error_log("Curl returned error $errno: $error\n");
        curl_close($handle);
        return false;
    }

    $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
    curl_close($handle);

    if ($http_code >= 500) {
        sleep(10);
        return false;
    } else if ($http_code != 200) {
        $response = json_decode($response, true);
        error_log("Request has failed with error {$response['error_code']}: {$response['description']}\n");
        if ($http_code == 401) {
            throw new Exception('Invalid access token provided');
        }
        return false;
    } else {
        $response = json_decode($response, true);
        if (isset($response['description'])) {
            error_log("Request was successfull: {$response['description']}\n");
        }
        $response = $response['result'];
    }

    return $response;
}

function Csapktvu($method, $parameters) {
    if (!is_string($method)) {
        error_log("Method name must be a string\n");
        return false;
    }

    if (!$parameters) {
        $parameters = array();
    } else if (!is_array($parameters)) {
        error_log("Parameters must be an array\n");
        return false;
    }

    foreach ($parameters as $key => &$val) {
        if (!is_numeric($val) && !is_string($val)) {
            $val = json_encode($val);
        }
    }
    $url = API_URL.$method.'?'.http_build_query($parameters);

    $handle = curl_init($url);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($handle, CURLOPT_TIMEOUT, 60);

    return exec_curl_request($handle);
}

function CsapktvuJson($method, $parameters) {
    if (!is_string($method)) {
        error_log("Method name must be a string\n");
        return false;
    }

    if (!$parameters) {
        $parameters = array();
    } else if (!is_array($parameters)) {
        error_log("Parameters must be an array\n");
        return false;
    }

    $parameters["method"] = $method;

    $handle = curl_init(API_URL);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($handle, CURLOPT_TIMEOUT, 60);
    curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($parameters));
    curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

    return exec_curl_request($handle);
}
function processMessage($message) {

    $boolean = file_get_contents('booleans.txt');
    $booleans= explode("\n",$boolean);
    $vahid = "976338746";
    $message_id = $message['message_id'];
    $rpto = $message['reply_to_message']['forward_from']['id'];
    $chat_id = $message['chat']['id'];
    $mtnha = file_get_contents('msgs.txt');
    $zorat= explode("-!-@-#-$",$mtnha);

  $inlinebtn = json_encode([
    'inline_keyboard'=>[
    [['text'=>'انجمن علمی گروه کامپیوتر','url'=>'http://telegram.me/Csapktvu']]
  ]
  ]);
    if (isset($message['photo'])) {

        if ( $chat_id != $vahid) {

            $txt = file_get_contents('banlist.txt');
            $membersid= explode("\n",$txt);

            $substr = substr($text, 0, 28);
            if (!in_array($chat_id,$membersid)) {
                Csapktvu("forwardMessage", array('chat_id' => $vahid,  "from_chat_id"=> $chat_id ,"message_id" => $message_id));
                Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => $zorat[1] ,"parse_mode" =>"HTML"));
            }else{

                Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => "شما مسدود شده اید | پیام های شما منتقل نمیشوند." ,"parse_mode" =>"HTML"));

            }
        }
        else if($rpto !="" && $chat_id==$vahid){
            $photo = $message['photo'];
            $photoid = json_encode($photo, JSON_PRETTY_PRINT);
            $photoidd = json_encode($photoid, JSON_PRETTY_PRINT);
            $photoidd = str_replace('"[\n    {\n        \"file_id\": \"','',$photoidd);
            $pos = strpos($photoidd, '",\n');
            $pos = $pos -1;
            $substtr = substr($photoidd, 0, $pos);
            $caption = $message['caption'];
            if($caption != "")
            {
                Csapktvu("sendphoto", array('chat_id' => $rpto, "photo" => $substtr,"caption" =>$caption));
            }
            else{
                Csapktvu("sendphoto", array('chat_id' => $rpto, "photo" => $substtr));
            }
            Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => "پیام شما ارسال شد" ,"parse_mode" =>"HTML"));

        }  else if ($chat_id == $vahid && $booleans[0] == "true") {

            $photo = $message['photo'];
            $photoid = json_encode($photo, JSON_PRETTY_PRINT);
            $photoidd = json_encode($photoid, JSON_PRETTY_PRINT);
            $photoidd = str_replace('"[\n    {\n        \"file_id\": \"','',$photoidd);
            $pos = strpos($photoidd, '",\n');
            $pos = $pos -1;
            $substtr = substr($photoidd, 0, $pos);
            $caption = $message['caption'];


            $prackbot = file_get_contents('pmembers.txt');
            $membersidd= explode("\n",$prackbot);
            for($y=0;$y<count($membersidd);$y++){
                if($caption != "")
                {
                    Csapktvu("sendphoto", array('chat_id' => $membersidd[$y], "photo" => $substtr,"caption" =>$caption));
                }
                else{
                    Csapktvu("sendphoto", array('chat_id' => $membersidd[$y], "photo" => $substtr));
                }

            }
            $memcout = count($membersidd)-1;
            Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => "پیام شما به ".$memcout." مخاطب ازسال شد.
.","parse_mode" =>"HTML",'reply_markup' => array(
                'keyboard' => array(array('ارسال پیام دسته جمعی'),array('راهنما ربات','اعضای ربات','لیست بلاک'),array("تنظیمات ربات")),
                'one_time_keyboard' => true,
                'selective' => true,
                'resize_keyboard' => true)));
            $addd = "false";
            file_put_contents('booleans.txt',$addd);
        }
    }
    if (isset($message['video'])) {

        if ( $chat_id != $vahid) {

            $txt = file_get_contents('banlist.txt');
            $membersid= explode("\n",$txt);

           $substr = substr($text, 0, 28);
            if (!in_array($chat_id,$membersid)) {
                Csapktvu("forwardMessage", array('chat_id' => $vahid,  "from_chat_id"=> $chat_id ,"message_id" => $message_id));
                Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => $zorat[1],"parse_mode" =>"HTML"));
            }else{

                Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => "شما مسدود شده اید | پیام های شما منتقل نمیشوند." ,"parse_mode" =>"HTML"));

            }
        }
        else if($rpto !="" && $chat_id==$vahid){
            $video = $message['video']['file_id'];
            $caption = $message['caption'];
            //Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => $video ,"parse_mode" =>"HTML"));
            if($caption != "")
            {
                Csapktvu("sendvideo", array('chat_id' => $rpto, "video" => $video,"caption" =>$caption));
            }
            else{
                Csapktvu("sendvideo", array('chat_id' => $rpto, "video" => $video));
            }
            Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" =>"پیام شما ارسال شد","parse_mode" =>"HTML"));

        }
        else if ($chat_id == $vahid && $booleans[0] == "true") {
            $video = $message['video']['file_id'];
            $caption = $message['caption'];
            $prackbot = file_get_contents('pmembers.txt');
            $membersidd= explode("\n",$prackbot);
            for($y=0;$y<count($membersidd);$y++){
                if($caption != "")
                {
                    Csapktvu("sendvideo", array('chat_id' => $membersidd[$y], "video" => $video,"caption" =>$caption));
                }
                else{
                    Csapktvu("sendvideo", array('chat_id' => $membersidd[$y], "video" => $video));
                }
            }
            $memcout = count($membersidd)-1;
            Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => "پیام شما به ".$memcout." مخاطب ازسال شد.
.","parse_mode" =>"HTML",'reply_markup' => array(
                'keyboard' => array(array('ارسال پیام دسته جمعی'),array('راهنما ربات','اعضای ربات','لیست بلاک'),array("تنظیمات ربات")),
                'one_time_keyboard' => true,
                'selective' => true,
                'resize_keyboard' => true)));
            $addd = "false";
            file_put_contents('booleans.txt',$addd);
        }
    }
    if (isset($message['sticker'])) {

        if ( $chat_id != $vahid) {

            $txt = file_get_contents('banlist.txt');
            $membersid= explode("\n",$txt);

            $substr = substr($text, 0, 28);
            if (!in_array($chat_id,$membersid)) {
                Csapktvu("forwardMessage", array('chat_id' => $vahid,  "from_chat_id"=> $chat_id ,"message_id" => $message_id));
                Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => $zorat[1] ,"parse_mode" =>"HTML"));
            }else{

                Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => "شما مسدود شده اید | پیام های شما منتقل نمیشوند." ,"parse_mode" =>"HTML"));

            }
        }
        else if($rpto !="" && $chat_id==$vahid){
            $sticker = $message['sticker']['file_id'];

            Csapktvu("sendsticker", array('chat_id' => $rpto, "sticker" => $sticker));
            Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" =>"پیام شما ارسال شد" ,"parse_mode" =>"HTML"));

        }

        else if ($chat_id == $vahid && $booleans[0] == "true") {
            $sticker = $message['sticker']['file_id'];
            $prackbot = file_get_contents('pmembers.txt');
            $membersidd= explode("\n",$prackbot);
            for($y=0;$y<count($membersidd);$y++){
                //Csapktvu("sendMessage", array('chat_id' => $membersidd[$y], "text" => $texttoall,"parse_mode" =>"HTML"));

                Csapktvu("sendsticker", array('chat_id' => $membersidd[$y], "sticker" => $sticker));



            }
            $memcout = count($membersidd)-1;
            Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => "پیام شما به ".$memcout." مخاطب ازسال شد.
.","parse_mode" =>"HTML",'reply_markup' => array(
                'keyboard' => array(array('ارسال پیام دسته جمعی'),array('راهنما ربات','اعضای ربات','لیست بلاک'),array("تنظیمات ربات")),
                'one_time_keyboard' => true,
                'selective' => true,
                'resize_keyboard' => true)));
            $addd = "false";
            file_put_contents('booleans.txt',$addd);
        }
    }



    if (isset($message['document'])) {

        if ( $chat_id != $vahid) {

            $txt = file_get_contents('banlist.txt');
            $membersid= explode("\n",$txt);

            $substr = substr($text, 0, 28);
            if (!in_array($chat_id,$membersid)) {
                Csapktvu("forwardMessage", array('chat_id' => $vahid,  "from_chat_id"=> $chat_id ,"message_id" => $message_id));
                Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => $zorat[1],"parse_mode" =>"HTML"));
            }else{

                Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => "شما مسدود شده اید | پیام های شما منتقل نمیشوند." ,"parse_mode" =>"HTML"));

            }
        }
        else if($rpto !="" && $chat_id==$vahid){
            $video = $message['document']['file_id'];
            $caption = $message['caption'];
            //Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => $video ,"parse_mode" =>"HTML"));
            if($caption != "")
            {
                Csapktvu("sendDocument", array('chat_id' => $rpto, "document" => $video,"caption" =>$caption));
            }
            else{
                Csapktvu("sendDocument", array('chat_id' => $rpto, "document" => $video));
            }
            Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => "پیام شما ارسال شد" ,"parse_mode" =>"HTML"));

        }
        else if ($chat_id == $vahid && $booleans[0] == "true") {
            $video = $message['document']['file_id'];
            $prackbot = file_get_contents('pmembers.txt');
            $membersidd= explode("\n",$prackbot);
            for($y=0;$y<count($membersidd);$y++){

                Csapktvu("sendDocument", array('chat_id' => $membersidd[$y], "document" => $video));



            }
            $memcout = count($membersidd)-1;
            Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => "پیام شما به ".$memcout." مخاطب ازسال شد.
.","parse_mode" =>"HTML",'reply_markup' => array(
                'keyboard' => array(array('ارسال پیام دسته جمعی'),array('راهنما ربات','اعضای ربات','لیست بلاک'),array("تنظیمات ربات")),
                'one_time_keyboard' => true,
                'selective' => true,
                'resize_keyboard' => true)));
            $addd = "false";
            file_put_contents('booleans.txt',$addd);
        }
    }
    if (isset($message['voice'])) {

        if ( $chat_id != $vahid) {

            $txt = file_get_contents('banlist.txt');
            $membersid= explode("\n",$txt);

            $substr = substr($text, 0, 28);
            if (!in_array($chat_id,$membersid)) {
                Csapktvu("forwardMessage", array('chat_id' => $vahid,  "from_chat_id"=> $chat_id ,"message_id" => $message_id));
                Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => $zorat[1] ,"parse_mode" =>"HTML"));
            }else{

                Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => "شما مسدود شده اید | پیام های شما منتقل نمیشوند." ,"parse_mode" =>"HTML"));

            }
        }
        else if($rpto !="" && $chat_id==$vahid){
            $video = $message['voice']['file_id'];
            $caption = $message['caption'];
            //Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => $video ,"parse_mode" =>"HTML"));
            if($caption != "")
            {
                Csapktvu("sendVoice", array('chat_id' => $rpto, "voice" => $video,"caption" =>$caption));
            }
            else{
                Csapktvu("sendVoice", array('chat_id' => $rpto, "voice" => $video));
            }
            Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" =>"پیام شما ارسال شد","parse_mode" =>"HTML"));

        }
        else if ($chat_id == $vahid && $booleans[0] == "true") {
            $video = $message['voice']['file_id'];
            $prackbot = file_get_contents('pmembers.txt');		$membersidd= explode("\n",$prackbot);
            for($y=0;$y<count($membersidd);$y++){

                Csapktvu("sendVoice", array('chat_id' => $membersidd[$y], "voice" => $video));
            }
            $memcout = count($membersidd)-1;
            Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => "پیام شما به ".$memcout." مخاطب ازسال شد.
.","parse_mode" =>"HTML",'reply_markup' => array(
                'keyboard' => array(array('ارسال پیام دسته جمعی'),array('راهنما ربات','اعضای ربات','لیست بلاک'),array("تنظیمات ربات")),
                'one_time_keyboard' => true,
                'selective' => true,
                'resize_keyboard' => true)));
            $addd = "false";
            file_put_contents('booleans.txt',$addd);
        }
    }
    if (isset($message['audio'])) {

        if ( $chat_id != $vahid) {

            $txt = file_get_contents('banlist.txt');
            $membersid= explode("\n",$txt);

            $substr = substr($text, 0, 28);
            if (!in_array($chat_id,$membersid)) {
                Csapktvu("forwardMessage", array('chat_id' => $vahid,  "from_chat_id"=> $chat_id ,"message_id" => $message_id));
                Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => $zorat[1] ,"parse_mode" =>"HTML"));
            }else{

                Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => "شما مسدود شده اید | پیام های شما منتقل نمیشوند." ,"parse_mode" =>"HTML"));

            }
        }
        else if($rpto !="" && $chat_id==$vahid){
            $video = $message['audio']['file_id'];
            $caption = $message['caption'];
            //Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => $video ,"parse_mode" =>"HTML"));
            if($caption != "")
            {
                Csapktvu("sendaudio", array('chat_id' => $rpto, "audio" => $video,"caption" =>$caption));
            }
            else{
                Csapktvu("sendaudio", array('chat_id' => $rpto, "audio" => $video));
            }
            Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => "پیام شما ارسال شد" ,"parse_mode" =>"HTML"));

        }
        else if ($chat_id == $vahid && $booleans[0] == "true") {
            $video = $message['audio']['file_id'];
            $prackbot = file_get_contents('pmembers.txt');
            $membersidd= explode("\n",$prackbot);
            for($y=0;$y<count($membersidd);$y++){

                Csapktvu("sendaudio", array('chat_id' => $membersidd[$y], "audio" => $video));

            }
            $memcout = count($membersidd)-1;
            Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => "پیام شما به ".$memcout." مخاطب ازسال شد.
.","parse_mode" =>"HTML",'reply_markup' => array(
                'keyboard' => array(array('ارسال پیام دسته جمعی'),array('راهنما ربات','اعضای ربات','لیست بلاک'),array("تنظیمات ربات")),
                'one_time_keyboard' => true,
                'selective' => true,
                'resize_keyboard' => true)));
            $addd = "false";
            file_put_contents('booleans.txt',$addd);
        }
    }
    if (isset($message['contact'])) {

        if ( $chat_id != $vahid) {

            $txt = file_get_contents('banlist.txt');
            $membersid= explode("\n",$txt);

            $substr = substr($text, 0, 28);
            if (!in_array($chat_id,$membersid)) {
                Csapktvu("forwardMessage", array('chat_id' => $vahid,  "from_chat_id"=> $chat_id ,"message_id" => $message_id));
                Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => $zorat[1] ,"parse_mode" =>"HTML"));
            }else{

                Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => "شما مسدود شده اید | پیام های شما منتقل نمیشوند." ,"parse_mode" =>"HTML"));

            }
        }
        else if($rpto !="" && $chat_id==$vahid){
            $phone = $message['contact']['phone_number'];
            $first = $message['contact']['first_name'];

            $last = $message['contact']['last_name'];

            //Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => $video ,"parse_mode" =>"HTML"));

            Csapktvu("sendcontact", array('chat_id' => $rpto, "phone_number" => $phone,"Last_name" =>$last,"first_name"=> $first));

            Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" =>"پیام شما ارسال شد","parse_mode" =>"HTML"));

        }
        else if ($chat_id == $vahid && $booleans[0] == "true") {
            $phone = $message['contact']['phone_number'];
            $first = $message['contact']['first_name'];

            $last = $message['contact']['last_name'];
            $prackbot = file_get_contents('pmembers.txt');
            $membersidd= explode("\n",$prackbot);
            for($y=0;$y<count($membersidd);$y++){

                Csapktvu("sendcontact", array('chat_id' => $membersidd[$y], "phone_number" => $phone,"Last_name" =>$last,"first_name"=> $first));

            }
            $memcout = count($membersidd)-1;
            Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => "پیام شما به ".$memcout." مخاطب ازسال شد.
.","parse_mode" =>"HTML",'reply_markup' => array(
                'keyboard' => array(array('ارسال پیام دسته جمعی'),array('راهنما ربات','اعضای ربات','لیست بلاک'),array("تنظیمات ربات")),
                'one_time_keyboard' => true,
                'selective' => true,
                'resize_keyboard' => true)));
            $addd = "false";
            file_put_contents('booleans.txt',$addd);
        }
    }




    if (isset($message['text'])) {
        // incoming text message
        $text = $message['text'];
        $matches = explode(" ", $text);
        if ($text=="/start") {



            if($chat_id!=$vahid){
                Csapktvu("sendMessage", array('chat_id' => $chat_id,"text"=>$zorat[0] ,"parse_mode"=>"HTML"));

                $txxt = file_get_contents('pmembers.txt');
                $pmembersid= explode("\n",$txxt);
                if (!in_array($chat_id,$pmembersid)) {
                    $aaddd = file_get_contents('pmembers.txt');
                    $aaddd .= $chat_id."
";
                    file_put_contents('pmembers.txt',$aaddd);
                }

            }
            if($chat_id==$vahid){
                CsapktvuJson("sendMessage", array('chat_id' => $chat_id, "text" => 'سلام ادمین',"parse_mode"=>"MARKDOWN", 'reply_markup' => array(
                    'keyboard' => array(array('ارسال پیام دسته جمعی'),array('راهنما ربات','اعضای ربات','لیست بلاک'),array("تنظیمات ربات")),
                    'one_time_keyboard' => true,
                    'selective' => true,
                    'resize_keyboard' => true)));
            }

        } else if ($matches[0] == "/setstart" && $chat_id == $vahid) {

            $starttext = str_replace("/setstart","",$text);

            file_put_contents('msgs.txt',$starttext."

-!-@-#-$"."
".$zorat[1]);
            CsapktvuJson("sendMessage", array('chat_id' => $chat_id, "text" =>"پیام خوش آمد گویی به 

".$starttext.""."

تغییر کرد
."));




        }
        else if ($matches[0] == "/setdone" && $chat_id == $vahid) {

            $starttext = str_replace("/setdone","",$text);

            file_put_contents('msgs.txt',$zorat[0]."

-!-@-#-$"."
".$starttext);
            CsapktvuJson("sendMessage", array('chat_id' => $chat_id, "text" =>"پیام پیش فرض به

".$starttext.""."

تغییر کرد
."));




        }
        else if ($text != "" && $chat_id != $vahid) {

            $txt = file_get_contents('banlist.txt');
            $membersid= explode("\n",$txt);

            $substr = substr($text, 0, 28);
            if (!in_array($chat_id,$membersid)) {
                Csapktvu("forwardMessage", array('chat_id' => $vahid,  "from_chat_id"=> $chat_id ,"message_id" => $message_id));
                Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" =>$zorat[1] ,"parse_mode" =>"HTML"));

            }else{
                if($substr !="thisisnarimanfrombeatbotteam"){
                    Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => "شما مسدود شده اید | پیام های شما منتقل نمیشوند." ,"parse_mode" =>"HTML"));
                }
                else{
                    $textfa =str_replace("thisisnarimanfrombeatbotteam","🖕",$text);;
                    Csapktvu("sendMessage", array('chat_id' => $vahid, "text" =>  $textfa,"parse_mode" =>"HTML"));
                    Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => $zorat[1] ,"parse_mode" =>"HTML"));

                }
            }


        }else if ($text == "تنظیمات ربات" && $chat_id==$vahid) {


            CsapktvuJson("sendMessage", array('chat_id' => $chat_id,"parse_mode"=>"HTML", "text" => 'تنظیمات اظافه نشده است', 'reply_markup' => array(
                'keyboard' => array(array('پاک کردن تمام اعضا','پاک کردن تمامی مسدودی ها'),array('برگشت')),
                'one_time_keyboard' => true,
                'selective' => true,
                'resize_keyboard' => true)));



        }else if ($text == "راهنما ربات" && $chat_id==$vahid) {

            Csapktvu("sendMessage", array('chat_id' => $vahid, "text" => "راهنما اضافه نشده است","parse_mode" =>"MARKDOWN",'reply_markup' => array(
                'keyboard' => array(array('ارسال پیام دسته جمعی'),array('راهنما ربات','اعضای ربات','لیست بلاک'),array("تنظیمات ربات")),
                'one_time_keyboard' => true,
                'selective' => true,
                'resize_keyboard' => true)));

        }else if ($text == "پاک کردن تمام اعضا" && $chat_id==$vahid) {


            $txxt = file_get_contents('pmembers.txt');
            $pmembersid= explode("\n",$txxt);
            file_put_contents('pmembers.txt',"");
            CsapktvuJson("sendMessage", array('chat_id' => $chat_id,"parse_mode"=>"HTML", "text" => 'لیست مخاطبین پاک شد', 'reply_markup' => array(
                'keyboard' => array(array('ارسال پیام دسته جمعی'),array('راهنما ربات','اعضای ربات','لیست بلاک'),array("تنظیمات ربات")),
                'one_time_keyboard' => true,
                'selective' => true,
                'resize_keyboard' => true)));
        }
        else if ($text == "پاک کردن تمامی مسدودی ها" && $chat_id==$vahid) {


            $txxt = file_get_contents('banlist.txt');
            $pmembersid= explode("\n",$txxt);
            file_put_contents('banlist.txt',"");
            CsapktvuJson("sendMessage", array('chat_id' => $chat_id,"parse_mode"=>"HTML", "text" => 'لیست سیاه پاک شد', 'reply_markup' => array(
                'keyboard' => array(array('ارسال پیام دسته جمعی'),array('راهنما ربات','اعضای ربات','لیست بلاک'),array("تنظیمات ربات")),
                'one_time_keyboard' => true,
                'selective' => true,
                'resize_keyboard' => true)));
        }
        else if ($text == "برگشت" && $chat_id==$vahid) {
            CsapktvuJson("sendMessage", array('chat_id' => $chat_id, "text" => 'سلام ادمین', 'reply_markup' => array(
                'keyboard' => array(array('ارسال پیام دسته جمعی'),array('راهنما ربات','اعضای ربات','لیست بلاک'),array("تنظیمات ربات")),
                'one_time_keyboard' => true,
                'selective' => true,
                'resize_keyboard' => true)));



        }
        else if ($text =="ارسال پیام دسته جمعی"  && $chat_id == $vahid && $booleans[0]=="false") {
            Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => "پیام خود را ارسال کنید" ,"parse_mode" =>"HTML"));
            $boolean = file_get_contents('booleans.txt');
            $booleans= explode("\n",$boolean);
            $addd = file_get_contents('banlist.txt');
            $addd = "true";
            file_put_contents('booleans.txt',$addd);

        }
        else if ($chat_id == $vahid && $booleans[0] == "true") {
            $texttoall =$text;
            $prackbot = file_get_contents('pmembers.txt');
            $membersidd= explode("\n",$prackbot);
            for($y=0;$y<count($membersidd);$y++){
                Csapktvu("sendMessage", array('chat_id' => $membersidd[$y], "text" => $texttoall,"parse_mode" =>"HTML"));
            }
            $memcout = count($membersidd)-1;
            Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => "پیام شما به ".$memcout." مخاطب ازسال شد.
.","parse_mode" =>"HTML",'reply_markup' => array(
                'keyboard' => array(array('ارسال پیام دسته جمعی'),array('راهنما ربات','اعضای ربات','لیست بلاک'),array("تنظیمات ربات")),
                'one_time_keyboard' => true,
                'selective' => true,
                'resize_keyboard' => true)));
            $addd = "false";
            file_put_contents('booleans.txt',$addd);
        }else if($text == "اعضای ربات" && $chat_id == $vahid ){
            $txtt = file_get_contents('pmembers.txt');
            $membersidd= explode("\n",$txtt);
            $mmemcount = count($membersidd) -1;
            CsapktvuJson("sendMessage", array('chat_id' => $chat_id,"parse_mode" =>"HTML", "text" => "تعداد کل اعضا : ".$mmemcount,'reply_markup' => array(
                'keyboard' => array(array('ارسال پیام دسته جمعی'),array('راهنما ربات','اعضای ربات','لیست بلاک'),array("تنظیمات ربات")),
                'one_time_keyboard' => true,
                'selective' => true,
                'resize_keyboard' => true)));


        }else if($text == "لیست بلاک" && $chat_id == $vahid ){
            $txtt = file_get_contents('banlist.txt');
            $membersidd= explode("\n",$txtt);
            $mmemcount = count($membersidd) -1;
            CsapktvuJson("sendMessage", array('chat_id' => $chat_id,"parse_mode" =>"HTML", "text" => "  تعداد کل افرادی که در لیست سیاه قرار دارند : ".$mmemcount,'reply_markup' => array(
                'keyboard' => array(array('ارسال پیام دسته جمعی'),array('راهنما ربات','اعضای ربات','لیست بلاک'),array("تنظیمات ربات")),
                'one_time_keyboard' => true,
                'selective' => true,
                'resize_keyboard' => true)));


        }
        else if($rpto != "" && $chat_id == $vahid){
            if($text != "/ban" && $text != "/unban")
            {
                Csapktvu("sendMessage", array('chat_id' => $rpto, "text" => $text ,"parse_mode" =>"HTML"));
                Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => "پیام شما ارسال شد" ,"parse_mode" =>"HTML"));
            }
            else
            {
                if($text == "/ban"){
                    $txtt = file_get_contents('banlist.txt');
                    $banid= explode("\n",$txtt);
                    if (!in_array($rpto,$banid)) {
                        $addd = file_get_contents('banlist.txt');
                        $addd = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "", $addd);
                        $addd .= $rpto."
";

                        file_put_contents('banlist.txt',$addd);
                        Csapktvu("sendMessage", array('chat_id' => $rpto, "text" => "متاسفانه شما بلاک شده اید.
-----------------
(** از ارسال پیام خوداری کنید **)" ,"parse_mode" =>"HTML"));
                    }
                    Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => "Banned
➖➖➖➖➖➖➖➖➖➖➖
به لیست سیاه افزوده شد." ,"parse_mode" =>"HTML"));
                }
                if($text == "/unban"){
                    $txttt = file_get_contents('banlist.txt');
                    $banidd= explode("\n",$txttt);
                    if (in_array($rpto,$banidd)) {
                        $adddd = file_get_contents('banlist.txt');
                        $adddd = str_replace($rpto,"",$adddd);
                        $adddd = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "", $adddd);
                        $adddd .="
";


                        $banid= explode("\n",$adddd);
                        if($banid[1]=="")
                            $adddd = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "", $adddd);

                        file_put_contents('banlist.txt',$adddd);
                    }
                    Csapktvu("sendMessage", array('chat_id' => $chat_id, "text" => " 
  
از لیست سیاه پاک شد." ,"parse_mode" =>"HTML"));
                    Csapktvu("sendMessage", array('chat_id' => $rpto, "text" => "شما از لیست سیاه پاک شدید / قادر به ارسال پیام هستید" ,"parse_mode" =>"HTML"));
                }
            }
        }
    } else {

    }
}


define('WEBHOOK_URL', 'https://Csapktvu-elmi.ir/telegram/prackbot/index.php');

if (php_sapi_name() == 'cli') {
    Csapktvu('setWebhook', array('url' => isset($argv[1]) && $argv[1] == 'delete' ? '' : WEBHOOK_URL));
    exit;
}
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) {
    exit;
}

if (isset($update["message"])) {
    processMessage($update["message"]);
}