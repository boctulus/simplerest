# StdOut::print() vs dd() - Output Methods in SimpleRest

## Overview

In the SimpleRest framework, there are different methods for outputting information depending on the context and purpose. Understanding when to use `StdOut::print()` versus `dd()` is important for proper debugging and user communication.

## StdOut::print()

### When to Use

- **Command-line interface (CLI) output**: When building commands that interface with users via the command line
- **User-friendly output**: When you need to provide clear, formatted messages to the end user
- **Production code**: When the output should be preserved in production environments
- **Controlled output**: When you want clean, formatted output without extra debugging information
- **Non-terminating output**: When you want to print information without stopping the program execution

### Examples of Usage

```php
StdOut::print("Processing completed successfully");
StdOut::print("Error: Connection failed");
StdOut::print("Records found: " . count($results));
```

## dd() (Dump and Die)

### When to Use

- **Debugging**: When you need to inspect variables during development
- **Development and testing**: When temporarily inspecting the contents of variables or execution flow
- **Immediate termination**: When you want to stop execution after dumping data
- **Complex data structures**: When you need to see the full structure of complex objects/arrays
- **Development-only scenarios**: When the output is not intended for end users

### Examples of Usage

```php
dd($user, "User details");
dd($query->buildSQL());
dd($request->all());
```

## Key Differences

| Feature | StdOut::print() | dd() |
|---------|----------------|------|
| **Execution** | Continues after output | Stops execution immediately |
| **Target Audience** | End users | Developers |
| **Format** | Clean, formatted output | Detailed dump with type information |
| **Production Safety** | Safe for production | Should be removed before production |
| **Use Case** | Final output, user feedback | Debugging, development |

## Best Practices

### Use StdOut::print() when:

1. Creating command-line tools that communicate with users
2. Providing error messages or status updates
3. Outputting results from SQL queries or other operations
4. Building production-ready output systems
5. You want the program to continue after output

### Use dd() when:

1. Debugging during development
2. Inspecting variable contents temporarily
3. Tracing execution flow
4. Understanding the structure of complex objects
5. You need immediate termination for debugging purposes

## Security Considerations

- `dd()` should **never** be used in production code as it reveals internal data structures
- `StdOut::print()` is appropriate for production when providing user-facing messages
- Always remove `dd()` calls before deploying to production
- Be careful with what data you output via `StdOut::print()` in production environments

## Example in Command Context

```php
// Good use of StdOut::print() in a command
function list(...$args) {
    // ... processing
    if (empty($results)) {
        StdOut::print("No records found\r\n");
        return;
    }
    $this->displayAsTable($results);
}

// Good use of dd() during development
function processQuery($sql) {
    // During development - remove before production
    dd($sql, "Debug SQL query");
    
    // ... actual implementation
}
```

## Summary

Choose `StdOut::print()` for user-facing output in CLI applications and `dd()` for temporary debugging during development. Following these guidelines will ensure proper application behavior and maintain security standards.