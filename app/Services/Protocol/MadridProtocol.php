<?php
namespace App\Services\Protocol;

class MadridProtocol implements ProtocolInterface {
    public function getCode(): string { return 'madrid'; }
    public function getName(): string { return 'Madrid'; }
    public function isFullyImplemented(): bool { return false; }
    public function getManageUrl(int $reportId): string { return '/protocolo-acoso'; }
}