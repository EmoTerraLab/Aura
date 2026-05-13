<?php
echo "Current PHP user: " . posix_getpwuid(posix_geteuid())['name'] . "\n";
echo "Current script owner: " . get_current_user() . "\n";
