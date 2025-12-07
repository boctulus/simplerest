<?php

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
	 * List contents of a table
	 *
	 * Usage: php com sql list '{db}.{table}' [--offset=N] [--limit=N] [--format=table]
	 */
	function list(...$args) {
		if (empty($args)) {
			StdOut::print("Error: table argument is required\r\n");
			StdOut::print("Usage: php com sql list '{db}.{table}' [--offset=N] [--limit=N] [--format=table]\r\n");
			return;
		}

		$table_arg = array_shift($args);
		$offset = 0;
		$limit = 10; // Default limit
		$format = 'simple'; // Default format

		// Parse options
		foreach ($args as $arg) {
			if (Strings::startsWith('--offset=', $arg)) {
				$offset = (int) substr($arg, 9);
			} elseif (Strings::startsWith('--limit=', $arg)) {
				$limit = (int) substr($arg, 8);
			} elseif (Strings::startsWith('--format=', $arg)) {
				$format = substr($arg, 9);
			}
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
		sql list '{db}.{table}' [--offset=N] [--limit=N] [--format=table]    List contents of a table
		sql count '{db}.{table}'                                      Count records in a table
		sql select "SELECT ..." --connection={db}                     Execute a SELECT query
		sql export '{db}.{table}' --format=csv|json [--path|--file]=path    Export table data
		sql query "SQL QUERY ..." --connection={db}                   Execute a general SQL query
		sql statement "SQL STATEMENT ..." --connection={db} [--force] Execute a non-SELECT SQL statement (INSERT, UPDATE, DELETE, etc.); use --force for destructive operations
		sql statement "SQL STATEMENT ..." --connection={db} [--confirm] Alternative flag for destructive operations

		ℹ  {db} in this case is DB NAME, not "db name connection"

		Examples:

		php com sql list 'main.users'                              List first 10 records from users table
		php com sql list 'main.users' --limit=20                   List first 20 records
		php com sql list 'main.users' --offset=10 --limit=5        List 5 records starting from offset 10
		php com sql list 'main.users' --format=table               Display results as ASCII table
		php com sql list 'db_195.products' --limit=50 --format=table
		php com sql count 'zippy.categories'                       Count records in zippy.categories table
		php com sql select "SELECT COUNT(*) as total FROM products" --connection=zippy    Execute SELECT query
		php com sql export 'zippy.categories' --format=csv         Export table to CSV
		php com sql export 'zippy.products' --format=json --path=/tmp/products.json    Export to specific path
		php com sql export 'zippy.categories' --format=csv --file=/tmp/categories.csv    Export using --file option
		php com sql query "SELECT COUNT(*) FROM categories" --connection=zippy    Execute general SQL query
		php com sql statement "INSERT INTO users (name, email) VALUES ('John', 'john@example.com')" --connection=main    Execute INSERT statement
		php com sql statement "DROP TABLE old_table" --connection=main --force    Execute DROP with confirmation flag
		php com sql statement "DELETE FROM users" --connection=main --force    Execute DELETE without WHERE clause
		php com sql statement "TRUNCATE TABLE temp_data" --connection=main --confirm    Execute TRUNCATE with confirm flag

		STR;

		dd(strtoupper(Strings::before(__METHOD__, 'Command::')) . ' HELP');
		dd($str);
	}

	/**
	 * Count records in a table
	 *
	 * Usage: php com sql count '{db}.{table}'
	 */
	function count(...$args) {
		if (empty($args)) {
			StdOut::print("Error: table argument is required\r\n");
			StdOut::print("Usage: php com sql count '{db}.{table}'\r\n");
			return;
		}

		$table_arg = array_shift($args);

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
			// Build query to count records
			$query = table($table);
			$count = $query->count();

			StdOut::print("Table '$table' has $count record(s) in connection '$db'\r\n");

		} catch (\Exception $e) {
			StdOut::print("Error: " . $e->getMessage() . "\r\n");
		}
	}

	/**
	 * Execute a SELECT query
	 *
	 * Usage: php com sql select "SELECT * FROM table" --connection={db}
	 */
	function select(...$args) {
		if (empty($args)) {
			StdOut::print("Error: SQL query argument is required\r\n");
			StdOut::print("Usage: php com sql select \"SELECT * FROM table\" --connection={db}\r\n");
			return;
		}

		$query = array_shift($args);
		$db = 'main'; // Default connection

		// Parse options
		foreach ($args as $arg) {
			if (Strings::startsWith('--connection=', $arg)) {
				$db = substr($arg, 13); // Length of '--connection='
			}
		}

		// Validate connection exists
		if (!DB::connectionExists($db)) {
			StdOut::print("Error: Connection '$db' is not registered in db_connections\r\n");
			return;
		}

		// Validate this is a SELECT query
		$queryUpper = strtoupper(trim($query));
		if (!Strings::startsWith('SELECT', $queryUpper)) {
			StdOut::print("Error: Only SELECT queries are allowed for security reasons\r\n");
			return;
		}

		// Set connection
		DB::setConnection($db);

		try {
			$results = DB::select($query);

			if (empty($results)) {
				StdOut::print("Query executed successfully but returned no results\r\n");
				return;
			}

			// Display results
			$this->displayAsTable($results);

		} catch (\Exception $e) {
			StdOut::print("Error: " . $e->getMessage() . "\r\n");
		}
	}

	/**
	 * Export table data to CSV or JSON
	 *
	 * Usage: php com sql export '{db}.{table}' --format=csv|json [--path=/path/to/file|--file=/path/to/file]
	 */
	function export(...$args) {
		if (empty($args)) {
			StdOut::print("Error: table argument is required\r\n");
			StdOut::print("Usage: php com sql export '{db}.{table}' --format=csv|json [--path=/path/to/file|--file=/path/to/file]\r\n");
			return;
		}

		$table_arg = array_shift($args);
		$format = 'csv'; // Default format
		$path = null;

		// Parse options
		foreach ($args as $arg) {
			if (Strings::startsWith('--format=', $arg)) {
				$format = strtolower(substr($arg, 9)); // Length of '--format='
			} elseif (Strings::startsWith('--path=', $arg)) {
				$path = substr($arg, 7); // Length of '--path='
			} elseif (Strings::startsWith('--file=', $arg)) {
				$path = substr($arg, 7); // Length of '--file=' (alternative to --path)
			}
		}

		// Validate format
		if (!in_array($format, ['csv', 'json'])) {
			StdOut::print("Error: Format must be 'csv' or 'json'\r\n");
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
			// Get all records from the table
			$query = table($table);
			$results = $query->get();

			if (empty($results)) {
				StdOut::print("No records found in table '$table'\r\n");
				return;
			}

			// Generate default path if not provided
			if ($path === null) {
				$timestamp = date('Y-m-d_H-i-s');
				$path = "exports/{$table}_{$timestamp}.{$format}";
			}

			// Ensure directory exists
			$dir = dirname($path);
			if (!is_dir($dir)) {
				if (!mkdir($dir, 0755, true)) {
					StdOut::print("Error: Could not create directory '$dir'\r\n");
					return;
				}
			}

			// Export based on format
			if ($format === 'csv') {
				$this->exportToCSV($results, $path);
			} else { // json
				$this->exportToJSON($results, $path);
			}

			StdOut::print("Successfully exported {$table} ({$db}) to $path ({$format} format, " . count($results) . " records)\r\n");

		} catch (\Exception $e) {
			StdOut::print("Error: " . $e->getMessage() . "\r\n");
		}
	}

	/**
	 * Execute a SQL statement (non-SELECT queries: INSERT, UPDATE, DELETE, etc.)
	 *
	 * Usage: php com sql statement "SQL STATEMENT" --connection={db} [--force]
	 */
	function statement(...$args) {
		if (empty($args)) {
			StdOut::print("Error: SQL statement argument is required\r\n");
			StdOut::print("Usage: php com sql statement \"SQL STATEMENT\" --connection={db} [--force]\r\n");
			return;
		}

		$query = array_shift($args);
		$db = 'main'; // Default connection
		$force = false; // Default to no force

		// Parse options
		foreach ($args as $arg) {
			if (Strings::startsWith('--connection=', $arg)) {
				$db = substr($arg, 13); // Length of '--connection='
			} elseif ($arg === '--force' || $arg === '--confirm') {
				$force = true;
			}
		}

		// Validate connection exists
		if (!DB::connectionExists($db)) {
			StdOut::print("Error: Connection '$db' is not registered in db_connections\r\n");
			return;
		}

		// Check the type of query (only allow non-SELECT statements for security)
		$queryUpper = strtoupper(trim($query));

		// Disallow SELECT statements to prevent confusion with select method
		if (Strings::startsWith('SELECT', $queryUpper)) {
			StdOut::print("Error: SELECT statements are not allowed. Use 'sql select' for SELECT queries.\r\n");
			return;
		}

		// Check for potentially destructive operations that require confirmation
		$isDestructive = false;
		$message = '';

		// Check for DROP statement
		if (Strings::startsWith('DROP', $queryUpper)) {
			$isDestructive = true;
			$message = "DROP statement detected. This will permanently delete a table or database.";
		}
		// Check for TRUNCATE statement
		elseif (Strings::startsWith('TRUNCATE', $queryUpper)) {
			$isDestructive = true;
			$message = "TRUNCATE statement detected. This will permanently delete all data in a table.";
		}
		// Check for DELETE without WHERE clause
		elseif (Strings::startsWith('DELETE', $queryUpper)) {
			// Check if there's a WHERE clause by looking for it in the query
			// We'll look for WHERE after DELETE but not in comments or strings
			if (!preg_match('/\bWHERE\b/i', $query)) {
				$isDestructive = true;
				$message = "DELETE statement without WHERE clause detected. This will delete all records in the table.";
			}
		}

		// If it's a destructive operation and --force was not used, ask for confirmation
		if ($isDestructive && !$force) {
			StdOut::print("$message\r\n");
			StdOut::print("Use --force or --confirm flag to execute this statement.\r\n");
			return;
		}

		DB::setConnection($db);

		try {
			$affected = DB::statement($query);

			StdOut::print("Statement executed successfully. Affected rows: {$affected}\r\n");

		} catch (\Exception $e) {
			StdOut::print("Error: " . $e->getMessage() . "\r\n");
		}
	}

	/**
	 * Execute a general SQL query (supports SELECT, INSERT, UPDATE, DELETE)
	 *
	 * Usage: php com sql query "SQL QUERY" --connection={db}
	 */
	function query(...$args) {
		if (empty($args)) {
			StdOut::print("Error: SQL query argument is required\r\n");
			StdOut::print("Usage: php com sql query \"SQL QUERY\" --connection={db}\r\n");
			return;
		}

		$query = array_shift($args);
		$db = 'main'; // Default connection

		// Parse options
		foreach ($args as $arg) {
			if (Strings::startsWith('--connection=', $arg)) {
				$db = substr($arg, 13); // Length of '--connection='
			}
		}

		// Validate connection exists
		if (!DB::connectionExists($db)) {
			StdOut::print("Error: Connection '$db' is not registered in db_connections\r\n");
			return;
		}

		// Check the type of query to determine how to handle it
		$queryUpper = strtoupper(trim($query));

		// For SELECT queries, use the select method logic
		if (Strings::startsWith('SELECT', $queryUpper)) {
			DB::setConnection($db);

			try {
				$results = DB::select($query);

				if (empty($results)) {
					StdOut::print("Query executed successfully but returned no results\r\n");
					return;
				}

				// Display results
				$this->displayAsTable($results);

			} catch (\Exception $e) {
				StdOut::print("Error: " . $e->getMessage() . "\r\n");
			}
		}
		// For other queries (INSERT, UPDATE, DELETE), execute directly
		else {
			// Only allow safe operations
			$allowed = ['DESCRIBE', 'SHOW'];
			$isValid = false;

			foreach ($allowed as $type) {
				if (Strings::startsWith($type, $queryUpper)) {
					$isValid = true;
					break;
				}
			}

			if (!$isValid) {
				StdOut::print("Error: Query type not allowed for security reasons\r\n");
				return;
			}

			DB::setConnection($db);

			try {
				$affected = DB::statement($query);

				StdOut::print("Query executed successfully. Affected rows: {$affected}\r\n");

			} catch (\Exception $e) {
				StdOut::print("Error: " . $e->getMessage() . "\r\n");
			}
		}
	}

	/**
	 * Export data to CSV format
	 */
	private function exportToCSV(array $data, string $path) {
		$file = fopen($path, 'w');
		if (!$file) {
			throw new \Exception("Could not open file for writing: $path");
		}

		// Add BOM for UTF-8 to handle special characters in Excel
		fwrite($file, "\xEF\xBB\xBF");

		// Write headers (column names from first row)
		if (!empty($data)) {
			$headers = array_keys($data[0]);
			fputcsv($file, $headers);

			// Write rows
			foreach ($data as $row) {
				// Convert null values to empty strings for CSV
				$row = array_map(function($value) {
					return $value === null ? '' : $value;
				}, $row);
				fputcsv($file, $row);
			}
		}

		fclose($file);
	}

	/**
	 * Export data to JSON format
	 */
	private function exportToJSON(array $data, string $path) {
		$jsonContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		if ($jsonContent === false) {
			throw new \Exception("Error encoding data to JSON");
		}

		$result = file_put_contents($path, $jsonContent);
		if ($result === false) {
			throw new \Exception("Could not write to file: $path");
		}
	}
}



