# Query Builder

SimpleRest viene con un constructor de consultas ("queries") y sentencias ("statements") basado en métodos encadenados. 

Algunas caracteristicas:

Siendo un desarrollo separado, el Query Builder fue disenado para ser altamente compatible con el de Laravel, mas potente (al incluir caracteristicas del ORM), mas rapido y totalmente multi-tenant.

Internamente hace uso intensivo de caching para mayor performance.


## Inserción de registros

SimpleREST ofrece varios métodos para insertar registros en la base de datos. Los principales son create() e insert(), cada uno con sus características particulares.

1. **`create(array $data, $ignore_duplicates = false)`**  
   Intenta crear un registro o múltiples registros en la base de datos.

2. **`createOrIgnore(array $data)`**  
   Crea un registro ignorando duplicados.

3. **`createOrUpate(array $data)`** 
	Crea o actualiza registros.

4. **`insert(array $data, bool $useTransaction = true)`**  
   Método principal para insertar uno o varios registros con el ciclo de vida completo del modelo.

5. **`rawInsert(array $data)`**  
   Inserta directamente en la base de datos, omitiendo hooks y mutadores.

6. **`bulkInsert(array $data, int $batchSize = 1000)`**  
   Realiza una inserción masiva optimizada utilizando una sola consulta.

7. **`executeInsert(array $data)`**  
   Método auxiliar para ejecutar la inserción de un solo registro (utilizado por `insert()` y `rawInsert()`).

8. **`insertOrIgnore(array $data)`**  
   Inserta un registro ignorando errores de duplicación.

9. **`insertOrUpdate(array $data, ?array $uniqueFields = null)`**  
   Inserta o actualiza múltiples registros basados en campos únicos, envolviendo las operaciones en una transacción.


Método create()

El método básico para insertar un único registro. Acepta un array asociativo de campos y valores:

    $last_id = DB::table('users')->create([
		'name' => 'John',
		'age' => 22,
	]);

También puede manejar inserciones múltiples, pero las procesa una por una:

    $data = [
		['name' => 'John', 'age' => 22],
		['name' => 'Jane', 'age' => 25]
	];

$last_id = DB::table('users')->create($data);

Características de create():

-	No usa transacciones automáticamente
-	Retorna el ID del último registro insertado
-	Si falla una inserción en una serie, las anteriores permanecen
-	Ejecuta los hooks onCreating() y onCreated()

Método insert()

Diseñado especialmente para inserciones múltiples seguras:

	$data = [
		['name' => 'John', 'age' => 22],
		['name' => 'Jane', 'age' => 25]
	];

	$last_id = DB::table('users')->insert($data);

Características de insert():

-	Maneja transacciones automáticamente
-	Si falla cualquier inserción, hace rollback de todas
-	Mejor manejo de errores
-	Retorna el ID del último registro insertado
-	Construido sobre create(), añadiendo capa de seguridad

Manejo de campos unique

Para ignorar errores al intentar insertar registros que violan restricciones unique:

	// Con create()
	$model->createOrIgnore($data);

	// Con insert()
	$model->insertOrIgnore($data);

La diferencia es que insertOrIgnore() opera dentro de una transacción.

Campos JSON

Para campos JSON, simplemente pase un array:

    $model->create([
		'name' => 'Product X',
		'attributes' => [
			'color' => 'red',
			'size' => 'large',
			'features' => ['waterproof', 'durable']
		]
	]);

El array será automáticamente convertido a JSON antes de la inserción.

Recomendaciones de uso

-	Use create() para inserciones simples donde no necesite transacciones
-	Use insert() para inserciones múltiples donde necesite garantía transaccional
-	Use *OrIgnore() cuando necesite silenciar errores de campos unique
-	Para campos JSON, siempre use arrays PHP nativos

Esta documentación mejorada proporciona una visión más clara de las diferencias entre los métodos y cuándo usar cada uno. 


# Obtención de registros

Antes que nada es posible usar la clase DB solo (si asi se desea) para hacer la conexion la DB y hacer el resto de forma artesanal.

Ej:

	$q = "select .....";

	$conn = DB::getConnection();
	
	$st = $conn->prepare($q);		
	
	$st->execute(); 
	$rows = $st->fetchAll();
	
	dd($rows, 'ROWS');


# Usando el metodo table()

Todos los registros (menos los marcados como borrados)

	$rows = DB::table('products')
	->get();

Para filtrar usar where()

	$rows = DB::table('products')
	->where(['size' => '2L'])
	->get();

También es válido usar el método where() con un array (campo, valor)

	$rows = DB::table('products')
	->where(['size', '2L'])
	->get();

Si bien el método where() admite userse de dos ambas dos formas no es aconsejable combinarlas:

	// ok
	$rows = DB::table('products')
	->where(['size', '2L'])
	->where(['cost', 100])
	->get();

	// ok
	$rows = DB::table('products')
	->where(['size' => '2L'])
	->where(['cost' => 100])
	->get();

	// No se recomienda (!)
	$rows = DB::table('products')
	->where(['size' => '2L'])
	->where(['cost', 100])
	->get();

La última forma es funcional pero podría fallar en algún caso.

# Comparar dos campos en el WHERE

	$m = (DB::table('users'))
	->whereColumn('firstname', 'lastname', '=');  

	dd($m->get()); 


# Forma básica de debugueo de queries

Siempre que se use el método DB::table() será posible debuguear la última query de la siguiente manera:

    dd(DB::getLog());

Ejemplo:

	$rows = (DB::table('users'))
	->whereColumn('firstname', 'lastname', '=')
	->get();  

	dd(DB::getLog()));

Hay otras formas de debugueo de consultas que se pueden encontrar más adelante en este manual.


# Ocultar y des-ocultar campos

Si existe el modelo y el schema correspondiente para una tabla se pueden suprimir campos:

	class UsersModel extends Model
	 { 	
		protected $hidden   = [	'password' ];

		// ...

En este caso password será por defecto suprimido cuando no haya un SELECT o sea cuando sea un SELECT *

Es posible ocultar o desocultar campos programáticamente siempre que se utilice select() o selectRaw() en la consulta:

	$u = DB::table('users');
    $u->unhide(['password']);
    $u->hide(['confirmed_email']);
    $u->where(['id'=>$id]);

    dd($u->get());


Si se desea des-ocultar cualquier campo oculto se dispone del método unhideAll()

	$rows = DB::table('users')
    ->crossJoin('products')
    ->where(['users.id', 90])
    ->unhideAll()
    ->get();


# Campos no rellenables 

Del mismo modo que como ocurre con los campos ocultos hay campos que son no-fillables:

	class UsersModel extends Model
	 { 	
		protected $hidden   = [	'password' ];
		protected $not_fillable = ['confirmed_email', 'is_active'];

		// ...


Igualmente hay métodos para programáticamente hacer rellenables o no-rellenables ciertos campos:

	$u = DB::table('users');
    $u->fill(['email']);
    $u->unfill(['password']);
    $id = $u->create([
    				'email'=>$email, 
    				'password'=>$password, 
    				'firstname'=>$firstname, 
    				'lastname'=>$lastname
    ]);
    
# Personalización de modelos

Los campos se pueden definir como "fillables", 
"no fillables", 
"ocultos", 
se puede enviar al frontend un "nuevos nombres" para los campos,
se puede definir el "orden de campos" en que deben ser mostrados en el frontend,
se puede modificar el "orden" -ORDER BY- (o mas de uno) por defecto para los campos,
se pueden enviar una sugerencia de "formato" para los campos en el frontend

Ej:

Dentro de un modelo:

	function __construct(bool $connect = false, $schema = null, bool $load_config = true){
        parent::__construct($connect, $schema, $load_config);

        $this->unfill([
            'status',
            // 'response',
            // 'result'           
        ]);

        $this->hide([
            'created_at',
            'updated_at',
            'deleted_at'
        ]);

        $this->field_names = array_merge($this->field_names, [
            'id'         => 'ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
            'result'     => 'Resultato',
        ]);

        /*
            Indicacion para el FronEnd

            Los campos que aparecen primero, deben mostrarse primero. Afecta a get_model_defs() y get_defs()
        */
        $this->field_order = [
            'response',
            'status',
            'result'
        ];
        
        /*
            Default sort
        */
        $this->order       = [
            $this->id() => 'DESC',
			// demas ordenamientos
        ];

        $this->formatters['response'] = 'textarea';
        $this->formatters['result']   = 'textarea';
    }

# Borrado de registros

Para eliminar un registro, establezca una condición y llame al método delete()

Ej:

	DB::table('products')
	->find(145)
	->delete();

Lo anterior equivaldría (asumiendo que el id se llama 'id') a lo siguiente:

	DB::table('products')
	->where['id' => 145]
	->delete();

También la condición podría estar dada por un whereRaw()

Nota importante:

El metodo find() requiere del Schema para determinar el "id" (clave primaria) de la tabla y este solo es utilizado con DB::table() pero no con el helper table()

// OK
DB::table('products')
->find(145)
->delete();

// Funcionara solo si la clave primaria se llama "id"
table('products')
->find(145)
->delete();

// OK
table('products')
->where['id' => 145]
->delete();


# Borrado "suave" o soft-delete

Métodos relacionados:

	withTrashed(bool $soft_delete)	borra un registro. Hay parámetros adicionales.
	trashed()       				devuelve si el registro fue borrado suavemente.
    undelete()      				restaura el registro si fue borrado suavemente.
    forceDelete()   				borra un registro definitvamente.
    withTrashed()   				incorpora a la selección registros borrados suavemente.
    onlyTrashed()   				selecciona solo registros borrados suavemente.

Si una tabla y su schema dispone de un campo "deleted_at" de tipo DATETIME, al hacer un delete() el registro no es borrado fisicamente sino solo se oculta de los resultados.

Ej:

	DB::table('products')
	->where(['id' => $id])
	->get());

	// SELECT * FROM products WHERE id = $id AND deleted_at IS NULL

En caso de querer des-ocultar registros borrados para que sean visualizados en una consulta usar deleted() o withTrashed()

	DB::table('products')
	->where(['id' => $id])
	->deleted()
	->get());

	// SELECT * FROM products WHERE id = $id 

Por el contrario si desea mostrar solo registros que fueron borrados de forma suave use withTrashed()

Ej:

	DB::table('products')
	->where(['id' => $id])
	->withTrashed()
	->get());


# Borrar un registro de forma definitiva 

Si se desea que al momento de borrar el campo se elimine de forma definitiva puede usar setSoftDelete(false)

	$u = DB::table('users')
	->find($id)
	->setSoftDelete(false)
	->delete();  

Por defecto SimpleRest solo selecciona registros que no estén borrados ya sea físicamente o virtualmente con "borrado suave" por concistencia.

Nota: el campo de tipo DATETIME requerido para borrar registros no tiene porque llamarse "deteled_at" y ese nombre puede setearse en el modelo mediante el atributo $deletedAt.

También puede ocupar el método forceDelete() que borra un registro de forma definitiva haya o no sido previamente borrado suavemente.

Ej:

	DB::table('products');
	->find(5510)
	->forceDelete();

O..

	DB::table('products');
	->where($some_condition)
	->forceDelete();


# Chequear si un registro fue borrado "suavemente"

El método trashed() devuelve un boolean indicando si el registro fue borrado con softdelete o no pero fallará arrojando excepción si el softdelete no está habilitado.

	$trashed = DB::table('products')
	->find(145)
	->trashed());


# Restaurar un registro fue borrado "suavemente"

El método undelete() restaura un registro borrado con softdelete pero fallará arrojando excepción si el softdelete no está habilitado. 

Este método no chequea si realmente el registro fue borrado aunque sino existe tampoco generará excepción. En caso de querer verficar previamente si el registro fue borrado utilice trashed()


# Obtener el nombre del Id de la tabla

SimpleRest considera 'id' al nombre de la PRIMARY KEY (si es simple), del campo AUTOINCREMENT o bien del campo UUID de la tabla. Esto permite compatibilidad con diseños donde las tablas tienen claves primarias compuestas siempre que exista un campo que pueda representar unívocamente a cada registro.

Utilice el método id() para obtener el nombre del id correspondiente.

Ej:

	$id_name = DB::table('super_cool_table')->id();


# Joins

Un INNER JOIN se hace de la siguiente manera:

	$instance->join('table2', 'table2.id', '=',  'table1._id');

El operador puede ser =, >, <, >= o <=

Ej:

	$m = DB::table('users')
	->join('user_sp_permissions', 'users.id', '=',  'user_sp_permissions.user_id')
	->join('sp_permissions', 'sp_permissions.id', '=', 'user_sp_permissions.id')

	->select(['sp_permissions.name as perm', 'username', 'is_active']);

	dd($m->get()); 
	dd($m->dd()); 
     

Los joins pueden simplificarse como auto-joins de haber un schema para el modelo:

	$m = DB::table('users')
	//->join('user_sp_permissions');
	->join('sp_permissions');

	$m->select(['sp_permissions.name as perm', 'username', 'is_active']);

	dd($m->get()); 
	dd($m->dd()); 

Es importante notar que *no* debe hacerse el JOIN() *explícito* con la tabla puente y la table relacionada
por esta porque en tal caso la relación con la tabla puente quedaría duplicada. Si se incluyera 
se generaría para el caso de MySQL un error como

	SQLSTATE[42000]: Syntax error or access violation: 1066 Not unique table/alias: 'user_sp_permissions'

Si hay una tabla puente, la relación debe hacerse con la table del otro lado del puente y no incluir a la tabla puente en cuestión.


# Left y Right joins

Ej:

	$users = DB::table('users')->select([
	    "users.id",
	    "users.name",
	    "users.email",
	    "countries.name as country_name"
	])
	->leftJoin("countries", "countries.id", "=", "users.country_id")
	->get();

# Cross y natural joins

	$rows = DB::table('users')
    ->crossJoin('products')
    ->where(['users.id', 90])
    ->unhideAll()
    ->deleted()
    ->get();

	$rows = (new Model())->table('employee')
    ->naturalJoin('department')
    ->unhideAll()
    ->deleted()
    ->get();


# Alias (as)

Es posible declarar alias tanto para la table principal como para las tablas a ser unidas por join.

Ej1)

	DB::getConnection('az');

	$rows = DB::table('users', 'u')
	->join('products')
	->join('roles')
	->unhideAll()
	->deleted()
	//->dontExec()
	->get();
	
	dd($rows);
	dd(DB::getLog());  

Ej2)

	DB::getConnection('az');

	$rows = DB::table('users', 'u')
	->join('products as p')
	->join('roles as r')
	->unhideAll()
	->deleted()
	//->dontExec()
	->get();
	
	dd($rows);
	dd(DB::getLog());   

El SQL generado en este caso sería algo como:

	SELECT * FROM users as u 
	INNER JOIN products as p ON u.id=p.belongs_to 
	INNER JOIN user_roles ON u.id=user_roles.user_id 
	INNER JOIN roles as r ON r.id=user_roles.role_id;

Cabe notar que no es posible especificar el alias para la tabla puente del ejemplo (`user_role`)

Tampoco es posible explicitar el "alias" (con 'as') cuando haya se haga un join hacia una tabla desde la cual existe más una relación con la primera. 

Ej:

	$rows = DB::table('users', 'u')
    ->join('products as p')
	->get();

Posible salida:

	SELECT * 
	FROM users as u 
	INNER JOIN products as p ON p.belongs_to = u.id 

En el caso anterior no habría problema si products tiene dos FKs (`belongs_to` y `deleted_by`) para users pero el "problema" se presenta si la relación se hace al revés:

	$rows = DB::table('products', 'p')
    ->join('users as u')
	->get();

Posible salida:

	SELECT * 
	FROM products as p 
	INNER JOIN users as __belongs_to ON __belongs_to.id = p.belongs_to 
	INNER JOIN users as __deleted_by ON __deleted_by.id = p.deleted_by

El "problema" es que cuando hay más de una relación entre dos tablas se necesita un alias por cada relación y con "as" solo es posible especificar una por lo cual la ambiguedad la resuelve automáticamente el framework.


SubRecursos

# Recuperar registros de tablas relacionadas

Con el metodo connectTo() combinado con metodos como get() que hacen un SELECT, se devuelven registros de tablas relacionadas devolviendo los datos de forma estructurada.

Ej:

	$rows = DB::table('courses')
		->where(['title', 'Calculus I'])            
		->connectTo(['categories', 'users', 'tags']) 
		->get();

Salida:

	Array
	(
		[0] => Array
			(
				[id] => 6
				[title] => Calculus I
				[active] => 1
				[category_id] => 2
				[professor_id] => 2
				[created_at] => 2025-02-23 20:57:29
				[updated_at] => 2025-02-23 20:57:29
				[category] => Array
					(
						[id] => 2
						[name] => Mathematics
						[created_at] => 2025-02-23 20:34:08.000000
						[updated_at] => 2025-02-23 20:34:08.000000
					)

				[professor] => Array
					(
						[0] => Array
							(
								[id] => 2
								[name] => Bob Smith
								[role] => professor
								[email] => bob@example.com
								[created_at] => 2025-02-23 20:34:08.000000
								[updated_at] => 2025-02-23 20:34:08.000000
							)

					)

				[users] => Array
					(
						[0] => Array
							(
								[id] => 4
								[name] => Diana White
								[role] => student
								[email] => diana@example.com
								[created_at] => 2025-02-23 20:34:08.000000
								[updated_at] => 2025-02-23 20:34:08.000000
							)

						[1] => Array
							(
								[id] => 5
								[name] => Ethan Green
								[role] => student
								[email] => ethan@example.com
								[created_at] => 2025-02-23 20:34:08.000000
								[updated_at] => 2025-02-23 20:34:08.000000
							)

					)

				[tags] => Array
					(
						[0] => Array
							(
								[id] => 2
								[name] => Mathematics
								[created_at] => 2025-02-23 23:30:31.000000
								[updated_at] => 2025-02-23 23:30:31.000000
							)

						[1] => Array
							(
								[id] => 3
								[name] => Physics
								[created_at] => 2025-02-23 23:30:31.000000
								[updated_at] => 2025-02-23 23:30:31.000000
							)
					)
			)
	)


Activación automática

La cualificación de campos se habilita automáticamente al usar el método connectTo(), pero también puedes activarla manualmente:

	DB::table('courses')
	->qualify()  // Habilitar manualmente la cualificación
	->where(['professor.name', 'Bob Smith'])
	->get();

O desactivarla:

DB::table('courses')
   ->dontQualify()  // Deshabilitar cualificación
   ->where({condicion});


Cualificación en WHERE sobre tablas relacionadas con connectTo()

Lo ideal en estos casos es cualificar el campo utilizado como filtro con el nombre de la tabla ya que varias tablas podrian repetir el nombre de los campos.

Ej:

 	DB::getConnection('edu');

	$m = DB::table('courses');

	$rows = $m
	->connectTo(['categories', 'users', 'tags'])
	->where(['categories.name', 'Mathematics'])
	->where(['users.name', 'Bob Smith'])
	->where(['users.role', 'professor'])	
	->get();

	dd(
		$rows
	);


Diferencias entre connectTo() y join()

Cuando se realiza con JOIN con join() se tiene control total sobre como se hace el "join". En automatico join() realiza un INNER JOIN.

Ademas de join() se puede usar joinTo() para unir varias tablas de forma conjunta con una sintaxis equivalente a la de connectTo() aunque el resultado no es igual.

Ej:

	DB::getConnection('edu');

	$rows = DB::table('courses')
	->where(['title', 'Calculus I'])            
	->joinTo(['categories', 'users', 'tags']) 
	->get();

producira un resultado "aplanado" similar a:

	Array
	(
		[0] => Array
			(
				[id] => 2
				[title] => Calculus I
				[active] => 1
				[category_id] => 2
				[professor_id] => 2
				[created_at] => 2025-02-23 23:30:31
				[updated_at] => 2025-02-23 23:30:31
				[name] => Mathematics
				[email] => bob@example.com
				[role] => professor
				[course_id] => 6
				[tag_id] => 2
			)

		[1] => Array
			(
				[id] => 3
				[title] => Calculus Ie
				[active] => 1
				[category_id] => 2
				[professor_id] => 2
				[created_at] => 2025-02-23 23:30:31
				[updated_at] => 2025-02-23 23:30:31
				[name] => Physics
				[email] => bob@example.com
				[role] => professor
				[course_id] => 6
				[tag_id] => 3
			)

	)


Nota:

Con connectTo() son soportados los metodos get() y first()


Método findTableByAlias()

Este método analiza las relaciones definidas en el esquema para determinar a qué tabla real corresponde un alias. 

Por ejemplo, si tienes un campo professor_id en la tabla `courses` que referencia a users.id, el alias 'professor' se mapeará a la tabla 'users'.

	/**
	* Encuentra la tabla real correspondiente a un alias derivado
	* 
	* @param string $alias El alias a buscar
	* @return string|null La tabla correspondiente o null si no se encuentra
	*/
	public function findTableByAlias($alias)


### Referencia de métodos de la clase Model

La clase Model responsable del Query Builder tiene una gran cantidad de métodos que proveen las distintas funcionalidades.

# Paginacion

El framework ofrece varios metodos de paginacion:

	offset / limit 
	take / skip  -- similar al anterior
	paginate

Con paginate() es directamente pasando la cantidad de paginas y el tamano de pagina.

Ej:

	$page_size = $_GET['size'] ?? 10;
	$page      = $_GET['page'] ?? 1;

	DB::getConnection('az');

	$rows = DB::table('products')
	->paginate($page, $page_size)
	->get();

Por otro lado la clase Paginator se encarga de generar el SQL para el modelo y ofrece metodos de calculo de paginacion.

En si, paginate() es equivalente a llamar a take() y offset()

	$rows = DB::table('products')
	->take($page_size)
	->offset($offset)
	->get();

pero ahora necesitamos saber cuando vale offset.

Existe otra forma de paginación "más a bajo nivel" que es manipulando directamente la clase Paginator.

Ej:

	header('Content-Type: application/json; charset=utf-8');

	$page_size = $_GET['size'] ?? 10;
	$page      = $_GET['page'] ?? 1;

	$offset = Paginator::calcOffset($page, $page_size);

	DB::getConnection('az');

	$rows = DB::table('products')
	->take($page_size)
	->offset($offset)
	->get();

	$row_count = DB::table('products')->count();

	$paginator = Paginator::calc($page, $page_size, $row_count);
	$last_page = $paginator['totalPages'];

	return [
		"last_page" => $last_page, 
		"data" => $rows
	];


Veamos un ejemplo completo:

Renderizaremos una tabla con su paginador 100% funcional en pocas lineas.

	function table(Array $data)
    {
        css_file('third_party/bootstrap/5.x/bootstrap.min.css');
        js_file('third_party/bootstrap/5.x/bootstrap.bundle.min.js');

        ?>
        <div class="container mt-5">
            <h2>Reviews</h2>

            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Comentario</th>
                        <th scope="col">Puntaje</th>
                        <th scope="col">Cliente</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['rows'] as $row) : ?>
                        <tr>
                            <td><?php echo $row['comment']; ?></td>
                            <td><?php echo $row['score']; ?></td>
                            <td><?php echo $row['author']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Paginador -->
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $data['paginator']['last_page']; $i++) : 
                        $page_link = Url::addQueryParam(Url::currentUrl(), 'page', $i);
                    ?>
                        <li class="page-item"><a class="page-link" href="<?= $page_link ?>"><?= $i ?></a></li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>

    <?php
    }


# Value

Ej:

	DB::table('products')->where([
		['cost', 5000, '>=']
	])
	->value('name');

# Casting con value()

Ej:

	$city = 'Santiago';
        
	$gmt = DB::table('timezones')
	->where([
		'city' => $city
	])
	->value('gmt', 'float');

	dd($gmt, 'GMT');

# Los where

Los arrays de los where pueden ser asociativos:

	$facturas = DB::table('facturas')
	->where(['created_by' => 401])
	->get();

O no-asociativos:

	$facturas = DB::table('facturas')
	->where(['created_by', 401])
	->get();

La ventaja de los no-asociativos es que permiten especificar un operador como tercer parámetro:

	DB::table('products')->where([ 
            ['cost', 200, '>=']
    ])->get();

Pueden haber varios WHERE en cuyo caso se hace un "AND WHERE"

	$rows = DB::table('products')
	->where(['size', '2L'])
	->where(['cost', 100, '<'])
	->get();

No se aconseja para nada mezclar arrays WHEREs con arrays asociativos y no-asociativos.

# whereNot

Efectua un WHERE {campo} != {valor}

Ej:

	$pids = DB::table('background_process')
	->whereNot('process', 'worker')
	->pluck('pid');

# whereNull / whereNotNull

	$rows = DB::table('products')
	->whereNull('workspace')
	->get();

	$rows = DB::table('products')
	->whereNotNull('workspace')
	->get();

# whereIn / whereNotIn

Es posible hacer un WHERE IN( array ) y un WHERE NOT IN ( array ) con whereIn() y whereNotIn() respectivamente.

	$rows = DB::table('products')
    ->whereIn('size', ['0.5L', '3L'])
	->get());

	$rows = DB::table('products')
    ->whereNotIn('size', ['0.5L', '3L'])
	->get());


# whereBetween / whereNotBetween

	$rows = DB::table('products')
	->select(['name', 'cost'])
	->whereBetween('cost', [100, 250])
	->get());

	$rows = DB::table('products')
	->select(['name', 'cost'])
	->whereNotBetween('cost', [100, 250])
	->get());

# whereOr

Se puede realizar un "WHERE OR" o sea hacer un OR dentro de cada sub-where

Ej:

	$rows = DB::table('products')
	->where(['belongs_to', 90])
	->whereOr([ 
		['name', ['CocaCola', 'PesiLoca']], 
		['cost', 550, '>='],
		['cost', 100, '<']
	])
	->get();

O bien,...

	DB::table('products')->deleted()
	->where(['belongs_to', 90])
	->where([                         
		['name', ['CocaCola', 'PesiLoca']],
		['cost', 550, '>='],
		['cost', 100, '<']
	], 'OR')
	->whereNotNull('description');

Resultando en el SQL

	SELECT  name, cost, id FROM products WHERE 
	belongs_to = '90' AND 
	(
		name IN ('CocaCola', 'PesiLoca') OR 
		cost >= 550 OR 
		cost < 100
	) AND 
	description IS NOT NULL

# orWhere

Tambien se puede efectuar un "OR WHERE" o sea un ...OR ( WHERE($cond) ) 

Ej:

	$rows = DB::table('users')
	->where([ 'email'=> $email ]) 
	->orWhere(['username' => $username ])

# whereDate

Este tipo de where busca tanto en campos de tipo date como datetime.

Ej:

	$facturas = DB::table('facturas')
	->whereDate('created_at', '2021-12-29')
	->get();

Puede especificarse un operador =, > o <

Ej:

	$facturas = DB::table('facturas')
	->whereDate('created_at', '2021-12-29', '>')
	->get();


# group

Se explica en detalle más adelante pero group() permite agrupar condiciones permitiendo crear consultas muy complejas.

Ej:

	$rows = DB::table('products')

	->where([
		['cost', 100, '>'], // AND
		['id', 50, '<']
	]) 

	// AND
	->whereRaw('name LIKE ?', ['%a%'])
	
	// AND
	->group(function($q){  
		$q->where(['is_active', 1])
		// OR
			->orWhere([
			['cost', 100, '<='], 
			['description', NULL, 'IS NOT']
		]);  
	})
	
	// AND
	->where(['belongs_to', 150, '>'])

	->select(['id', 'cost', 'size', 'description', 'belongs_to'])
	->get();


Nota: puede reemplazar group({callback}) por where({callback}) y seguira funcionando dada azucar sintactica que forma parte de la compatibilidad con Laravel query builder.

# not

El operador not() niega todo un grupo de condiciones. Ej:

	$rows = DB::table('products')
	->where(['belongs_to', 150, '>'])
	->not(function($q) {
		$q->whereRegEx('name', 'a$')
		->or(function($q){ 
			$q->where([
				['cost', 100, '<='],
				['description', NULL, 'IS NOT']
			]);
		});             
	})
	->where(['size', '1L', '>='])
	->get();


# whereLike

Siempre teniendo en cuenta que LIKE es muy ineficiente,... tenemos whereLike()

Ej:

	$m = (new Model())
	->table('products')
	->whereLike('name', '%a%')
	->select(['id', 'name']);

	dd($m->get());
	var_dump($m->dd());


# whereRegEx / whereNotRegEx

Es posible usar expresiones regulares siempre claro que el motor de base de datos las soporte.

Ej:
	$rows = DB::table('products')
	->whereRegEx('name', 'Coke')
	->or(function($q){
		$q->where(['cost', 100, '<=']);
	})
	->get();     

	$rows = DB::table('products')
    ->whereNotRegEx('name', 'Coke')
	->get();

# whereRaw

Ej:

	$rows = DB::table('products')
	->where(['belongs_to' => 90])
	->whereRaw('cost < IF(size = "1L", ?, 100) AND size = ?', [300, '1L'])
	->orderBy(['cost' => 'ASC'])
	->get();

# orWhereRaw

Ej:

	$rows = DB::table('products', 'p')
	->where([
		['cost', 50, '>'], // AND
		['id', 190, '<=']
	]) 
	// AND
	->group(function($q){  
		$q->where(['is_active', 1])
		// OR
		->orWhereRaw('name LIKE ?', ['%a%']);  
	})
	// AND
	->where(['belongs_to', 1, '>'])
	
	->select(['id', 'name', 'cost', 'size', 'description', 'belongs_to'])
	->get();


# whereExists

Si se desea hacer un "WHERE EXISTS" utilice whereExists()

Ej:

	$rows = DB::table('products')
    ->whereExists('(SELECT 1 FROM users WHERE products.belongs_to = users.id AND users.lastname = ?)', ['AB']);

Lo anterior generá un SQL como:

	SELECT * FROM products WHERE EXISTS (SELECT 1 FROM users WHERE products.belongs_to = users.id AND users.lastname = 'AB');


# selectRaw

Por lo general cuando se desea usar un alias a un campo es necesario hacer algo como

	$vals = DB::table('products')
	->setFetchMode('COLUMN')
	->selectRaw('cost * 1.05 as cost_after_inc')->get();

O lo mismo pero con parámetros 

	$vals = DB::table('products')
	->setFetchMode('COLUMN')
	->selectRaw('cost * ? as cost_after_inc', [1.05])->get();

# havingRaw

En general las funciones raw permiten corregir situaciones donde es muy difícil expresar con el query builder exactamente lo que queremos o bien este falla en construir algo con la sintáxis correcta para el motor de base de datos que estamos utilizando.

Ej:

	$rows = DB::table('products')
	->deleted()
	->groupBy(['name'])
	->having(['c', 3, '>'])
	->select(['name'])
	->selectRaw('COUNT(*) as c')
	->get();

Podría ser traducido a SQL como:

	SELECT 
	COUNT(*) as c, 
	name 
	FROM 
	products 
	GROUP BY 
	name 
	HAVING 
	products.c > 3;

Lo cual no es totalmente válido y en MySQL genera el error:

	Uncaught PDOException: SQLSTATE[42S22]: 
	Column not found: 1054 Unknown column 'products.c' in 'having clause'

La solución en este caso viene de la mano de havingRaw()

	$rows = DB::table('products')
	->deleted()
	->groupBy(['name'])
	->select(['name'])
	->selectRaw('COUNT(*) as c')
	->havingRaw('c > ?', [3])
	->get();

Generando esta vez un SQL correcto:

	SELECT 
	COUNT(*) as c, 
	name 
	FROM 
	products 
	GROUP BY 
	name 
	HAVING 
	c > 3;


Hay situaciones que son para usar havingRaw() pero el Query Builder hace la conversión internamente de having() a havingRaw() aunque tiene un costo en rendimiento y se pierde cierto control de la Query formada.

Ej:

	DB::table('products')
	->select(['size'])
	->selectRaw('AVG(cost)')
	->groupBy(['size'])
	->having(['AVG(cost)', 150, '>='])
	->get();

En este caso el primer parámetro no es un campo sino que hay una función aplicada sobre un campo e internamente se ejecutará como:

	DB::table('products')
	->select(['size'])
	->selectRaw('AVG(cost)')
	->groupBy(['size'])
	->havingRaw('AVG(cost) >= ?', [150])
	->get();

Es posible forzar el uso de havingRaw() cuando sea necesario con setStrictModeHaving(true)

Ej:

	DB::table('products')		
	->setStrictModeHaving(true)
	->select(['size'])
	->selectRaw('AVG(cost)')
	->groupBy(['size'])
	->having(['AVG(cost)', 150, '>='])
	->get();

Ya con el modo estricto activado se generará una excepción:

	PHP Fatal error:  Uncaught Exception: Use havingRaw() instead for AVG(cost) >= ?


# when

El método when() simplifica la creación de queries condicionales y generalmente se usa en conjunto con where() o sus variantes.

Veamos un snipet extraido del propio core del framework escrito sin when() y luego con when() 

-- sin when()

	$m = DB::table('migrations');

	if ($to_db == '__NULL__'){
		$m->whereNull('db');
	} else {
		$m->where(['db' => $to_db]);
	}
			
	$filenames = $m->orderBy(['created_at' => 'DESC'])
	->pluck('filename');

El método when() tiene tres parámetos: la condición y dos callbacks de los cuales el primero se aplica si la condición es verdadera y la segunda si es falsa.

-- con when()

	$affected = DB::table('migrations')
	->when($to_db != DB::getDefaultConnectionId(), function($q) use($to_db){
		$q->where(['db' => $to_db]);
	},function($q){
		$q->whereRaw('1');
	})
	->delete();


# Grupos

Para colocar paréntesis en el WHERE del SQL resultante usando el Query Builder existen los "grupos" implementados con el método group()

Ej:

	DB::table('xxxx')
	->group(function($q){
		$q->where(condA)
		->orWhere(condB)
	})
	->where(condC);


Lo anterior en el WHERE de arma algo como

	WHERE (A OR B) AND C

Veamos otro ejemplo:

	DB::table('xxxx')
	->where(condC)
	->group(function($q){
		$q->where(condA)
		->orWhere(condB)
	});

Lo anterior equivale a tener en el WHERE algo como

	WHERE C AND (A OR B)


La diferencia es que en último caso se está suponiendo que el operador que queremos usar para conectar el grupo con la primera condición (C) es un 'AND' pero podría no ser el caso por lo que se dispone de grupos especiales llamados "conectores" donde se explicita el operador de la conjunción:

Conectores:

	and()
	or()
	andNot()
	orNot()

Ej:
	A OR (B AND C)


En pseudo-código (ver como se usa la función where) sería algo así:	

	DB::table('xxxx')
	->where(condA)
	->or(function($q){
		$q->where(condB)
		->where(condC);
	})

o ..

	DB::table('xxxx')
	->where(condA)
	->or(function($q){
		$q->where([
			condB,
			condC
		]);
	})


Es importante tener en cuenta que se abren paréntesis hacia el lado derecho solamente con lo cual,

	DB::table('xxxx')
	->where(condA)
	->where(condB)
	->or(function($q){
		$q->where([
			condC
		]);
	})


Genera en SQL una expresión del tipo "A AND B OR C" y *no* "(A AND B) OR C". En caso de estar buscándo lo anterior podría hacerse de la siguiente manera:

	DB::table('xxxx')
	->where([
			condA,
			condB
	])
	->or(function($q){
		$q->where([
			condC
		]);
	})

Otra opción, más general, es usar un grupo también para "agrupar" las condiciones A y B

	DB::table('xxxx')
	->group(function($q){
		$q->where(condA);
		$q->where(condB);
	})
	->or(function($q){
		$q->where(
			condC
			);
	})


Ejemplo funcional de operadores OR / AND anidados en WHERE con group()

	$m = DB::table('products')

	->group(function($q){  
		$q->where([
			['cost', 100, '>'],
			['id', 50, '<']
		]) 
		// OR
		->orWhere([
			['cost', 100, '<='],
			['description', NULL, 'IS NOT']
		]);  
	})
	// AND
	->where(['belongs_to', 150, '>'])
	
	->select(['id', 'cost', 'size', 'description', 'belongs_to']);

	dd($m->get()); 


Los grupos / conectores aplican también a having() y havingRaw()

Ej:

	DB::table('products')->deleted()

	->groupBy(['cost', 'size', 'belongs_to'])
	->having(['cost', 100, '>='])
	->or(function($q){
		$q->havingRaw('SUM(cost) > ?', [500])
		->having(['size' => '1L']);
	})
	->orderBy(['size' => 'DESC'])
	->get(['cost', 'size', 'belongs_to']); 


# Restricciones que aplican para having() y havingRaw()

Puede -según sea el caso- que having() y havingRaw() no sean combinables en la misma query y que de ser necesario el uso de havingRaw() entonces todo lo que corresponda a HAVING deba realizarse con *unico* havingRaw().


# Qualificación de campos

Por defecto todos los campos son auto-qualificados como tabla.campo o si hay un alias para la tabla como alias.campo en la query construida. Esto obviamente tiene cierto impacto de performance pero es el comportamiento por defecto dado que evita posibles colisiones de nombres cuando hay JOINs.

Puede desactivarse la auto-qualificación con el método dontQualify()

Ej:

	$m = DB::table('products')
	->groupBy(['cost', 'size'])
	->having(['cost', 100])
	->select(['cost', 'size']);

	dd($m->dd());  

Produce:

	SELECT 
	products.cost, 
	products.size 
	FROM 
	products 
	WHERE 
	products.deleted_at IS NULL 
	GROUP BY 
	products.cost, 
	products.size 
	HAVING 
	products.cost = 100;

Mientras que,

	$m = DB::table('products')
	->dontQualify()
	->groupBy(['cost', 'size'])
	->having(['cost', 100])
	->select(['cost', 'size']);

	dd($m->dd());  

Produce:

	SELECT 
	products.cost, 
	products.size 
	FROM 
	products 
	WHERE 
	products.deleted_at IS NULL 
	GROUP BY 
	products.cost, 
	products.size 
	HAVING 
	products.cost = 100

Para cambiar el flag se hace con el método doQualify()


# Wrapping de campos con comillas / apostrofes 

Por defecto no se envuelven entre comillas a los campos y esto puede ser critico para campos que son palabras reservadas como es el caso de "KEY" para MYSQL.

Para cambiar el flag se hace con el método wrap()

Ej:

	$res = table('managed_cache')        
	->select(['key', 'created_at'])
	->where('key', 'test_key')
	->dd();

	dd($res);

Imprime:

	SELECT key,created_at FROM `wp_managed_cache` WHERE key = 'test_key'

Mientras que,...

	$res = table('managed_cache')        
	->select(['key', 'created_at'])
	->where('key', 'test_key')
	->dd();

	dd($res);

Imprime:

	SELECT `key`,`created_at` FROM `wp_managed_cache` WHERE `key` = 'test_key'


# Update

El update se realiza con el método update() que recibe como parámetro un array asociativo con los campos a modificar:

Ej:
	$affected_rows = DB::table('users')
	->where([
		'firstname' => 'HHH', 
		'lastname' => 'AAA'
	])
	->update([
		'firstname'=>'Nico', 
		'lastname'=>'Buzzi'
	]);
    

Es posible solo "tocar" la fecha de actualización de un registro sin modificar nada más con el método touch()

Ej:

	DB::table('products')
	->find(145)
	->touch();


# Scopes

En Simplerest crear lo que otros frameworks llaman "scopes" sobre las consultas es demasiado sencillo: 

Simplemente vaya al modelo en cuestión y agregue los metodos que requiera. Ej:

	class ProductsModel extends MyModel
	{ 	
		// ...

		function costScope(){
			$this->where(['cost', 100, '>=']);
			return $this;
		}

		// ...
	}

Luego es solo llamar el método que aplica la restricción. 

Ej:

	DB::getConnection('az');  

    dd(
		DB::table('products')
		->where(['id', 200, '>'])

		/*
			Agrego el o los scopes que desee 
		*/	
		->costScope()

		->count(),
		'SCOPE costScope'
    );


### La clase DB

La clase DB es una librería clave cuyo rol principal es manejar las conexiones de base de datos y ofrecer información sobre las mismas. Posee además un mini Query Builder para consultas "raw".


# Obtención de información de drivers

Método DB::driver()							devuelve driver de la conexión actual
Método DB::driverVersion(bool $numeric)		devuelve la versión del driver			
Método DB::isMariaDB()						devuelve si es MariaDB

Ej:

	dd(DB::driver(), 'Driver');
	dd(DB::driverVersion(), 'Driver version');
	dd(DB::driverVersion(true), 'Driver version (num)');
	dd(DB::isMariaDB(), 'Is MariaDB');

El resultado será algo como:

	--[ Driver ]-- 
	mysql

	--[ Driver version ]-- 
	5.7.35-0ubuntu0.18.04.2

	--[ Driver version (num) ]-- 
	5.7.35

	--[ Is MariaDB ]-- 
	false


### Ejecución de "consultas crudas"

Las consultas puramente crudas son aquellas que son un simple string en SQL que pueden contener los "?" para los parámetros en caso de que las consultas o sentencias sean preparadas. 

# select

Se dispone del método DB::select()()

Ej:

	$res = DB::select('SELECT * FROM products');

O pasando parámetros:

	$res = DB::select('SELECT * FROM products WHERE cost > ? AND size = ?', [550, '1 mm']);

Un ejemplo completo donde se genera una query cruda y luego se ejecuta:

	$m = DB::table('products')
	->dontBind()    
	->dontExec()    
	->select(['size', 'cost'])
	->groupBy(['size'])        
	->having(['cost', null, '>='])
	->having(['size' => null]);

	$sql = $m->toSql();

	dd(DB::select($sql, [5, '1L']));
	dd($sql, 'pre-compiled SQL');
	dd(DB::getLog(), 'Excecuted SQL');

# insert

Para la inserción de registros de forma "cruda" existe el método DB::insert()

Ej:

	$id = DB::insert('insert into baz (id_baz, name, cost) values (?, ?, ?)', [100, 'cool thing', '16.25']);
    dd(DB::getLog(), 'Excecuted SQL');

Ej:

	$id = DB::insert('insert ignore into `baz2` (id_baz2, name, cost) values (?, ?, ?)', [5000, 'cool thing', '16.25']);
	dd(DB::getLog(), 'Excecuted SQL');

La función intenta encontrar la PRIMARY KEY de la tabla para así devolver el 'id' y a tal fin revisa si existe un archivo de schema creado y sino lo encuentra intenta con 'id' como nombre para la PRIMARY KEY.


# update

Para la actualización en crudo de datos se dispone del método DB::update()

Ej:

	$affected_rows = DB::update('update `baz2` SET name = ?, cost = ? WHERE id_baz2 = ?', ['something', '99.99', 5000]);


# delete

Similarmente se dispone del método DB::delete()

Ej:

	$affected_rows = DB::delete('DELETE FROM `baz2` WHERE id_baz2 = ?', [5000]);


# statement

Finalmente existe un método genérico para ejecutar "raw" statements o sea, comandos SQL que no son de tipo consulta o sea distintos de select.

Ej:

	$affected_rows = DB::statement('DELETE FROM `baz2` WHERE id_baz2 = ?', [5000]);


Nota: 

Las funciones "raw" de la clase DB admiten un parámetro para el tenant_id y en caso de tener que cambiar la conexión al finalizar la conexión original es restaurada.

Ej:

	dd(DB::getCurrentConnectionId());

	$res = DB::select('SELECT * FROM my_table', [], null, 'conn_2');

	dd(DB::getCurrentConnectionId());


Nota:

En el archivo config/databases.php hay varias definiciones que son inyectadas en la configuración y entre ellas el prefijo de las tablas. En WordPress por ejemplo por defecto es "wp_"

Se puede definir un prefijo de tablas distinto para cada "conexión"

Ej:

	<?php

	return 
    
		'db_connections' => 
		
		[
			// ...

			'woo3' => [
				'host'		=> env('DB_HOST_WOO3', '127.0.0.1'),
				'port'		=> env('DB_PORT_WOO3'),
				'driver' 	=> env('DB_CONNECTION_WOO3'),
				'db_name' 	=> env('DB_NAME_WOO3'),
				'user'		=> env('DB_USERNAME_WOO3'), 
				'pass'		=> env('DB_PASSWORD_WOO3'),
				'charset'	=> env('DB_CHARSET_WOO3', 'utf8'),
				'schema'	=> null,  
				'pdo_options' => [
					\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
					\PDO::ATTR_EMULATE_PREPARES => true 
				],
				'tb_prefix'  => 'wp_',  // <------------------------------- aqui
			],

			// ..
		]


	Entonces,...

	DB::getConnection('woo3');

	// wp_
	dd(
		tb_prefix(), 'PREFIX'
	);

	$rows = table('users')
		->orderBy(['ID' => 'DESC'])
		->first();

	dd($rows);


Notas:

El prefijo puede usarse para hacer queries como en el ejemplo previo.

El metodo DB::statement() intenta agregar el prefijo a las tablas para CREATE TABLE, ALTER TABLE, INSERT INTO, etc lo cual puede ser util en algunos casos. 

Sino se desea que se agregue nada simplemente deje 'tb_prefix' en null, false o como candena vacia ('').

Si se utiliza DB::statement() en migraciones se agregará de prefijo. 

Ej:

	php com migrations migrate --to=woo3 --dir=woo3


# Procedimientos almacenados

Para la ejecución de procedimientos almacenados se puede sacar ventaja de los métodos "raw" de la clase DB, en particular:

DB::statement()         para ejecución de sentencias que no devuelven resultado
DB::select()            para la ejecución de sentencias que devuelven resultado
DB::safeSelect() 		similar a select() pero optimizado para SP donde se haga un fetchAll

En los siguientes ejemplos se usará la keyword "CALL" presente en MySQL pero se entiende que deba usarse la equivalente según el RDBMS donde en SQLSRV por ejemplo es "EXEC".

Ej:

    DB::statement("CALL insertEvent('?')", ['2012.01.01 12:12:12']);

Y para el caso de un DB::select()

Ej:

    $price = DB::select('CALL productpricing()');

O si contuviera algún parámetro como por ejemplo el id de categoría:

Ej:

    $price = DB::select('CALL productpricing(?)', [34]);

Además es posible especificar el "modo" en el que se traen los resultados ("ASSOC", "NUM", ...) que se corresponden a los de PDO:

Finalmente tanto DB::statement() como DB::select() permiten pasar el id de la conexión en el último parámetro

y con safeSelect()

Ej:

	$s = 'TX';

	try {
		$conn = DB::getConnection('parts');

		dd(
			DB::safeSelect("CALL partFinder(?)", [$s])
		);

	} catch (\Exception $e) {
		dd("Error: " . $e->getMessage());
	}

Tambien es posible recuperar parametros de salida (los que llevan la keyword OUT) de un Store Procedure ejecutando codigo adicional.

Ej:

	$s = 'X';
	$offset = 2;
	$limit  = 2; 

	try {
		$conn = DB::getConnection('parts');
		$rows =	DB::safeSelect("CALL partFinder(?, ?, ?, @rowCount)", [$s, $offset, $limit], 'ASSOC', null, $stmt);

		// Obtener el valor del parámetro de salida
		$stmt   = $conn->query("SELECT @rowCount as rowCount");
		$result = $stmt->fetch(\PDO::FETCH_ASSOC);

		// Recuperar el valor de rowCount
		$row_count = $result['rowCount'];

		// Cerrar cursor del procedimiento almacenado
		$stmt->closeCursor();

		dd($rows, 'ROWS');
		dd($row_count, 'COUNT');

	} catch (\Exception $e) {
		dd("Error: " . $e->getMessage());
	}
	
o dentro de una transacción.

Ej:

	$s = 'X';
	$offset = 2;
	$limit  = 2; 

	$conn = DB::getConnection('parts');

	DB::beginTransaction();

	try {

		$rows =	DB::safeSelect("CALL partFinder(?, ?, ?, @rowCount)", [$s, $offset, $limit], 'ASSOC', null, $stmt);

		// Obtener el valor del parámetro de salida
		$stmt   = $conn->query("SELECT @rowCount as rowCount");
		$result = $stmt->fetch(\PDO::FETCH_ASSOC);

		// Recuperar el valor de rowCount
		$row_count = $result['rowCount'];

		// Cerrar cursor del procedimiento almacenado
		$stmt->closeCursor();

		dd($rows, 'ROWS');
		dd($row_count, 'COUNT');
		
		DB::commit(); 

	}catch(\Exception $e){
		DB::rollback();

		dd($e->getMessage(), "Error en transacción");
	}	


# Transacciones

La forma básica de crear una transacción con su "roll back" en caso de fallo tiene la siguiente estructura:

	DB::beginTransaction();

	try {

		// ...
		// ...
		
		DB::commit(); 

	}catch(\Exception $e){
		DB::rollback();

		dd($e->getMessage(), "Error en transacción");
	}	

Ej:

	DB::beginTransaction();

	try {
		$name = '';
		for ($i=0;$i<20;$i++)
			$name .= chr(rand(97,122));

		$id = DB::table('products')->create([ 
			'name' => $name, 
			'description' => 'bla bla bla', 
			'size' => rand(1,5).'L',
			'cost' => rand(0,500),
			'belongs_to' => 90
		]);   

		//throw new \Exception("AAA"); 

		DB::commit();

	} catch (\Exception $e) {
		DB::rollback();
		throw $e;
	} catch (\Throwable $e) {
		DB::rollback();            
	}            

Cabe notar que se olvidara colocar el DB::beginTransaction() se producirá el error "There is no is_active transaction".

Una forma más conveniente de realizar transacciones es usando una función anómima con transaction()

	DB::transaction(function(){
		// operación sobre la base de datos   
		// operación sobre la base de datos 
		// operación sobre la base de datos 
	}); 

Ej:

	DB::transaction(function(){
		$name = '';
		for ($i=0;$i<20;$i++)
			$name .= chr(rand(97,122));

		$id = DB::table('products')->create([ 
			'name' => $name, 
			'description' => 'Esto es una prueba', 
			'size' => rand(1,5).'L',
			'cost' => rand(0,500),
			'belongs_to' => 90
		]);   
	});     


Output mutators

	$rows = DB::table('users')
	->registerOutputMutator('username', function($str){ return strtoupper($str); })
	->get();

Transformers 

	$t = new UsersTransformer();

	$rows = DB::table('users')
	->registerTransformer($t, $this)
	->get();

otro ejemplo:

	$t = new \Boctulus\Simplerest\transformers\ProductsTransformer();

	$rows = DB::table('products')
	->where(['size'=>'2L'])
	// ...
	->registerTransformer($t)
	->get();

Son combinables transformers con output mutators:

	$t = new \Boctulus\Simplerest\transformers\UsersTransformer();

	$rows = DB::table('users')
	->registerOutputMutator('username', function($str){ return strtoupper($str); })
	->registerTransformer($t)
	->get();



# Fetch modes 

Los modos de obtención de datos pueden setearse  en cualquera de los casos mediante el método setFetchMode() antes de llamar a un método final como get(), first(), min(), avg(), etc 

Ej:

	dd((new BarModel())
	->connect()
	->setFetchMode('ASSOC')
	->get());


# Debuguear una query (desde el controlador)

Hay varios métodos para hacer un debug de una query comenzando por DB::dd() que ocupa el lugar del método get()

Ejemplo:

	$res = DB::table('products')
		->groupBy(['name'])
		->having(['c', 3, '>='])
		->select(['name'])
		->selectRaw('COUNT(name) as c')
		->get());

var_dump($res);

Reemplazando ->get() por ->dd()

	$res = DB::table('products')
		->groupBy(['name'])
		->having(['c', 3, '>='])
		->select(['name'])
		->selectRaw('COUNT(name) as c')
		->dd());

var_dump($res);

Resultado:

	SELECT AVG(cost), size FROM products WHERE deleted_at IS NULL GROUP BY size HAVING AVG(cost) >= 150

El problema del método dd() es que no funciona si en su lugar hay una función get() parametrizada -en vez de usar select()- o bien una función agregativa comon min(), max(), count(), sum() y avg() 

Otra función de debug, disponible cuando se usa DB::table() es DB::getLog()

Ejemplo:

	$c = DB::table('products')
		->where([ 'belongs_to'=> 90] )
		->count('*', 'count');

	dd(DB::getLog());


Nota:

Para que DB::getLog() arroje la query estaba debe haber sido "armada" algo que sucede cuando se "compila" al ejecutar funciones como get(), first(), pluck() .... o dd()

	$m = DB::table('products')
	->where(['size', ['0.5L', '3L'], 'NOT IN']);

	// Nada
	dd(DB::getLog());

Sin embargo lo siguiente *SI* devuelve la query:

	$m = DB::table('products')
	->where(['size', ['0.5L', '3L'], 'NOT IN']);

	$m->dd();
	dd(DB::getLog());
    
Lo siguiente también sirve:

	DB::table('products')
	->where(['size', ['0.5L', '3L'], 'NOT IN'])
	->dontExec()
	->dontBind()
	->get();

	dd(DB::getLog());
	
El método más avanzado para debugueo le pertenece al modelo y es debug()

Ej:
	$data = [
		'name' => 'Acetazolamida 250 mg x 20 comp',
		'regular_price' => '4290',
		'status' => 'draft',
		'stock' => 0,
		'type' => 'simple',
		'attributes' => [
			'precio_promo' => 650,
			'laboratorio' => 'Farmaquimicas'
 		]
	];

	/*
		Requiere guardar la instancia del modelo
	*/

	$m = DB::table('products');
	$m->create($data);

	d(
		$m->debug()
	);
	
# Obtención de la query pre-compilada

Una función de debug que puede utilizarse es getLastPrecompiledQuery(), la cual devuelve el último query antes de ser bindeado con los parámetros -aunque per se no evita el binding-.

Ejemplo:

	$uno = DB::table('products')->deleted()
	->select(['id', 'name', 'description', 'belongs_to'])
	->where(['belongs_to', 90]);

	$m2  = DB::table('products')->deleted();
	$dos = $m2
	->select(['id', 'name', 'description', 'belongs_to'])
	->where(['belongs_to', 4])
	->where(['cost', 200, '>='])
	->union($uno)
	->orderBy(['id' => 'ASC'])
	->get();

	dd($m2->getLastPrecompiledQuery());

En cualquier caso es posible realizar un debug *sin* ejecutar la consulta con el método dontExec() y mediante el método dontBind() evitar el "bindeo" de parámetros. Esto podría ser útil para armar una sub-query.

Ejemplo:

	$res = DB::table('products')
	->dontBind()   // <--- here 
	->dontExec()   // <--- here 
	->groupBy(['size'])
	->having(['AVG(cost)', 150, '>='])
	->select(['size'])
	->selectRaw('AVG(cost)')
	->get();

	dd(DB::getLog());

También podemos evitar se cree una conexión a la base de datos pasando false como tercer parámetro a DB::table() y finalmente si nuestro interés es obtener la consulta pre-compilada o sea armada pero aún con los parámetros sin bindear podemos llamar a toSql().

Ej:

	$m = DB::table('products', null, false)
	->where(['belongs_to', null])
	->group(function($q){
		$q->where(['size', null])
		->orWhere([
			['cost', null, '<='],
			['cost', null, '>=']
		]);
	})
	->whereRaw('cost < IF(size = "1L", ?, 100) AND size = ?', [null, null])
	->orderBy(['cost' => 'ASC']);

	$sql = $m
	->dontBind()
	->toSql();
	
	dd($sql);

Si desea realizar pruebas de performance remítase en la documentación a Time::exec()


# Parámetros para binding

Los parámetros a ser bindeados pueden recuperarse también con getLastBindingParamters()

Ej:

	$m = DB::table('products');

	$m->where(['size', ['0.5L', '3L'], 'NOT IN'])
	->dontExec()
	->dontBind()
	->get();
	
	$q = $m->toSQL();
	$params = $m->getLastBindingParamters();

	d($q);
	d($params);


Como si fueran pocas funciones de debug existe el método Model::getLog()

	$model = new \Boctulus\Simplerest\Core\Model($conn);
	$res = $model->create(['name' => 'Jhon', 'age' => 32]);
	dd($model->getLog());
	

# Debuguear una query (desde el propio modelo)

Es posible usar hooks sobre el modelo para debuguear una query. Por ejemplo si se desea debuguear un create ("INSERT INTO") sería así:

	class XXXXXModel extends Model
	{ 
		// ...

		function onCreated(array &$data, $last_inserted_id)
		{
			dd($this->dd());
		}
	}

Si la query falla entonces solo simule que se ejecuta usando el método dontExec() así: 

	class XXXXXModel extends Model
	{ 
		// ...

		function onCreating(array &$data)
		{
			$this->dontExec();
		}

		function onCreated(array &$data, $last_inserted_id)
		{
			dd($this->dd());
		}
	}


En el caso particular de los INSERTs es posible que Model::dd() no reemplace los parámetros y los deje como "preparados" obtieniendo algo como:

    INSERT INTO my_table (
      column1, column2
    ) 
    VALUES (
        :column1, :column2
    )

Entonces se puede obtener por separado los parámetros recibidos para el binding con Model::getLastBindingParamters()

Ej:

    function onCreating(array &$data)
    {
        $this->dontExec();  
    }

    function onCreated(array &$data, $last_inserted_id)
    {
        dd($this->dd(), 'SQL');
        dd($this->getLastBindingParamters(), 'PARAMETERS');
    }


# Modos de Ejecución

Se implementa un sistema de modos de ejecución que permite controlar cómo se procesan las operaciones de base de datos. Esto es especialmente útil para depuración, auditoría y pruebas.

## Modos Disponibles

Se soporta tres modos de ejecución:

- **EXECUTION_MODE_NORMAL**: (valor por defecto) Ejecuta las operaciones normalmente en la base de datos.
- **EXECUTION_MODE_SIMULATE**: Simula la ejecución sin modificar la base de datos.
- **EXECUTION_MODE_PREVIEW**: Devuelve información sobre la operación SQL sin ejecutarla.

## Uso de los Modos de Ejecución

### Modo Normal

Este es el comportamiento predeterminado, donde todas las operaciones se ejecutan en la base de datos.

```php
// Uso implícito del modo normal
$user = DB::table('users')->create([
    'name' => 'John Smith',
    'email' => 'john@example.com'
]);

// Uso explícito del modo normal
$user = DB::table('users')
    ->setExecutionMode(Model::EXECUTION_MODE_NORMAL)
    ->create([
        'name' => 'John Smith',
        'email' => 'john@example.com'
    ]);
```

### Modo de Simulación

Este modo es útil para probar la lógica de la aplicación sin afectar los datos. El sistema procesará la operación completa (incluyendo validaciones y hooks) pero no ejecutará la consulta SQL final.

```php
// Simular una inserción
$simulatedId = DB::table('users')
    ->setExecutionMode(Model::EXECUTION_MODE_SIMULATE)
    ->create([
        'name' => 'Test User',
        'email' => 'test@example.com'
    ]);

// $simulatedId contendrá un ID ficticio (-1)
// La tabla 'users' no será modificada
```

Este modo es especialmente útil para:
- Probar flujos de trabajo complejos sin afectar datos reales
- Verificar que todas las validaciones y hooks funcionan correctamente
- Registrar operaciones para auditoría sin ejecutarlas

### Modo de Previsualización

Este modo devuelve información detallada sobre la operación SQL que se ejecutaría, sin llevarla a cabo realmente.

```php
// Previsualizar una actualización
$previewInfo = DB::table('products')
    ->where('id', 5)
    ->setExecutionMode(Model::EXECUTION_MODE_PREVIEW)
    ->update([
        'price' => 19.99,
        'stock' => 100
    ]);

// $previewInfo contendrá información detallada como:
// - La operación (update)
// - La tabla (products)
// - Los datos a actualizar
// - La consulta SQL formateada
// - Los parámetros de binding
```

Este modo es valioso para:
- Depuración de consultas complejas
- Generación de documentación
- Herramientas de administración que muestran el SQL a ejecutar
- Auditoría de operaciones potenciales

## Ejemplos de Casos de Uso

### API con Modo de Simulación

```php
function createUser() {
    $data = request()->getBody();
    
    // Verificar si debe ser una simulación
    $enableSaving = !isset($data['enable_saving']) || 
                   filter_var($data['enable_saving'], FILTER_VALIDATE_BOOLEAN);
    
    if (!$enableSaving) {
        unset($data['enable_saving']);
        $mode = Model::EXECUTION_MODE_SIMULATE;
    } else {
        $mode = Model::EXECUTION_MODE_NORMAL;
    }
    
    // Configurar modo y ejecutar
    $instance = DB::table('users');
    $instance->setExecutionMode($mode);
    $result = $instance->create($data);
    
    // Enviar respuesta apropiada
    $response = [
        'user' => $data,
        'id' => $result
    ];
    
    if (!$enableSaving) {
        $response['_saved'] = false;
        $response['_notice'] = 'Data was not saved to database (simulation mode)';
    }
    
    return response()->send($response, 201);
}
```

### Generador de SQL para Informes

```php
function generateSqlReport() {
    $queries = [];
    
    // Recopilar varias consultas sin ejecutarlas
    $queries['new_users'] = DB::table('users')
        ->where('created_at', date('Y-m-d'), '>=')
        ->setExecutionMode(Model::EXECUTION_MODE_PREVIEW)
        ->get();
        
    $queries['out_of_stock'] = DB::table('products')
        ->where('stock', 0)
        ->setExecutionMode(Model::EXECUTION_MODE_PREVIEW)
        ->get();
        
    $queries['pending_orders'] = DB::table('orders')
        ->where('status', 'pending')
        ->setExecutionMode(Model::EXECUTION_MODE_PREVIEW)
        ->get();
    
    // Generar informe con todas las consultas SQL
    return view('admin.sql_report', ['queries' => $queries]);
}
```

### Operación Bulk con Validación Previa

```php
function importProducts($products) {
    // Primero validar todos los registros sin guardarlos
    $errors = [];
    $instance = DB::table('products')->setExecutionMode(Model::EXECUTION_MODE_SIMULATE);
    
    foreach ($products as $index => $product) {
        try {
            $instance->create($product);
        } catch (InvalidValidationException $e) {
            $errors[$index] = json_decode($e->getMessage(), true);
        }
    }
    
    // Si hay errores, detener y reportar
    if (!empty($errors)) {
        return [
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $errors
        ];
    }
    
    // Si todo es válido, proceder con la inserción real
    $instance->setExecutionMode(Model::EXECUTION_MODE_NORMAL);
    $results = [];
    
    foreach ($products as $product) {
        $results[] = $instance->create($product);
    }
    
    return [
        'success' => true,
        'message' => 'All products imported successfully',
        'inserted_ids' => $results
    ];
}
```

## Consideraciones de Rendimiento

- El modo `EXECUTION_MODE_SIMULATE` tiene un rendimiento similar al modo normal, ya que realiza todas las validaciones y preparación, pero evita la ejecución final.
- El modo `EXECUTION_MODE_PREVIEW` es considerablemente más rápido ya que se detiene antes de preparar la consulta completa.

## Integración con Logs y Auditoría

Los modos de ejecución se integran perfectamente con el sistema de logs:

```php
// Configuración para registrar todas las consultas (ejecutadas o simuladas)
if ($this->executionMode == self::EXECUTION_MODE_SIMULATE) {
    $this->logSQL();
    // Resto del código de simulación...
}
```

Esto garantiza que las consultas simuladas también aparezcan en los logs, facilitando la auditoría y depuración.

# Formateo de las queries

Con fines de debugging se utiliza por defecto un paquete que formatea las queries SQL en la mayor parte de los casos aunque no es perfecto y puede querer desactivarse o parametrizarse de otra manera.

En MyModel podría hacerse:

	function __construct(bool $connect = false, $schema = null, bool $load_config = true){
        parent::__construct($connect, $schema, $load_config);

		static::$sql_formatter_callback = function(string $sql){
      		return MySqlFormatter($sql);
    	};
    }

O bien

	function __construct(bool $connect = false, $schema = null, bool $load_config = true){
        parent::__construct($connect, $schema, $load_config);
	
		$this->setSqlFormatter(function(string $sql){
			return MySqlFormatter($sql);
		});
	}

Donde si simplemente quisiera anularse por completo el sql formater bien podría hacerse:

	function __construct(bool $connect = false, $schema = null, bool $load_config = true){
        parent::__construct($connect, $schema, $load_config);

		static::$sql_formatter_callback = null;
    }	


Además es posible activar o des-activar el formateo de queries con los métodos Model::sqlFormaterOn(), Model::sqlFormaterOff() o bien pasándole un boolean a dd()

Producen el mismo resultado:

	echo $m->sqlFormaterOn()->dd();
	echo $m->dd(true);


Algunos ejemplos:

 	/*
        Sql formater habilitado via Model::sqlFormaterOn()
    */

	$m = DB::table('products')
	->deleted()
	->groupBy(['cost', 'size', 'belongs_to'])
	->having(['cost', 100, '>='])
	->or(function($q){
		$q->havingRaw('SUM(cost) > ?', [500])
		->having(['size' => '1L']);
	})
	->select(['cost', 'size', 'belongs_to']);

	dd(
		$m
		->sqlFormaterOn()   /* habilito */
		->dd()
	);


    /*
        Sql formater des-habilitado (por defecto)
    */
   
	$m = DB::table('products')
	->deleted()
	->groupBy(['cost', 'size', 'belongs_to'])
	->having(['cost', 100, '>='])
	->or(function($q){
		$q->havingRaw('SUM(cost) > ?', [500])
		->having(['size' => '1L']);
	})
	->select(['cost', 'size', 'belongs_to']);

	dd($m->dd());
    

    /*
        Sql formater habilitado via Model::dd()
    */

	$m = DB::table('products')
	->deleted()
	->groupBy(['cost', 'size', 'belongs_to'])
	->having(['cost', 100, '>='])
	->or(function($q){
		$q->havingRaw('SUM(cost) > ?', [500])
		->having(['size' => '1L']);
	})
	->select(['cost', 'size', 'belongs_to']);

	dd(
		$m
		->dd(true)
	);


Como Model::sqlFormatter() es un método público y convenientemente estático fácilmente se puede usar fuera del ámbito de la clase. El método es parametrizable y los parámetros pasan directamente al formateador.
    

    /*
        Sql formateador es aplicado en un segundo paso
        y se parametriza para colorizar 
    */

	$m = DB::table('products')
	->deleted()
	->groupBy(['cost', 'size', 'belongs_to'])
	->having(['cost', 100, '>='])
	->or(function($q){
		$q->havingRaw('SUM(cost) > ?', [500])
		->having(['size' => '1L']);
	})
	->select(['cost', 'size', 'belongs_to']);

	dd(
		Model::sqlFormatter($m->dd(), true)
	);
    

Se provee de la función helper sql_formater() que es un atajo de Model::sqlFormatter()

    /*
        Sql formateador es aplicado en un segundo paso
        y se parametriza para colorizar pero usando el helper sql_formater 
    */

	$m = DB::table('products')
	->deleted()
	->groupBy(['cost', 'size', 'belongs_to'])
	->having(['cost', 100, '>='])
	->or(function($q){
		$q->havingRaw('SUM(cost) > ?', [500])
		->having(['size' => '1L']);
	})
	->select(['cost', 'size', 'belongs_to']);

	dd(
		sql_formater($m->dd(), true)
	);    


#### Campos manejados por el framework

Hay una cantidad de campos (que si están presentes en la tabla y declarados en el schema del modelo) que son manejados directamente por el framework. Consideremos la siguiente tabla:

id 				int(11)		
name			varchar(60)				
is_active		tinyint(4)
belongs_to		int(11)			*	
created_at		datetime		*			
created_by		int(11)			*	
updated_at		datetime		*	
updated_by		int(11)			*	
deleted_at 		datetime		*
deleted_by		tinyint(4)		*
is_locked		tinyint(4)		*

belongs_to		apunta directamente al user_id de quien crea el registro. Si el usuario tiene el permiso especial 'transfer' puede cambiar ese valor y hacer que apunte a otro usuario. Un caso especial son los registros creados dentro de un 'folder'.

created_by 		apunta indefectiblemente al user_id quien creó el registro.
update_by		apunta indefectiblemente al user_id del último usuario que modificó un registro. 
deleted_by		apunta indefectiblemente al user_id que hizo un borrado suave un registro.
created_at 		apunta indefectiblemente a la fecha-hora en que se creó un registro.
updated_at 		apunta indefectiblemente a la fecha-hora en que se modificó por última vez un registro.
deleted_at 		apunta indefectiblemente a la fecha-hora en que se creó un registro.
is_locked 		cuando un registro es mofificado por un usuario con permiso de 'lock' automáticamente se guarda un 1.


Los campos created_by, update_at y deleted_at son rellenados por Model pero al modelo no le compete si hay un sistema de autenticación y que usuario está haciendo el query así que los campos created_by, update_by, deleted_by y belongs_to son manejados a nivel de la API por ApiController al igual que el campo is_locked.

Nota: no olvides que los campos que necesites deben estar en las tablas y en el schema del modelo correspondiente. 


### Los metodos init() y boot() de Model

Los modelos tienen un par de metodos que se ejecutan uno antes del constructor y otro justo despues:

	boot()		se ejecuta antes
	init()		se ejecuta despues


### Mutators

Se presentan casos donde es necesario hacer una transformación de los datos ya sea antes de enviarlos la query o bien antes de guardarlos en base de datos. 

El caso emblemático es cuando se requiere hacer un "hash" del password antes de guardarse ya sea en la creación o edición de un registro. Entonces usaremos un "input mutator".

Ejemplo

class UsersModel extends Model
{ 
	// ...

    function __construct($db = NULL){
		$this->registerInputMutator('password', function($pass){ 
			return password_hash($pass, PASSWORD_DEFAULT); 
		}, function($op, $dato){
			return ($dato !== null);
		});

        parent::__construct($db);
    }
}

El método registerInputMutator() acepta 3 parámetros:

- El campo cuyo contenido se quiere mutar (condicionalmente)
- Un callback que transforma el dato de entrada 
- Un callback (opcional) que determina en que caso se debe aplicar la función de entrada y que es alimentada con el tipo de operación ('UPDATE' o 'CREATE') y el dato de entrada. En caso de omitirse aplica siempre el mutator para ese campo.

El procedimiento es registrar los mutators para cada campo del modelo que los requiera. Otro uso de input mutators es para utilizar UUIDs donde dentro de un Trait podemos tener la funcionalidad de generar el uuid:

	namespace Boctulus\Simplerest\traits;

	trait Uuids
	{
		protected function init()
		{
			parent::init();

			$this->registerInputMutator('uuid', function($id){ 
				return uuid_create(UUID_TYPE_RANDOM); 
			}, function($op, $dato){
				return ($op == 'CREATE');
			}); 
		}    
	}

<-- Como sería un efecto indeseado que se auto-generara un UUID reemplazando el actual al hacer un UPDATE, entonces ponemos como condición que se aplique solo en el CREATE.	

En el modelo donde queremos implementar el uuid, debemos declar el campo como string, importamos y hacemos el "use" del Trait:

	<?php
	namespace Boctulus\Simplerest\Models;

	use Boctulus\Simplerest\Core\Model;
	use Boctulus\Simplerest\traits\Uuids;

	class BarModel extends Model 
	{ 
		use Uuids;
		
		function __construct($db = NULL){
			parent::__construct($db);
		}
	}

Recordar actualizar el Schema donde el campo uuid debe ser un string y pertenecer a los nullables.

Output Mutators 

Además de los Input Mutators, tenemos también los *Output Mutators* que permiten aplicar una función sobre la salida de la ejecución de una query. En otros lenguajes / frameworks son conocidos como "accessors".

Ejemplo:

	$rows = DB::table('users')
	->registerOutputMutator('username', function($str){ return strtoupper($str); })
	->get();

Lógicamente un Output Mutator sobre un campo no es compatible con declarar a ese mismo campo como "hidden". 

Los mutators de salida pueden aplicarse incluso cuando hay funciones agregativas y las cláusulas GROUP BY y HAVING. 

Ejemplo:

	rows = DB::table('products')
	->registerOutputMutator('size', function($str){ return strtolower($str); })
	->groupBy(['size'])
	->having(['AVG(cost)', 150, '>='])
	->select(['size'])
	->selectRaw('AVG(cost)')
	->get();


### Transformers

Los transformers se aplican a la salida de la ejecución de una query y en orden después de los "output mutators" si los hubiere.

Una transformación permite (a diferencia de un output mutator) no solo aplicar funciones sobre la salida de cada campo sino también crear campos virtuales, eliminar campos o cambiarles el nombre.

Ejemplo:

	class UsersTransformer 
	{
		public function transform(object $user, Controller $controller = NULL)
		{
			return [
				'id' => $user->id,
				'username' => $user->username,
				'is_active' => $user->is_active,
				'email' => $user->email,
				'confirmed_email' => $user->confirmed_email,
				'password' => $user->password,
				'firstname' => 'Mr. ' . $user->firstname,
				'lastname' => $user->lastname,
				'full_name' => "{$user->firstname} {$user->lastname}",
				'deleted_at' => $user->deleted_at,
				'belongs_to' => $user->belongs_to
			];
		} 
		
		//...

Si un campo del SCHEMA no está presente desaparece de la salida (en caso de estar presente) y si se asigna con otra key, su nombre será otro. Ejemplo:

	family_name' => $user->lastname
	
Es importante destacar que no funciona si hay funciones agregativas presentes en la query y tampoco sirve para cambiar datos accediendo por un campo virtual por ejemplo.

Al registrar un transformer cualquier campo oculto se vuelve visible aunque cabe recordar que sino está presente como key del array devuelto desaparece.

Es posible acceder a propiedades del controller que invocó al transformer, ejemplo:

	class UsersTransformer 
	{
		public function transform(object $user, $controller = NULL)
		{
			return [
				'id' => $user->id,
				//...
				'password' => $controller->is_admin ? $user->password : false,
			];
		}
	}

Y ahora en un Controller paso $this como segundo parámetro a registerTransformer() para brindar acceso a las propieades del controlador:

	$t = new \Boctulus\Simplerest\transformers\UsersTransformer();

	$rows = DB::table('users')
	->registerTransformer($t, $this)
	->get();

	dd($rows);

En el ejemplo si el controller tiene un campo is_admin (como sucede con los resource controllers en SimpleRest) entonces según el valor mostrará o no el password.
 
Mutators y transformers pueden usarse juntos, ej:

	$t = new \Boctulus\Simplerest\transformers\UsersTransformer();

	$rows = DB::table('users')
	->registerOutputMutator('username', function($str){ return strtoupper($str); })
	->registerTransformer($t)
	->get();

	dd($rows);

Nota: de momento los Transformers se han probado desde Controllers y no desde las APIs <-- quizás sea posible usarlos de alguna forma si surge una necesidad real.


### Campos fillables, no-fillables, nullables y ocultos

Se puede definir un array de campos "fillables" aunque por lo general se lo puede dejar en automático. También es posible definir por el contrario, campos a excluir como "no fillables".

	protected $fillable = [
							'email',
							'password',
							'firstname',
							'lastname',
							'deleted_at',
							'belongs_to'
	];

	// o ...
	protected $not_fillable = ['confirmed_email'];

Los campos no-nullables serian los requeridos para las validaciones y se definen de igual modo: 

	protected $nullable = ['id', 'firstname', 'lastname', 'deleted_at', 'belongs_to', 'confirmed_email'];

Por último tenemos los campos ocultos:

	protected $hidden   = [	'password' ];


# Hooks sobre el modelo

Se definen varios event hooks sobre el modelo que se disparan ante una operación CRUD

	protected function onReading() { }
	protected function onRead(int $count) { }

	protected function onCreating(Array &$data) {	}
	protected function onCreated(Array &$data, $last_inserted_id) { }

	protected function onUpdating(Array &$data) { }
	protected function onUpdated(Array &$data, ?int $count) { }

	protected function onDeleting(Array &$data) { }
	protected function onDeleted(Array &$data, ?int $count) { }

	protected function onRestoring(Array &$data) { }
	protected function onRestored(Array &$data, ?int $count) { }

Como regla general los nombres de eventos que terminan en -ing se envían antes de que se conserven los cambios en el modelo, mientras que los eventos que terminan en -ed se envían después de que se conserven los cambios en el modelo.

Un uso práctico de estos eventos es para des-confirmar un email cuando se ha cambiado. En este caso se hace uso de la función isDirty() que acepta como parámetro el campo que necesitamos saber si ha cambiado.

	protected function onUpdating($data) {
		if ($this->isDirty('email')) {
			$this->update(['confirmed_email' => 0]);
		}	
	}

La función isDirty() acepta un campo o un array de campos o incluso puede estar vacia en cuyo caso verifica si algún campo sería cambiado.

Otro uso práctico sería el uso del evento onReading() para hacer un (inner, left, right, natural, cross,...) JOIN automático para devolver el contenido de tablas relacionadas cada vez que se lea un registro de esa tabla. Así:

class MaestroModel extends Model
{ 
	// ...

	function onReading(){
		$this->leftJoin('detalle');
	}
}

Desde cualquier hook del modelo es posible acceder a propiedades (si son públicas) útiles como $w_vars y $w_vals -entre otras- con las que es posible reconstruir el where.


## Modelos y schemas en ubicacion arbitraria

Es perfectamente posible cambiar la ubicacion de schemas y modelos a cualquier ubicacion.

Pasos:

1.- Mover schemas y modelos
2.- Ajustar namespaces de los schemas (edicion dentro del archivo .php del modelo)
3.- Ajustar namespace de los modelos de la conexion (recordar que el framework es multitenant) que es utilizado por DB::table()

La funcion set_model_namespace() concatena al namespace pasado como parametro '\\Models\\'

Ej:
```
    /*
        Conexion a la base de datos: 'laravel_pos'
        Namespace del package: 'Boctulus\FriendlyposWeb'
    */

    DB::getConnection('laravel_pos');

    set_model_namespace('laravel_pos', 'Boctulus\FriendlyposWeb');

    $res = dd(
        DB::table('unidad_medida')->get()
    );

    DB::closeConnection('laravel_pos');
```

# ORM integrado (en desarrollo)

De momento se ha implementado el acceso a instacias de modelos con el metodo estatico findOrFail() sobre la clase del modelo.

```php
// Crear conexion sino es la default
DB::getConnection('laravel_pos');

$um = UnidadMedidaModel::findOrFail(5);
dd($um->getOne()); 

DB::closeConnection('laravel_pos');
```