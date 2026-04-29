<?php
namespace App\Models;

class ProtocolCase extends Model
{
    protected $table = 'protocol_cases';

    // Estados del Workflow
    const PHASE_DETECCION = 'deteccion';
    const PHASE_VALORACION = 'valoracion';
    const PHASE_COMUNICACION = 'comunicacio';
    const PHASE_INTERVENCION = 'intervencio';
    const PHASE_CIERRE = 'tancament';
    const PHASE_BARNAHUS = 'violencia_sexual_actiu';

    // Estados del Workflow Aragón
    const PHASE_AR_COMUNICACION = 'comunicacion_recibida';
    const PHASE_AR_INICIADO = 'protocolo_iniciado';
    const PHASE_AR_NO_INICIADO = 'protocolo_no_iniciado';
    const PHASE_AR_VALORACION = 'en_valoracion';
    const PHASE_AR_VALORADO = 'valorado';
    const PHASE_AR_CONTRATO = 'contrato_conducta';
    const PHASE_AR_EXPEDIENTE = 'expediente_disciplinario';
    const PHASE_AR_SEGUIMIENTO = 'en_seguimiento';
    const PHASE_AR_CERRADO = 'cerrado';
    const PHASE_AR_REABIERTO = 'reabierto';

    public function findByReport(int $reportId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE report_id = :report_id");
        $stmt->execute(['report_id' => $reportId]);
        return $stmt->fetch();
    }

    public function create(array $data)
    {
        $sql = "INSERT INTO {$this->table} (report_id, ccaa_code, current_phase, deadline_at) 
                VALUES (:report_id, :ccaa_code, :current_phase, :deadline_at)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'report_id'     => $data['report_id'],
            'ccaa_code'     => $data['ccaa_code'],
            'current_phase' => $data['current_phase'] ?? self::PHASE_DETECCION,
            'deadline_at'   => $data['deadline_at'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function updatePhase(int $id, string $phase)
    {
        $case = $this->find($id);
        
        // Salvaguarda Barnahus: Bloquear fases normales si es violencia sexual
        if ($case && $case['severity_preliminary'] === 'violencia_sexual') {
            $allowed = [self::PHASE_BARNAHUS, self::PHASE_COMUNICACION, self::PHASE_CIERRE];
            if (!in_array($phase, $allowed)) {
                throw new \Exception("Acción denegada: Los casos de violencia sexual deben derivarse directamente a Barnahus. Está prohibido realizar diagnósticos internos.");
            }
        }

        $stmt = $this->db->prepare("UPDATE {$this->table} SET current_phase = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$phase, $id]);
    }

    public function updateClassification(int $id, string $severity, string $classification)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET severity_preliminary = ?, classification = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$severity, $classification, $id]);
    }

    public function assignTeam(int $id, array $teamIds)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET valuation_team = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([json_encode($teamIds), $id]);
    }

    public function saveSecurityMap(int $id, array $mapData)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET security_map = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([json_encode($mapData), $id]);
    }

    public function updateCommunications(int $id, array $comms)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET communications = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([json_encode($comms), $id]);
    }

    public function updateClosureChecks(int $id, array $checks)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET closure_checks = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([json_encode($checks), $id]);
    }

    public function updateAcknowledgment(int $id, ?int $ack)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET aggressor_acknowledges_facts = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$ack, $id]);
    }

    public function updateCcaa(int $id, string $ccaa)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET ccaa_code = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$ccaa, $id]);
    }
}
