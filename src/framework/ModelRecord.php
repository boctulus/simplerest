<?php

namespace Boctulus\Simplerest\Core;

/** A single persisted row; SQL operations are delegated to the existing Model Query Builder. */
class ModelRecord
{
    private array $attributes;
    private array $original;
    private bool $persisted;

    public function __construct(private Model $model, array $attributes = [], bool $exists = false)
    {
        $this->attributes = $attributes;
        $this->original = $attributes;
        $this->persisted = $exists;
    }

    public function __get(string $name)
    {
        return $this->attributes[$name] ?? null;
    }

    public function __set(string $name, $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    public function attributes(): array
    {
        return $this->attributes;
    }

    public function exists(): bool
    {
        return $this->persisted;
    }

    public function save(): bool
    {
        $query = $this->query();
        $key = $this->keyName();

        if (!$this->persisted) {
            $id = $query->create($this->attributes);
            if ($id === false || $id === null) {
                return false;
            }
            if (!array_key_exists($key, $this->attributes)) {
                $this->attributes[$key] = $id;
            }
            $this->persisted = true;
        } else {
            $id = $this->original[$key] ?? null;
            if ($id === null || $id === '') {
                throw new \LogicException("Cannot update a record without its original primary key");
            }
            if (($this->attributes[$key] ?? null) !== $id) {
                throw new \LogicException("Changing a persisted record's primary key is unsupported");
            }
            $changes = [];
            foreach ($this->attributes as $field => $value) {
                if (!array_key_exists($field, $this->original) || $value !== $this->original[$field]) {
                    $changes[$field] = $value;
                }
            }
            unset($changes[$key]);
            if (!$changes) {
                return true;
            }
            if ($query->where([$key => $id])->update($changes) === false) {
                return false;
            }
        }

        $this->original = $this->attributes;
        return true;
    }

    public function delete(): bool
    {
        if (!$this->persisted) {
            return false;
        }
        $key = $this->keyName();
        $id = $this->original[$key] ?? null;
        if ($id === null || $id === '') {
            throw new \LogicException("Cannot delete a record without its original primary key");
        }
        $deleted = $this->query()->where([$key => $id])->delete();
        if ($deleted === false || $deleted === 0) {
            return false;
        }
        $this->persisted = false;
        return true;
    }

    private function keyName(): string
    {
        return $this->model->hasSchema() ? $this->model->getKeyName() : 'id';
    }

    private function query(): Model
    {
        $class = get_class($this->model);
        $query = new $class(false);
        $table = $this->model->getTableName();
        if (!$table) {
            throw new \LogicException('A table is required to persist a record');
        }
        if ($query->getTableName() !== $table) {
            $query->table($table);
        }
        $query->setConn($this->model->getConn() ?? \Boctulus\Simplerest\Core\Libs\DB::getConnection());
        return $query;
    }
}
