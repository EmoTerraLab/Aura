<?php
namespace App\Services\Protocol;

class MelillaProtocol implements ProtocolInterface {
    public function getCode(): string { return 'melilla'; }
    public function getName(): string { return 'Melilla'; }
    public function isFullyImplemented(): bool { return false; }
    public function getManageUrl(int $reportId): string { return '/protocolo-acoso'; }
}