# SimpleRest Machine ID

`boctulus/simplerest-machine-id` contains `Hardware::UniqueMachineID()`, a
legacy helper that derives an identifier using operating-system commands. It
retains the class name `Boctulus\Simplerest\Core\Libs\Hardware` and uses
SimpleRest's `Files` utility on Windows.

The identifier is environment-dependent and should not be treated as a
cryptographic secret or as a guaranteed stable hardware identity.
