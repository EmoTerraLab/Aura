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
            'andalucia'       => 'Andalucía',
            'asturias'        => 'Asturias',
            'baleares'        => 'Baleares',
            'canarias'        => 'Canarias',
            'cantabria'       => 'Cantabria',
            'castilla_leon'   => 'Castilla y León',
            'castilla_mancha' => 'Castilla-La Mancha',
            'valencia'        => 'Comunidad Valenciana',
            'extremadura'     => 'Extremadura',
            'galicia'         => 'Galicia',
            'madrid'          => 'Madrid',
            'murcia'          => 'Murcia',
            'navarra'         => 'Navarra',
            'pais_vasco'      => 'País Vasco',
            'rioja'           => 'La Rioja',
            'ceuta'           => 'Ceuta',
            'melilla'         => 'Melilla'
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
