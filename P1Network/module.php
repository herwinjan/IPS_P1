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

/**
 * Trait mit Hilfsfunktionen für Variablenprofile.
 */
trait VariableProfile
{

    /**
     * Erstell und konfiguriert ein VariablenProfil für den Typ integer mit Assoziationen
     *
     * @access protected
     * @param string $Name Name des Profils.
     * @param string $Icon Name des Icon.
     * @param string $Prefix Prefix für die Darstellung.
     * @param string $Suffix Suffix für die Darstellung.
     * @param array $Associations Assoziationen der Werte als Array.
     */
    protected function RegisterProfileIntegerEx($Name, $Icon, $Prefix, $Suffix, $Associations)
    {
        if (sizeof($Associations) === 0) {
            $MinValue = 0;
            $MaxValue = 0;
        } else {
            $MinValue = $Associations[0][0];
            $MaxValue = $Associations[sizeof($Associations) - 1][0];
        }
        $this->RegisterProfileInteger($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, 0);
        $old = IPS_GetVariableProfile($Name)["Associations"];
        $OldValues = array_column($old, 'Value');
        foreach ($Associations as $Association) {
            IPS_SetVariableProfileAssociation($Name, $Association[0], $Association[1], $Association[2], $Association[3]);
            $OldKey = array_search($Association[0], $OldValues);
            if (!($OldKey === false)) {
                unset($OldValues[$OldKey]);
            }
        }
        foreach ($OldValues as $OldKey => $OldValue) {
            IPS_SetVariableProfileAssociation($Name, $OldValue, '', '', 0);
        }
    }

    /**
     * Erstell und konfiguriert ein VariablenProfil für den Typ integer
     *
     * @access protected
     * @param string $Name Name des Profils.
     * @param string $Icon Name des Icon.
     * @param string $Prefix Prefix für die Darstellung.
     * @param string $Suffix Suffix für die Darstellung.
     * @param int $MinValue Minimaler Wert.
     * @param int $MaxValue Maximaler wert.
     * @param int $StepSize Schrittweite
     */
    protected function RegisterProfileInteger($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize)
    {
        $this->RegisterProfile(1, $Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize);
    }

    /**
     * Erstell und konfiguriert ein VariablenProfil für den Typ float
     *
     * @access protected
     * @param string $Name Name des Profils.
     * @param string $Icon Name des Icon.
     * @param string $Prefix Prefix für die Darstellung.
     * @param string $Suffix Suffix für die Darstellung.
     * @param int $MinValue Minimaler Wert.
     * @param int $MaxValue Maximaler wert.
     * @param int $StepSize Schrittweite
     */
    protected function RegisterProfileFloat($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize, $Digits)
    {
        $this->RegisterProfile(2, $Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize, $Digits);
    }

    /**
     * Erstell und konfiguriert ein VariablenProfil für den Typ float
     *
     * @access protected
     * @param int $VarTyp Typ der Variable
     * @param string $Name Name des Profils.
     * @param string $Icon Name des Icon.
     * @param string $Prefix Prefix für die Darstellung.
     * @param string $Suffix Suffix für die Darstellung.
     * @param int $MinValue Minimaler Wert.
     * @param int $MaxValue Maximaler wert.
     * @param int $StepSize Schrittweite
     */
    protected function RegisterProfile($VarTyp, $Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize, $Digits = 0)
    {
        if (!IPS_VariableProfileExists($Name)) {
            IPS_CreateVariableProfile($Name, $VarTyp);
        } else {
            $profile = IPS_GetVariableProfile($Name);
            if ($profile['ProfileType'] != $VarTyp) {
                throw new Exception("Variable profile type does not match for profile " . $Name, E_USER_WARNING);
            }
        }

        IPS_SetVariableProfileIcon($Name, $Icon);
        IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
        IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, $StepSize);
        if ($VarTyp == vtFloat) {
            IPS_SetVariableProfileDigits($Name, $Digits);
        }
    }

    /**
     * Löscht ein Variablenprofile, sofern es nicht außerhalb dieser Instanz noch verwendet wird.
     * @param string $Name Name des zu löschenden Profils.
     */
    protected function UnregisterProfil(string $Name)
    {
        if (!IPS_VariableProfileExists($Name)) {
            return;
        }
        foreach (IPS_GetVariableList() as $VarID) {
            if (IPS_GetParent($VarID) == $this->InstanceID) {
                continue;
            }
            if (IPS_GetVariable($VarID)['VariableCustomProfile'] == $Name) {
                return;
            }
            if (IPS_GetVariable($VarID)['VariableProfile'] == $Name) {
                return;
            }
        }
        IPS_DeleteVariableProfile($Name);
    }
}


class P1Module extends IPSModule
{
use VariableHelper,VariableProfile;

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