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
class P1Module extends IPSModule
{

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
            $this->Data=$this->Data.$dt;

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