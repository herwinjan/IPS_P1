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
 * @property string $Data
 * @property integer $Count
 *
 */
class P1Module extends IPSModule
{
    
     /**
         * Data
         * @var string $Data
         */
    public $Data="";

     /**
         * Data
         * @var int $Cound
         */
    public $Count=0;

    

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
        $this->RegisterPropertyString("Data","");
        
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

    public function ReceiveData($JSONString)
    {
        $data = json_decode($JSONString);

        $dt = utf8_decode($data->Buffer);
        $pos = strpos($dt,"!");

        IPS_LogMessage("P1 Count", $this->Count++);


        if ($pos === false)
        {
            $this->Data.=$dt;
            IPS_LogMessage("P1Data u", $this->Data);
            return false;

        }else
        {
            $this->Data=$this->Data.$dt;
            IPS_LogMessage("P1Data", $this->Data);
            $this->Data="";
            
        }

        
        return true;
    }


}
