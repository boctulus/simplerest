<?php

namespace Boctulus\Simplerest\tests;

use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\ModelRecord;
use PHPUnit\Framework\TestCase;

class ModelRecordUnitTest extends TestCase
{
    private function sourceModel(Model $writeQuery): Model
    {
        $model = $this->createMock(Model::class);
        $model->method('getTableName')->willReturn('users');
        $model->method('hasSchema')->willReturn(false);
        $model->method('newRecordQuery')->willReturn($writeQuery);
        return $model;
    }

    public function test_create_sets_identity_only_after_success(): void
    {
        $write = $this->createMock(Model::class);
        $write->expects($this->once())->method('create')
            ->with(['name' => 'Ana'])->willReturn(17);
        $record = new ModelRecord($this->sourceModel($write), ['name' => 'Ana']);

        $this->assertFalse($record->exists());
        $this->assertTrue($record->save());
        $this->assertTrue($record->exists());
        $this->assertSame(17, $record->id);
    }

    public function test_failed_create_keeps_record_unsaved(): void
    {
        $write = $this->createMock(Model::class);
        $write->method('create')->willReturn(false);
        $record = new ModelRecord($this->sourceModel($write), ['name' => 'Ana']);

        $this->assertFalse($record->save());
        $this->assertFalse($record->exists());
    }

    public function test_save_updates_only_changed_fields_then_becomes_clean(): void
    {
        $write = $this->createMock(Model::class);
        $write->expects($this->once())->method('where')
            ->with(['id' => 17])->willReturnSelf();
        $write->expects($this->once())->method('update')
            ->with(['name' => 'Bea'])->willReturn(1);
        $record = new ModelRecord($this->sourceModel($write), [
            'id' => 17, 'name' => 'Ana', 'role' => 'admin',
        ], true);
        $record->name = 'Bea';

        $this->assertTrue($record->save());
        $this->assertTrue($record->save());
        $this->assertSame('admin', $record->role);
    }

    public function test_unsaved_record_cannot_be_deleted(): void
    {
        $write = $this->createMock(Model::class);
        $write->expects($this->never())->method('delete');
        $record = new ModelRecord($this->sourceModel($write), ['name' => 'Ana']);

        $this->assertFalse($record->delete());
    }
}
