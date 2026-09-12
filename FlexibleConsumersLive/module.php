<?php

declare(strict_types=1);

class FlexibleConsumersLive extends IPSModule
{
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
            'power'  => 56815
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
         * HTML wird einmal geladen, Werte danach live aktualisiert.
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
            return '<div style="padding:20px;">module.html nicht gefunden.</div>';
        }


        $html =
            file_get_contents($file);


        if ($html === false) {
            return '<div style="padding:20px;">module.html konnte nicht gelesen werden.</div>';
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
                (int) $config['status'];

            $ids[] =
                (int) $config['power'];
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


    private function ReadInt(
        int $id
    ): ?int {
        if (
            $id <= 0 ||
            !IPS_VariableExists($id)
        ) {
            return null;
        }


        return (int)
            GetValue($id);
    }


    private function ReadFloat(
        int $id
    ): ?float {
        if (
            $id <= 0 ||
            !IPS_VariableExists($id)
        ) {
            return null;
        }


        return (float)
            GetValue($id);
    }


    private function ReadBool(
        int $id
    ): ?bool {
        if (
            $id <= 0 ||
            !IPS_VariableExists($id)
        ) {
            return null;
        }


        return (bool)
            GetValue($id);
    }


    private function SendLiveValues(): void
    {
        $payload = [];


        foreach (
            self::CONSUMERS
            as $key => $config
        ) {

            $payload[$key] = [

                'mode' =>
                    $this->ReadInt(
                        (int) $config['mode']
                    ),

                'active' =>
                    $this->ReadBool(
                        (int) $config['status']
                    ),

                'power' =>
                    $this->ReadFloat(
                        (int) $config['power']
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
