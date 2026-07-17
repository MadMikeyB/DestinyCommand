<?php

namespace Tests\Unit;

use App\Destiny\ManifestRepository;
use PHPUnit\Framework\TestCase;
use SQLite3;

class ManifestRepositoryTest extends TestCase
{
    private string $databasePath;

    private ManifestRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->databasePath = tempnam(sys_get_temp_dir(), 'manifest-repo-');

        $database = new SQLite3($this->databasePath);
        $database->exec('CREATE TABLE DestinyInventoryItemDefinition (hash INTEGER PRIMARY KEY, json TEXT)');
        $database->exec("INSERT INTO DestinyInventoryItemDefinition (hash, json) VALUES (-1, '{\"displayProperties\":{\"name\":\"Legacy Hash\"}}')");
        $database->exec("INSERT INTO DestinyInventoryItemDefinition (hash, json) VALUES (123, '{\"displayProperties\":{\"name\":\"Gjallarhorn\"}}')");
        $database->close();

        $this->repository = new ManifestRepository(
            $this->databasePath,
            (object) [
                'DestinyInventoryItemDefinition' => ['hash'],
            ],
        );
    }

    protected function tearDown(): void
    {
        if (file_exists($this->databasePath)) {
            unlink($this->databasePath);
        }

        parent::tearDown();
    }

    public function test_it_can_browse_a_definition_table(): void
    {
        $definitions = $this->repository->browseDefinition('DestinyInventoryItemDefinition');

        $this->assertArrayHasKey('123', $definitions);
        $this->assertSame('Gjallarhorn', $definitions['123']->displayProperties->name);
    }

    public function test_it_can_resolve_unsigned_hashes_against_signed_sqlite_rows(): void
    {
        $definition = $this->repository->getDefinition('InventoryItem', 4294967295);

        $this->assertNotFalse($definition);
        $this->assertSame('Legacy Hash', $definition->displayProperties->name);
    }

    public function test_it_throws_when_definition_metadata_is_missing(): void
    {
        $repository = new ManifestRepository($this->databasePath, (object) []);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Manifest table metadata is missing (#DC531)');

        $repository->getDefinition('InventoryItem', 123);
    }
}
