export VTIGERCRM_ROOTDIR=`dirname "$0"`/..
export USE_PHP=php

cd $VTIGERCRM_ROOTDIR
echo $VTIGERCRM_ROOTDIR
$USE_PHP -f exchangeAutoSync.php
