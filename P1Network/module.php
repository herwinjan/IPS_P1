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
 * @property int $FrameID
 * @property array $ReplyData
 * @property string $BufferIN
 * @property string $CirclePlusMAC
 * @property string $StickMAC
 * @property string $NetworkID
 * @property array $NewNodes
 * @property array $Nodes
 * @property int $SearchIndex
 * @property Plugwise_NetworkState $NetworkState
 */
class PlugwiseNetwork extends IPSModule
{
    use BufferHelper,
        DebugHelper,
        InstanceStatus,
        Semaphore,
        VariableHelper {
        InstanceStatus::MessageSink as IOMessageSink; // MessageSink gibt es sowohl hier in der Klasse, als auch im Trait InstanceStatus. Hier wird für die Methode im Trait ein Alias benannt.
    }
    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function Create()
    {
        parent::Create();
        $this->RequireParent("{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}");
        
        $this->Buffer = "";
        
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

        $this->Buffer = "";
        parent::ApplyChanges();


        // Config prüfen
        $this->RegisterParent();

        // Wenn Kernel nicht bereit, dann warten... KR_READY kommt ja gleich
        if (IPS_GetKernelRunlevel() <> KR_READY) {
            return;
        }


        // Wenn Parent aktiv, dann Anmeldung an der Hardware bzw. Datenabgleich starten
        if ($this->HasActiveParent()) {
            $this->StartNetwork();
        } else {
            $this->SetStatus(IS_INACTIVE);
        }
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
     * Wird ausgeführt wenn der Kernel hochgefahren wurde.
     */
    protected function KernelReady()
    {
        $this->RegisterParent();
        if ($this->HasActiveParent()) {
            
        }
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



       
        return true;
    }



}

/** @} */