<?php

namespace MoveCrm\WorkflowHandler;

interface IWorkflowAPIBridge {
    public function __construct(array $config);

    public function sendToRemote(array $sendInformation);
//    public function retrySend(array $sendInformation);
//    public function checkSendResults();
    public function connectionTest();
}

