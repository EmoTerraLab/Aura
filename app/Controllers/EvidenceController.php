<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;

class EvidenceController
{
    /**
     * GET /protocol/evidence/{id}/download
     * Sirve el archivo de evidencia con control de permisos estricto.
     */
    public function download($id): void
    {
        $id = (int)$id;
        $db = Database::getInstance();

        // 1. Obtener datos de la evidencia
        $stmt = $db->prepare("SELECT * FROM protocol_evidence WHERE id = ?");
        $stmt->execute([$id]);
        $evidence = $stmt->fetch();

        if (!$evidence) {
            http_response_code(404);
            die("Evidencia no encontrada.");
        }

        // 2. Verificar permisos
        // Solo Admin, Dirección, COCOBE o el usuario que la subió
        $canAccess = Auth::hasRole(['admin', 'direccion']) || Auth::isCocobe() || Auth::id() === $evidence['uploaded_by'];

        if (!$canAccess) {
            http_response_code(403);
            die("No tienes permiso para acceder a esta evidencia.");
        }

        $filePath = __DIR__ . '/../../storage/evidence/' . $evidence['filename'];

        if (!file_exists($filePath)) {
            http_response_code(404);
            die("El archivo físico no existe en el servidor.");
        }

        // 3. Servir el archivo de forma segura
        header('Content-Type: ' . ($evidence['mime_type'] ?? 'application/octet-stream'));
        header('Content-Disposition: attachment; filename="' . $evidence['original_name'] . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');

        readfile($filePath);
        exit;
    }
}
