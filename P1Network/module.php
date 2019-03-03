<?php

/**
 * WebsocketServer Klasse implementiert das Websocket-Protokoll für einen ServerSocket.
 * Erweitert IPSModule.
 *
 * @package       P1Module
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2017 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       1.1
 *
 */
class P1Module extends IPSModule {

 /**
  * Interne Funktion des SDK.
  *
  * @access public
  */
 public function Create() {
  parent::Create();
  $this->RequireParent("{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}");
  $this->SetBuffer("Data", "");

 }

 /**
  * Interne Funktion des SDK.
  *
  * @access public
  */
 public function Destroy() {
  if (IPS_InstanceExists($this->InstanceID)) {

  }
  parent::Destroy();
 }

 /**
  * Interne Funktion des SDK.
  *
  * @access public
  */
 public function ApplyChanges() {
  parent::ApplyChanges();

 }

 /**
  * Interne Funktion des SDK.
  *
  * @access public
  */
 public function MessageSink($TimeStamp, $SenderID, $Message, $Data) {
  $this->IOMessageSink($TimeStamp, $SenderID, $Message, $Data);

//        switch ($Message)
  //        {
  //            case IPS_KERNELSTARTED:
  //                $this->KernelReady();
  //                break;
  //        }
 }

 /**
  * Wird ausgeführt wenn sich der Status vom Parent ändert.
  * @access protected
  */
 protected function IOChangeState($State) {
  if ($State == IS_ACTIVE) {

  } else {

  }
 }

 /**
  * Interne Funktion des SDK.
  *
  * @access public
  */
 public function GetConfigurationForm() {
 }

 /**
  * Interne Funktion des SDK.
  *
  * @access public
  */
 public function GetConfigurationForParent() {
  $Config['StopBits'] = 1;
  $Config['BaudRate'] = 9600;
  $Config['Parity'] = 'Even';
  $Config['DataBits'] = 7;
  return json_encode($Config);
 }

 public function ReceiveData($JSONString) {
  $data = json_decode($JSONString);

  $dt = utf8_decode($data->Buffer);
  $pos = strpos($dt, "!");

  $Data = $this->GetBuffer('Data');

  if ($pos === false) {
   $Data .= $dt;
   $this->SetBuffer("Data", $Data);

  } else {
   $Data = $Data . $dt;
   IPS_LogMessage("P1Data", $Data);

   preg_match('/^(1-0:1\.8\.1\((\d+\.\d+)\*kWh\))/m', $Data, $output_array);
   $verbruiknacht = floatval(@$output_array[2]);
   IPS_LogMessage("P1Data", $verbruiknacht);

   preg_match('/^(1-0:1\.8\.2\((\d+\.\d+)\*kWh\))/m', $Data, $output_array);
   $verbruikdag = floatval(@$output_array[2]);
   IPS_LogMessage("P1Data", $verbruikdag);

   preg_match('/^(1-0:2\.8\.1\((\d+\.\d+)\*kWh\))/m', $Data, $output_array);
   $opbrengstnacht = floatval(@$output_array[2]);
   IPS_LogMessage("P1Data", $opbrengstnacht);

   preg_match('/^(1-0:2\.8\.2\((\d+\.\d+)\*kWh\))/m', $Data, $output_array);
   $opbrengstdag = floatval(@$output_array[2]);
   IPS_LogMessage("P1Data", $opbrengstdag);

   preg_match('/^(0-0:96\.14\.0\((\d+)\))/m', $Data, $output_array);
   $tarief = boolval(@$output_array[2]);
   IPS_LogMessage("P1Data", $tarief);

   preg_match('/^(1-0:1\.7\.0\((\d+.\d+)\*kW\))/m', $Data, $output_array);
   $huidigvebruik = floatval(@$output_array[2]);
   IPS_LogMessage("P1Data", $huidigvebruik);

   preg_match('/^(1-0:2\.7\.0\((\d+.\d+)\*kW\))/m', $Data, $output_array);
   $huidigopbrengst = floatval(@$output_array[2]);
   IPS_LogMessage("P1Data", $huidigopbrengst);

   preg_match('/^(\((\d+\.\d+)\))/m', $Data, $output_array);
   $gasverbruik = floatval(@$output_array[2]);
   IPS_LogMessage("P1Data", $gasverbruik);

   $this->SetBuffer("Data", "");

  }

  return true;
 }

}
