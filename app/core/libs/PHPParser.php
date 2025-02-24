<?php
namespace simplerest\core\libs;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Comment;
use PhpParser\ParserFactory;
use PhpParser\Lexer;
use PhpParser\PrettyPrinter\Standard;

class PHPParser extends NodeVisitorAbstract {
    protected $functionsToKeep;
    
    public function __construct() {
        // Constructor sin parámetros
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