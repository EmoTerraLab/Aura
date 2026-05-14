<?php
namespace Tests\Protocols;

use Tests\ProtocolTestCase;
use App\Services\Protocol\AragonProtocol;
use App\Models\ProtocolCase;

class AragonProtocolTest extends ProtocolTestCase
{
    private AragonProtocol $protocol;

    public function setUp(): void
    {
        parent::setUp();
        $this->protocol = new AragonProtocol();
    }

    public function testGetAllStates()
    {
        $states = $this->protocol->getAllStates();
        $this->assertIsArray($states);
        $this->assertNotEmpty($states);
        foreach ($states as $state) {
            $this->assertTrue(is_string($state));
        }
    }

    public function testGetInitialState()
    {
        $initial = $this->protocol->getInitialState();
        $this->assertTrue(is_string($initial));
        $this->assertTrue(in_array($initial, $this->protocol->getAllStates()));
    }

    public function testValidTransitions()
    {
        $states = $this->protocol->getAllStates();
        foreach ($states as $state) {
            $transitions = $this->protocol->getValidTransitions($state);
            $this->assertIsArray($transitions);
            
            foreach ($transitions as $toState) {
                $can = $this->protocol->canTransition($state, $toState, []);
                $this->assertTrue($can === true, "Should be able to transition from $state to $toState");
            }
        }
    }

    public function testInvalidTransitions()
    {
        $initial = $this->protocol->getInitialState();
        $final = ProtocolCase::PHASE_AR_CERRADO;
        
        // Skip intermediate phases
        if ($initial !== $final) {
            $can = $this->protocol->canTransition($initial, $final, []);
            $this->assertTrue($can !== true, "Should NOT be able to jump from $initial to $final");
        }
    }

    public function testGetTimelineSteps()
    {
        $steps = $this->protocol->getTimelineSteps();
        $this->assertIsArray($steps);
        $this->assertNotEmpty($steps);
        foreach ($steps as $step) {
            $this->assertTrue(isset($step['key']));
            $this->assertTrue(isset($step['label']));
            $this->assertTrue(isset($step['icon']));
        }
    }

    public function testGetStateLabel()
    {
        $states = $this->protocol->getAllStates();
        foreach ($states as $state) {
            $label = $this->protocol->getStateLabel($state);
            $this->assertNotEmpty($label);
            $this->assertTrue(is_string($label));
        }
    }

    public function testSyncState()
    {
        $reportId = 1;
        $state = ProtocolCase::PHASE_AR_INICIADO;
        
        // Ensure report exists for foreign key
        $this->db->exec("INSERT INTO reports (id, classroom_id, content) VALUES (1, 1, 'Test')");
        
        $this->protocol->syncState($reportId, $state);
        
        $case = $this->getRegionalCase('ARA', $reportId);
        $this->assertNotEmpty($case);
        $this->assertEquals($state, $case['status']);
    }

    public function testIsFullyImplemented()
    {
        $this->assertTrue(is_bool($this->protocol->isFullyImplemented()));
    }
}
