<?php
namespace App\Services\Protocol;

class CantabriaProtocol implements ProtocolInterface {
    public function getCode(): string { return 'cantabria'; }
    public function getName(): string { return 'Cantabria'; }
    public function isFullyImplemented(): bool { return false; }
    public function getManageUrl(int $reportId): string { return '/protocolo-acoso'; }
}