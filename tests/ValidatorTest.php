<?php

namespace simplerest\tests;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '../../vendor/autoload.php';

if (php_sapi_name() != "cli"){
	return; 
}

require_once __DIR__ . '/../app.php';

use PHPUnit\Framework\TestCase;
use simplerest\core\libs\Validator;

class ValidatorTest extends TestCase
{   
	/*	
		Cuando hay reglas pero no hay datos, no se debe lanzar una excepción
		(comportamiento esperado)
	*/
	function testvalidateEmptyData()
    {        
		$validator = new Validator();
        $res = $validator->validate([],['username'=>'admin','correo'=>'admin@serviya.com']);
		$this->assertTrue($res);
    }

	function testvalidateEmptyRuleException()
    {
        $this->expectException(\InvalidArgumentException::class);
		$validator = new Validator();
		$validator->validate([], []); // Sin reglas debería lanzar excepción
    }
	
    /** @test */
    public function test_empty_rules_validation()
    {
        $this->assertTrue(
            (new Validator())->validate(
                [
                    'username' => 'admin',
                    'correo' => 'admin@serviya.com'
                ],
                [
                    'username' => [],
                    'correo' => []
                ]
            )
        );
    }

    /** @test */
    public function test_required_fields_with_valid_data()
    {
        $this->assertTrue(
            (new Validator())->validate(
                [
                    'username' => 'admin',
                    'correo' => 'admin@serviya.com'
                ],
                [
                    'username' => ['required' => true],
                    'correo' => ['required' => false]
                ]
            )
        );
    }

    /** @test */
    public function test_required_field_with_empty_value()
    {
        $validator = new Validator();
        $result = $validator->validate(
            [
                'username' => '',
                'correo' => 'admin@serviya.com'
            ],
            [
                'username' => ['required' => true],
                'correo' => ['required' => false]
            ]
        );

        $this->assertFalse($result);
        $errors = $validator->getErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('username', $errors);
    }

    /** @test */
    public function test_string_max_length_validation()
    {
        $validator = new Validator();
        $result = $validator->validate(
            [
                'username' => 'admin',
                'correo' => 'administradormundialdelmundoconocidouniversal@serviya.com'
            ],
            [
                'username' => [],
                'correo' => ['type' => 'string', 'max' => '30']
            ]
        );

        $this->assertFalse($result);
        $errors = $validator->getErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('correo', $errors);
    }

	/** @test */
	public function test_string_length_validations()
	{
		$validator = new Validator();

		// Test max length
		$result = $validator->validate(
			['field' => 'abc'],
			['field' => ['type' => 'string', 'max' => '5']]
		);
		$this->assertTrue($result, "String bajo el máximo debería pasar");

		$result = $validator->validate(
			['field' => '123456'],
			['field' => ['type' => 'string', 'max' => '5']]
		);
		$this->assertFalse($result, "String sobre el máximo debería fallar");

		// Test min length
		$result = $validator->validate(
			['field' => 'abc'],
			['field' => ['type' => 'string', 'min' => '2']]
		);
		$this->assertTrue($result, "String sobre el mínimo debería pasar");

		$result = $validator->validate(
			['field' => 'a'],
			['field' => ['type' => 'string', 'min' => '2']]
		);
		$this->assertFalse($result, "String bajo el mínimo debería fallar");
	}

    /** @test */
    public function test_not_number_validation()
	{
		$validator = new Validator();
		$result = $validator->validate(
			['nombre' => 'Sebastian2'],
			['nombre' => ['type' => 'notnum']]
		);

		$this->assertFalse($result);  // Esperamos que falle porque 'Sebastian2' contiene números
	}

    /** @test */
    public function test_empty_value_skips_type_validation()
    {
        $this->assertTrue(
            (new Validator())->validate(
                ['fuerza' => ''],
                ['fuerza' => ['type' => 'integer', 'min' => '30']]
            )
        );
    }

    /** @test */
    public function test_valid_ipv4_regex_validation()
    {
        $this->assertTrue(
            (new Validator())->validate(
                ['IPv4' => '192.168.0.27'],
                ['IPv4' => ['type' => 'regex:/^((2[0-4]|1\d|[1-9])?\d|25[0-5])(\.(?1)){3}\z/']]
            )
        );
    }

    /** @test */
    public function test_invalid_ipv4_regex_validation()
    {
        $validator = new Validator();
        $result = $validator->validate(
            ['IPv4' => '192.168.0.300'],
            ['IPv4' => ['type' => 'regex:/^((2[0-4]|1\d|[1-9])?\d|25[0-5])(\.(?1)){3}\z/']]
        );

        $this->assertFalse($result);
        $errors = $validator->getErrors();
        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('IPv4', $errors);
    }
	
	function testisTypeExceptionPorTipoEmpty()
    {
        $this->expectException(\InvalidArgumentException::class);
        Validator::isType('Brayan','');
    }
	
	function testisTypeExceptionPorTipoDesconocido()
	{
		$this->expectException(\InvalidArgumentException::class);
		Validator::isType('Brayan', 'tipo_inexistente');
	}
		
	function testisTypeExceptionPorDatoNull()
    {
        $this->expectException(\InvalidArgumentException::class);
        Validator::isType(NULL,'int');
    }
	
	function testisTypeExceptionPorRegexInvalida()
    {
        $this->expectException(\InvalidArgumentException::class);
        Validator::isType('190-200-200','regex:/(.*/');
    }
	
	function testisType()
    {
		$this->assertTrue(
			Validator::isType('Brayan','string')
        );
		
		$this->assertFalse(
			Validator::isType('1250','notnum')
        );
		
		$this->assertFalse(
			Validator::isType('Sebastian2','notnum')
        );
		
		$this->assertTrue(
			Validator::isType('1250','string')
        );
		
		$this->assertTrue(
			Validator::isType('','string')
        );
		
		$this->assertTrue(
			Validator::isType('   ','string')
        );
		
		$this->assertTrue(
			Validator::isType('32543','number')
        );
		
		$this->assertTrue(
			Validator::isType('-32543','number')
        );
		
		$this->assertTrue(
			Validator::isType(' 16  ','number')
        );
		
		$this->assertTrue(
			Validator::isType(' 16  ','int')
        );
		
		$this->assertFalse(
			Validator::isType('16.3','int')
        );
		
		$this->assertFalse(
			Validator::isType(' 16.3  ','int')
        );
		
		
		$this->assertTrue(
			Validator::isType('16.3','decimal(8,2)')
		);
			
		// $this->assertTrue(
		// 	Validator::isType('-.02','decimal(8,2')
		// );
		
		$this->assertFalse(
			Validator::isType('-.02','decimal(8,2')
        );
		

		$this->assertTrue(
			Validator::isType('.023','number')
        );
		
		$this->assertTrue(
			Validator::isType('192.168.0.27','regex:/^((2[0-4]|1\d|[1-9])?\d|25[0-5])(\.(?1)){3}\z/')
        );
		
		$this->assertFalse(
			Validator::isType('192.168.0.300','regex:/^((2[0-4]|1\d|[1-9])?\d|25[0-5])(\.(?1)){3}\z/')
        );
	}
	
	// /*
	// 	Propuestas por Claude
	// */

	function testArrayValidation()
	{
		$validator = new Validator();
		
		$this->assertTrue($validator->validate(
			[
				'numbers' => [1, 2, 3]
			],
			[
				'numbers' => [
					'type' => 'array',
					'min_len' => 2
				]
			]
		));

		$this->assertFalse($validator->validate(
			[
				'numbers' => 'no_es_array'
			],
			[
				'numbers' => [
					'type' => 'array'
				]
			]
		));
	}

    public function testUniqueFields()
{
    $validator = new Validator();
    
    $db = $this->getMockBuilder('simplerest\core\libs\DB')
        ->disableOriginalConstructor()
        ->getMock();
    
    // Mock del método estático table
    $db::staticExpects($this->any())
        ->method('table')
        ->with('users')
        ->willReturn($db);
        
    $db->expects($this->any())
        ->method('where')
        ->willReturn($db);
        
    $db->expects($this->any())
        ->method('exists')
        ->willReturn(false);
        
    $validator->setUniques(['email'], 'users');
    
    $result = $validator->validate(
        [
            'email' => 'test@test.com'
        ],
        [
            'email' => ['type' => 'email']
        ]
    );
    
    $this->assertTrue($result);
}

    // public function testIgnoredFields()
    // {
    //     $validator = new Validator();
    //     $validator->ignoreFields(['password']);
        
    //     $this->assertTrue($validator->validate(
    //         [
    //             'username' => ['required' => true],
    //             'password' => ['required' => true]
    //         ],
    //         [
    //             'username' => 'admin',
    //             'password' => '' // debería pasar porque password está ignorado
    //         ]
    //     ));
    // }

    // public function testFillableFields()
    // {
    //     $validator = new Validator();
        
    //     $this->assertFalse($validator->validate(
    //         ['username' => ['required' => true]],
    //         ['username' => 'admin', 'role' => 'admin'],
    //         ['username'] // solo username es fillable
    //     ));
    // }
	
}
