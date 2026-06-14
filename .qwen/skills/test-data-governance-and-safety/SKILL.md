---
name: test-data-governance-and-safety
description: Enforce mandatory flagging of test data with is_test_data field, schema migrations, UI visibility controls, global cleanup tools, and KPI/analytics protection
---

# SKILL — Test Data Governance & Safety

---

# Rule: Mandatory Flagging of Test Data

Whenever generating **test, dummy, mock, or demonstration data**, it **MUST always be explicitly marked** using a dedicated flag field.

The standard field name is:

```
is_test_data
```

Allowed values:

```
true
false
```

This rule applies to **all data storage formats**, including:

* SQL databases
* NoSQL databases
* CSV
* XLSX
* JSON
* Any persisted dataset

Every generated test record **must set**:

```
is_test_data = true
```

---

# Rule: Schema Enforcement

If the dataset **does not already contain the field `is_test_data`**, it must be added before inserting test data.

### For SQL Databases

The correct process is:

1. Create a migration

```
ADD COLUMN is_test_data BOOLEAN NOT NULL DEFAULT FALSE
```

2. Execute the migration.
3. Insert test records using:

```
is_test_data = TRUE
```

Direct modification of production schemas **without migration is not allowed**.

---

# Rule: UI Visibility Control

If `is_test_data` did not previously exist in the system schema, the system **must ask whether a visibility control should be added in the application settings**.

Typically this belongs in a module such as:

```
Settings
Admin
System
Developer Settings
```

A **UI switch must be offered**:

```
Show Test Data
```

Behavior:

* OFF (default): test data is hidden in datagrids and listings.
* ON: test data appears alongside production data.

Example filtering rule:

```
WHERE is_test_data = FALSE
```

Unless the switch is enabled.

---

# Rule: Global Test Data Cleanup Tool

When introducing the `is_test_data` field into the system, the system should **offer to create a cleanup utility in Settings**.

Suggested option:

```
Delete All Test Data
```

This operation must:

1. Iterate through **all registered databases** defined in:

```
config/databases.config.js
```

2. For each database:

* Scan all tables (SQL)
* Scan all collections (NoSQL)

3. Delete records where:

```
is_test_data = TRUE
```

---

# Rule: Mandatory Confirmation for Bulk Deletion

Bulk deletion of test data **must require explicit confirmation**.

Example warning:

```
This action will permanently delete all test data across all databases.
Do you want to continue?
```

Optional additional safety:

```
Type DELETE_TEST_DATA to confirm
```

---

# Rule: Safe Deletion Exception

Confirmation **is not required** when deleting a **single specific record**.

Example:

```
DELETE FROM products WHERE id = 123
```

But confirmation **is required** for queries such as:

```
DELETE FROM products WHERE is_test_data = TRUE
```

or any operation affecting **multiple records**.

---

# Rule: KPI and Analytics Protection

Any system that generates:

* dashboards
* metrics
* KPIs
* heatmaps
* analytics reports

**must exclude test data by default**.

Filtering rule:

```
is_test_data = FALSE
```

This prevents **dummy data contaminating business metrics**.

---

# Summary

Mandatory system guarantees:

1. Test data **must always include the `is_test_data` flag**.
2. If the field does not exist, **a migration or schema update must be created**.
3. The system **must ask whether to add a Settings switch to show/hide test data**.
4. The system **should provide a global cleanup tool** to remove test data.
5. Cleanup must **scan all databases listed in `config/databases.config.js`**.
6. **Bulk deletion requires confirmation**.
7. KPIs, analytics, and dashboards **must exclude test data by default**.

