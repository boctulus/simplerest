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

		ℹ  {db} in this case is DB NAME, not "db name connection"

		Examples:

		php com sql list 'main.users'                              List first 10 records from users table
		php com sql list 'main.users' --limit=20                   List first 20 records
		php com sql list 'main.users' --offset=10 --limit=5        List 5 records starting from offset 10
		php com sql list 'main.users' --format=table               Display results as ASCII table
		php com sql list 'db_195.products' --limit=50 --format=table

		STR;

		dd(strtoupper(Strings::before(__METHOD__, 'Command::')) . ' HELP');
		dd($str);
	}
}



