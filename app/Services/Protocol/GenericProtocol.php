<?php
namespace App\Services\Protocol;

/**
 * GenericProtocol - Fallback para Comunidades Autónomas sin flujo de trabajo automatizado.
 */
class GenericProtocol implements ProtocolInterface {
    private string $ccaaCode;

    public function __construct(string $ccaaCode) {
        $this->ccaaCode = $ccaaCode;
    }

    public function getCode(): string {
        return $this->ccaaCode;
    }

    public function getName(): string {
        $names = [
            'AND' => 'Andalucía',
            'AST' => 'Asturias',
            'BAL' => 'Baleares',
            'CAN' => 'Canarias',
            'CNT' => 'Cantabria',
            'CYL' => 'Castilla y León',
            'CLM' => 'Castilla-La Mancha',
            'EXT' => 'Extremadura',
            'GAL' => 'Galicia',
            'MAD' => 'Madrid',
            'MUR' => 'Murcia',
            'NAV' => 'Navarra',
            'PV'  => 'País Vasco',
            'RIO' => 'La Rioja',
            'CEU' => 'Ceuta',
            'MEL' => 'Melilla'
        ];
        return $names[$this->ccaaCode] ?? ucfirst($this->ccaaCode);
    }

    public function isFullyImplemented(): bool {
        return false;
    }

    public function getManageUrl(int $reportId): string {
        // Redirigir a la vista estática de consulta general
        return '/protocolo-acoso';
    }
}
