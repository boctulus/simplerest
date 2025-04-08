<?php

namespace Boctulus\Simplerest\Core\Libs;

use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Comment;
use PhpParser\ParserFactory;
use PhpParser\Lexer;
use PhpParser\PrettyPrinter\Standard;

/*
    Nota sore Modifiers:
        
    public const PUBLIC    =  1;
    public const PROTECTED =  2;
    public const PRIVATE   =  4;
    public const STATIC    =  8;
    public const ABSTRACT  = 16;
    public const FINAL     = 32;
    public const READONLY  = 64;
    public const PUBLIC_SET = 128;
    public const PROTECTED_SET = 256;
    public const PRIVATE_SET = 512;

    https://apiref.phpstan.org/2.1.x/source-vendor.nikic.php-parser.lib.PhpParser.Modifiers.html#9

*/

class PHPParser extends NodeVisitorAbstract {
    protected $functionsToKeep;
    
    public function __construct() {
        // Constructor sin parámetros
    }
    
    /**
     * Converts static properties and methods into non-static ones.
     * 
     * ----- WARNING : esta funcion contiene bugs -----
     * 
     * It skips properties that are defined in all uppercase with an initial value different from null,
     * as they are treated as constants.
     * Also modifies static method calls to instance method calls.
     *
     * @param string $code The PHP code to transform.
     * @return string The transformed PHP code.
     */
    public static function convertStaticToNonStatic(string $code): string {
        // Create a lexer and parser.
        $lexer = new Lexer\Emulative(null);
        $parser = (new ParserFactory())->createForHostVersion($lexer);
        $ast = $parser->parse($code);

        // Create a node traverser.
        $traverser = new NodeTraverser();

        // Visitor to modify properties and methods.
        $traverser->addVisitor(new class extends NodeVisitorAbstract {
            public function enterNode(Node $node) {
                // Process class nodes.
                if ($node instanceof Stmt\Class_) {
                    foreach ($node->stmts as $stmt) {
                        // Modify properties: remove static modifier if not constant-like.
                        if ($stmt instanceof Stmt\Property && $stmt->isStatic()) {
                            // Loop over each property in the declaration.
                            $keepStatic = false;
                            foreach ($stmt->props as $prop) {
                                // Get property name as string.
                                $propName = $prop->name->toString();
                                // Check if property name is all uppercase and has a non-null default.
                                if (ctype_upper($propName) && $prop->default !== null) {
                                    $keepStatic = true;
                                    break;
                                }
                            }
                            // If not marked to keep static, remove the static flag.
                            if (!$keepStatic) {
                                $stmt->flags = $stmt->flags & ~8;
                            }
                        }
                        // Modify methods: remove static modifier.
                        if ($stmt instanceof Stmt\ClassMethod && $stmt->isStatic()) {
                            $stmt->flags = $stmt->flags & ~8;
                        }
                    }
                }
            }
        });

        // Visitor to modify static method calls.
        $traverser->addVisitor(new class extends NodeVisitorAbstract {
            public function enterNode(Node $node) {
                // Process static calls.
                if ($node instanceof Node\Expr\StaticCall) {
                    // Check if the class part is a name.
                    if ($node->class instanceof Node\Name) {
                        $className = $node->class->toString();
                        // If the call uses "self" or "static", convert to instance call.
                        if (in_array(strtolower($className), ['self', 'static'])) {
                            return new Node\Expr\MethodCall(
                                new Node\Expr\Variable('this'),
                                $node->name,
                                $node->args
                            );
                        }
                        // Optionally, you could check for explicit class names matching the current class.
                    }
                }
            }
        });

        // Traverse and modify the AST.
        $modifiedAst = $traverser->traverse($ast);

        // Pretty print the modified AST back to PHP code.
        $prettyPrinter = new Standard();
        return $prettyPrinter->prettyPrintFile($modifiedAst);
    }

    public function leaveNode(Node $node) {
        if ($node instanceof Stmt\Class_) {
            $newStmts = [];
            $hasRemovedMethods = false;
            
            foreach ($node->stmts as $stmt) {
                if ($stmt instanceof Stmt\ClassMethod) {
                    $methodName = $stmt->name->toString();
                    if (in_array($methodName, $this->functionsToKeep)) {
                        // Si removimos métodos antes de este y aún no hemos agregado un comentario
                        if ($hasRemovedMethods) {
                            $this->addCommentNode($newStmts);
                            $hasRemovedMethods = false;
                        }
                        $newStmts[] = $stmt;
                    } else {
                        $hasRemovedMethods = true;
                    }
                } else {
                    // Para no-métodos (propiedades, constantes, etc.), los mantenemos
                    $newStmts[] = $stmt;
                }
            }
            
            // Si terminamos la clase con métodos removidos, añadimos un comentario al final
            if ($hasRemovedMethods) {
                $this->addCommentNode($newStmts);
            }
            
            $node->stmts = $newStmts;
            return $node;
        }
        
        return null;
    }
    
    protected function addCommentNode(array &$newStmts) {
        $comment = new Comment("// ...");
        $nop = new Stmt\Nop();
        $nop->setAttribute('comments', [$comment]);
        $newStmts[] = $nop;
    }
    
    /*
        TODO: implementar bool $keep_header_comments = true y $keep_body_comments = true
    */
    function reduceCode(string $php_code, array $keep_functions, bool $keep_header_comments = true, $keep_body_comments = true) : string {
        $this->functionsToKeep = $keep_functions ?? [];
        $lexer = new Lexer\Emulative(null, ['usedAttributes' => ['comments', 'startLine', 'endLine']]);
        $parser = (new ParserFactory())->createForHostVersion($lexer);
       
        try {
            $ast = $parser->parse($php_code);
            $traverser = new NodeTraverser();
            $traverser->addVisitor($this);
            $modifiedAst = $traverser->traverse($ast);
            $printer = new Standard();
            return $printer->prettyPrintFile($modifiedAst);
        } catch (\PhpParser\Error $error) {
            throw new \RuntimeException("Parse error: {$error->getMessage()}");
        }
    }
}