<?php
require_once('includes/main/WebUI.php');
require_once('include/Webservices/Create.php');
require_once('include/Webservices/Revise.php');
require_once('modules/Users/Users.php');
echo "Start Graebel Security Migration<br>\n";
//echo "Adding New Standard Profiles<br>\n";
//require_once('one-off scripts/master-scripts/Hotfixes/HotFix_UserProfilesAndRoles.php');
//echo "Setting Profile Permissions<br>\n";
//require_once('Profile_Permissions_Graebel.php');
$db = PearDatabase::getInstance();
$user         = new Users();
$current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
//set this to the appropriate vanline id for the vanline, for Graebel this is 2
$corporateVanlineId = 2;

echo "Clearing out broken fields<br>\n";
$sql = "SELECT * FROM `vtiger_field` AS `a` WHERE NOT EXISTS(SELECT * FROM (SELECT * FROM `vtiger_field` GROUP BY tabid, columnname, fieldname) AS `b` WHERE `a`.fieldid = `b`.fieldid)";
$result = $db->pquery($sql, []);
while ($row =& $result->fetchRow()) {
    $sql2 = "DELETE FROM `vtiger_field` WHERE fieldid = ?";
    echo "Deleting Duplicate Field : ".$row['fieldid']."<br>\n";
    $db->pquery($sql2, [$row['fieldid']]);
}
echo "Creating the Vanline<br>\n";
$sql = "SELECT vanline_id FROM `vtiger_vanlinemanager` WHERE vanline_id = 17";
$result = $db->pquery($sql, []);
$row = $result->fetchRow();
if ($row) {
    echo "Vanline already exists<br>\n";
} else {
    try {
        $data         = [
            "vanline_id"       => "17",
            "vanline_name"     => "Graebel Moving",
            "assigned_user_id" => "19x1",
            "local_report_url" =>"https://print.moverdocs.com/graebel/IGCReportingService.asmx?wsdl",
            "address1"         => "12790 Merit Drive",
            "address2"         => "",
            "city"             => "Dallas",
            "state"            => "TX",
            "zip"              => "75251",
            "country"          => "United States",
            "phone1"           => "5555555555"
        ];
        $newVanline   = vtws_create('VanlineManager', $data, $current_user);
    } catch (WebServiceException $ex) {
        echo "Error : Failed Creation of Vanline : ".$ex->getMessage();
        echo "<br>\n<br>\n";
    }
    echo "Vanline created<br>\n";
}
echo "Creating the Agents<br>\n";
$agents = [
    ["agency_name"=>"GVL Corporate",
     "address1"=>"12790 Merit Drive",
     "city"=>"Dallas",
     "state"=>"TX",
     "zip"=>"75251",
     "phone1"=>"5555555555",
     "fax"=>"5555555555",
     "email"=>"",
     "agency_code"=>"997"
    ],
    ["agency_name"=>"GVL Corporate HHG Sales Team",
     "address1"=>"12790 Merit Drive",
     "city"=>"Dallas",
     "state"=>"TX",
     "zip"=>"75251",
     "phone1"=>"5555555555",
     "fax"=>"5555555555",
     "email"=>"",
     "agency_code"=>"981"
    ],
    ["agency_name"=>"Graebel Eastern Movers, Inc.",
     "address1"=>"923 N. Lenola Rd.",
     "city"=>"Moorestown",
     "state"=>"NJ",
     "zip"=>"08057",
     "phone1"=>"8562359100",
     "fax"=>"8562739467",
     "email"=>"",
     "agency_code"=>"931"
    ],
    ["agency_name"=>"Graebel Northeastern Movers, Inc.",
     "address1"=>"9 Aspen Dr. Canfield Bus. Park II, Blk 18",
     "city"=>"Randolph",
     "state"=>"NJ",
     "zip"=>"07869",
     "phone1"=>"9732277310",
     "fax"=>"9732273905",
     "email"=>"",
     "agency_code"=>"933"
    ],
    ["agency_name"=>"Graebel New England Movers, Inc",
     "address1"=>"200 Danton Drive",
     "city"=>"Methuen",
     "state"=>"MA",
     "zip"=>"01844",
     "phone1"=>"9789833900",
     "fax"=>"9789833922",
     "email"=>"",
     "agency_code"=>"942"
    ],
    ["agency_name"=>"Graebel Connecticut Movers, Inc.",
     "address1"=>"33 Stiles Lane",
     "city"=>"North Haven",
     "state"=>"CT",
     "zip"=>"06473",
     "phone1"=>"2032888122",
     "fax"=>"2032887244",
     "email"=>"",
     "agency_code"=>"945"
    ],
    ["agency_name"=>"Graebel Mid Atlantic Movers, Inc.",
     "address1"=>"45180 Global Plaza",
     "city"=>"Sterling",
     "state"=>"VA",
     "zip"=>"20166",
     "phone1"=>"7033485050",
     "fax"=>"7033485059",
     "email"=>"",
     "agency_code"=>"946"
    ],
    ["agency_name"=>"Graebel N. Carolina Movers, Inc.",
     "address1"=>"2901 Stewart Creek Boulevard",
     "city"=>"Charlotte",
     "state"=>"NC",
     "zip"=>"28216",
     "phone1"=>"7043927057",
     "fax"=>"7043927562",
     "email"=>"",
     "agency_code"=>"952"
    ],
    ["agency_name"=>"Graebel Raleigh Movers, Inc.",
     "address1"=>"4234 Surles Court, Suite 300",
     "city"=>"Durham",
     "state"=>"NC",
     "zip"=>"27703",
     "phone1"=>"9194740525",
     "fax"=>"9194740529",
     "email"=>"",
     "agency_code"=>"953"
    ],
    ["agency_name"=>"Graebel S. Carolina Movers, Inc.",
     "address1"=>"One Brozinni Court Suite H",
     "city"=>"Greenville",
     "state"=>"SC",
     "zip"=>"29615",
     "phone1"=>"8646279600",
     "fax"=>"8646270960",
     "email"=>"",
     "agency_code"=>"965"
    ],
    ["agency_name"=>"Graebel Tampa Bay Movers, Inc.",
     "address1"=>"5250 Eagle Trail Drive",
     "city"=>"Tampa Bay",
     "state"=>"FL",
     "zip"=>"33634",
     "phone1"=>"8138848428",
     "fax"=>"8138863204",
     "email"=>"",
     "agency_code"=>"926"
    ],
    ["agency_name"=>"Graebel Orlando Movers, Inc.",
     "address1"=>"6800 Kingsporte Parkway",
     "city"=>"Orlando",
     "state"=>"FL",
     "zip"=>"32819",
     "phone1"=>"4078563700",
     "fax"=>"4074380191",
     "email"=>"",
     "agency_code"=>"944"
    ],
    ["agency_name"=>"Graebel S. Florida Movers, Inc",
     "address1"=>"2900 S.W. 15th Street",
     "city"=>"Deerfield Beach",
     "state"=>"FL",
     "zip"=>"33442",
     "phone1"=>"9543791113",
     "fax"=>"9543791114",
     "email"=>"",
     "agency_code"=>"959"
    ],
    ["agency_name"=>"Graebel Atlanta Movers, Inc",
     "address1"=>"5105 Avalon Ridge Parkway",
     "city"=>"Norcross",
     "state"=>"GA",
     "zip"=>"30071",
     "phone1"=>"7702636311",
     "fax"=>"7702421891",
     "email"=>"",
     "agency_code"=>"929"
    ],
    ["agency_name"=>"Graebel Tennessee Movers, Inc.",
     "address1"=>"225 Industrial Boulevard",
     "city"=>"LaVergne",
     "state"=>"TN",
     "zip"=>"37086",
     "phone1"=>"6157934600",
     "fax"=>"6157934610",
     "email"=>"",
     "agency_code"=>"949"
    ],
    ["agency_name"=>"Graebel Pittsburgh Movers, Inc.",
     "address1"=>"201 Center Avenue",
     "city"=>"Leetsdale",
     "state"=>"PA",
     "zip"=>"15056",
     "phone1"=>"7242519755",
     "fax"=>"7242519757",
     "email"=>"",
     "agency_code"=>"941"
    ],
    ["agency_name"=>"Graebel Cincinnati Movers, Inc.",
     "address1"=>"9085 Le Saint Drive",
     "city"=>"Fairfield",
     "state"=>"OH",
     "zip"=>"45014",
     "phone1"=>"5138744662",
     "fax"=>"5138604738",
     "email"=>"",
     "agency_code"=>"951"
    ],
    ["agency_name"=>"Graebel Moving and Storage, Inc.",
     "address1"=>"7333 West Stewart Avenue",
     "city"=>"Wausau",
     "state"=>"WI",
     "zip"=>"54402",
     "phone1"=>"7158454281",
     "fax"=>"7158429593",
     "email"=>"",
     "agency_code"=>"921"
    ],
    ["agency_name"=>"Graebel Moving and Warehouse Corp.",
     "address1"=>"1701 Airport Road",
     "city"=>"Waukesha",
     "state"=>"WI",
     "zip"=>"53188",
     "phone1"=>"2626501962",
     "fax"=>"2626501963",
     "email"=>"",
     "agency_code"=>"923"
    ],
    ["agency_name"=>"Graebel American Movers, Inc.",
     "address1"=>"1011 Asbury Drive",
     "city"=>"Buffalo Grove",
     "state"=>"IL",
     "zip"=>"60089",
     "phone1"=>"8478088400",
     "fax"=>"8478088450",
     "email"=>"",
     "agency_code"=>"925"
    ],
    ["agency_name"=>"Graebel Midwest Moving and Storage",
     "address1"=>"41345 Koppernick Rd",
     "city"=>"Canton",
     "state"=>"MI",
     "zip"=>"48187",
     "phone1"=>"7344557650",
     "fax"=>"",
     "email"=>"",
     "agency_code"=>"947"
    ],
    ["agency_name"=>"Graebel Indianapolis Movers, Inc.",
     "address1"=>"6751 East 30th Street, Suite C and D",
     "city"=>"Indianapolis",
     "state"=>"IN",
     "zip"=>"46220",
     "phone1"=>"3175425300",
     "fax"=>"3175425308",
     "email"=>"",
     "agency_code"=>"918"
    ],
    ["agency_name"=>"Graebel Kansas City Movers, Inc.",
     "address1"=>"9755 Commerce Parkway",
     "city"=>"Lenexa",
     "state"=>"KS",
     "zip"=>"66219",
     "phone1"=>"9138884554",
     "fax"=>"9138880377",
     "email"=>"",
     "agency_code"=>"940"
    ],
    ["agency_name"=>"Graebel St. Louis Movers, Inc",
     "address1"=>"3905 Ventures Way",
     "city"=>"Earth City",
     "state"=>"MO",
     "zip"=>"63045",
     "phone1"=>"3143449998",
     "fax"=>"3143444360",
     "email"=>"",
     "agency_code"=>"950"
    ],
    ["agency_name"=>"Graebel Minnesota Movers, Inc.",
     "address1"=>"945 Aldrin Drive",
     "city"=>"Eagan",
     "state"=>"MN",
     "zip"=>"55121",
     "phone1"=>"6516868000",
     "fax"=>"6516866946",
     "email"=>"",
     "agency_code"=>"802"
    ],
    ["agency_name"=>"Graebel CO. Springs Movers, Inc.",
     "address1"=>"615 Valley Street",
     "city"=>"Colorado Springs",
     "state"=>"CO",
     "zip"=>"80915",
     "phone1"=>"7195963306",
     "fax"=>"7195963493",
     "email"=>"",
     "agency_code"=>"904"
    ],
    ["agency_name"=>"Graebel Denver Movers, Inc.",
     "address1"=>"16456 E. Airport Circle",
     "city"=>"Aurora",
     "state"=>"CO",
     "zip"=>"80011",
     "phone1"=>"3032146600",
     "fax"=>"3032146969",
     "email"=>"",
     "agency_code"=>"938"
    ],
    ["agency_name"=>"Graebel Albuquerque",
     "address1"=>"702 Carmony NE",
     "city"=>"Albuqueque",
     "state"=>"NM",
     "zip"=>"87107",
     "phone1"=>"5058318000",
     "fax"=>"5058310111",
     "email"=>"",
     "agency_code"=>"902"
    ],
    ["agency_name"=>"Graebel Houston Movers, Inc.",
     "address1"=>"10901 Tanner Road",
     "city"=>"Houston",
     "state"=>"TX",
     "zip"=>"77041",
     "phone1"=>"7139371115",
     "fax"=>"7139376522",
     "email"=>"",
     "agency_code"=>"927"
    ],
    ["agency_name"=>"Graebel Dallas Movers, Inc.",
     "address1"=>"2660 Market Street",
     "city"=>"Garland",
     "state"=>"TX",
     "zip"=>"75041",
     "phone1"=>"9728648505",
     "fax"=>"9272711305",
     "email"=>"",
     "agency_code"=>"928"
    ],
    ["agency_name"=>"Graebel New Orleans, Inc.",
     "address1"=>"120 Production Drive",
     "city"=>"Slidell",
     "state"=>"LA",
     "zip"=>"70460",
     "phone1"=>"9856417600",
     "fax"=>"9856417706",
     "email"=>"",
     "agency_code"=>"937"
    ],
    ["agency_name"=>"Graebel Oklahoma Movers,Inc.",
     "address1"=>"420 S. 145th East Ave. Suite D",
     "city"=>"Tulsa",
     "state"=>"OK",
     "zip"=>"74108",
     "phone1"=>"9182509506",
     "fax"=>"9182507331",
     "email"=>"",
     "agency_code"=>"939"
    ],
    ["agency_name"=>"Graebel San Antonio Movers, Inc.",
     "address1"=>"6421 FM 3009 Ste. 200 Tri-Cty Dist. Ctr. # 2",
     "city"=>"Schertz",
     "state"=>"TX",
     "zip"=>"78154",
     "phone1"=>"2106374900",
     "fax"=>"2102257049",
     "email"=>"",
     "agency_code"=>"956"
    ],
    ["agency_name"=>"Graebel Lightning Movers, Inc.",
     "address1"=>"1120 N. 47th Avenue",
     "city"=>"Phoenix",
     "state"=>"AZ",
     "zip"=>"85043",
     "phone1"=>"6024470200",
     "fax"=>"6024470554",
     "email"=>"",
     "agency_code"=>"930"
    ],
    ["agency_name"=>"Graebel Los Angeles Movers, Inc.",
     "address1"=>"2095 California Avenue",
     "city"=>"Corona",
     "state"=>"CA",
     "zip"=>"92881",
     "phone1"=>"9512565400",
     "fax"=>"9517369262",
     "email"=>"",
     "agency_code"=>"932"
    ],
    ["agency_name"=>"Graebel Quality Movers",
     "address1"=>"21902 64th Ave. S.",
     "city"=>"Kent",
     "state"=>"WA",
     "zip"=>"98032",
     "phone1"=>"2533959766",
     "fax"=>"2533959766",
     "email"=>"",
     "agency_code"=>"828"
    ],
    ["agency_name"=>"Graebel Sacramento Movers, Inc.",
     "address1"=>"1760 Enterprise Blvd",
     "city"=>"West Sacramento",
     "state"=>"CA",
     "zip"=>"95691",
     "phone1"=>"9166172541",
     "fax"=>"9166172570",
     "email"=>"",
     "agency_code"=>"917"
    ],
    ["agency_name"=>"Graebel Erickson Movers, Inc.",
     "address1"=>"2020 South 10th Street",
     "city"=>"San Jose",
     "state"=>"CA",
     "zip"=>"95113",
     "phone1"=>"4087197400",
     "fax"=>"4087197425",
     "email"=>"",
     "agency_code"=>"935"
    ],
    ["agency_name"=>"Graebel Oregon Movers, Inc.",
     "address1"=>"26099 S.W. 95th Avenue, Suite 603",
     "city"=>"Wilsonville",
     "state"=>"OR",
     "zip"=>"97070",
     "phone1"=>"5035708080",
     "fax"=>"5035701198",
     "email"=>"",
     "agency_code"=>"960"
    ],
    ["agency_name"=>"Ace Moving & Storage Corp.",
     "address1"=>"4 Cave Hill Rd.",
     "city"=>"Carisle",
     "state"=>"PA",
     "zip"=>"17013",
     "phone1"=>"7172499933",
     "fax"=>"7172499891",
     "email"=>"",
     "agency_code"=>"751"
    ],
    ["agency_name"=>"Ace Moving & Storage Corp.",
     "address1"=>"125 Stewart Rd.",
     "city"=>"Wikes-Barre",
     "state"=>"PA",
     "zip"=>"18706",
     "phone1"=>"5708216112",
     "fax"=>"5708216115",
     "email"=>"",
     "agency_code"=>"752"
    ],
];
echo "Creating Agents<br>\n";
$sql = "SELECT vanlinemanagerid FROM `vtiger_vanlinemanager` WHERE vanline_id = ?";
$vanlineId = $db->pquery($sql, [17])->fetchRow()['vanlinemanagerid'];
foreach ($agents as $data) {
    echo "<br>\nCreating Agent : ".$data['agency_name']."(".$data['agency_code'].")<br>\n";
    $sql = "SELECT agency_code FROM `vtiger_agentmanager` WHERE agency_code = ?";
    $result = $db->pquery($sql, [$data['agency_code']]);
    $row = $result->fetchRow();
    if ($row) {
        echo "Agent : ".$data['agency_name']."(".$data['agency_code'].") already exists<br>\n";
    } else {
        $data['assigned_user_id'] = '19x1';
        $data['vanline_id']       = '38x'.$vanlineId;
        $data['email']            = $data['email']?:"support@igcsoftware.com";
        try {
            $newAgent = vtws_create('AgentManager', $data, $current_user);
            echo "Agent : ".$data['agency_name']."(".$data['agency_code'].") created successfully<br>\n<br>\n";
        } catch (WebServiceException $ex) {
            echo "Error : Failed Creation of Agent : ".$ex->getMessage();
            echo "<br>\n<br>\n";
        }
    }
}
$users = [
    [
        "User Name" => "jack.donnell@graebelmoving.com",
        "New Role" => "Parent Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "terry.carter@graebelmoving.com",
        "New Role" => "Parent Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "lisa.scobell@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "921",
    ],
    [
        "User Name" => "craig.jaeger@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "802",
    ],
    [
        "User Name" => "raylene.tetu@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "942",
    ],
    [
        "User Name" => "rayann.kelly@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "933",
    ],
    [
        "User Name" => "ray.ricci@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "944",
    ],
    [
        "User Name" => "patrick.spiegel@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "926",
    ],
    [
        "User Name" => "johnny.jiminez@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "929",
    ],
    [
        "User Name" => "dave.ciampi@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "933",
    ],
    [
        "User Name" => "christine.mills@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "925",
    ],
    [
        "User Name" => "chase.hymer@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "951",
    ],
    [
        "User Name" => "rachel.gigee@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "927",
    ],
    [
        "User Name" => "julie.schroeder@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "928",
    ],
    [
        "User Name" => "geff.moyer@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "940",
    ],
    [
        "User Name" => "leslie.brass@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "927",
    ],
    [
        "User Name" => "mark.kitchen@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "938",
    ],
    [
        "User Name" => "james.samuelsen@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "930",
    ],
    [
        "User Name" => "jay.jones@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "928",
    ],
    [
        "User Name" => "dave.sutton@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "932",
    ],
    [
        "User Name" => "brad.perry@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "960",
    ],
    [
        "User Name" => "beau.jess@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "828",
    ],
    [
        "User Name" => "thomas.silvius@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "935",
    ],
    [
        "User Name" => "jerry.ullman@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "950",
    ],
    [
        "User Name" => "tom.kelner@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "956",
    ],
    [
        "User Name" => "geoff.hartog@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "904",
    ],
    [
        "User Name" => "carley.crago@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "932",
    ],
    [
        "User Name" => "Frank.byrne@grabel.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "Scott.stallings@grabel.com",
        "New Role" => "Salesperson",
        "Branch" => "953",
    ],
    [
        "User Name" => "Elvis.machado@grabel.com",
        "New Role" => "Salesperson",
        "Branch" => "931",
    ],
    [
        "User Name" => "geoffery.dahill@graebel.com",
        "New Role" => "Salesperson",
        "Branch" => "945",
    ],
    [
        "User Name" => "Matthew.contrady@graebel.com",
        "New Role" => "Salesperson",
        "Branch" => "931",
    ],
    [
        "User Name" => "justin.page@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "929",
    ],
    [
        "User Name" => "Todd.Robinson@graebel.com",
        "New Role" => "Salesperson",
        "Branch" => "927",
    ],
    [
        "User Name" => "clint.beard@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "jared.lemcke@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "921,923,925,951,802,950",
    ],
    [
        "User Name" => "john.cummins@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "942,945,933,931,946",
    ],
    [
        "User Name" => "carrie.takamatsu@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "952,953,965,944,926,959,929,949",
    ],
    [
        "User Name" => "chris.well@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "940,939,928,927,956",
    ],
    [
        "User Name" => "Thomas.Stallart@graebel.com",
        "New Role" => "Sales Manager",
        "Branch" => "828,960,917,935,932,932,904,938",
    ],
    [
        "User Name" => "janelle.ungethum@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "terez.kinnick@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "debbie.slagoski@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "christine.kyhos@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "brianna.ullenbrauck@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "sue.blair@graebelmoving.com",
        "New Role" => "Parent Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "mark.walker@graebelmoving.com	",
        "New Role" => "Parent Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "kailin.zheng@graebelmoving.com",
        "New Role" => "Parent Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "dorrene.robert@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "daniel.escaloni@graebelmoving.com",
        "New Role" => "Parent Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "david.kramer@graebelmoving.com",
        "New Role" => "Parent Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "salesforceadmin@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "robert.salsa@graebelmoving.com",
        "New Role" => "Parent Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "Cress.Terrell@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "russ.medina@graebelmoving.com",
        "New Role" => "Parent Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "marianne.thiex@graebelmoving.com",
        "New Role" => "Parent Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "crystal.hoover@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "sue.fraser@graebelmoving.com",
        "New Role" => "Parent Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "rosie.beeber@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "debra.torrey@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "tina.bradfish@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "chris.volkmer@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "lou.tedesco@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "charles.canfield@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "jerry.nelms@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "dante.diaz@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "lisa.diaz@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "mark.shapard@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "roger.habeck@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "bill.borgman@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "stacey.froehlich@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "seth.bohne@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "Ginny.Steinhauer@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "Robin.Turton@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "rob.mckillips@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "950",
    ],
    [
        "User Name" => "matt.shea@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "946",
    ],
    [
        "User Name" => "craig.carver@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "981",
    ],
    [
        "User Name" => "steve.gavin@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "928",
    ],
    [
        "User Name" => "courtney.wright@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "bethany.steinbach@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "john.verrell@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "942",
    ],
    [
        "User Name" => "dale.traver@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "931",
    ],
    [
        "User Name" => "sara.mccaw@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "925",
    ],
    [
        "User Name" => "colin.holden@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "aric.anderson@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "928",
    ],
    [
        "User Name" => "cory.gilles@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "923",
    ],
    [
        "User Name" => "jason.studer@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "952",
    ],
    [
        "User Name" => "mia.erickson@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "802",
    ],
    [
        "User Name" => "wes.waddell@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "959",
    ],
    [
        "User Name" => "dave.marsden@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "925",
    ],
    [
        "User Name" => "teri.culver@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "949",
    ],
    [
        "User Name" => "mark.reineking@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "921",
    ],
    [
        "User Name" => "don.rupe@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "927",
    ],
    [
        "User Name" => "eric.smothers@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "939",
    ],
    [
        "User Name" => "tom.theisen@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "929",
    ],
    [
        "User Name" => "william.huntting@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "945",
    ],
    [
        "User Name" => "jacob.moreno@graebelmoving.com	",
        "New Role" => "Sales Manager",
        "Branch" => "935",
    ],
    [
        "User Name" => "gary.estep@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "930",
    ],
    [
        "User Name" => "wesly.rogers@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "950",
    ],
    [
        "User Name" => "tom.quinn@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "932",
    ],
    [
        "User Name" => "susan.vance@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "951",
    ],
    [
        "User Name" => "robin.pickard@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "953",
    ],
    [
        "User Name" => "michael.dimarzo@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "933",
    ],
    [
        "User Name" => "terry.hooker@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "940",
    ],
    [
        "User Name" => "william.burlison@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "828",
    ],
    [
        "User Name" => "Jeff.carr@graebelmoving.com	 ",
        "New Role" => "Sales Manager",
        "Branch" => "828",
    ],
    [
        "User Name" => "mark.mcewen@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "942",
    ],
    [
        "User Name" => "john.jones@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "925",
    ],
    [
        "User Name" => "phil.burton@graebel.com",
        "New Role" => "Sales Manager",
        "Branch" => "938",
    ],
    [
        "User Name" => "gregsr.cutlip@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "dan.dailey@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "dan.colleran@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "vince.rosauer@graebelmoving.com",
        "New Role" => "Child Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "charley.heaton@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "carl.berg@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "hw@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "michael.brenholt@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "peter.meiklejohn@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "michael.miles@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "aaron.newhouse@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "mike.jones@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "joe.grajewski@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "stephen.thompson@graebelmoving.com1",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "mike.moreno@graebelmoving.com1",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "kim.schiefelbein@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "charlie.reichert@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "victor.frazier@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "steve.cox@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "candy.rodriguez@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "greg.cutlipjr@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "dan.tanner@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "justin.cutlip@graebelmoving.com	",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "scott.schires@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "gregg.volmer@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "tom.martin@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "jay.eisele@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "sean.brady@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "jon.flanagan@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "edwin.correa@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "robert.werner@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "scott.burke@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "ryan.saylor@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "denise.rovetto@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "michael.duquette@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "debra.rochester@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "wes.cochran@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "solveyourmove@gmail.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "josh.humphreys@graebelmoving.com",
        "New Role" => "Please Delete",
        "Branch" => "",
    ],
    [
        "User Name" => "john.bernier@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "942,945,933,931,925,951,921,946,923,802",
    ],
    [
        "User Name" => "lori.gilroy@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "933",
    ],
    [
        "User Name" => "mark.kasprzak@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "923",
    ],
    [
        "User Name" => "david.esteppe@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "946",
    ],
    [
        "User Name" => "bart.harrison@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "946",
    ],
    [
        "User Name" => "matthew.beaupre@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "942",
    ],
    [
        "User Name" => "michael.landes@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "931",
    ],
    [
        "User Name" => "steve.aldana@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "925",
    ],
    [
        "User Name" => "aj.hampshire@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "931",
    ],
    [
        "User Name" => "chris.ford@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "949",
    ],
    [
        "User Name" => "mike.sivak@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "939",
    ],
    [
        "User Name" => "paul.wilson@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "940",
    ],
    [
        "User Name" => "charlie.shockley@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "929",
    ],
    [
        "User Name" => "steve.jahns@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "929",
    ],
    [
        "User Name" => "doug.greenamyer@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "949",
    ],
    [
        "User Name" => "eric.gephart@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "926",
    ],
    [
        "User Name" => "josh.auwater@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "952",
    ],
    [
        "User Name" => "jerry.anderson@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "926",
    ],
    [
        "User Name" => "shawn.martin@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "935",
    ],
    [
        "User Name" => "don.rayborne@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "932",
    ],
    [
        "User Name" => "mark.schrader@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "928",
    ],
    [
        "User Name" => "shane.vinagre@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "935",
    ],
    [
        "User Name" => "joe.diaz@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "932",
    ],
    [
        "User Name" => "roberto.rodriguez@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "917",
    ],
    [
        "User Name" => "michael.jimenez@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "927",
    ],
    [
        "User Name" => "matt.pilsterpearson@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "960",
    ],
    [
        "User Name" => "rick.cotton@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "927",
    ],
    [
        "User Name" => "jon.miller@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "956",
    ],
    [
        "User Name" => "joe.hargett@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "956",
    ],
    [
        "User Name" => "david.hunterjr@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "938",
    ],
    [
        "User Name" => "jeff.clouse@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "938",
    ],
    [
        "User Name" => "david.kennedy@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "960",
    ],
    [
        "User Name" => "christopher.marr@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "935",
    ],
    [
        "User Name" => "robert.schmidt@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "828",
    ],
    [
        "User Name" => "darius.cincys@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "828",
    ],
    [
        "User Name" => "zach.rupe@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "927",
    ],
    [
        "User Name" => "jeffrey.davis@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "928",
    ],
    [
        "User Name" => "lee.barber@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "930",
    ],
    [
        "User Name" => "eddy.mojena@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "959",
    ],
    [
        "User Name" => "kenneth.page@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "944",
    ],
    [
        "User Name" => "John.HobanJr@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "946",
    ],
    [
        "User Name" => "jamie.pirtle@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "952,953,965,944,926,959,929,949,939,950,940",
    ],
    [
        "User Name" => "jonathan.cutlip@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "927",
    ],
    [
        "User Name" => "john.burton@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "828,960,917,935,932,930,938,956,927,928,904",
    ],
    [
        "User Name" => "mark.walker@graebelmoving.com",
        "New Role" => "Parent Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "geoff.dahill@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "945",
    ],
    [
        "User Name" => "frank.byrne@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "952",
    ],
    [
        "User Name" => "Scott.stallings@graebelmoving.com",
        "New Role" => "Salesperson ",
        "Branch" => "953",
    ],
    [
        "User Name" => "Elvis.machado@grabelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "931",
    ],
    [
        "User Name" => "Laura.cannata@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "925",
    ],
    [
        "User Name" => "Thomas.Stallard@graebel.com",
        "New Role" => "Sales Manager",
        "Branch" => "828, 960, 935, 917, 932, 930, 904, 938",
    ],
    [
        "User Name" => "brad.igers@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "828",
    ],
    [
        "User Name" => "Martin.aamodt@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "960",
    ],
    [
        "User Name" => "troy.handrick@graebelmoving.com",
        "New Role" => "Parent Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "tom.stallard@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "828,960,917,935,932,932,904,938",
    ],
    [
        "User Name" => "jacob.moreno@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "935",
    ],
    [
        "User Name" => "Jeff.carr@graebelmoving.com",
        "New Role" => "Sales Manager",
        "Branch" => "828",
    ],
    [
        "User Name" => "seanb@assetcontrols.com",
        "New Role" => "Parent Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "ktanck@mobilemover.com",
        "New Role" => "Parent Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "prem.mandava@graebelmoving.com",
        "New Role" => "Parent Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "Daniel.christenson@graebelmoving.com",
        "New Role" => "Parent Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "terry.bruso@graebelmoving.com",
        "New Role" => "Parent Van Line User",
        "Branch" => "Corporate",
    ],
    [
        "User Name" => "helen.maracle@graebelmoving.com",
        "New Role" => "Salesperson",
        "Branch" => "935",
    ],
];
echo "Starting updating Users<br>\n";
foreach ($users as $user) {
    //handle the user switch
    echo "Starting Update for User: ".$user["User Name"]."<br>\n";
    $sql = "SELECT id FROM `vtiger_users` WHERE user_name = ?";
    $result = $db->pquery($sql, [$user["User Name"]]);
    $row = $result->fetchRow();
    if ($row) {
        $sql = "SELECT roleid FROM `vtiger_role` WHERE rolename = ?";
        $roleId = $db->pquery($sql, [$user['New Role']])->fetchRow()['roleid'];
        if ($user['New Role'] != 'Parent Van Line User' && $user['New Role'] != 'Child Van Line User') {
            $agents   = explode(',', $user['Branch']);
            $memberOf = [];
            foreach ($agents as $agent) {
                $sql        = "SELECT agentmanagerid FROM `vtiger_agentmanager` WHERE agency_code = ?";
                $memberOf[] = $db->pquery($sql, [$agent])->fetchRow()['agentmanagerid'];
            }
            try {
                $wsid = vtws_getWebserviceEntityId('Users', $row['id']);
                $data = ['roleid' => $roleId, 'id' => $wsid, 'agent_ids' => $memberOf, 'agent_ids_order' => $user['Branch']];
                $lead = vtws_revise($data, $current_user);
                echo "Successfully Updated User: ".$user["User Name"]."<br>\n";
            } catch (WebServiceException $ex) {
                echo "Failed to update User: ".$user["User Name"]." : ".$ex->getMessage()."<br>\n<br>\n";
            }
        } else {
            //handle corporate users here
            $sql = "SELECT vanlinemanagerid FROM `vtiger_vanlinemanager` WHERE vanline_id = ?";
            $memberOf = $db->pquery($sql, [$corporateVanlineId])->fetchRow()['vanlinemanagerid'];
            try {
                $wsid = vtws_getWebserviceEntityId('Users', $row['id']);
                $data = ['roleid' => $roleId, 'id' => $wsid, 'agent_ids' => $memberOf];
                $lead = vtws_revise($data, $current_user);
                echo "Successfully Updated User: ".$user["User Name"]."<br>\n";
            } catch (WebServiceException $ex) {
                echo "Failed to update User: ".$user["User Name"]." : ".$ex->getMessage()."<br>\n<br>\n";
            }
        }
    } else {
        echo "Could not locate User: ".$user["User Name"]."<br>\n";
    }
}
/* How Conrado was doing it think it can be simplified to a VTWS revise call and piggy back off existing user logic
function moveExistingUsers($roleName, $newRoleId) {
    //Exiting role depth -> New role
    $newRolesEquivalence = [
        'CEO - CFO - COO'         => ['Parent Vanline User', 123],
        'Senior Sales Leadership' => ['Child Van Line User', 3452],
    ];
    $db = PearDatabase::getInstance();
    foreach ($newRolesEquivalence as $oldRoleId => $newRoleArray) {
        $result = $db->pquery("SELECT * FROM vtiger_user2role
                                            AND vtiger_user2role.roleid != ?",
                              [$oldRoleId]);
        if ($result && $db->num_rows($result) > 0) {
            require_once('modules/Users/CreateUserPrivilegeFile.php');
            while ($row =& $db->fetch_row($result)) {
                $user_id = $row['userid'];
                //Update users Role
                $newRoleId = $newRoleArray[0];
                $db->pquery('UPDATE vtiger_user2role SET roleid =? WHERE userid=?', [$newRoleId, $user_id]);
                createUserPrivilegesfile($user_id);
                //Update Member of field
                $agentId = $newRoleArray[1];
                $db->pquery('UPDATE vtiger_users SET agent_ids =? WHERE userid=?', [$agentId, $user_id]);
                //Update owner field of records
                $db->pquery('UPDATE vtiger_crmentity SET agentid =? WHERE smownerid=?', [$agentId, $user_id]);
            }
        }
    }
}*/
