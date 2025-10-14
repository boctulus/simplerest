<?php

use Boctulus\Simplerest\Core\Libs\Migration;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;

// Migrar con `php com migrations migrate --file=D:\laragon\www\simplerest\packages\boctulus\zippy\database\migrations\2025_10_14_156666928_create_category_mappings.php`
class CreateCategoryMappings extends Migration
{
    protected $table      = 'category_mappings';
    protected $connection = 'zippy';

    public function up()
    {
        $sc = new Schema($this->table);
        $sc
          ->integer('id')->auto()->pri()
          ->varchar('raw_value', 255)->notNullable()->comment('Original category value from scraper')
          ->varchar('normalized', 255)->notNullable()->index()->comment('Normalized version for matching')
          ->varchar('category_id', 21)->nullable()->index()->comment('FK to categories.id')
          ->varchar('category_slug', 150)->nullable()->index()->comment('Denormalized slug for quick access')
          ->enum('mapping_type', ['exact','normalized','substring','fuzzy','manual','auto_create','unmapped'])->nullable()
            ->comment('Type of mapping: exact=direct match, normalized=case-insensitive match, substring=partial match, fuzzy=similarity-based, manual=human-reviewed, auto_create=new subcategory, unmapped=needs review')
          ->float('confidence')->nullable()->comment('Confidence score 0-100 for fuzzy matches')
          ->text('notes')->nullable()->comment('Additional notes or reasons for mapping')
          ->bool('is_reviewed')->default(false)->index()->comment('Has this mapping been manually reviewed?')
          ->datetime('reviewed_at')->nullable()->comment('When was this mapping last reviewed')
          ->datetimes()
          ->softDeletes();

        $sc->create();

        // Add unique index on normalized to prevent duplicate raw values (MySQL doesn't support partial unique indexes)
        // For now, use a regular unique index on normalized - duplicates must be handled in application logic
        // DB::statement("CREATE UNIQUE INDEX idx_normalized_unique ON {$this->table}(normalized)");

        // Add FK constraint (optional - comment out if categories table doesn't have proper IDs)
        // DB::statement("ALTER TABLE {$this->table} ADD CONSTRAINT fk_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL");
    }

    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}