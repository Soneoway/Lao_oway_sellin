<?php
for ($i=1; $i <= 5; $i++) {
    $command = sprintf("php -f /var/www/html/warehouse/bin/cli.php imei one num=%d", $i);
    exec( sprintf("$command > /var/www/html/warehouse/bin/log/log-%s.txt &", $i), $arrOutput );
}
