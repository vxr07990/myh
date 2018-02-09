<?php
include_once 'include/Webservices/SessionManager.php';

function validate($value, $type = '', $mandatory = false, $module = '', $options=[])
{
    switch ($type) {
        case 'webservice':
            $return = validateWebservice($value, $module);
            break;
        case 'multi':
            $return = validateMulti($value, $options);
            break;
        case 'phone':
            $return = validatePhone($value);
            break;
        case 'email':
            $return = validateEmail($value);
            break;
        default:
            return false;
    }
    return (validateMandatory($value, $mandatory) && $return)||(!$mandatory && !validateMandatory($value, !$mandatory));
}
function validateEmail($value)
{
    return filter_var($value, FILTER_VALIDATE_EMAIL);
}
function validatePhone($value)
{
    if (preg_match('/^(\d{3})?(\d{7})$/', $value, $matches) != 1) {
        return false;
    }
    return true;
}
function validateMulti($value, $options)
{
    if (!in_array($value, $options)) {
        return false;
    }
    return true;
}
function validateWebservice($value, $module)
{
    if (!empty($module)) {
        $parts   = explode('x', $value);
        $correct = vtws_getWebserviceEntityId($module, $parts[1]);
        if ($value == $correct) {
            return true;
        }
    }
    return false;
}
function validateMandatory($value, $mandatory = true)
{
    if ($mandatory && empty($value)) {
        return false;
    }
    return true;
}
function convertFromWebservice($value)
{
    //this doesn't validate anything please still use the validator
    return explode('x', $value)[1];
}
function generateErrorArray($errCode, $errMessage)
{
    $result            = [];
    $result['success'] = 'false';
    $error             = [];
    $error['code']     = $errCode;
    $error['message']  = $errMessage;
    $result['errors']  = [$error];

    return $result;
}

function curlPOST($post_string, $webserviceURL)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $webserviceURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    $curlResult = curl_exec($ch);
    curl_close($ch);

    return $curlResult;
}

function curlGET($get_string, $webserviceURL)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $webserviceURL.$get_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
    $curlResult = curl_exec($ch);
    curl_close($ch);

    return $curlResult;
}

function validatePOSTForDocumentUpload($input) {
    if (!isset($input) || empty($input)) {
        $errCode    = "NO input_DATA_FOUND";
        $errMessage = "No POST data was found in the request";
        return json_encode(generateErrorArray($errCode, $errMessage));
    }

    if (!isset($input['sessionName'])) {
        $errCode    = "MISSING_SESSIONID";
        $errMessage = "Session Identifier was not provided";
        return json_encode(generateErrorArray($errCode, $errMessage));
    }

    if (!isset($input['element'])) {
        $errCode    = "MISSING_ELEMENT";
        $errMessage = "Element information was not provided";
        return json_encode(generateErrorArray($errCode, $errMessage));
    }
}

function validateWSSession($sessionName) {
    try {
        $sessionManager = new SessionManager();
        if (!$sessionName || strcasecmp($sessionName, "null") === 0) {
            $sessionName = null;
        }
        $sid = $sessionManager->startSession($sessionName, false);
        if (!$sid) {
            $errCode    = "INVALID_SESSION";
            $errMessage = "Provided sessionName is invalid or expired";
            return json_encode(generateErrorArray($errCode, $errMessage));
        }
    } catch (WebServiceException $e) {
        $errCode    = $e->getCode();
        $errMessage = $e->getMessage();
        return json_encode(generateErrorArray($errCode, $errMessage));
    } catch (Exception $e) {
        $errCode    = WebServiceErrorCode::$INTERNALERROR;
        $errMessage = "Unknown Error while processing request";
        return json_encode(generateErrorArray($errCode, $errMessage));
    }
}

function validatePostDataParameter (&$data, $field) {
    if (!isset($data[$field])) {
        $errCode    = "MISSING_REQ_PARAM";
        $errMessage = "Required parameter '".$field."' was not provided";

        return json_encode(generateErrorArray($errCode, $errMessage));
    }
}

function validateUserID($module, $userId) {
    $fail = false;
    if ($module != 'Users' && $module != 'Groups') {
        //fail not a user or group?  don't know what it is throw it out.
        $fail = true;
    }
    if (!validate($userId, 'webservice', true, $module)) {
        $fail = true;
    }
    if ($fail) {
        $errCode    = 'USER_NOT_FOUND';
        $errMessage = 'id: '.$userId.' in module: '.$module.' does not exist in database';

        return json_encode(generateErrorArray($errCode, $errMessage));
    }
}
