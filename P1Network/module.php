<?php

/**
 * @addtogroup plugwise
 * @{
 *
 * @package       P1
 * @file          module.php
 * @author        Herwin Jan Steehouwer (herwin@steehouwer.nu)
 * @copyright     2018 Herwin Jan Steehouwer
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       0.1
 *
 */



/**
 * P1 meter lezer
 *
 * @package       P1
 * @author        Herwin Jan Steehouwer (herwin@steehouwer.nu)
 * @copyright     2018 Herwin Jan Steehouwer
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       0.1
 * @example <b>Ohne</b>
 * @property string $Data
 */

/**
 * Ein Trait welcher es ermöglicht über einen Ident Variablen zu beschreiben.
 */
trait VariableHelper
{

    /**
     * Setzte eine IPS-Variable vom Typ bool auf den Wert von $value
     *
     * @access protected
     * @param string $Ident Ident der Statusvariable.
     * @param bool $Value Neuer Wert der Statusvariable.
     * @return bool true wenn Variable vorhanden sonst false.
     */
    protected function SetValueBoolean($Ident, $Value, $Profile = "")
    {
        $id = @$this->GetIDForIdent($Ident);
        if ($id == false) {
            $id = $this->RegisterVariableBoolean(str_replace(' ', '', $Ident), $this->Translate($Ident), $Profile);
        }
        SetValueBoolean($id, $Value);
        return true;
    }

    /**
     * Setzte eine IPS-Variable vom Typ integer auf den Wert von $value.
     *
     * @access protected
     * @param string $Ident Ident der Statusvariable.
     * @param int $Value Neuer Wert der Statusvariable.
     * @return bool true wenn Variable vorhanden sonst false.
     */
    protected function SetValueInteger($Ident, $Value, $Profile = "")
    {
        $id = @$this->GetIDForIdent($Ident);
        if ($id == false) {
            $id = $this->RegisterVariableInteger(str_replace(' ', '', $Ident), $this->Translate($Ident), $Profile);
        }
        SetValueInteger($id, $Value);
        return true;
    }

    /**
     * Setzte eine IPS-Variable vom Typ float auf den Wert von $value.
     *
     * @access protected
     * @param string $Ident Ident der Statusvariable.
     * @param float $Value Neuer Wert der Statusvariable.
     * @return bool true wenn Variable vorhanden sonst false.
     */
    protected function SetValueFloat($Ident, $Value, $Profile = "")
    {
        $id = @$this->GetIDForIdent($Ident);
        if ($id == false) {
            $id = $this->RegisterVariableFloat(str_replace(' ', '', $Ident), $this->Translate($Ident), $Profile);
        }
        SetValueFloat($id, $Value);
        return true;
    }

    /**
     * Setzte eine IPS-Variable vom Typ string auf den Wert von $value.
     *
     * @access protected
     * @param string $Ident Ident der Statusvariable.
     * @param string $Value Neuer Wert der Statusvariable.
     * @return bool true wenn Variable vorhanden sonst false.
     */
    protected function SetValueString($Ident, $Value, $Profile = "")
    {
        $id = @$this->GetIDForIdent($Ident);
        if ($id == false) {
            $id = $this->RegisterVariableString(str_replace(' ', '', $Ident), $this->Translate($Ident), $Profile);
        }
        SetValueString($id, $Value);
        return true;
    }
}

class P1Module extends IPSModule
{
use VariableHelper;

    public $Data="";

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function Create()
    {
        parent::Create();
        $this->RequireParent("{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}");
        $this->Data="";
        
        
        
    }

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function Destroy()
    {
        if (IPS_InstanceExists($this->InstanceID)) {
            
        }
        parent::Destroy();
    }

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function ApplyChanges()
    {
        parent::ApplyChanges();

    }

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
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
    protected function IOChangeState($State)
    {
        if ($State == IS_ACTIVE) {
            
            
        } else {
            
        }
    }

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function GetConfigurationForm()
    {
    }

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function GetConfigurationForParent()
    {
        $Config['StopBits'] = 1;
        $Config['BaudRate'] = 9600;
        $Config['Parity'] = 'Even';
        $Config['DataBits'] = 7;
        return json_encode($Config);
    }





    ################## DATAPOINTS PARENT
    /**
     * Empfängt Daten vom Parent.
     *
     * @access public
     * @param string $JSONString Das empfangene JSON-kodierte Objekt vom Parent.
     * @result bool True wenn Daten verarbeitet wurden, sonst false.
     */
    public function ReceiveData($JSONString)
    {
        $data = json_decode($JSONString);

        $dt = utf8_decode($data->Buffer);
        $pos = strpos($dt,"!");

        if ($pos === false)
        {
            $this->Data.=$dt;

        }else
        {
            $this->Data=$this->Data.$dt;
            IPS_LogMessage("P1Data", $this->Data);
            $this->Data="";
        }

        
        return true;
    }



}

/** @} */