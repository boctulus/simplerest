<?php

namespace Boctulus\Simplerest\Commands;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\StdOut;
use Boctulus\Simplerest\Core\Interfaces\ICommand;
use Boctulus\Simplerest\Core\Traits\CommandTrait;

class SqlCommand implements ICommand
{
	use CommandTrait;

	/**
	 * Custom handle to support subcommands
	 */
	public function handle($args) {
		if (empty($args)) {
			$this->help();
			return;
		}

		$method = array_shift($args);

		if (!method_exists($this, $method)) {
			StdOut::print("Method not found: $method\r\n");
			$this->help();
			return;
		}

		// Call method dynamically with remaining arguments
		call_user_func_array([$this, $method], $args);
	}

	/**
     * Find a record by its primary key ID, with options to delete or edit it.
     *
     * Usage:
     *   php com sql find {db}.{table} --id={value} [--format=table]
     *   php com sql find {db}.{table} --id={value} --delete
     *   php com sql find {db}.{table} --id={value} --edit field1=value1 field2=value2 ...
     */
    function find(...$args) {
        if (empty($args)) {
            StdOut::print("Error: table argument is required\r\n");
            $this->printFindUsage();
            return;
        }

        $table_arg = array_shift($args);
        $id_value = null;
        $format = 'simple'; // Default format
        $delete = false;
        $edit = false;
        $update_data = [];

        // Parse options
        foreach ($args as $arg) {
            if (Strings::startsWith('--id=', $arg)) {
                $id_value = substr($arg, 5);
            } elseif (Strings::startsWith('--format=', $arg)) {
                $format = substr($arg, 9);
            } elseif ($arg === '--delete') {
                $delete = true;
            } elseif ($arg === '--edit') {
                $edit = true;
            } elseif (Strings::contains('=', $arg) && $edit) {
                // Parse field=value pairs for edit operation
                list($field, $value) = explode('=', $arg, 2);
                $update_data[$field] = $value;
            }
        }

        // Validate ID value
        if ($id_value === null) {
            StdOut::print("Error: --id parameter is required\r\n");
            $this->printFindUsage();
            return;
        }

        // Parse table argument: {db}.{table}
        if (!Strings::contains('.', $table_arg)) {
            StdOut::print("Error: table argument must be in format '{db}.{table}'\r\n");
            return;
        }

        list($db, $table) = explode('.', $table_arg, 2);

        // Validate connection exists
        if (!DB::connectionExists($db)) {
            StdOut::print("Error: Connection '$db' is not registered in db_connections\r\n");
            return;
        }

        // Set connection
        DB::setConnection($db);

        try {
            // Check if schema exists for this table
            if (!has_schema($table)) {
                StdOut::print("Error: No schema found for table '$table' in connection '$db'\r\n");
                StdOut::print("Hint: Create a schema file in app/Schemas/$db/" . Strings::snakeToCamel($table) . "Schema.php\r\n");
                return;
            }

            // Get schema and primary key name
            $schema = get_schema($table);
            $id_name = $schema['id_name'];

            if (empty($id_name)) {
                StdOut::print("Error: Could not determine primary key for table '$table'\r\n");
                return;
            }

            // Build query directly using the primary key field name
            $instance = table($table);
            $result = $instance->where([$id_name => $id_value])->first();

            if (empty($result)) {
                StdOut::print("No record found with $id_name = $id_value\r\n");
                return;
            }

            // Handle delete operation
            if ($delete) {
                $instance->where([$id_name => $id_value])->delete();
                StdOut::print("Record with $id_name = $id_value has been deleted successfully\r\n");
                return;
            }

            // Handle edit operation
            if ($edit) {
                if (empty($update_data)) {
                    StdOut::print("Error: No fields provided for edit operation\r\n");
                    StdOut::print("Usage: php com sql find {db}.{table} --id={value} --edit field1=value1 field2=value2\r\n");
                    return;
                }

                $instance->where([$id_name => $id_value])->update($update_data);
                
                StdOut::print("Record with $id_name = $id_value has been updated successfully\r\n");
                foreach ($update_data as $field => $value) {
                    StdOut::print("  - $field = $value\r\n");
                }
                return;
            }

            // Display result based on format
            if ($format === 'table') {
                $this->displayAsTable([$result]);
            } else {
                $this->displaySimple([$result]);
            }

        } catch (\Exception $e) {
            StdOut::print("Error: " . $e->getMessage() . "\r\n");
        }
    }

	/**
	 * List contents of a table or list databases (backward compatibility)
	 *
	 * Usage: 
	 *   php com sql list '{db}.{table}' [--take=N|--limit=N] [--skip=M|--offset=M] [--format=table]  # List table contents
	 *   php com sql list '{connection}'                                                                    # List database tables (alias for describe database)
	 */
	function list(...$args) {
		if (empty($args)) {
			StdOut::print("Error: table argument is required\r\n");
			StdOut::print("Usage: php com sql list '{db}.{table}' [--take=N|--limit=N] [--skip=M|--offset=M] [--format=table]\r\n");
			StdOut::print("       php com sql list '{connection}' # Lists tables in connection (alias for describe database)\r\n");
			return;
		}

		$table_arg = array_shift($args);
		
		// Check if the argument is just a connection name (no dot) or a connection.table format
		if (!Strings::contains('.', $table_arg)) {
			// If it's just a connection name, treat it as an alias for describe database
			// Parse options to make sure there are none that don't apply to describe database
			$remaining_args = $args; // These should be empty for describe database
			
			// If there are additional arguments, it's likely not a database name
			if (!empty($remaining_args)) {
				StdOut::print("Error: For connection-only syntax, no additional options are allowed\r\n");
				StdOut::print("Usage: php com sql list '{connection}' # Lists tables in connection (alias for describe database)\r\n");
				return;
			}
			
			// Call describe database functionality
			$this->describeDatabase($table_arg);
			return;
		}

		// Original logic for table listing
		$offset = 0;
		$limit = 10; // Default limit
		$format = 'simple'; // Default format

		// Parse options (support both --take/--skip and --limit/--offset)
		foreach ($args as $arg) {
			if (Strings::startsWith('--offset=', $arg)) {
				$offset = (int) substr($arg, 9);
			} elseif (Strings::startsWith('--skip=', $arg)) {
				$offset = (int) substr($arg, 7);
			} elseif (Strings::startsWith('--limit=', $arg)) {
				$limit = (int) substr($arg, 8);
			} elseif (Strings::startsWith('--take=', $arg)) {
				$limit = (int) substr($arg, 7);
			} elseif (Strings::startsWith('--format=', $arg)) {
				$format = substr($arg, 9);
			}
		}

		// Parse table argument: {db}.{table}
		list($db, $table) = explode('.', $table_arg, 2);

		// Validate connection exists
		if (!DB::connectionExists($db)) {
			StdOut::print("Error: Connection '$db' is not registered in db_connections\r\n");
			return;
		}

		// Set connection
		DB::setConnection($db);

		try {
			// Build query
			$query = table($table);

			if ($limit > 0) {
				$query->limit($limit);
			}

			if ($offset > 0) {
				$query->offset($offset);
			}

			$results = $query->get();

			if (empty($results)) {
				StdOut::print("No records found\r\n");
				return;
			}

			// Display results based on format
			if ($format === 'table') {
				$this->displayAsTable($results);
			} else {
				$this->displaySimple($results);
			}

		} catch (\Exception $e) {
			StdOut::print("Error: " . $e->getMessage() . "\r\n");
		}
	}

	/**
	 * Search for records in a table
	 *
	 * Usage: php com sql search '{db}.{table}' --search='word word ...' [--take=N|--limit=N] [--skip=M|--offset=M] [--format=table]
	 */
	function search(...$args) {
		if (empty($args)) {
			StdOut::print("Error: table argument is required\r\n");
			StdOut::print("Usage: php com sql search '{db}.{table}' --search='word word ...' [--take=N|--limit=N] [--skip=M|--offset=M] [--format=table]\r\n");
			return;
		}

		$table_arg = array_shift($args);
		$search_term = null;
		$offset = 0;
		$limit = 10; // Default limit
		$format = 'simple'; // Default format

		// Parse options
		foreach ($args as $arg) {
			if (Strings::startsWith('--search=', $arg)) {
				$search_term = substr($arg, 9);
				// Remove quotes if present
				$search_term = trim($search_term, "'\"");
			} elseif (Strings::startsWith('--offset=', $arg)) {
				$offset = (int) substr($arg, 9);
			} elseif (Strings::startsWith('--skip=', $arg)) {
				$offset = (int) substr($arg, 7);
			} elseif (Strings::startsWith('--limit=', $arg)) {
				$limit = (int) substr($arg, 8);
			} elseif (Strings::startsWith('--take=', $arg)) {
				$limit = (int) substr($arg, 7);
			} elseif (Strings::startsWith('--format=', $arg)) {
				$format = substr($arg, 9);
			}
		}

		// Validate search term
		if ($search_term === null || $search_term === '') {
			StdOut::print("Error: --search parameter is required\r\n");
			StdOut::print("Usage: php com sql search '{db}.{table}' --search='word word ...' [--take=N|--limit=N] [--skip=M|--offset=M] [--format=table]\r\n");
			return;
		}

		// Parse table argument: {db}.{table}
		if (!Strings::contains('.', $table_arg)) {
			StdOut::print("Error: table argument must be in format '{db}.{table}'\r\n");
			return;
		}

		list($db, $table) = explode('.', $table_arg, 2);

		// Validate connection exists
		if (!DB::connectionExists($db)) {
			StdOut::print("Error: Connection '$db' is not registered in db_connections\r\n");
			return;
		}

		// Set connection
		DB::setConnection($db);

		try {
			// Get table schema to know which fields to search in
			$schema = null;
			if (has_schema($table)) {
				$schema = get_schema($table);
			}

			// Build query
			$query = table($table);

			// If we have schema, search in text fields only
			if ($schema) {
				$text_fields = [];
				foreach ($schema['attr_types'] as $field => $type) {
					// Search in string/text fields
					if (in_array($type, ['STR', 'TEXT'])) {
						$text_fields[] = $field;
					}
				}

				if (empty($text_fields)) {
					StdOut::print("Warning: No text fields found in schema. Searching all fields.\r\n");
				} else {
					// Search in all text fields using OR conditions
					foreach ($text_fields as $index => $field) {
						if ($index === 0) {
							$query->where([[$field, "%$search_term%", 'LIKE']]);
						} else {
							$query->orWhere([[$field, "%$search_term%", 'LIKE']]);
						}
					}
				}
			} else {
				// No schema available - cannot determine which fields to search
				StdOut::print("Warning: No schema found for table '$table'. Please specify search fields manually or create a schema.\r\n");
				return;
			}

			if ($limit > 0) {
				$query->limit($limit);
			}

			if ($offset > 0) {
				$query->offset($offset);
			}

			$results = $query->get();

			if (empty($results)) {
				StdOut::print("No records found matching '$search_term'\r\n");
				return;
			}

			// Display results based on format
			if ($format === 'table') {
				$this->displayAsTable($results);
			} else {
				$this->displaySimple($results);
			}

		} catch (\Exception $e) {
			StdOut::print("Error: " . $e->getMessage() . "\r\n");
		}
	}

	/**
	 * Describe database schema - supports database and table subcommands
	 *
	 * Usage:
	 *   php com sql describe database '{connection}'        List tables in database
	 *   php com sql describe table '{connection}.{table}'   List fields in table
	 */
	function describe(...$args) {
		if (empty($args)) {
			StdOut::print("Error: describe subcommand is required (database|table)\r\n");
			$this->printDescribeUsage();
			return;
		}

		$subcommand = array_shift($args);

		switch ($subcommand) {
			case 'database':
				$this->describeDatabase(...$args);
				break;
			case 'table':
				$this->describeTable(...$args);
				break;
			default:
				StdOut::print("Error: Invalid describe subcommand '$subcommand'. Use 'database' or 'table'.\r\n");
				$this->printDescribeUsage();
				return;
		}
	}

	/**
	 * Describe database - list tables
	 *
	 * Usage: php com sql describe database '{connection}'
	 */
	private function describeDatabase(...$args) {
		if (empty($args)) {
			StdOut::print("Error: connection argument is required\r\n");
			StdOut::print("Usage: php com sql describe database '{connection}'\r\n");
			return;
		}

		$connection = $args[0];

		// Validate connection exists
		if (!DB::connectionExists($connection)) {
			StdOut::print("Error: Connection '$connection' is not registered in db_connections\r\n");
			return;
		}

		// Set connection
		DB::setConnection($connection);

		try {
			// Get tables from database
			$tables = DB::getTableNames($connection);

			if (empty($tables)) {
				StdOut::print("No tables found in connection '$connection'\r\n");
				return;
			}

			StdOut::print("Tables in connection '$connection':\r\n");
			StdOut::print("----------------------------------------\r\n");

			foreach ($tables as $table) {
				StdOut::print("- $table\r\n");
			}

		} catch (\Exception $e) {
			StdOut::print("Error: " . $e->getMessage() . "\r\n");
		}
	}

	/**
	 * Describe table - list fields
	 *
	 * Usage: php com sql describe table '{connection}.{table}'
	 */
	private function describeTable(...$args) {
		if (empty($args)) {
			StdOut::print("Error: table argument is required\r\n");
			StdOut::print("Usage: php com sql describe table '{connection}.{table}'\r\n");
			return;
		}

		$table_arg = $args[0];

		// Parse table argument: {db}.{table}
		if (!Strings::contains('.', $table_arg)) {
			StdOut::print("Error: table argument must be in format '{connection}.{table}'\r\n");
			return;
		}

		list($connection, $table) = explode('.', $table_arg, 2);

		// Validate connection exists
		if (!DB::connectionExists($connection)) {
			StdOut::print("Error: Connection '$connection' is not registered in db_connections\r\n");
			return;
		}

		// Set connection
		DB::setConnection($connection);

		try {
			// Get table columns/fields using SHOW COLUMNS query
			$sql = "SHOW COLUMNS FROM `$table`";
			$columns = DB::select($sql);

			if (empty($columns)) {
				StdOut::print("No columns found for table '$table' in connection '$connection'\r\n");
				return;
			}

			StdOut::print("Fields in table '$table' (connection '$connection'):\r\n");
			StdOut::print("--------------------------------------------------------\r\n");

			// Display column information in a table format
			$headers = ['Field', 'Type', 'Null', 'Key', 'Default', 'Extra'];
			$this->displayTableWithHeaders($columns, $headers);

		} catch (\Exception $e) {
			StdOut::print("Error: " . $e->getMessage() . "\r\n");
		}
	}

	/**
	 * Display data as table with custom headers
	 */
	private function displayTableWithHeaders(array $data, array $headers) {
		if (empty($data)) {
			return;
		}

		// Calculate column widths based on headers and data
		$widths = [];
		foreach ($headers as $index => $header) {
			$widths[$index] = strlen($header);
		}

		// Check data widths
		foreach ($data as $row) {
			foreach ($headers as $index => $header) {
				$value = $row[$header] ?? '';
				$widths[$index] = max($widths[$index], strlen($value));
			}
		}

		// Build separator line
		$separator = '+';
		foreach ($headers as $index => $header) {
			$separator .= str_repeat('-', $widths[$index] + 2) . '+';
		}

		// Print header
		StdOut::print("$separator\r\n");
		$header_line = '|';
		foreach ($headers as $index => $header) {
			$header_line .= ' ' . str_pad($header, $widths[$index]) . ' |';
		}
		StdOut::print("$header_line\r\n");
		StdOut::print("$separator\r\n");

		// Print rows
		foreach ($data as $row) {
			$line = '|';
			foreach ($headers as $index => $header) {
				$value = $row[$header] ?? '';
				$value = $value === null ? 'NULL' : (string)$value;
				$line .= ' ' . str_pad($value, $widths[$index]) . ' |';
			}
			StdOut::print("$line\r\n");
		}

		// Print footer
		StdOut::print("$separator\r\n");
	}

	/**
	 * Print usage for describe command
	 */
	private function printDescribeUsage() {
		StdOut::print("Usage:\r\n");
		StdOut::print("  php com sql describe database '{connection}'        List tables in database\r\n");
		StdOut::print("  php com sql describe table '{connection}.{table}'   List fields in table\r\n");
	}

	/**
	 * Display results in simple format (one record per line)
	 */
	private function displaySimple(array $results) {
		$count = count($results);
		StdOut::print("Found $count record(s):\r\n\r\n");

		foreach ($results as $index => $row) {
			StdOut::print("Record #" . ($index + 1) . ":\r\n");
			foreach ($row as $key => $value) {
				$display_value = $value === null ? 'NULL' : $value;
				StdOut::print("  $key: $display_value\r\n");
			}
			StdOut::print("\r\n");
		}
	}

	/**
	 * Display results as ASCII table
	 */
	private function displayAsTable(array $results) {
		if (empty($results)) {
			return;
		}

		// Get column names from first row
		$columns = array_keys($results[0]);

		// Calculate column widths
		$widths = [];
		foreach ($columns as $col) {
			$widths[$col] = strlen($col);
		}

		// Check data widths
		foreach ($results as $row) {
			foreach ($row as $col => $value) {
				$display_value = $value === null ? 'NULL' : (string)$value;
				$widths[$col] = max($widths[$col], strlen($display_value));
			}
		}

		// Build separator line
		$separator = '+';
		foreach ($columns as $col) {
			$separator .= str_repeat('-', $widths[$col] + 2) . '+';
		}

		// Print header
		StdOut::print("$separator\r\n");
		$header = '|';
		foreach ($columns as $col) {
			$header .= ' ' . str_pad($col, $widths[$col]) . ' |';
		}
		StdOut::print("$header\r\n");
		StdOut::print("$separator\r\n");

		// Print rows
		foreach ($results as $row) {
			$line = '|';
			foreach ($columns as $col) {
				$value = $row[$col] === null ? 'NULL' : (string)$row[$col];
				$line .= ' ' . str_pad($value, $widths[$col]) . ' |';
			}
			StdOut::print("$line\r\n");
		}

		// Print footer
		StdOut::print("$separator\r\n");

		$count = count($results);
		StdOut::print("\r\n$count record(s) found\r\n");
	}

	function help($name = null, ...$args){
		$str = <<<STR
		# DESCRIBE
		
		sql describe database '{connection}'                          List tables in database
		sql describe table '{connection}.{table}'                     List fields in table
		
		# LIST

		sql list '{connection}'                                       List tables in database (alias for 'describe database') 
		sql list '{db}.{table}' [--take=N|--limit=N] [--skip=M|--offset=M] [--format=table]   List contents of a table
		
		# SEARCH
		
		sql search '{db}.{table}' --search='text' [--take=N] [--skip=M] [--format=table]     Search records in text fields

		# FIND
		
		sql find '{db}.{table}' --id={value} [--format=table]                                Find a record by its primary key
		
		# DELETE
		
		sql find '{db}.{table}' --id={value} --delete                                         Delete a record by its primary key
		
		# EDIT
		
		sql find '{db}.{table}' --id={value} --edit field=some_value
		sql find '{db}.{table}' --id={value} --edit field=some_value other_field=other_value

		ℹ  {db} in this case is the connection name from config/databases.php, not the database name

		Examples - DESCRIBE:

		php com sql describe database 'main'                         List all tables in 'main' connection
		php com sql describe table 'main.users'                      List all fields in 'users' table of 'main' connection
		php com sql list 'main'                                      Same as 'describe database main' - list tables in connection

		Examples - FIND:

		php com sql find 'zippy.products' --id=217548              Find product with primary key = 217548
		php com sql find 'main.users' --id=5 --format=table        Find user with ID 5 and display as table

		Examples - FIND + action
		
		php com sql find 'main.users' --id=5 --delete              Delete user with ID 5
		php com sql find 'main.users' --id=5 --edit name="Pablo Bozzolo" age=49 occupation="Computer Programmer"

		Examples - LIST:

		php com sql list 'main.users'                              List first 10 records from users table
		php com sql list 'main.users' --take=20                    List first 20 records
		php com sql list 'main.users' --skip=10 --take=5           List 5 records starting from offset 10
		php com sql list 'main.users' --format=table               Display results as ASCII table
		php com sql list 'zippy.products' --take=50 --format=table

		Examples - SEARCH:

		php com sql search 'zippy.products' --search='MEDALLON'           Search for products containing 'MEDALLON'
		php com sql search 'zippy.products' --search='POLLO' --take=5     Search and limit to 5 results
		php com sql search 'main.users' --search='john' --format=table    Search users and display as table

		STR;

		dd(strtoupper(Strings::before(__METHOD__, 'Command::')) . ' HELP');
		dd($str);
	}
}



