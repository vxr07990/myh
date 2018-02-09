<?php

namespace MoveCrm\GraebelAPI;

include_once('vtlib/Vtiger/Net/Client.php');
use MoveCrm\InputUtils;
use stdClass;
use Vtiger_Net_Client;

/*
 * The goal for this is to build a class that will handle the API calls.
 *
 */
class APIHandler
{

    //debug output on --- just to a devLog.log
    const DEBUG          = true;
    const DEBUG_LOG_FILE = 'logs/api_debug.log';
    //For recording results in a table.
    protected $db = false;
    const STORAGE_TABLE        = 'vtiger_api_responses';
    const HAS_BEEN_POSTED      = 'SENT'; //@TODO decide if we need more statuses.
    const POST_FAIL            = 'FAIL'; //@TODO decide if we need more statuses.
    const RECORD_GET_TOKEN     = false;
    const IS_TOKEN             = 1;
    const TABLE_METHOD_DEFAULT = 'getToken';
    protected $TABLE_METHOD = self::TABLE_METHOD_DEFAULT;
    /**
     * This can be overrode on construction
     * @var string $HOST_NAME
     * @var string $USERNAME
     * @var string $ACCESS_KEY
     */
    protected static $GET_TOKEN_URI = '/api/account/getToken';
    protected static $HOST_NAME     = '';
    protected static $USERNAME      = '';
    protected static $ACCESS_KEY    = '';
    protected static $TIMEOUT         = null;
    protected static $SUCCESS_MESSAGE = 'Your request has been sent to queue';
    protected static $RETRY_LIMIT     = 5;
    protected static $RETRY_SLEEP_MS  = 500;
    /**
     * @var array                  $initVars
     * @var bool|Vtiger_Net_Client $httpClient
     * @var string                 $requestPayload
     */
    protected $initVars       = [];
    protected $httpClient     = false;
    protected $requestPayload = '';
    //These are to map their expected values to some form of english
    //@NOTE: see v3.02 CreditCheckPass field defined as CHAR(1)
    const CHAR_TRUE  = 'Y';
    const CHAR_FALSE = 'N';
    //@NOTE: Discussion with API resolved some bit to be true/false binary and not Yes/No or Y/N  see v3.02 TransactionType field defined at BIT.
    const BIT_TRUE  = true;
    const BIT_FALSE = false;
    //@NOTE: Discussion with API team 2016-08-18 led to this being INT and not string.
    protected static $TRANSACTION_TYPE = [
        'insert' => 0,
        'update' => 1,
        'delete' => 2,
    ];
    protected static $MOVE_HQ_STATUS   = [
        'insert' => 'Insert',
        'update' => 'Update',
        'delete' => 'Delete',
    ];
    //@NOTE: not in the invoice api because these are used in the orders and other handlers.
    const CUSTOMER_DELIVERY_PREFERENCE_DEFAULT     = 'email';
    const CUSTOMER_INVOICE_DOCUMENT_FORMAT_DEFAULT = 'pdf';
    const CUSTOMER_CUSTOMER_TYPE_DEFAULT           = 'consumer/cod';
    //@TODO: These should probably go into a database.
    //OR be a relation picklist in the module.
    protected static $CUSTOMER_TYPE           = [
        'national accounts'         => 'CorporateHHG',
        'one time national account' => 'CorporateHHG',
        'rmc'                       => 'CorporateHHG',
        'consumer/cod'              => 'COD',
        'military'                  => 'HHG',
        'gsa'                       => 'HHG',
        'wps'                       => 'Workspace',
        'logistics'                 => 'Workspace',
        //'unused1'                   => 'WorkspaceMAC', //will be removed after API doc v3.04
        //'unused2'                   => 'WorkspaceWPS', //will be removed after API doc v3.04
        //'unused3'                   => 'Corporate', //removed as of API doc v3.04
        /*
National Accounts -> CorporateHHG
RMC -> CorporateHHG
One Time National Account -> CorporateHHG

Military -> HHG
GSA -> HHG

Consumer/COD -> COD

WPS -> Workplace
Logistics -> Workplace
        */
    ];
    protected static $DELIVERY_PREFERENCE     = [
        //'customer portal' => '01',
        //'sms'             => '02',
        //'smtp'            => '03',
        //'email'           => '04',
        //'e-mail'          => '04',
        'customer portal' => 'CustomerPortal',
        'html'            => 'HTML',
        'mail'            => 'Mail',
        'email'           => 'Email',
        'e-mail'          => 'Email',
    ];
    protected static $INVOICE_DOCUMENT_FORMAT = [
        'pdf'   => 'PDF',
        'excel' => 'Excel',
        'word'  => 'Word',
        'html'  => 'HTML'
        //'pdf'   => '01',
        //'excel' => '02',
        //'word'  => '03',
        //'html'  => '04'
    ];
    //@TODO: 3.05 eventually this will change to pull from their API.
    protected static $INVOICE_TEMPLATE = [
        'bottom line invoice'       => '01',
        'gross and net'             => '02',
        'no discount'               => '03',
        'performance based'         => '04',
        'gross only w/o remarks'    => '05',
        'gross net and gross only'  => '06',
        'grsw invoice'              => '07',
        'gross only invoice'        => '08',
        'permanent storage invoice' => '09',
        'invoice with payment'      => '10',  //sends ONLY services above only
        'project one line invoice'  => '11',  //PROJECT
        'event item invoice'        => '12',  //EVENT
        'event total invoice'       => '13',  //EVENT
        'jll invoice'               => '14',  //PROJECT
        'cbre invoice'              => '15',  //EVENT
        'state farm invoice'        => '16',  //EVENTS
        'asurion invoice'           => '17',  //EVENTS
    ];
    protected static $REQUIRES_PROJECTS = [11, 14];
    protected static $REQUIRES_EVENTS = [12, 13, 15, 16, 17];
    //@TODO: 3.05 eventually this will change to pull from their API.
    protected static $INVOICE_PACKET         = [
        'cod hhgs standard'                                                  => '01',
        'corporate hhgs standard'                                            => '02',
        'corporate hhgs extended or audited account'                         => '02b',
        'mmi hhgs standard'                                                  => '03',
        'mmi hhgs standard intrastate or local'                              => '03b',
        'jll workspace standard'                                             => '04',
        'military hhgs standard (entered in syncada/powertrack for payment)' => '05',
        'military hhgs interline'                                            => '05b',
        'gsa hhgs standard'                                                  => '06',
        'gsa hhgs whr'                                                       => '06b',
        'cartus hhgs standard'                                               => '07',
        'graebel movers international hhgs standard'                         => '08',
        'hhgs standard – sirva'                                              => '09',
        'cummins hhgs standard'                                              => '10',
        'relocation management worldwide hhgs standard'                      => '11',
        'altair  hhgs standard'                                              => '12',
        'jp morgan chase'                                                    => '13',
        'chevron'                                                            => '14',
        'chevron  intrastate or local'                                       => '14b',
        'siemens shared services'                                            => '15',
        'workspace - no backup'                                              => '16',
        'workspace - project'                                                => '17',
        'workspace - warehouse project'                                      => '18',
    ];
    protected static $INVOICE_PACKET_DOCLIST = [
        '01'  => [
            'Rate Sheet'
        ],
        '02'  => [
            'Rate Sheet',
            //'Bill of Lading',
            'Origin Bill of Lading',
            'Destination Bill of Lading',
        ],
        '02b' => [
            'Rate Sheet',
            //'Bill of Lading',
            'Origin Bill of Lading',
            'Destination Bill of Lading',
            'Weight Tickets',
            //'Accessorial Forms',
            'Origin Accessorial Forms',
            'Destination Accessorial Forms',
            'Pack Per Inventory Count',
            '3rd Party Invoice',
            //'Inventories'
        ],
        '03'  => [
            'Rate Sheet',
            //'Bill of Lading',
            'Origin Bill of Lading',
            'Destination Bill of Lading',
            'Estimate of Charges',
            'Weight Tickets',
            //'Accessorial Forms',
            'Origin Accessorial Forms',
            'Destination Accessorial Forms',
            'Pack Per Inventory Count',
            '3rd Party Invoice',
            //'Inventories',
            'Billing Release Form'
        ],
        '03b' => [
            'Rate Sheet',
            //'Bill of Lading',
            'Origin Bill of Lading',
            'Destination Bill of Lading',
            'Estimate of Charges',
            'Weight Tickets',
            //'Accessorial Forms',
            'Origin Accessorial Forms',
            'Destination Accessorial Forms',
            'Pack Per Inventory Count',
            '3rd Party Invoice',
            //'Inventories',
            'Billing Release Form',
            'Tariff Pages'
        ],
        '04'  => [],
        '05'  => [],
        '05b' => [],
        '06'  => [
            'Rate Sheet',
            //'Bill of Lading',
            'Origin Bill of Lading',
            'Destination Bill of Lading',
            'Weight Tickets',
            //'Accessorial Forms',
            'Origin Accessorial Forms',
            'Destination Accessorial Forms',
            'Pack Per Inventory Count',
            '3rd Party Invoice',
            //'Inventories',
            'Billing Release Form',
            'Cartus Work Order',
            'Government Voucher'
        ],
        '06b' => [
            'Rate Sheet',
            //'Bill of Lading',
            'Origin Bill of Lading',
            'Destination Bill of Lading',
            'Weight Tickets',
            //'Accessorial Forms',
            'Origin Accessorial Forms',
            'Destination Accessorial Forms',
            'Pack Per Inventory Count',
            '3rd Party Invoice',
            //'Inventories',
            'Billing Release Form',
            'Cartus Work Order',
            'Government Voucher',
            'Email'
        ],
        '07'  => [
            'Rate Sheet',
            //'Bill of Lading',
            'Origin Bill of Lading',
            'Destination Bill of Lading',
            'Weight Tickets',
            //'Accessorial Forms',
            'Origin Accessorial Forms',
            'Destination Accessorial Forms',
            'Pack Per Inventory Count',
            '3rd Party Invoice',
            //'Inventories',
            'Billing Release Form',
            'Cartus Work Order'
        ],
        '08'  => [
            'Email'
        ],
        '09'  => [
            'Rate Sheet',
            //'Bill of Lading',
            'Origin Bill of Lading',
            'Destination Bill of Lading',
            'Weight Tickets',
            //'Accessorial Forms',
            'Origin Accessorial Forms',
            'Destination Accessorial Forms',
            'Pack Per Inventory Count',
            '3rd Party Invoice',
            //'Inventories',
            'Billing Release Form',
            'Cartus Work Order'
        ],
        '10'  => [],
        '11'  => [],
        '12'  => [],
        '13'  => [],
        '14'  => [],
        '14b' => [],
        '15'  => [],
        '16'  => [],
        '17'  => [
            'Rate Sheet',
            'Account Authorizations',
            'Timesheets',
            'Commercial Material/Equipment Control Form',
            'Warehouse Receive/Delv Report',
            'Customer Specific Forms'
        ],
        '18'  => [
            'Rate Sheet',
            'Account Authorizations',
            'Timesheets',
            'Material and Equipment Form',
            'Warehouse Receive/Delv Report',
        ],
    ];

	/**
	 * Construct new instance.
	 *
	 * @param array $initVars
	 */
    public function __construct(array $initVars = [])
    {
        self::$HOST_NAME = getenv('API_HOST_NAME');
        self::$USERNAME = getenv('API_USERNAME');
        self::$ACCESS_KEY = getenv('API_ACCESS_KEY');

		if (!is_array($initVars)) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (APIHandler.php:" . __LINE__ . ") Error: InitVars is not an array.", FILE_APPEND);
            }
			throw new \Exception(__CLASS__ . ' expects the constructor input to be an array.');
		}

		//store the initVars in case I need to come back to them for whatever bloody reason.
		$this->initVars = $initVars;
		$this->httpClient = new \Vtiger_Net_Client();

		if (array_key_exists('orderNumber', $this->initVars)) {
			$this->orderNumber = $this->initVars['orderNumber'];
		}

        if (array_key_exists('recordNumber', $this->initVars)) {
            $this->recordNumber = $this->initVars['recordNumber'];
        }

        if (array_key_exists('trigger', $this->initVars)) {
            $this->trigger = $this->initVars['trigger'];
        }

		//allow override of hostname
		if (array_key_exists('host_name', $this->initVars)) {
			self::$HOST_NAME = $initVars['host_name'];
		}

		//allow override of username
		if (array_key_exists('username', $this->initVars)) {
			self::$USERNAME = $initVars['username'];
		}

		//allow override of access_key
        if (array_key_exists('access_key', $this->initVars)) {
            self::$ACCESS_KEY = $initVars['access_key'];
        }

        if (!self::$HOST_NAME) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (APIHandler.php:" . __LINE__ . ") Error: No API_HOST_NAME set in .env.", FILE_APPEND);
            }
            throw new \Exception("No API hostname set: (" . self::$HOST_NAME . ")", '52001');
        }

        if (!self::$USERNAME) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (APIHandler.php:" . __LINE__ . ") Error: No API_USERNAME set in .env.", FILE_APPEND);
            }
            throw new \Exception("No API hostname set: (" . self::$USERNAME . ")", '52002');
        }

        if (!self::$ACCESS_KEY) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (APIHandler.php:" . __LINE__ . ") Error: No API_ACCESS_KEY set in .env.", FILE_APPEND);
            }
            throw new \Exception("No API hostname set: (" . self::$ACCESS_KEY . ")", '52003');
        }

		//@TODO: perhaps we shouldn't call this in the constructor?
        //@TODO: No we shouldn't call now, we need to change this to elsewhere. maybe processSend?
		//Prepare for sending by getting an access token and setting up AuthenticationInfo for later use
        //@NOTE: moved out so it'll only call if we are really ready to do the thing.
		//$this->GetToken();
	}

	/**
	 * Returns a thing you know stuff
	 *
	 * @param string $fieldname
	 * @param string $value
	 * @return mixed
	 */
    public function set($fieldname, $value)
    {
		$this->$fieldname = $value;
		return $this->$fieldname;
	}

	/**
	 * Returns a thing you know stuff
	 *
	 * @param string $fieldname
	 * @return mixed
	 */
    public function get($fieldname)
    {
		if ($fieldname) {
			$prop = new \ReflectionProperty(self, $fieldname);
            if ($prop->isStatic()) {
				return self::$fieldname;
			}
			return $this->$fieldname;
		}
	}

    public static function getStatic($fieldname)
    {
		$prop = new \ReflectionProperty(self, $fieldname);
        if ($prop->isStatic()) {
			return self::$fieldname;
		}

        if (self::DEBUG) {
            file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (APIHandler.php:" . __LINE__ . ") Error: Unable to statically retrieve variable.", FILE_APPEND);
        }
		throw new \Exception("Unable to statically retrieve variable: $fieldname", '54000');
	}

	/**
	 * postRequst primary access point... just does the thing.
	 *
	 * @param array  $postVars
	 * @param string $uri
	 *
	 * @return stdClass
	 * @throws \Exception
	 */
    public function postRequest($postVars, $uri = '')
    {
        //make sure we have a token at this point
        $this->GetToken();
        $requestPayload = '';

		//no check on $uri is passed because maybe the host_name is the path it could happen.
		$this->httpClient->url = self::$HOST_NAME.$uri;

		if (is_array($postVars)) {
            $this->httpClient->setHeaders(['Content-Type' => 'application/json']);
			$requestPayload = json_encode($postVars);
		} elseif (!$postVars) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (APIHandler.php:" . __LINE__ . ") Error: postVars is something but not an array.", FILE_APPEND);
            }
			throw new \Exception(__METHOD__ . ' expects the first value to be an array.');
		}

		if (self::DEBUG) {
			file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (APIHandler.php:".__LINE__.") postVars : ".print_r($postVars, true), FILE_APPEND);
			file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (APIHandler.php:" . __LINE__ . "): requestPayload (".$requestPayload.")", FILE_APPEND);
			file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (APIHandler.php:" . __LINE__ . ") this->httpClient->url : ".print_r($this->httpClient->url, true), FILE_APPEND);
		}

        $this->requestPayload = $requestPayload;

		return $this->processSend($this->httpClient->doPost($requestPayload, self::$TIMEOUT));
	}

	/**
	 * putRequst primary access point... just does the thing.
	 *
	 * @param array  $postVars
	 * @param string $uri
	 *
	 * @return stdClass
	 * @throws \Exception
	 */
    public function putRequest($postVars, $uri = '')
    {
        //make sure we have a token at this point
        $this->GetToken();
		$requestPayload = '';

		//no check on $uri is passed because maybe the host_name is the path it could happen.
		$this->httpClient->url = self::$HOST_NAME.$uri;

		if (is_array($postVars)) {
            $this->httpClient->setHeaders(['Content-Type' => 'application/json']);
			$requestPayload = json_encode($postVars);
		} elseif (!$postVars) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (APIHandler.php:" . __LINE__ . ") Error: put postVars is something but not an array.", FILE_APPEND);
            }
			throw new \Exception(__METHOD__ . ' expects the first value to be an array.');
		}

		if (self::DEBUG) {
			file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (APIHandler.php:".__LINE__.") postVars : ".print_r($postVars, true), FILE_APPEND);
			file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (APIHandler.php:" . __LINE__ . "): requestPayload (".$requestPayload.")", FILE_APPEND);
			file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (APIHandler.php:" . __LINE__ . ") this->httpClient->url : ".print_r($this->httpClient->url, true), FILE_APPEND);
		}

        $this->requestPayload = $requestPayload;

        return $this->processSend($this->httpClient->doPost($requestPayload, self::$TIMEOUT));
        //return $this->processSend($this->httpClient->doPut($requestPayload, self::$TIMEOUT));
	}

	/**
	 * getRequest primary access point.
	 *
	 * @param string $uri
     * @param bool/int $logAction
     * @param bool/int $isTokenRequest
     * @return stdClass
	 */
    public function getRequest($uri = '', $logAction, $isTokenRequest = false)
    {
        if ($isTokenRequest != self::IS_TOKEN) {
            //IF this is a token request we can't call getToken that is madness!
            $this->GetToken();
        }

		//no check on $uri is passed because maybe the host_name is the path it could happen.
		$this->httpClient->url = self::$HOST_NAME.$uri;

		if (self::DEBUG) {
			file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (APIHandler.php:" . __LINE__ . "): this->httpClient->url (".$this->httpClient->url.")", FILE_APPEND);
		}

        return $this->processSend($this->httpClient->doGet(false, self::$TIMEOUT), $logAction);
	}

	/**
	 * queries remote host to set the accessToken for future requests.
	 * @return string
	 * @throws \Exception
	 */
    protected function GetToken()
    {
	    $tokenGetAttempts = 0;
        while (self::$RETRY_LIMIT > $tokenGetAttempts++) {
            $responseObject = $this->attemptTokenRetrieval();
            if (property_exists($responseObject, 'Token')) {
                break;
            }
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (APIHandler.php:" . __LINE__ . ") Failed to retrieve token attempt (" . $tokenGetAttempts . ") (sleeping: " . self::$RETRY_SLEEP_MS ." ms)", FILE_APPEND);
            }
            usleep(self::$RETRY_SLEEP_MS);
        }

        if (!property_exists($responseObject, 'Token')) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (APIHandler.php:" . __LINE__ . ") Error: No token returned from remote.", FILE_APPEND);
            }
			throw new \Exception('No Token retrieved from remote server');
		}

		//update the auth info for later access
		$this->AuthorizationInfo['AccessToken'] = $this->createAccessToken($responseObject->Token);

		if (self::DEBUG) {
			file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (APIHandler.php:" . __LINE__ . ") this->AuthorizationInfo : ".print_r($this->AuthorizationInfo, true), FILE_APPEND);
			file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (APIHandler.php:" . __LINE__ . ") this->AuthorizationInfo : ".print_r(json_encode($this->AuthorizationInfo), true), FILE_APPEND);
		}

		//update the header with the Auth info so we are good to go.
		$this->httpClient->setHeaders(['AuthorizationInfo' => json_encode($this->AuthorizationInfo)]);
	}

	/**
	 * Create the Access Token from the provided Token.
	 *
	 * @param string $token
	 *
	 * @return string
	 * @throws \Exception
	 */
    private function createAccessToken($token)
    {
		if (!$token) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (APIHandler.php:" . __LINE__ . ") Error: No token provided.", FILE_APPEND);
            }
			throw new \Exception('No Token provided');
		}
		$rv = md5(self::$ACCESS_KEY . $token);
		return $rv;
	}

	/**
	 * process the response body so we can throw errors whatnot.
	 *
	 * @param string $responseBody
	 *
	 * @return stdClass
	 * @throws \Exception
	 */
    private function processSend($responseBody, $logAction = true)
    {
		$responseObject = json_decode($responseBody);
		//we may need the headers, so I'm leaving this here as a note.
        //$responseHeaders = $this->httpClient->client->getResponseHeader();
		$responseCode = $this->httpClient->client->getResponseCode();

        if (!is_a($responseObject, 'stdClass')) {
			$responseObject = new stdClass;
			$responseObject->response = $responseBody;
			//@TODO: Response code COULD be 2xx.  Examples show just 200, 201 is often "created" though.
			if ($responseBody == self::$SUCCESS_MESSAGE || $responseCode == 200 || $responseCode == 201) {
				$responseObject->success = true;
			}
		}

        if (self::DEBUG) {
			file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (APIHandler.php:" . __LINE__ . ") responseBody : ".print_r($responseBody, true), FILE_APPEND);
			file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (APIHandler.php:" . __LINE__ . ") responseCode : ".print_r($responseCode, true), FILE_APPEND);
			file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (APIHandler.php:" . __LINE__ . ") responseObject : ".print_r($responseObject, true), FILE_APPEND);
		}

        if (property_exists($responseObject, 'ErrorCode')) {
            $Message = '';
			$ErrorCode = '';
			if (property_exists($responseObject, 'Message')) {
				$Message = "Message: " . $responseObject->Message;
			}
			if (property_exists($responseObject, 'StackTrace')) {
				$Message .= "\nStackTrace: " . $responseObject->StackTrace;
			}
			if (property_exists($responseObject, 'ErrorCode')) {
                $ErrorCode = $responseObject->ErrorCode;
                $Message .= "\nErrorCode: " . $responseObject->ErrorCode;
			}
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (APIHandler.php:" . __LINE__ . ") Error: ErrorCode exists!", FILE_APPEND);
            }
            if ($logAction) {
                $this->recordResults(self::POST_FAIL, $responseBody, $responseCode);
            }
			throw new \Exception($Message);
            //$ErrorCode isn't always a number?
            //throw new \Exception($Message, $ErrorCode);
        } elseif (!$responseObject) {
            if (self::DEBUG) {
                file_put_contents(self::DEBUG_LOG_FILE, "\n DEBUG (APIHandler.php:" . __LINE__ . ") Error: no response from the remote server!", FILE_APPEND);
            }
            if ($logAction) {
                $this->recordResults(self::POST_FAIL, $responseBody, $responseCode);
            }
			throw new \Exception('No response from remote server.');
		}
		if ($logAction) {
            $this->recordResults(self::HAS_BEEN_POSTED, $responseBody, $responseCode);
        }
		return $responseObject;
	}

    /**
     * Ok the idea is that we could have 0 to 3 input record numbers.  It's loose.
     * If you have an order number though it will be able to populate the contact and account from it. But you can't go the other way.
     * Order will only populate account and contact if an order has them AND those are not seperately passed in.
     *
     * @return bool
     */
    protected function initializeRecordModels()
    {
        $ok = false;
        //record number could be a contact or account or order number.
        if ($this->recordNumber) {
            try {
                if ($unknownRecordModel = \Vtiger_Record_Model::getInstanceById($this->recordNumber)) {
                    switch ($unknownRecordModel->getModuleName()) {
                        case 'Orders':
                            $this->orderRecordModel = $unknownRecordModel;
                            $this->orderNumber = $this->recordNumber;
                            break;
                        case 'Accounts':
                            $this->accountRecordModel = $unknownRecordModel;
                            $this->accountNumber = $this->recordNumber;
                            break;
                        case 'Contacts':
                            $this->contactRecordModel = $unknownRecordModel;
                            $this->contactNumber = $this->recordNumber;
                            break;
                        default:
                    }
                }
            } catch (\Exception $ex) {
                //throw $ex; //accept this exception
            }
        }

        //@TODO: find a better way to set the array for this.
        //I am not happy with this... but it's at least a loop instead of each done seperately.
        foreach (['order', 'contact', 'account'] as $module) {
            try {
                $this->pullRecordModel($module.'Number', $module.'RecordModel');
                $ok = true;
                if ($module == 'order') {
                    //Only use the order's contact if no contact is provided.
                    if (!$this->contactNumber && $this->orderRecordModel) {
                        $this->contactNumber = $this->orderRecordModel->get('orders_contacts');
                    }
                    //Only use the order's account if no account is provided.
                    if (!$this->accountNumber && $this->orderRecordModel) {
                        $this->accountNumber = $this->orderRecordModel->get('orders_account');
                    }
                    //Only use the order's contract if no contract is provided.
                    if (!$this->contractNumber && $this->orderRecordModel) {
                        $this->contractNumber = $this->orderRecordModel->get('account_contract');
                    }
                }
            } catch (\Exception $ex) {
                //throw $ex;
                //it's fine.
            }
        }

        return $ok;
    }

    /**
     * @param $number
     * @param $recordModel
     *
     * @throws \Exception
     */
    protected function pullRecordModel($number, $recordModel)
    {
        //@TODO: no safety check that these are valid things.
        if ($this->$number && !$this->$recordModel) {
            try {
                $this->$recordModel = \Vtiger_Record_Model::getInstanceById($this->$number);
                if (!$this->$recordModel) {
                    throw new \Exception('Unable to pull ' . $recordModel . ' Model: (' . $this->$number . ').', '50001a');
                }
            } catch (\Exception $ex) {
                throw new \Exception('Unable to pull ' . $recordModel . ' Model: (' . $this->$number . ').', '50001b');
            }
        } elseif (!$this->$number) {
            throw new \Exception('No record number provided: ' . $number . ' -- (' . $this->$number . ').', '50001c');
        } elseif ($this->$recordModel) {
            //This case is OK.
        } else {
            //can I get here?
            throw new \Exception('You should not be here: ' . $number . ' -- ' . $recordModel, '50001d');
        }
    }

    /**
     * @param $userid
     *
     * @return bool|string
     */
    protected function retreiveUsernameFromID($userid)
    {
        $username = false;
        if ($userid) {
            try {
                if ($userModel = \Vtiger_Record_Model::getInstanceById($userid, 'Users')) {
                    $username = $userModel->get('user_name');
                }
            } catch (\Exception $ex) {
                //failed to retrieve user.
            }
        }
        return $username;
    }

    /**
     * function to make the input date as a iso8601 formatted date.
     * @param $inputDate
     *
     * @return null|string
     */
    protected function formatDate($inputDate)
    {
        if (!$inputDate) {
            return null;
        }

        //ex:   "LoadDate": "2016-06-22T20:08:46.5714315+05:30",
        //this is SOAP format, but ISO8601 should be fine since we don't store milliseconds anyway.
        //ISO 8601 date => date('c')
        $date = new \DateTime($inputDate);
        //return $date->format('c');
        return $date->format('Y-m-d');
    }

    /**
     * takes an input number and returns it as a float with 2 decimal places
     *
     * @param $inputNumber
     *
     * @return float|Number
     */
    protected function ensureDecimalNumber($inputNumber)
    {
        if (!isset($inputNumber)) {
            return 0.0;
        }

        //This is cheating, but it does the thing
        return \CurrencyField::convertToDBFormat($inputNumber);
    }

    /**
     * takes an input number and returns it as a float with 2 decimal places OR if empty then null
     *
     * @param $inputNumber
     *
     * @return NULL|float|Number
     */
    protected function ensureDecimalNumberOrNull($inputNumber)
    {
        //OT 3639, they want nulls returned on these instead of 0
        if (!isset($inputNumber) || $inputNumber == 0) {
            return null;
        }
        return $this->ensureDecimalNumber($inputNumber);
    }

    /**
     * function to return an integer for an inputnumber
     *
     * @param $inputNumber
     *
     * @return int
     */
    protected function ensureInteger($inputNumber)
    {
        if (!$inputNumber) {
            return 0;
        }
        return (int) \CurrencyField::convertToDBFormat($inputNumber);
    }

    /**
     * @param $item
     * @param $default
     *
     * @return mixed
     */
    protected function assignIfNotEmpty(&$item, $default) {
        //return isset($item) ? $item : $default;
        return (!empty($item)) ? $item : $default;
    }

    /**
     * @param $method
     * @param $record
     *
     * @return bool
     */
    protected function hasBeenSent($method, $record)
    {
        if (!$method) {
            return false;
        }

        if (!$record) {
            return false;
        }

        if (!$this->db) {
            $this->db = \PearDatabase::getInstance();
        }

        //ensure method exists on db object.
        if ($this->db && method_exists($this->db, 'pquery')) {
            $stmt   = 'SELECT `status` FROM `'.self::STORAGE_TABLE.'` WHERE `method` = ? AND `record_id` = ? AND `status` = ? LIMIT 1';
            $result = $this->db->pquery($stmt, [$method, $record, self::HAS_BEEN_POSTED]);
            $row    = $result->fetchRow();
            if ($row) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $itemId
     * @param $itemType
     * @param $method
     *
     * @return mixed
     */
    protected function transactionItemAlreadySent($itemId, $itemType, $method)
    {
        //@TODO build this out to see if an item has already been sent.
        if (!$method) {
            return self::$TRANSACTION_TYPE['insert'];
        }

        if (true) {
            return self::$TRANSACTION_TYPE['update'];
        }
        return self::$TRANSACTION_TYPE['insert'];
    }

    /**
     * @param      $status
     * @param      $responseBody
     * @param      $responseCode
     * @param bool $method
     * @param bool $record
     *
     * @return bool
     */
    private function recordResults($status, $responseBody, $responseCode, $method = false, $record = false)
    {
        if (!$method) {
            $method = $this->getLogTableMethod();
        }

        if (!$record) {
            $record = $this->getLogRecordId();
        }

        $url = $this->getLogUrl();

        $this->insertLogTable($record, $method, $url, $this->requestPayload, $responseCode, $responseBody, $status);
        return false;
    }

    /**
     * @param $record
     * @param $method
     * @param $url
     * @param $requestPayload
     * @param $responseCode
     * @param $responseBody
     * @param $status
     *
     * @return bool
     */
    private function insertLogTable($record, $method, $url, $requestPayload, $responseCode, $responseBody, $status)
    {
        if (!$this->db) {
            $this->db = \PearDatabase::getInstance();
        }

        if ($this->db && method_exists($this->db, 'pquery')) {
            $stmt = 'INSERT INTO `'.self::STORAGE_TABLE.'` (
                    `record_id`,
                    `method`,
                    `url`,
                    `payload`,
                    `response_code`,
                    `response_body`,
                    `status`
                    ) VALUES (?, ?, ?, ?, ?, ?, ?)';
            $this->db->pquery($stmt,
                              [
                                  $record,
                                  $method,
                                  $url,
                                  $requestPayload,
                                  $responseCode,
                                  $responseBody,
                                  $status
                              ]);

            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    protected function getLogTableMethod()
    {
        //if the table_method is not default, return it
        if ($this->TABLE_METHOD != self::TABLE_METHOD_DEFAULT) {
            return $this->TABLE_METHOD;
        }

        //when we have an httpClient and a url we can use the endpoint for the method.
        if ($this->httpClient) {
            $temp_method = $this->httpClient->url;
            $temp_method = preg_replace('/.*\//i', '', $temp_method);
            if ($temp_method) {
                return $temp_method;
            }
        }
        return $this->TABLE_METHOD;
    }

    /**
     * @return mixed
     */
    protected function getLogRecordId()
    {
        return $this->orderNumber;
    }

    /**
     * @return string
     */
    protected function getLogUrl()
    {
        $url = self::$HOST_NAME.self::$GET_TOKEN_URI;
        if ($this->httpClient) {
            $url = $this->httpClient->url;
        }
        return $url;
    }

    /**
     * @param $longState
     *
     * @return bool|string
     */
    public function translateStateToTwoChar($longState)
    {
        if (!$longState) {
            //no input, returning itself is fine
            return $longState;
        }

        if (strlen($longState) == 2) {
            //it's only 2 characters so hopefully it's right!
            return $longState;
        }

        if (!$this->db) {
            $this->db = \PearDatabase::getInstance();
        }

        //ensure method exists on db object.
        if ($this->db && method_exists($this->db, 'pquery')) {
            //@NOTE: table format:
            // +----+----------------------+------+
            // | id | state                | abbr |
            // +----+----------------------+------+
            // |  1 | District of Columbia | DC   |
            // |  2 | Delaware             | DE   |
            $stmt   = 'SELECT `abbr` FROM `vtiger_states` WHERE `state` = ? LIMIT 1';
            $result = $this->db->pquery($stmt, [$longState]);

            //ensure we have a result and the method is there to use.
            if (!$result || !method_exists($result, 'fetchRow')) {
                return $longState;
            }

            $row = $result->fetchRow();
            if ($row) {
                return $row['abbr'];
            }
        }

        //fail over return whatever they inputted.
        return $longState;
    }

    /**
     * @return stdClass
     */
    private function attemptTokenRetrieval()
    {
        $this->AuthorizationInfo = [
            'UserName' => self::$USERNAME
        ];

        //GetToken is the base starting point.  so we do setURL to generate a new instance of HTTP_Request();
        $this->httpClient->setUrl(self::$HOST_NAME.self::$GET_TOKEN_URI, false);

        //Set the header with what we have for authInfo
        $this->httpClient->setHeaders(['AuthorizationInfo' => json_encode($this->AuthorizationInfo)]);

        //this may be superfluous.
        return $this->getRequest(self::$GET_TOKEN_URI, self::RECORD_GET_TOKEN, self::IS_TOKEN);
    }

    /**
     * function getCharFlag takes in a flag and returns CHAR_TRUE or CHAR_FALSE
     *
     * @param        $flag
     * @param string $default
     *
     * @return string
     */
    protected function getCharFlag($flag, $default = self::CHAR_FALSE)
    {
        //if the flag is set (could be 1, true, y, anything) we can only assume it's TRUE
        if (InputUtils::CheckboxToBool($flag)) {
            return self::CHAR_TRUE;
        }

        //if the flag is false then it's CHAR_FALSE
        if ($flag === false) {
            return self::CHAR_FALSE;
        }

        //@TODO: Evaluate if this needed.
        //@NOTE: might be unnecessary, but check for 0.
        if ($flag === 0) {
            return self::CHAR_FALSE;
        }

        //just to be totally safe check all the possible strings off could be as well.
        if (in_array(strtolower($flag), ['0', 'off', 'no', 'false', strtolower(self::CHAR_FALSE)])) {
            return self::CHAR_FALSE;
        }

        //Allows the user to pass in something else to use as a default, otherwise it's CHAR_FALSE (set in declaration).
        //I think that allows a user to pass in '' or false.
        return $default;
    }
}
