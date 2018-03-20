<?php

/*
 * Several log methods to shortcut the Laravel Log Facade
 */
function logEmergency($message){
	\Illuminate\Support\Facades\Log::emergency($message);
}
function logCritical($message){
	\Illuminate\Support\Facades\Log::critical($message);
}
function logWarning($message){
	\Illuminate\Support\Facades\Log::warning($message);
}
function logNotice($message){
	\Illuminate\Support\Facades\Log::notice($message);
}
function logInfo($message){
	\Illuminate\Support\Facades\Log::info($message);
}
function logDebug($message){
	\Illuminate\Support\Facades\Log::debug($message);
}