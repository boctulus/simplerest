# SimpleRest String Obfuscation

`boctulus/simplerest-string-obfuscation` contains the legacy `SimpleCrypt`
helper under `Boctulus\Simplerest\Core\Libs`.

Despite its historical class name, this code is reversible character
reordering with padding. It is not cryptography and must not be used to protect
passwords, tokens, personal data, or other secrets.
