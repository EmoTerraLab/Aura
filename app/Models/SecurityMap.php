<?php
namespace App\Models;

class SecurityMap extends Model
{
    protected $table = 'security_maps';

    public function findByCase(int $caseId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE protocol_case_id = :case_id");
        $stmt->execute(['case_id' => $caseId]);
        return $stmt->fetch();
    }

    public function upsert(array $data)
    {
        $existing = $this->findByCase($data['protocol_case_id']);
        
        $params = [
            'protocol_case_id'   => $data['protocol_case_id'],
            'espais_segurs'      => $data['espais_segurs'] ?? '',
            'espais_de_risc'     => $data['espais_de_risc'] ?? '',
            'persones_de_suport' => $data['persones_de_suport'] ?? '',
            'mesures_urgencia'   => json_encode($data['mesures_urgencia'] ?? [])
        ];

        if ($existing) {
            $sql = "UPDATE {$this->table} SET 
                    espais_segurs = :espais_segurs, 
                    espais_de_risc = :espais_de_risc, 
                    persones_de_suport = :persones_de_suport, 
                    mesures_urgencia = :mesures_urgencia, 
                    updated_at = CURRENT_TIMESTAMP 
                    WHERE id = :id";
            $params['id'] = $existing['id'];
        } else {
            $sql = "INSERT INTO {$this->table} 
                    (protocol_case_id, espais_segurs, espais_de_risc, persones_de_suport, mesures_urgencia) 
                    VALUES (:protocol_case_id, :espais_segurs, :espais_de_risc, :persones_de_suport, :mesures_urgencia)";
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
}
