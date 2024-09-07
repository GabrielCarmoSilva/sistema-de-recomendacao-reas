<?php
while (true) {
    exec('php artisan queue:work --sleep=3 --tries=3');
    sleep(1); // Sleep for a while before restarting
}