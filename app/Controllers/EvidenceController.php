<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;

class EvidenceController
{
    /**
     * GET /protocol/evidence/{id}/download
     * Serveix el fitxer d'evidència amb control estricte de permisos (Sènior Refactor).
     */
    public function download(string|int $id): void
    {
        $id = (int)$id;
        $db = Database::getInstance();

        // 1. Recuperar metadades
        $stmt = $db->prepare("SELECT * FROM protocol_evidence WHERE id = ?");
        $stmt->execute([$id]);
        $evidence = $stmt->fetch();

        if (!$evidence) {
            http_response_code(404);
            echo "Evidència no trobada.";
            return;
        }

        // 2. Control d'accés (RBAC + COCOBE)
        $isAuthorized = Auth::hasRole(['admin', 'direccion']) 
                        || Auth::isCocobe() 
                        || (int)Auth::id() === (int)$evidence['uploaded_by'];

        if (!$isAuthorized) {
            http_response_code(403);
            echo "Accés denegat: No teniu permisos per veure aquesta prova.";
            return;
        }

        // 3. Validació de seguretat del path (Prevé LFI)
        $filename = basename($evidence['filename']);
        $filePath = __DIR__ . '/../../storage/evidence/' . $filename;

        if (!file_exists($filePath)) {
            http_response_code(404);
            echo "El fitxer físic ha estat eliminat o mogut.";
            return;
        }

        // 4. Stream segur
        // SEC-008 FIX: Sanitizar nombre para prevenir header injection
        $safeName = preg_replace('/[^\x20-\x7E]/', '_', basename($evidence['original_name'] ?? 'download'));
        header('Content-Type: ' . ($evidence['mime_type'] ?? 'application/octet-stream'));
        header('Content-Disposition: attachment; filename="' . $safeName . '"');
        header('Content-Length: ' . filesize($filePath));
        header('X-Content-Type-Options: nosniff');
        
        readfile($filePath);
        exit;
    }
}
