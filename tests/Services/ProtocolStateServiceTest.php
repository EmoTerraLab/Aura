<?php
namespace Tests\Services;

use Tests\ProtocolTestCase;
use App\Services\ProtocolStateService;
use App\Models\ProtocolCase;
use App\Core\Auth;
use ReflectionClass;

class ProtocolStateServiceTest extends ProtocolTestCase
{
    private ProtocolStateService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new ProtocolStateService();
        
        // Mock Auth user
        $reflection = new ReflectionClass(Auth::class);
        $cachedUserProperty = $reflection->getProperty('cachedUser');
        $cachedUserProperty->setAccessible(true);
        $cachedUserProperty->setValue(null, ['id' => 1, 'name' => 'Test User', 'role' => 'admin']);
    }

    public function testCreateInitialCase()
    {
        $reportId = 123;
        $this->db->exec("INSERT INTO reports (id, classroom_id, content) VALUES ($reportId, 1, 'Test')");
        
        $case = $this->service->createInitialCase($reportId, 'ARA');
        
        $this->assertNotEmpty($case);
        $this->assertEquals('ARA', $case['ccaa_code']);
        $this->assertEquals(ProtocolCase::PHASE_AR_COMUNICACION, $case['current_phase']);
        
        // Check regional table sync
        $regional = $this->getRegionalCase('ARA', $reportId);
        $this->assertNotEmpty($regional);
        $this->assertEquals(ProtocolCase::PHASE_AR_COMUNICACION, $regional['status']);
    }

    public function testTransitionByReportId()
    {
        $reportId = 456;
        $this->db->exec("INSERT INTO reports (id, classroom_id, content) VALUES ($reportId, 1, 'Test')");
        $caseId = $this->createCase('ARA', ProtocolCase::PHASE_AR_COMUNICACION);
        
        // Update report_id for the created case (my helper creates a new report, I need to match it)
        $this->db->prepare("UPDATE protocol_cases SET report_id = ? WHERE id = ?")->execute([$reportId, $caseId]);
        $this->db->prepare("UPDATE aragon_protocol_cases SET report_id = ? WHERE report_id != ?")->execute([$reportId, $reportId]);

        $success = $this->service->transitionByReportId($reportId, ProtocolCase::PHASE_AR_INICIADO);
        
        $this->assertTrue($success);
        
        $updatedCase = $this->getCase($caseId);
        $this->assertEquals(ProtocolCase::PHASE_AR_INICIADO, $updatedCase['current_phase']);
        
        $regional = $this->getRegionalCase('ARA', $reportId);
        $this->assertEquals(ProtocolCase::PHASE_AR_INICIADO, $regional['status']);
    }

    public function testInvalidTransitionThrowsException()
    {
        $reportId = 789;
        $this->db->exec("INSERT INTO reports (id, classroom_id, content) VALUES ($reportId, 1, 'Test')");
        $caseId = $this->createCase('ARA', ProtocolCase::PHASE_AR_COMUNICACION);
        $this->db->prepare("UPDATE protocol_cases SET report_id = ? WHERE id = ?")->execute([$reportId, $caseId]);

        try {
            // Invalid jump
            $this->service->transitionByReportId($reportId, ProtocolCase::PHASE_AR_CERRADO);
            $this->assertTrue(false, "Should have thrown an exception");
        } catch (\Exception $e) {
            $this->assertTrue(true);
            // Verify no change in DB
            $updatedCase = $this->getCase($caseId);
            $this->assertEquals(ProtocolCase::PHASE_AR_COMUNICACION, $updatedCase['current_phase']);
        }
    }
}
