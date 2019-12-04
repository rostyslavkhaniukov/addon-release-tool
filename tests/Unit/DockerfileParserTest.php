<?php

declare(strict_types=1);

namespace Tests\Unit;

use AirSlate\AddonsSDK\Contracts\ApiClient\PublicClientFactory;
use AirSlate\ApiClient\Client;
use AirSlate\ApiClient\Entities\Packet;
use AirSlate\ApiClient\Entities\Slate;
use AirSlate\ApiClient\Models\Packet\Update as PacketUpdate;
use AirSlate\ApiClient\Services\PacketsService as ClientPacketsService;
use AirSlate\ApiClient\Services\SlatesService as ClientSlatesService;
use App\Models\Watcher;
use App\Repositories\PrefillDataRepository;
use App\Repositories\WatcherRepository;
use App\Services\DataSource\DataRow;
use App\Services\SlateAddonAppropriatenessService;
use App\Services\SlatesService;
use Illuminate\Contracts\Bus\Dispatcher;
use Psr\Log\NullLogger;
use Tests\TestCase;

class SlatesServiceTest extends TestCase
{
    /** @var string */
    private $expectedSlateName;

    /** @var string */
    private $flowName;

    /** @var string|null */
    private $slateAddonName;

    /** @var string|null */
    private $addonName;

    public function testShouldCreateAndRenameSlate()
    {
        $this->setActualFlowName('Test flow');
        $this->setActualSlateAddonName('Test slate addon');
        $this->setActualAddonName('Test addon');
        $this->setExpectedPacketName('@Test addon (Test slate addon) Test flow Slate');

        $clientFactory = $this->makeClientFactory();

        $slatesService = new SlatesService(
            $clientFactory,
            new NullLogger(),
            $this->createMock(PrefillDataRepository::class),
            $this->createMock(WatcherRepository::class),
            $this->createMock(Dispatcher::class),
            $this->createMock(SlateAddonAppropriatenessService::class)
        );

        $slatesService->process($this->getWatcher(), new DataRow([], []));

        $this->assertTrue(true);
    }

    private function makeClientFactory(): PublicClientFactory
    {
        $client = $this->createMock(Client::class);
        $client->method('slates')->willReturn($this->mockSlatesService());

        $factory = $this->createMock(PublicClientFactory::class);
        $factory->method('makeForOrganization')->willReturn($client);

        return $factory;
    }

    private function mockPacketsService(): ClientPacketsService
    {
        $packets = $this->createMock(ClientPacketsService::class);

        $packets
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->getPacket());

        $packets
            ->expects($this->once())
            ->method('update')
            ->with(
                $this->equalTo('fake_packet_uid'),
                $this->equalTo($this->getPacketUpdate())
            )
            ->willReturn(true);

        return $packets;
    }

    private function mockSlatesService(): ClientSlatesService
    {
        $slates = $this->createMock(ClientSlatesService::class);
        $slates->method('packets')->willReturn($this->mockPacketsService());
        $slates->method('get')->willReturn($this->getFlow());

        return $slates;
    }

    private function getWatcher(): Watcher
    {
        $watcher = new Watcher();
        $watcher->organization_uid = 'fake_organization_uid';
        $watcher->slate_id = 'fake_flow_uid';
        $watcher->slate_addon_name = $this->slateAddonName;
        $watcher->addon_name = $this->addonName;

        return $watcher;
    }

    private function getPacketUpdate(): PacketUpdate
    {
        $packetUpdate = new PacketUpdate();
        $packetUpdate->setName($this->expectedSlateName);

        return $packetUpdate;
    }

    private function getPacket(): Packet
    {
        $packet = new Packet();
        $packet->id = 'fake_packet_uid';

        return $packet;
    }

    private function getFlow(): Slate
    {
        $flow = new Slate();
        $flow->name = $this->flowName;

        return $flow;
    }

    private function setActualFlowName(string $flowName)
    {
        $this->flowName = $flowName;
    }

    private function setActualSlateAddonName(?string $slateAddonName)
    {
        $this->slateAddonName = $slateAddonName;
    }

    /**
     * @param string|null $addonName
     */
    public function setActualAddonName(?string $addonName): void
    {
        $this->addonName = $addonName;
    }

    private function setExpectedPacketName(string $expectedSlateName)
    {
        $this->expectedSlateName = $expectedSlateName;
    }
}
