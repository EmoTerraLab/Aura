<?php
namespace Tests\Protocols;

use Tests\ProtocolTestCase;
use App\Services\Protocol\GaliciaProtocol;

class GaliciaProtocolTest extends ProtocolTestCase
{
    private GaliciaProtocol $protocol;

    public function setUp(): void
    {
        parent::setUp();
        $this->protocol = new GaliciaProtocol();
    }

    public function testGetAllStates()
    {
        $states = $this->protocol->getAllStates();
        $this->assertIsArray($states);
        $this->assertNotEmpty($states);
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
        $final = GaliciaProtocol::STATE_PECHE;
        
        if ($initial !== $final) {
            $can = $this->protocol->canTransition($initial, $final, []);
            $this->assertTrue($can !== true, "Should NOT be able to jump from $initial to $final");
        }
    }

    public function testSyncState()
    {
        $reportId = 1;
        $state = GaliciaProtocol::STATE_RECOLLIDA_INFORMACION;
        
        $this->db->exec("INSERT INTO reports (id, classroom_id, content) VALUES (1, 1, 'Test')");
        
        $this->protocol->syncState($reportId, $state);
        
        $case = $this->getRegionalCase('GAL', $reportId);
        $this->assertNotEmpty($case);
        $this->assertEquals($state, $case['status']);
    }
}
