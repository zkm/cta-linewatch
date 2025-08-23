<?php

namespace Tests\Database;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Tests\Support\Database\Seeds\ExampleSeeder;
use Tests\Support\Models\ExampleModel;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;

/**
 * @internal
 * @requires extension sqlite3
 */
#[RequiresPhpExtension('sqlite3')]
final class ExampleDatabaseTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $seed = ExampleSeeder::class;

    public function testModelFindAll(): void
    {
        if (! extension_loaded('sqlite3')) {
            $this->markTestSkipped('sqlite3 extension not available.');
        }
        $model = new ExampleModel();

        // Get every row created by ExampleSeeder
        $objects = $model->findAll();

        // Make sure the count is as expected
        $this->assertCount(3, $objects);
    }

    public function testSoftDeleteLeavesRow(): void
    {
        if (! extension_loaded('sqlite3')) {
            $this->markTestSkipped('sqlite3 extension not available.');
        }
        $model = new ExampleModel();
        $this->setPrivateProperty($model, 'useSoftDeletes', true);
        $this->setPrivateProperty($model, 'tempUseSoftDeletes', true);

        /** @var stdClass $object */
        $object = $model->first();
        $model->delete($object->id);

        // The model should no longer find it
        $this->assertNull($model->find($object->id));

        // ... but it should still be in the database
        $result = $model->builder()->where('id', $object->id)->get()->getResult();

        $this->assertCount(1, $result);
    }
}
