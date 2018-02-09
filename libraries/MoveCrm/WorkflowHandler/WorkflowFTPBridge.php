<?php

namespace MoveCrm\WorkflowHandler;
//include_once ('libraries/MoveCrm/LogUtils.php');
use MoveCrm;
//use MoveCrm\LogUtils;

class WorkflowFTPBridge implements IWorkflowAPIBridge {

    const CONFIGURATION_ERROR_INPUT = 40001;
    const CONFIGURATION_ERROR_FIELD = 40002;
    const CONNECTION_ERROR = 40003;
    const CONNECTION_LOGIN_ERROR = 40004;
    const CONNECTION_LOGIN_PASS_ERROR = 40005;
    const CONFIGURATION_ERROR_MODE = 40006;
    const SEND_ERROR_FIELD = 40007;
    const SEND_ERROR_MODE = 40008;
    const SEND_ERROR_NO_FTP = 40009;
    const CONNECTION_FAILED = 40010;

    protected $config = [
        'host'     => '',
        'username' => '',
        'password' => '',
        'port'     => 21,
        'timeout'  => 60,
        'mode'     => FTP_BINARY,
        'passive_mode' => true,
        'DEBUG_LOG' => ''
    ];

    protected $requiredConfigFields = [
        'host',
        'port',
        'username',
        'mode'
    ];

    protected $requiredSendFields = [
        'remoteFile',
        'localFile'
    ];

    protected $ftpHandle = null;
    protected $tempDir = '/tmp/';
    protected $tempFilename;

    public function __construct(array $config) {
        $this->debugLog('In Construct class: ' .get_class($this));
        $config = array_merge($this->config, $config);
        if (is_array($config)) {
            $this->_setConfig($config);
            $this->ftpHandle = $this->_connectToRemoteFTP();
            if (!$this->connectionTest()) {
                $this->debugLog(self::CONNECTION_FAILED . ' : Failed initial connection attempt.');
                throw new \Exception('Failed initial connection attempt.', self::CONNECTION_FAILED);
            }
        }
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function getConfig($key) {
        if (array_key_exists($key, $this->config)) {
            return $this->config[$key];
        }
    }

    /**
     * @param array $config
     */
    private function _setConfig(array $config) {
        $this->debugLog('Setting the configuration');
        $this->_verifyConfiguration($config);
        $this->config = $config;
    }

    /**
     * Function to verify configuration has the required config fields for this.
     *
     * @param array $config
     *
     * @throws \Exception
     */
    private function _verifyConfiguration(array $config) {
        $fail = false;
        $missingField = [];

        if (!is_array($config)) {
            //fail;
            $this->debugLog(self::CONFIGURATION_ERROR_INPUT . ' : Configuration is not an array.');
            throw new \Exception('Configuration is not an array.', self::CONFIGURATION_ERROR_INPUT);
        }

        foreach ($this->requiredConfigFields as $field) {
            if (
                !array_key_exists($field, $config) ||
                !$config[$field]
            ) {
                $fail = true;
                $missingField[$field] = 1;
            }
        }

        if ($fail) {
            //FAILED VERIFY
            $this->debugLog(self::CONFIGURATION_ERROR_FIELD . ' : Required Fields missing: '.print_r(array_keys($missingField), true));
            throw new \Exception('Required Fields missing: '.print_r(array_keys($missingField), true), self::CONFIGURATION_ERROR_FIELD);
        }

        if (
            $config['mode'] != FTP_ASCII &&
            $config['mode'] != FTP_BINARY
        ) {
            $this->debugLog(self::CONFIGURATION_ERROR_MODE . ' : Required Field "mode" has an invalid option: '.print_r(array_keys($config['mode']), true));
            throw new \Exception('Required Field "mode" has an invalid option: '.print_r(array_keys($config['mode']), true), self::CONFIGURATION_ERROR_MODE);
        }

        //ensure whatever pasv passed in is a bool
        if ($config['passive_mode']) {
            $config['passive_mode'] = true;
        } else {
            $config['passive_mode'] = false;
        }
        $this->debugLog('Finished verifying configuration');
    }

    public function sendToRemote(array $sendInformation) {
        $this->_validateSendInformation($sendInformation);
        $ftpPut = ftp_put($this->ftpHandle, $sendInformation['remoteFile'], $sendInformation['localFile'], $this->config['mode']);

        if (!$ftpPut) {
            $this->_setError(error_get_last());
        }

        return $ftpPut;
        //@TODO: consider using a stream instead of a saved file?
        //return ftp_fput($this->ftpHandle, $sendInformation['remoteFile'], $sendInformation['localFileHandle'], $this->config['mode']);
    }

    /**
     * function to check there is enough data to do an FTP send.
     *
     * @param array $sendInformation
     *
     * @return bool
     * @throws \Exception
     */
    protected function _validateSendInformation(array $sendInformation) {
        $fail = false;
        $missingField = [];

        if (is_null($this->ftpHandle)) {
            $this->debugLog(self::SEND_ERROR_NO_FTP .' : No FTP handler active.');
            throw new \Exception('No FTP handler active.', self::SEND_ERROR_NO_FTP);
        }

        foreach ($this->requiredSendFields as $field) {
            if (
                !array_key_exists($field, $sendInformation) ||
                !$sendInformation[$field]
            ) {
                $fail = true;
                $missingField[$field] = 1;
            }
        }

        if ($fail) {
            $this->debugLog(self::SEND_ERROR_FIELD . ' : Required Fields missing: '.print_r(array_keys($missingField), true));
            throw new \Exception('Required Fields missing: '.print_r(array_keys($missingField), true), self::SEND_ERROR_FIELD);
        }

        if (!$this->config['mode']) {
            $this->debugLog(self::SEND_ERROR_MODE . ' : Required Field "mode" missing: '.print_r(array_keys($sendInformation['mode']), true));
            throw new \Exception('Required Field "mode" missing: '.print_r(array_keys($sendInformation['mode']), true), self::SEND_ERROR_MODE);
        }

        //@TODO: consider using a stream instead of a saved file?
        //if (is_string($sendInformation['localFile'])) {
        //} else if (is_resource($sendInformation['localFile'])) {
        //}
        return true;
    }

    public function connectionTest() {
        if (is_null($this->ftpHandle)) {
            return false;
        }
        return true;
    }

    /**
     * Function to connect to the remote with the config values.
     *
     * @return resource
     * @throws \Exception
     */
    private function _connectToRemoteFTP() {
        $this->_disconnectFromRemoteFTP();
        $connection = ftp_connect($this->config['host'], $this->config['port'], $this->config['timeout']);
        if (!$connection) {
            //failed to connect
            $this->debugLog(self::CONNECTION_ERROR . ' : FTP Connection to ' . $this->config['host']. ' failed.');
            throw new \Exception('FTP Connection to ' . $this->config['host']. ' failed.', self::CONNECTION_ERROR);
        }

        if (!@ftp_login($connection, $this->config['username'], $this->config['password'])) {
            if ($this->config['password']) {
                $this->debugLog(self::CONNECTION_LOGIN_PASS_ERROR .' : FTP Connection to '.$this->config['host'].', as '.$this->config['username'].' with a password failed.');
                throw new \Exception('FTP Connection to '.$this->config['host'].', as '.$this->config['username'].' with a password failed.', self::CONNECTION_LOGIN_PASS_ERROR);
            } else {
                $this->debugLog(self::CONNECTION_LOGIN_ERROR . ' : FTP Connection to '.$this->config['host'].', as '.$this->config['username'].' without a password, failed.');
                throw new \Exception('FTP Connection to '.$this->config['host'].', as '.$this->config['username'].' without a password, failed.', self::CONNECTION_LOGIN_ERROR);
            }
        }

        if (isset($this->config['passive_mode'])) {
            ftp_pasv ($connection, $this->config['passive_mode']);
        }

        return $connection;
    }

    /**
     * Function disconnects from remote (if connected)
     *
     * @return null
     */
    protected function _disconnectFromRemoteFTP() {
        if ($this->ftpHandle) {
            ftp_close($this->ftpHandle);
        }
        return null;
    }

    public function setLocalTempDirectory($newDir) {
        if (!is_dir($newDir)) {
            return false;
        }
        $this->tempDir = $newDir;
        return true;
    }

    public function getLocalFileToSend($inputString) {
        if (!$inputString) {
            return false;
        }
        $this->tempFilename = $this->_generateFilename();
        if (!$this->tempFilename) {
            return false;
        }

        $bytesWrote = file_put_contents($this->tempFilename, $inputString);

        if ($bytesWrote === false) {
            //False means failed to put, we could get the last error and set that?
            return false;
        } else if ($bytesWrote <= 0) {
            //created a file, but it's empty, we should clean it up.
            unlink($this->tempFilename);
            return false;
        }

        return $this->tempFilename;
    }

    public function removeLocalFile() {
        if (
            file_exists($this->tempFilename) &&
            is_file($this->tempFilename)
        ) {
            unlink($this->tempFilename);
        }
    }

    /**
     * Function to generate a filename to use temporarily.
     *
     * @TODO: Potential race condition.
     *
     * @return bool|string
     */
    private function _generateFilename() {
        if (!is_dir($this->tempDir)) {
            if (!mkdir($this->tempDir)) {
                return false;
            }
        }
        $filename = $this->tempDir . uniqid('ftp_');
        while (file_exists($filename)) {
            $filename = uniqid('ftp_');
        }

        return $filename;
    }

    private function _setError(array $errorMessage) {
        //@TODO: handle this differently instead of just throwing.
        $this->debugLog($errorMessage);
        throw new \Exception($errorMessage['message']);
//        error_get_last();
//        Array (
//            [type] => 8
//            [message] => Undefined variable: a
//            [file] => C:\WWW\index.php
//            [line] => 2
//        )
    }

    protected function debugLog($str) {
        if ($this->config['DEBUG_LOG']) {
            MoveCrm\LogUtils::LogToFile($this->config['DEBUG_LOG'], 'debugLog: ' . print_r($str, true));
        }
    }
}
