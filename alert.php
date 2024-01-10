<?php
if(isset($messageError)){
   foreach($messageError as $message){
      echo '<div class="messageError" onclick="this.remove();">'.$message.'</div>';
   }
}
if(isset($messageSuccess)){
    foreach($messageSuccess as $message){
       echo '<div class="messageSuccess" onclick="this.remove();">'.$message.'</div>';
    }
}
?>