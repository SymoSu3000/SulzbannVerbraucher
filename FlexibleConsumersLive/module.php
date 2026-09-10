<?php

declare(strict_types=1);

class FlexibleConsumersLive extends IPSModule
{
    // ========================================================================
    // Verbraucher - feste Zuordnung
    // ========================================================================

    private const CONSUMERS = [

        [
            'id'       => 33007,
            'key'      => 'boiler',
            'name'     => 'Boiler',
            'sub'      => 'Warmwasser',

            'mode'     => 17552,
            'status'   => 10737,
            'power'    => 19394,
            'reason'   => 23015,
            'priority' => 55502,

            'icon'     => 'boiler'
        ],

        [
            'id'       => 52793,
            'key'      => 'dehum_ug',
            'name'     => 'Entfeuchter UG',
            'sub'      => 'Untergeschoss',

            'mode'     => 48725,
            'status'   => 21846,
            'power'    => 47479,
            'reason'   => 57923,
            'priority' => 53271,

            'icon'     => 'drop'
        ],

        [
            'id'       => 55387,
            'key'      => 'dehum_og',
            'name'     => 'Entfeuchter OG',
            'sub'      => 'Obergeschoss',

            'mode'     => 37326,
            'status'   => 20351,
            'power'    => 48347,
            'reason'   => 55280,
            'priority' => 10472,

            'icon'     => 'drop'
        ],

        [
            'id'       => 31862,
            'key'      => 'pool',
            'name'     => 'Poolpumpe + Salzelektrolyse',
            'sub'      => 'Pooltechnik',

            'mode'     => 29959,
            'status'   => 21048,
            'power'    => 56815,
            'reason'   => 26781,
            'priority' => 17428,

            'icon'     => 'pool'
        ],

        [
            'id'       => 16478,
            'key'      => 'buffer',
            'name'     => 'Heizstab / Puffer',
            'sub'      => 'Heizung',

            'mode'     => 24813,
            'status'   => 53546,
            'power'    => 37285,
            'reason'   => 50074,
            'priority' => 19333,

            'icon'     => 'heater'
        ]
    ];


    // ========================================================================
    // Create
    // ========================================================================

    public function Create(): void
    {
        parent::Create();

        $this->SetVisualizationType(1);
    }


    // ========================================================================
    // ApplyChanges
    // ========================================================================

    public function ApplyChanges(): void
    {
        parent::ApplyChanges();

        $this->RegisterConsumerMessages();
    }


    // ========================================================================
    // HTML SDK
    // ========================================================================

    public function GetVisualizationTile(): string
    {
        $file =
            __DIR__ .
            '/module.html';


        if (
            !file_exists(
                $file
            )
        ) {

            return
                '<div>module.html nicht gefunden</div>';
        }


        $html =
            file_get_contents(
                $file
            );


        if (
            $html === false
        ) {

            return
                '<div>module.html konnte nicht gelesen werden</div>';
        }


        return $html;
    }


    // ========================================================================
    // Aktionen
    // ========================================================================

    public function RequestAction(
        $Ident,
        $Value
    ): void {
        if (
            $Ident === 'Refresh'
        ) {

            $this->SendLiveValues();

            return;
        }


        throw new Exception(
            'Ungültige Aktion: ' .
            $Ident
        );
    }


    // ========================================================================
    // Nachrichten
    // ========================================================================

    public function MessageSink(
        $TimeStamp,
        $SenderID,
        $Message,
        $Data
    ): void {
        parent::MessageSink(
            $TimeStamp,
            $SenderID,
            $Message,
            $Data
        );


        if (
            $Message === VM_UPDATE
        ) {

            $this->SendLiveValues();
        }
    }


    // ========================================================================
    // Variablen registrieren
    // ========================================================================

    private function RegisterConsumerMessages(): void
    {
        foreach (
            self::CONSUMERS
            as
            $consumer
        ) {

            $ids = [

                intval(
                    $consumer['mode']
                ),

                intval(
                    $consumer['status']
                ),

                intval(
                    $consumer['power']
                ),

                intval(
                    $consumer['reason']
                ),

                intval(
                    $consumer['priority']
                )
            ];


            foreach (
                $ids
                as
                $id
            ) {

                if (
                    IPS_VariableExists(
                        $id
                    )
                ) {

                    $this->RegisterMessage(
                        $id,
                        VM_UPDATE
                    );
                }
            }
        }
    }


    // ========================================================================
    // Live-Werte senden
    // ========================================================================

    private function SendLiveValues(): void
    {
        $items =
            [];


        foreach (
            self::CONSUMERS
            as
            $consumer
        ) {

            $items[] =
                $this->BuildConsumerData(
                    $consumer
                );
        }


        // ====================================================================
        // Automatisch nach Priorität sortieren
        //
        // Kleine Zahl = höhere Priorität = weiter oben
        // ====================================================================

        usort(
            $items,
            function (
                array $a,
                array $b
            ): int {

                $priorityA =
                    intval(
                        $a['priority']
                        ??
                        PHP_INT_MAX
                    );

                $priorityB =
                    intval(
                        $b['priority']
                        ??
                        PHP_INT_MAX
                    );


                // Bei gleicher Priorität stabil nach Name sortieren
                if (
                    $priorityA
                    ===
                    $priorityB
                ) {

                    return
                        strcasecmp(
                            strval(
                                $a['name']
                                ??
                                ''
                            ),
                            strval(
                                $b['name']
                                ??
                                ''
                            )
                        );
                }


                return
                    $priorityA
                    <=>
                    $priorityB;
            }
        );


        $payload = [

            'type' =>
                'consumers',

            'timestamp' =>
                time(),

            'items' =>
                $items
        ];


        $this->UpdateVisualizationValue(
            json_encode(
                $payload,
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES
            )
        );
    }


    // ========================================================================
    // Einzelnen Verbraucher aufbauen
    // ========================================================================

    private function BuildConsumerData(
        array $consumer
    ): array {
        $modeID =
            intval(
                $consumer['mode']
            );

        $statusID =
            intval(
                $consumer['status']
            );

        $powerID =
            intval(
                $consumer['power']
            );

        $reasonID =
            intval(
                $consumer['reason']
            );

        $priorityID =
            intval(
                $consumer['priority']
            );


        $modeRaw =
            $this->ReadInt(
                $modeID
            );


        $isOn =
            $this->ReadBool(
                $statusID
            );


        $configuredPowerKW =
            $this->ReadFloat(
                $powerID
            );


        $reason =
            $this->ReadString(
                $reasonID
            );


        $priority =
            $this->ReadInt(
                $priorityID
            );


        return [

            'id' =>
                intval(
                    $consumer['id']
                ),

            'key' =>
                strval(
                    $consumer['key']
                ),

            'name' =>
                strval(
                    $consumer['name']
                ),

            'sub' =>
                strval(
                    $consumer['sub']
                ),

            'icon' =>
                strval(
                    $consumer['icon']
                ),

            // Betriebsart
            'modeRaw' =>
                $modeRaw,

            'modeText' =>
                $this->ModeText(
                    $modeRaw
                ),

            // Tatsächlicher Zustand
            'isOn' =>
                $isOn,

            'statusText' =>
                $isOn
                    ?
                    'Ein'
                    :
                    'Aus',

            // Konfigurierte EMS-Leistung
            'powerKW' =>
                $configuredPowerKW,

            'powerText' =>
                $this->FormatPowerKW(
                    $configuredPowerKW
                ),

            // EMS Status / Grund
            'reason' =>
                $reason,

            // Priorität
            'priority' =>
                $priority
        ];
    }


    // ========================================================================
    // Betriebsart
    //
    // 0 = Aus
    // 1 = Automatik
    // 2 = Erzwungen Ein
    // ========================================================================

    private function ModeText(
        int $mode
    ): string {
        switch (
            $mode
        ) {

            case 0:

                return
                    'Aus';


            case 1:

                return
                    'Automatik';


            case 2:

                return
                    'Erzwungen Ein';


            default:

                return
                    'Unbekannt';
        }
    }


    // ========================================================================
    // Leistung
    // ========================================================================

    private function FormatPowerKW(
        float $kw
    ): string {
        if (
            abs(
                $kw
            )
            <
            0.05
        ) {

            return
                '0 W';
        }


        if (
            abs(
                $kw
            )
            <
            1.0
        ) {

            return
                number_format(
                    $kw * 1000,
                    0,
                    '.',
                    ''
                )
                .
                ' W';
        }


        return
            number_format(
                $kw,
                1,
                '.',
                ''
            )
            .
            ' kW';
    }


    // ========================================================================
    // Sichere Leser
    // ========================================================================

    private function ReadInt(
        int $id
    ): int {
        if (
            !IPS_VariableExists(
                $id
            )
        ) {

            return 0;
        }


        return
            intval(
                GetValue(
                    $id
                )
            );
    }


    private function ReadBool(
        int $id
    ): bool {
        if (
            !IPS_VariableExists(
                $id
            )
        ) {

            return false;
        }


        return
            boolval(
                GetValue(
                    $id
                )
            );
    }


    private function ReadFloat(
        int $id
    ): float {
        if (
            !IPS_VariableExists(
                $id
            )
        ) {

            return 0.0;
        }


        return
            floatval(
                GetValue(
                    $id
                )
            );
    }


    private function ReadString(
        int $id
    ): string {
        if (
            !IPS_VariableExists(
                $id
            )
        ) {

            return '';
        }


        return
            strval(
                GetValue(
                    $id
                )
            );
    }
}
