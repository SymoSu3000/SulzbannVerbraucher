<?php

declare(strict_types=1);

class FlexibleConsumersLive extends IPSModule
{
    /*
     * ============================================================
     * FESTE, BESTÄTIGTE DATENPUNKTE
     * ============================================================
     *
     * ug:
     *   Mode   #48725
     *   Status #21846
     *   Power  #47479
     *
     * og:
     *   Mode   #37326
     *   Status #20351
     *   Power  #48347
     *
     * pool:
     *   Mode   #29959
     *   Power  #56815
     *   Status wird bei Bedarf unter Schaltinstanz #17098 gesucht.
     *
     * boiler:
     *   Mode   #17552
     *   Status #10737
     *   Power  #19394
     *
     * heater:
     *   Mode   #24813
     *   Status #53546
     *   Power  #37285
     */

    private const CONSUMERS = [

        'ug' => [
            'mode'   => 48725,
            'status' => 21846,
            'power'  => 47479
        ],

        'og' => [
            'mode'   => 37326,
            'status' => 20351,
            'power'  => 48347
        ],

        'pool' => [
            'mode'   => 29959,
            'status' => 21048,
            'power'  => 56815,
            'switch' => 17098
        ],

        'boiler' => [
            'mode'   => 17552,
            'status' => 10737,
            'power'  => 19394
        ],

        'heater' => [
            'mode'   => 24813,
            'status' => 53546,
            'power'  => 37285
        ]

    ];


    public function Create(): void
    {
        parent::Create();

        /*
         * Native Visualisierung.
         * HTML bleibt bestehen, nur Werte werden aktualisiert.
         */
        $this->SetVisualizationType(1);
    }


    public function ApplyChanges(): void
    {
        parent::ApplyChanges();

        foreach ($this->GetObservedIDs() as $id) {

            if (
                $id > 0 &&
                IPS_VariableExists($id)
            ) {

                $this->RegisterMessage(
                    $id,
                    VM_UPDATE
                );
            }
        }
    }


    public function GetVisualizationTile(): string
    {
        $file =
            __DIR__ .
            DIRECTORY_SEPARATOR .
            'module.html';


        if (!file_exists($file)) {

            return
                '<div style="padding:20px;">' .
                'module.html nicht gefunden.' .
                '</div>';
        }


        $html =
            file_get_contents(
                $file
            );


        if ($html === false) {

            return
                '<div style="padding:20px;">' .
                'module.html konnte nicht gelesen werden.' .
                '</div>';
        }


        return $html;
    }


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


        if ($Message === VM_UPDATE) {

            $this->SendLiveValues();
        }
    }


    public function RequestAction(
        $Ident,
        $Value
    ): void {

        if ($Ident === 'Refresh') {

            $this->SendLiveValues();

            return;
        }


        throw new Exception(
            'Invalid Ident: ' .
            $Ident
        );
    }


    /* ============================================================
       STATUSVARIABLE AUFLÖSEN
    ============================================================ */

    private function ResolveStatusID(
        array $config
    ): int {

        /*
         * Zuerst bekannte Status-ID verwenden.
         */

        $preferred =
            (int) (
                $config['status'] ??
                0
            );


        if (
            $preferred > 0 &&
            IPS_VariableExists(
                $preferred
            )
        ) {

            return $preferred;
        }


        /*
         * Pool:
         * Falls die bekannte Status-ID nicht existiert,
         * Bool-Variable "Value" unter der Schaltinstanz suchen.
         */

        $switchID =
            (int) (
                $config['switch'] ??
                0
            );


        if (
            $switchID <= 0 ||
            !IPS_InstanceExists(
                $switchID
            )
        ) {

            return 0;
        }


        foreach (
            IPS_GetChildrenIDs(
                $switchID
            )
            as $childID
        ) {

            if (
                !IPS_VariableExists(
                    $childID
                )
            ) {
                continue;
            }


            $object =
                IPS_GetObject(
                    $childID
                );


            $variable =
                IPS_GetVariable(
                    $childID
                );


            if (
                (
                    $object['ObjectIdent'] ??
                    ''
                ) === 'Value'
                &&
                (
                    $variable['VariableType'] ??
                    -1
                ) === 0
            ) {

                return $childID;
            }
        }


        return 0;
    }


    /* ============================================================
       BEOBACHTETE IDS
    ============================================================ */

    private function GetObservedIDs(): array
    {
        $ids = [];


        foreach (
            self::CONSUMERS
            as $config
        ) {

            $ids[] =
                (int) $config['mode'];

            $ids[] =
                (int) $config['power'];

            $ids[] =
                $this->ResolveStatusID(
                    $config
                );
        }


        return array_values(
            array_unique(
                array_filter(
                    $ids,
                    static fn($id) =>
                        $id > 0
                )
            )
        );
    }


    /* ============================================================
       WERTE LESEN
    ============================================================ */

    private function ReadInt(
        int $id
    ): ?int {

        if (
            $id <= 0 ||
            !IPS_VariableExists(
                $id
            )
        ) {

            return null;
        }


        return (int)
            GetValue(
                $id
            );
    }


    private function ReadFloat(
        int $id
    ): ?float {

        if (
            $id <= 0 ||
            !IPS_VariableExists(
                $id
            )
        ) {

            return null;
        }


        return (float)
            GetValue(
                $id
            );
    }


    private function ReadBool(
        int $id
    ): ?bool {

        if (
            $id <= 0 ||
            !IPS_VariableExists(
                $id
            )
        ) {

            return null;
        }


        return (bool)
            GetValue(
                $id
            );
    }


    /* ============================================================
       DATEN AN HTML SENDEN
    ============================================================ */

    private function SendLiveValues(): void
    {
        $payload = [];


        foreach (
            self::CONSUMERS
            as $key => $config
        ) {

            $statusID =
                $this->ResolveStatusID(
                    $config
                );


            $payload[$key] = [

                'mode' =>
                    $this->ReadInt(
                        (int)
                        $config['mode']
                    ),

                'active' =>
                    $this->ReadBool(
                        $statusID
                    ),

                'power' =>
                    $this->ReadFloat(
                        (int)
                        $config['power']
                    )

            ];
        }


        $json =
            json_encode(
                $payload,
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES
            );


        if ($json === false) {
            return;
        }


        $this->UpdateVisualizationValue(
            $json
        );
    }
}
