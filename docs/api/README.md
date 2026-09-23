# HTTP and REST API

## Audit status

The current source path for automatic API authorization has been traced. The built-in resource controller checks credentials and derives callable actions from the ACL before the action method is called. See the [authentication guide](../security/authentication.md), the [ACL guide](../security/acl.md), and the [claim ledger](../audit/authentication-acl.md).

This source trace does not establish a successful HTTP run. Endpoint discovery, query parameters, serialization, validation, and broad automatic CRUD claims remain under audit; the old automatic-endpoint summary is still an audit input. See the [pending audit area](../audit/pending/README.md).
