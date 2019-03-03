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

  if (!IPS_VariableProfileExists("P1kWhProfile")) {
   IPS_CreateVariableProfile("P1kWhProfile", 2);
   IPS_SetVariableProfileDigits("P1kWhProfile", 2);
   IPS_SetVariableProfileText("P1kWhProfile", "", "kWh");
  }
  if (!IPS_VariableProfileExists("P1kWattProfile")) {
   IPS_CreateVariableProfile("P1kWattProfile", 1);

   IPS_SetVariableProfileText("P1kWattProfile", "", "Watt");
  }
  if (!IPS_VariableProfileExists("P1GasProfile")) {
   IPS_CreateVariableProfile("P1GasProfile", 2);
   IPS_SetVariableProfileDigits("P1GasProfile", 2);
   IPS_SetVariableProfileText("P1GasProfile", "", "m3");
  }
  if (!IPS_VariableProfileExists("P1TariefProfile")) {
   IPS_CreateVariableProfile("P1TariefProfile", 0);

   IPS_SetVariableProfileAssociation("P1TariefProfile", 0, "Dag tarief", "", -1);
   IPS_SetVariableProfileAssociation("P1TariefProfile", 1, "Nacht tarief", "", -1);
  }

  $id = $this->CreateVariable("Verbuik Nacht", 2, 0, "P1VerbruikNacht", $this->InstanceID);
  IPS_SetVariableCustomProfile($id, "P1kWhProfile");
  $id = $this->CreateVariable("Verbuik Dag", 2, 0, "P1VerbruikDag", $this->InstanceID);
  IPS_SetVariableCustomProfile($id, "P1kWhProfile");
  $id = $this->CreateVariable("Huidig Vebruik", 2, 0, "P1HuidigVerbruik", $this->InstanceID);
  IPS_SetVariableCustomProfile($id, "P1kWattProfile");

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
 protected function _IOChangeState($State) {
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
   $verbruiknacht = (float)(@$output_array[2]);
   IPS_LogMessage("P1Data", $verbruiknacht);
   $sid = @IPS_GetObjectIDByIdent("P1VerbruikNacht", $this->InstanceID);
   if ($sid) {
    SetValue($sid, $verbruiknacht);
   }

   preg_match('/^(1-0:1\.8\.2\((\d+\.\d+)\*kWh\))/m', $Data, $output_array);
   $verbruikdag = (float)(@$output_array[2]);
   IPS_LogMessage("P1Data", $verbruikdag);

   preg_match('/^(1-0:2\.8\.1\((\d+\.\d+)\*kWh\))/m', $Data, $output_array);
   $opbrengstnacht = (float)(@$output_array[2]);
   IPS_LogMessage("P1Data", $opbrengstnacht);

   preg_match('/^(1-0:2\.8\.2\((\d+\.\d+)\*kWh\))/m', $Data, $output_array);
   $opbrengstdag = (float)(@$output_array[2]);
   IPS_LogMessage("P1Data", $opbrengstdag);

   preg_match('/^(0-0:96\.14\.0\((\d+)\))/m', $Data, $output_array);
   $tarief = boolval(@$output_array[2]);
   IPS_LogMessage("P1Data", $tarief);

   preg_match('/^(1-0:1\.7\.0\((\d+.\d+)\*kW\))/m', $Data, $output_array);
   $huidigvebruik = (float)(@$output_array[2]) * 1000;
   IPS_LogMessage("P1Data", $huidigvebruik);

   $sid = @IPS_GetObjectIDByIdent("P1HuidigVerbruik", $this->InstanceID);
   if ($sid) {
    SetValue($sid, $huidigvebruik);
   }

   preg_match('/^(1-0:2\.7\.0\((\d+.\d+)\*kW\))/m', $Data, $output_array);
   $huidigopbrengst = (float)(@$output_array[2]) * 1000;
   IPS_LogMessage("P1Data", $huidigopbrengst);

   $huidigtotaal = $huidigvebruik - $huidigopbrengst;
   IPS_LogMessage("P1Data", $huidigtotaal);

   preg_match('/^(\((\d+\.\d+)\))/m', $Data, $output_array);
   $gasverbruik = (float)(@$output_array[2]);
   IPS_LogMessage("P1Data", $gasverbruik);

   $this->SetBuffer("Data", "");

  }

  return true;
 }

 private function __CreateCategory($Name, $Ident = '', $ParentID = 0) {
  $RootCategoryID = $this->InstanceID;
  echo "CreateCategory: ( $Name, $Ident, $ParentID ) \n";
  if ('' != $Ident) {
   $CatID = @IPS_GetObjectIDByIdent($Ident, $ParentID);
   if (false !== $CatID) {
    $Obj = IPS_GetObject($CatID);
    if (0 == $Obj['ObjectType']) { // is category?
     return $CatID;
    }
   }
  }
  $CatID = IPS_CreateCategory();
  IPS_SetName($CatID, $Name);
  IPS_SetIdent($CatID, $Ident);
  if (0 == $ParentID) {
   if (IPS_ObjectExists($RootCategoryID)) {
    $ParentID = $RootCategoryID;
   }
  }
  IPS_SetParent($CatID, $ParentID);
  return $CatID;
 }

 private function __CreateVariable($Name, $Type, $Value, $Ident = '', $ParentID = 0) {
  //echo "CreateVariable: ( $Name, $Type, $Value, $Ident, $ParentID ) \n";
  if ('' != $Ident) {
   $VarID = @IPS_GetObjectIDByIdent($Ident, $ParentID);
   if (false !== $VarID) {
    $this->SetVariable($VarID, $Type, $Value);
    return $VarID;
   }
  }
  $VarID = @IPS_GetObjectIDByName($Name, $ParentID);
  if (false !== $VarID) { // exists?
   $Obj = IPS_GetObject($VarID);
   if (2 == $Obj['ObjectType']) { // is variable?
    $Var = IPS_GetVariable($VarID);
    if ($Type == $Var['VariableValue']['ValueType']) {
     $this->SetVariable($VarID, $Type, $Value);
     return $VarID;
    }
   }
  }
  $VarID = IPS_CreateVariable($Type);
  IPS_SetParent($VarID, $ParentID);
  IPS_SetName($VarID, $Name);
  if ('' != $Ident) {
   IPS_SetIdent($VarID, $Ident);
  }
  $this->SetVariable($VarID, $Type, $Value);
  return $VarID;
 }

 private function __SetVariable($VarID, $Type, $Value) {
  switch ($Type) {
  case 0: // boolean
   SetValueBoolean($VarID, $Value);
   break;
  case 1: // integer
   SetValueInteger($VarID, $Value);
   break;
  case 2: // float
   SetValueFloat($VarID, $Value);
   break;
  case 3: // string
   SetValueString($VarID, $Value);
   break;
  }
 }

}
