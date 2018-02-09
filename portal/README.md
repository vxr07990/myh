# README #

### CPtiger 1.0 ###

* Responsive Vtiger costumer portal
* Version 1.0

### How do I get set up? ###

* ~~Edit PortalConfig.php~~
* Set the `$Server_Path` variable in `PortalConfig.php`
* ~~Copy VTIGER_ROOTDIR content on vtiger installation folder~~
* Copy `VTIGER_ROOTDIR/soap/cpvtiger.php` to the `soap/` directory in the root of the vTiger install
* Merge the corresponding `$_REQUEST['service'] =="cpvtiger"` logic statement from `VTIGER_ROOTDIR/vtigerservice.php` into the `vtigerservice.php` of the host vTiger install
* Fix the unquoted instance of `$_REQUEST[service]` in the host `vtigerservice.php`
* Dependencies: vtiger 6.X 

Please look at http://cptiger.com for more details.
