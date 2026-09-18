<?php

declare(strict_types=1);

class FlexibleConsumersLive extends IPSModule
{
    private const CONSUMERS = [
        'ug' => ['mode' => 48725, 'status' => 21846, 'power' => 47479, 'reason' => 57923, 'temp' => 0, 'limit' => 0, 'settings' => 52793],
        'og' => ['mode' => 37326, 'status' => 20351, 'power' => 48347, 'reason' => 55280, 'temp' => 0, 'limit' => 0, 'settings' => 55387],
        'pool' => ['mode' => 29959, 'status' => 21048, 'power' => 56815, 'reason' => 26781, 'temp' => 0, 'limit' => 0, 'settings' => 31862],
        'boiler' => ['mode' => 17552, 'status' => 10737, 'power' => 19394, 'reason' => 23015, 'temp' => 39112, 'limit' => 41703, 'settings' => 33007],
        'heater' => ['mode' => 24813, 'status' => 53546, 'power' => 37285, 'reason' => 50074, 'temp' => 27553, 'limit' => 50024, 'settings' => 16478]
    ];

    public function Create(): void
    {
        parent::Create();
        $this->SetVisualizationType(1);
    }

    public function ApplyChanges(): void
    {
        parent::ApplyChanges();
        $this->SetVisualizationType(1);

        foreach ($this->GetObservedIDs() as $id) {
            if (IPS_VariableExists($id)) {
                $this->RegisterMessage($id, VM_UPDATE);
            }
        }
    }

    public function GetVisualizationTile(): string
    {
        $file = __DIR__ . DIRECTORY_SEPARATOR . 'module.html';
        if (!file_exists($file)) {
            return '<div style="padding:20px">module.html nicht gefunden.</div>';
        }

        $html = file_get_contents($file);
        if ($html === false) {
            return '<div style="padding:20px">module.html konnte nicht gelesen werden.</div>';
        }

        $json = json_encode(
            $this->BuildVisualizationData(),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        );

        return str_replace('__SULZFC_INITIAL_DATA__', $json === false ? '{}' : $json, $html);
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data): void
    {
        parent::MessageSink($TimeStamp, $SenderID, $Message, $Data);
        if ($Message === VM_UPDATE) {
            $this->SendLiveValues();
        }
    }

    public function RequestAction($Ident, $Value): void
    {
        if ($Ident === 'Refresh') {
            $this->SendLiveValues();
            return;
        }

        throw new Exception('Invalid Ident: ' . $Ident);
    }

    private function GetObservedIDs(): array
    {
        $ids = [];
        foreach (self::CONSUMERS as $config) {
            foreach (['mode', 'status', 'power', 'reason', 'temp', 'limit'] as $key) {
                $id = (int)$config[$key];
                if ($id > 0) {
                    $ids[] = $id;
                }
            }
        }

        return array_values(array_unique($ids));
    }

    private function ReadValue(int $id)
    {
        return $id > 0 && IPS_VariableExists($id) ? GetValue($id) : null;
    }

    private function BuildVisualizationData(): array
    {
        $payload = ['timestamp' => time(), 'consumers' => []];

        foreach (self::CONSUMERS as $key => $config) {
            $settingsID = (int)$config['settings'];
            $payload['consumers'][$key] = [
                'mode' => $this->ReadValue((int)$config['mode']),
                'active' => $this->ReadValue((int)$config['status']),
                'power' => $this->ReadValue((int)$config['power']),
                'reason' => $this->ReadValue((int)$config['reason']),
                'temperature' => $this->ReadValue((int)$config['temp']),
                'temperatureLimit' => $this->ReadValue((int)$config['limit']),
                'settingsObjectID' => $settingsID > 0 && IPS_ObjectExists($settingsID) ? $settingsID : null
            ];
        }

        return $payload;
    }

    private function SendLiveValues(): void
    {
        $json = json_encode($this->BuildVisualizationData(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json !== false) {
            $this->UpdateVisualizationValue($json);
        }
    }
}
