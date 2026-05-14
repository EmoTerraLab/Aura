<?php
namespace Tests\Protocols;

use Tests\ProtocolTestCase;
use App\Services\Protocol\ProtocolFactory;
use App\Services\Protocol\AragonProtocol;
use App\Services\Protocol\MurciaProtocol;
use App\Services\Protocol\GaliciaProtocol;

class ProtocolFactoryTest extends ProtocolTestCase
{
    public function testMakeAragon()
    {
        $protocol = ProtocolFactory::make('ARA');
        $this->assertTrue($protocol instanceof AragonProtocol);
    }

    public function testMakeMurcia()
    {
        $protocol = ProtocolFactory::make('MUR');
        $this->assertTrue($protocol instanceof MurciaProtocol);
    }

    public function testMakeGalicia()
    {
        $protocol = ProtocolFactory::make('GAL');
        $this->assertTrue($protocol instanceof GaliciaProtocol);
    }

    public function testMakeUnknown()
    {
        // According to ProtocolFactory.php, unknown code returns MadridProtocol (fallback)
        $protocol = ProtocolFactory::make('UNKNOWN');
        $this->assertTrue($protocol instanceof \App\Services\Protocol\MadridProtocol);
    }
}
