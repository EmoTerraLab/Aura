<?php
namespace App\Services\Protocol;

class AndaluciaProtocol implements ProtocolInterface {
    public function getCode(): string { return 'andalucia'; }
    public function getName(): string { return 'Andalucía'; }
    public function isFullyImplemented(): bool { return false; }
    public function getManageUrl(int $reportId): string { return '/protocolo-acoso'; }
}