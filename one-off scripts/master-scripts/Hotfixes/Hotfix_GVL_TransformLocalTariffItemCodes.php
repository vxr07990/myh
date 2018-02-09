<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 11/17/2016
 * Time: 12:04 PM
 */
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$codeMap = [
'15BC' => 'X15B',
'30MC' => 'X30M',
'3PCOMMISS' => 'MISC',
'AB1' => 'XAB1',
'AB2' => 'XAB2',
'AB3' => 'XAB3',
'AB4' => 'XAB4',
'BC' => 'XBC',
'BU1' => 'XBU1',
'BU2' => 'XBU2',
'CAL' => 'XCAL',
'CB' => 'XCB',
'CBCS' => 'XCBC',
'CCU' => 'XCCU',
'CHGORDFIXD' => 'XCOF',
'CPK' => 'XCPK',
'CRG' => 'XCRG',
'CT' => 'XCT',
'CWT' => 'XCWT',
'DEADHEAD' => 'DEMI',
'DLS' => 'XDLS',
'DLT' => 'XDLT',
'DP' => 'XDP',
'DR' => 'ADDI',
'DSH' => 'XDSH',
'DT0' => 'XDT0',
'DT1' => 'XDT1',
'DT2' => 'XDT2',
'DT3' => 'XDT3',
'DT4' => 'XDT4',
'DT5' => 'XDT5',
'DT6' => 'XDT6',
'DT7' => 'XDT7',
'DT8' => 'XDT8',
'DT9' => 'XDT9',
'DW' => 'XDW',
'ERP' => 'XERP',
'EXST' => 'XSTP',
'FL' => 'XFL',
'FL2' => 'XFL2',
'FREEASTRAY' => 'GMIS',
'FS' => 'FUSU',
'GDL' => 'XGDL',
'INST' => 'XINS',
'ISA' => 'XISA',
'ITEC' => 'XITC',
'JB' => 'XJB',
'KBD' => 'XKBD',
'L' => 'XL',
'LABR' => 'XLBR',
'LB1' => 'XLB1',
'LB2' => 'XLB2',
'LC' => 'XLC',
'LC1' => 'XLC1',
'LC2' => 'XLC2',
'LD' => 'XLD',
'LINS' => 'XLIN',
'LL' => 'XLL',
'LTL' => 'XLTL',
'M' => 'XM',
'MC' => 'XMC',
'MCF' => 'XMCF',
'MISC' => 'XMIS',
'MOVR' => 'XMOV',
'NPP' => 'XNPP',
'OVDT' => 'XOVD',
'P' => 'XP',
'PALL' => 'XPAL',
'PASSTHRU' => 'FEBR',
'PC' => 'XPC',
'PCL' => 'XPCL',
'PCS' => 'XPCS',
'PDS' => 'XPDS',
'PDWC' => 'XPDW',
'PEA' => 'XPEA',
'PERDIEM' => 'MISC',
'PJE' => 'XPJE',
'PJM' => 'XPJM',
'PKR' => 'XPKR',
'PM' => 'XPM',
'PV' => 'XPV',
'RAL' => 'XRAL',
'RDL' => 'XRDL',
'RSC' => 'XRSC',
'RSH' => 'XRSH',
'RVPC' => 'XRVC',
'RVPS' => 'XRVS',
'S' => 'XS',
'SB' => 'XSB',
'SB1' => 'XSB1',
'SB2' => 'XSB2',
'SC' => 'XSC',
'SF' => 'XSF',
'SFBL' => 'XSFL',
'SFBM' => 'XSFM',
'SP' => 'XSP',
'SPECCOMP' => 'SPCT',
'ST' => 'XST',
'STAX' => 'SATA',
'STOT' => 'XSTT',
'SUPI' => 'XSUP',
'SUPP' => 'XSPP',
'SURVEY' => 'SURV',
'SW1' => 'XSW1',
'SW2' => 'XSW2',
'TAP1' => 'XTP1',
'TAP2' => 'XTP2',
'TAP3' => 'XTP3',
'TAP4' => 'XTP4',
'TAP6' => 'XTP6',
'TEX' => 'XTEX',
'TL' => 'XTL',
'TMPY' => 'XTMP',
'TONU' => 'XTNU',
'TP1' => 'X3P',
'TP10' => 'X3P',
'TP11' => 'X3P',
'TP12' => 'X3P',
'TP13' => 'X3P',
'TP14' => 'X3P',
'TP15' => 'X3P',
'TP16' => 'X3P',
'TP17' => 'X3P',
'TP18' => 'X3P',
'TP19' => 'X3P',
'TP2' => 'X3P',
'TP20' => 'X3P',
'TP21' => 'X3P',
'TP3' => 'X3P',
'TP4' => 'X3P',
'TP5' => 'X3P',
'TP6' => 'X3P',
'TP7' => 'X3P',
'TP8' => 'X3P',
'TP9' => 'X3P',
'TS' => 'XTS',
'TT' => 'XTT',
'UPKR' => 'XUPK',
'VLT' => 'XVLT',
'WB' => 'XWB',
'WFRK' => 'XWFO',
'WLAB' => 'XWLB',
'WSUP' => 'XWSP',
'ZLB' => 'XZLB',
'PSV' => 'XPSV',
'CCB' => 'XCCB',
'PCK' => 'XPCK',
'PCD' => 'XPCD',
'PCC' => 'XPCC',
'PFC' => 'XPFC',
'TP22' => 'X3P',
'PA' => 'XPA',
'BRF' => 'XBRF',
'CWP' => 'XCWP',
'MSK' => 'XMSK',
'PLT' => 'XPLT',
'PPR' => 'XPPR',
'PRT' => 'XPRT',
'SGB' => 'XSGB',
'SUH' => 'XSUH',
'SW3' => 'XSW3',
'WDB' => 'XWDB',
'CLS' => 'XCLS',
'CUW' => 'XCUW',
'MAD' => 'XMAD',
'SCD' => 'XSCD',
'SG' => 'XSG',
'SGI' => 'XSGI',
'VAC' => 'XVAC',
'DSP' => 'XDSP',
'CFT' => 'XCUF',
'SW4' => 'XSW3',
'COMP_TECH' => 'XCPT',
'AUTOCAD_OP' => 'XCAD',
'PLAN_SRVSD' => 'XPSD',
'PROJ_COOR' => 'XPCO',
'SR_PRJ_MGR' => 'XSPM',
'SPACE_PLAN' => 'XSPL'
];

$db = &PearDatabase::getInstance();

foreach ($codeMap as $from => $to) {
    $match = $from . ' - ';
    // update tariff services
    $res = $db->pquery('SELECT tariffservicesid,service_code FROM vtiger_tariffservices where service_line=?',
                       ['WPS']);
    while ($row = $res->fetchRow()) {
        if (strpos($row['service_code'], $match) === 0) {
            $dashIndex = strpos($row['service_code'], '-');
            $repl =  $to . ' - ' . substr($row['service_code'], $dashIndex + 2);
            $db->pquery('UPDATE vtiger_tariffservices SET service_code=? WHERE tariffservicesid=?',
                        [$repl, $row['tariffservicesid']]);
        }
    }
}

// update detailed line items for local tariffs
$res = $db->pquery('SELECT detaillineitemsid,dli_tariff_item_name FROM vtiger_quotes INNER JOIN vtiger_detailed_lineitems ON (dli_relcrmid=quoteid)
              WHERE NOT EXISTS (SELECT * FROM vtiger_tariffmanager WHERE tariffmanagerid=effective_tariff)');
while ($row = $res->fetchRow()) {
    $code = $row['dli_tariff_item_name'];
    if (array_key_exists($code, $codeMap)) {
        $code = $codeMap[$code];
        $db->pquery('UPDATE vtiger_detailed_lineitems SET dli_tariff_item_name=? WHERE detaillineitemsid=?',
                    [$code, $row['detaillineitemsid']]);
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";