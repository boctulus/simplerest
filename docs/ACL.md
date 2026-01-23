### ACL

La implementación del ACL incluye "roles" y permisos individuales (llamados también "scopes") para cada usuario sobre cada tabla expuesta a través de la API.

# Declaración de roles y sus permisos

Se implentó un ACL centralizado que se configurará en /config/acl.php y requiere ajustar permisos de lectura y escritura sobre el directorio app/security. 

Los siguientes métodos proveen la funcionalidad de declaración:

addRole(string $role_name, $role_id = null)
addRoles(Array $roles)
addInherit(string $role_name, $to_role = null
addResourcePermissions()
addSpecialPermissions(Array $sp_permissions, $to_role = null)
setAsGuest()
setAsRegistered(string $name)

Nota:

Los métodos setAsGuest() y setAsRegistered() se utilizan para definir qué rol tendrá la funcionalidad de "guest" y la de usuario registrado respectivamente. En el caso de setAsGuest() es opcional si el nombre del rol se dejara como "guest".

La estructuración en la declaración de roles y permisos es muy flexible.

Ej:

	$acl = new Acl();

	$acl->addRole('guest', -1)
	->addResourcePermissions('products', ['read'])

	->addRole('vendedor', 1)
	->addInherit('guest')
	->addResourcePermissions('products', ['write'])
	->addResourcePermissions('foo', ['create', 'list'])

	->addRole('admin', 100)
	->addInherit('guest')
	->addSpecialPermissions(['read_all', 'write_all'])

	->addRole('superadmin', 500)
	->addInherit('admin')
	->addSpecialPermissions(['lock', 'fill_all']);


Y equivale a:

	$acl->addRoles([
		'guest' => -1,
		'vendedor' => 1,
		'admin' => 100,
		'superadmin' => 500
	])	

	->addResourcePermissions('products', ['read'], 'guest')	

	->addInherit('guest', 'vendedor')
	->addResourcePermissions('products', ['write'])	
	->addResourcePermissions('foo', ['create', 'list'])	

	->addInherit('guest', 'admin')
	->addSpecialPermissions(['read_all', 'write_all'])


	->addInherit('admin', 'superadmin')
	->addSpecialPermissions(['lock', 'fill_all']);
	

Cabe notar que los addInherit() deben ir *siempre* antes de los permisos que se quieran agregar al rol heredado. 

El rol de 'guest' debe estar definido con ese nombre o con otro. En caso de que se decida cambiar el nombre del rol guest de 'guest' a otro se debe llamar al método estático setGuest() con el nombre alternativo:

	Acl::setGuest('unregistered');

Además de setear el nombre alternativo es su responsabilidad crear el rol correspondiente con el método addRole()

El Acl genera una representación interna similar a:

	array(4) {
	["guest"]=>
	array(3) {
		["role_id"]=>
		int(-1)
		["sp_permissions"]=>
		array(0) {
		}
		["tb_permissions"]=>
		array(1) {
		["products"]=>
		array(1) {
			[0]=>
			string(4) "read"
		}
		}
	}
	["vendedor"]=>
	array(3) {
		["role_id"]=>
			int(1)
		["sp_permissions"]=>
			array(0) {
			}
		["tb_permissions"]=>
			array(2) {
			["products"]=>
			array(2) {
				[0]=>
				string(4) "read"
				[1]=>
				string(5) "write"
			}
			["foo"]=>
			array(2) {
				[0]=>
				string(6) "create"
				[1]=>
				string(4) "list"
			}
		}
	}
	["admin"]=>
	array(3) {
		["role_id"]=>
		int(100)
		["sp_permissions"]=>
		array(2) {
		[0]=>
		string(8) "read_all"
		[1]=>
		string(9) "write_all"
		}
		["tb_permissions"]=>
		array(1) {
		["products"]=>
		array(1) {
			[0]=>
			string(4) "read"
		}
		}
	}
	["superadmin"]=>
	array(3) {
		["role_id"]=>
		int(500)
		["sp_permissions"]=>
		array(2) {
		[0]=>
		string(8) "read_all"
		[1]=>
		string(9) "write_all"
		}
		["tb_permissions"]=>
		array(2) {
		["products"]=>
		array(1) {
			[0]=>
			string(4) "read"
		}
		["permissions"]=>
		array(2) {
			[0]=>
			string(4) "read"
			[1]=>
			string(5) "write"
		}
		}
	}
	}

Existen dos tipos de permisos que se pueden asignar a los roles:  "resource permissions" y "special permissions".

Los "resource permissions" se aplican sobre las tablas especificadas mientras que los "special permissions" son de caracter más general y definen la posibilidad de realizar ciertas acciones administrativas típicas de un "admin".

Además sobre "resource permissions" existen ciertos permisos "especiales" -no confundir con "special permissions"- que habilitan a leer registros que no le pertenecen al tenedor del rol:  

    show_all	- lee los registros propios y ajenos sobre ese recurso
    list_all	- lista los registros propios y ajenos sobre ese recurso
    read_all	- lista y lee los registros propios y ajenos sobre ese recurso

Estos permisos típicamente se deberían dar a un "guest" para que pueda por ejemplo leer todas las entradas de un blog o las publicaciones de un sitio de ventas. Si por el contrario a un "guest" se le diera un permiso "read" -en vez de "read_all"- entonces podría darse el caso contradictorio de que un "guest" pueda ver las publicaciones de un vendedor y ese mismo vendedor solo pueda ver las suyas propias y no las de otros vendedores.

Por lo anterior se aconseja no dar un permiso "read" a un "guest" sino en todo caso un "read_all" y hacer que directa o interectamente todos los roles deriven de "guest". 

Estos permisos especiales sobre "resource permissions" pueden tener otros usos interesantes como permitirle a un supervisor ver todos los registros de los usuarios y publicaciones de una tienda:

    ->addRole('supervisor', 502)  
    ->addInherit('registered')
    ->addResourcePermissions('users', ['read_all'])  // <--
    ->addResourcePermissions('products', ['read_all'])  // <--


# Persistencia del ACL

Las reglas del ACL se declaran en el archivo config/acl.php y son almacenadas en base de datos en la tabla "roles"

Luego el ACL es serializado y se almacena como asi como un archivo de cache en /storage/security

Para re-generar se puede usar el comando:

	make acl --force

Para poder deguear lo que construye el ACL antes de serializacion se puede usar --debug

Ej:

	make acl --force --debug

En realidad no suele ser necesario regenerar manualmente el ACL porque suele hacerse en automatico
pero eso depende de la logica dentro de acl.php --que puede modificarse--

Sin embargo si se quisiera cambiar el ID asociado a cada rol entonces podria ocurrir una colision de IDs o de nombres
y en ese caso deberian regenerarse.

Por otro lado en cualquier momemento es posible agregar un nuevo rol con sus reglas. Ej:

->addRole('admin', 50) 
->addInherit('registered')
->addSpecialPermissions(['read_all', 'write_all'])
->addResourcePermissions('tbl_contacto', ['read'])

->addRole('supervisor', 60)  
->addInherit('registered')
->addResourcePermissions('tbl_usuario_empresa', ['read_all']) 
->addSpecialPermissions([
    'fill_all', 
])

y no hay problema en agregar un rol intermedio:

->addRole('admin', 50) 
->addInherit('registered')
->addSpecialPermissions(['read_all', 'write_all'])
->addResourcePermissions('tbl_contacto', ['read'])

	/*
		NEW ***
	*/
	->addRole('admin2', 51) 
	->addInherit('admin')
	->addSpecialPermissions(['read_all', 'write_all'])
	->addResourcePermissions('tbl_contacto', ['read'])

->addRole('supervisor', 60)  
->addInherit('registered')
->addResourcePermissions('tbl_usuario_empresa', ['read_all']) 
->addSpecialPermissions([
   'fill_all', 
])

### Permisos

Los permisos sobre los endpoints son en general del tipo CRUD + permiso para listar los recursos: ['show', 'list', 'create', 'update',  'delete']


### Rol de Admin

En lugar de definir de forma monolítica los alcances de un "admin" éste se puede construir a partir de combinaciones de los siguientes permisos especiales asignables a cualquier rol

Ejemplos:

- "lock" - bloquear /besbloquear un registro 
- "lock" - modificar un registro bloqueado
- "lock" - borrar un registro bloqueado. Requiere también de "write_all_trashcan"
- "lock" - borrar definitivamente un registro bloqueado
- "lock" - restaurar (undelete) un registro bloqueado
- "read_all" - acceder a registros de otros usuarios (no incluye los protegidos en folders)
- "read_all" - listar registros de otros usuarios (no incluye los protegidos en folders pero si colecciones)
- "read_all_folders" - acceder a registros en folders que no se nos han compartido 
- "read_all_folders" - listar registros en folders que no se nos han compartido
- "read_all_trashcan" - acceder a  registros de otros en papelera
- "read_all_trashcan" - listar registros de otros en papelera
- "write_all" - modificar / borrar registros de otros usuarios (sin alterar su ownership)
- "write_all_folders" - modificar / borrar registros de otros usuarios en folders (sin alterar su ownership)
- "write_all_trashcan" - borrar definitivamente / restaurar registros de otros usuarios
- "write_all_collections" - escribir los registros de las colecciones de otros usuarios. 
- "transfer" - tranferir un registro o sea cambiar la propiedad (ownership) de un registro (campo belongs_to)
- "fill_all" - modificar la fecha de creación 
- "fill_all" - llenar cualquier campo incluso los no-fillables
- "grant" - conceder roles y permisos

Como colorario es posible tener muchos roles con caracteristicas de un admin o superadmin.


### Permisos a nivel de usuario

Los roles son permisos que se asignan masivamente (por igual) a todos los usuarios que poseen ese rol. Los roles se puden "sumar" obteniendo la suma de los permisos de cada rol.

Si se desea que un usuario particular tenga permisos distintos para una entidad particular que sean distintos de los del rol al que pertenece se pueden especificar "permisos indivuduales" creando un registro para ese usuario y esa tabla referida en la tabla "permissions". Solo puede haber una entrada en "permissions" para cada par (usuario, tabla).

Los cambios en los permisos a nivel de usuario al igual que los roles solo se aplican cuando el usuario "inicia sessión" o sea.. cuando obtiene los tokens y también cuando los tokens son renovados.

Hay distintas tablas y por tanto distintos endpoints para manejar distintos aspectos de los permisos:

	tabla roles

	tabla user_roles: <tabla puente> entre usuarios y roles.

	tabla sp_permissions:
		- Tabla con cada uno de los permisos especiales que existen para aplicar a nivel granular.

    tabla user_tb_permisions: 
        - show, list, create, update, delete 
        - Son permisos dados por un Admin sobre las tablas para usuarios específicos.

	tabla user_sp_permisions: 
        - show, list, create, update, delete 
        - Son permisos dados por un Admin para que usuarios específicos tengan determinados permisos "especiales".

	tabla folders:
		- Donde se definen "folders" sobre tablas que un usuario puede tener a fin de poder tener permisos específicos sobre esos folders. 

    tabla folder_permissions:
        - read, write
        - Son dados por los usuarios a ciertos usuarios sobre cierto folder de cierta tabla.

    folder_other_permissions:
        - read, write
        - Son dados por los usuarios a otros usuarios (sin especificar su id)
        - Puede especificarse el otorgamiento de permisos a usuarios no-registrados (guest)


A nivel de API para poder filtrar los roles del usuario logueado es con:

	/api/v1/user_roles?user_id=me

Esto es porque si es Admin, entonces se listarian todos los roles.

Lo anterior funciona porque "me" vale el user_id del usuario logueado.


# Como agregar / cambiar permisos a nivel de usuario 

Los permisos que "decoran" al o los roles que pueda poseer un usuario se pueden ser para un recurso (tabla) en particular ("resource permissions") o "especiales" (típicamente de roles tipo-admin)

Los permisos sobre recursos se pueden agregar o cambiar desde el endpoint

	/api/{version}/user_tb_permissions

Al listar se vería algo como:

	"data": {
        "user_tb_permissions": [
            {
                "id": 1,
                "tb": "my_table1",
                "can_list_all": null,
                "can_show_all": null,
                "can_list": 1,
                "can_show": null,
                "can_create": null,
                "can_update": null,
                "can_delete": null,
                "user_id": 119,
                "created_by": 119,
                "created_at": "2021-11-19 17:49:40",
                "updated_by": null,
                "updated_at": null
            },
			{
				"id": 5,
                // ...
			},
			// ..
        ]
    },

Para definir permisos sobre una tabla sería como en el siguiente ejemplo.  

Ej:

	{
		"tb": "table_xyz",
		"user_id": 119,
		"can_list": true
	}

Cabe destacar que siempre habrá un solo registro por tabla y que los permisos no se agregan individualmente sino todos los que se deseen setear a la vez y si se repitiera el proceso se sobre-escribiría lo que antes había.

Ej:

Si ahora se hiciera un POST nuevamente sobre /api/{version}/user_tb_permissions	

	{
		"tb": "table_xyz",
		"user_id": 119,
		"can_update": true,
		"can_show": true
	}

El resultado sería que para el "user_id" = 119 y la tabla "table_xyz" se tendrán solamente los permisos reciéntemente definidos para esa tabla y usuario.


Para conocer todos los campos (con distintos tipos de permisos) que se pueden enviar:

	GET /api/{version}/user_tb_permissions?defs=1


Los permisos "especiales" se trabajan sobre el endpoint

	/api/{version}/user_sp_permissions

Básicamente debe enviar:

	user_id
	sp_permission_id

Ej:

	POST /api/v1/user_sp_permissions

	{
		"user_id": 119,
		"sp_permission_id": 8
	}

Donde para conocer el "sp_permission_id" puede consultar el endpoint:

	/api/v1/sp_permissions


# Posible FrontEnd para los roles y permisos

A un Administrador se le podría presentarse la información sobre roles y permisos así:

Para un usuario:

	roles

		superadmin

	Special permissions

		read_all				[del]
		write_all				[del]	
		read_all_collections	[del]
		write_all_collections	[del]
		read_all_trashcan		[del]
		write_all_trashcan		[del]
		transfer				[del]
		lock					[del]
		impersonate				[del]
		
	[add]
		
	<-- los permisos especiales tienen como base los de su rol o roles y pueden ser decorados via tabla `user_sp_permissions`. Esto significa que lo que "se ve" no es la tabla `user_sp_permissions` sino el resultado de aplicar los permisos combinados de distintos roles y a eso los permisos presentes en la tabla `user_sp_permissions` si los hubiere.	

Para otro usuario:

	roles

		accounting
		supervisor

	Special permissions

		impersonate				[del]
		read_all				[del]	
		
	[add]	

	tb: users					[del]	

	[X]	list_all
	[X]	show_all
	[ ]	list
	[ ]	show
	[ ]	create
	[ ]	update
	[ ]	delete

	tb: sells					[del]

	[X]	list_all
	[X]	show_all
	[ ]	list
	[ ]	show
	[ ]	create
	[ ]	update
	[ ]	delete

	<-- estos permisos sobre-escriben los permisos propios de su rol o roles del usuario y tienen prioridad por sobre los permisos especiales.


# Scopes

En OAuth se habla de "scopes" como simil a permisos en un sentido más abstracto pero se puede hacer corresponder a los permisos individuales de la siguiente forma:
	
	["tb_permissions"]=> {
		["products"]=> [
			"read",
			"write"
		],
		["foo"]=> [
			"read"
		]
	}

equivale a 

	products.read
	products.write
	foo.read


SimpleRest *no* sigue el estándar de scopes de OAuth donde:

    - Los permisos se presentan de forma simple con la notación recurso.operación

    - La granulidad de los permisos suele expresarse para operaciones genéricas como "read", "write" y pueden definirse "alias" para ciertas operaciones como "emails.send" para "emails.create"

    - El desarrollador no "ve" -puede listar- permisos especiales (lo que no significa que no existan) 

https://www.freecodecamp.org/news/best-practices-for-building-api-keys-97c26eabfea9/


# Métodos para indagar sobre permisos y roles

El Acl provee un conjunto de métodos básicos para conocer el rol o los permisos del usuario y puede extender a partir de "paquetes" (service providers).

getEveryPosibleRole()                   						Devuelve todos los roles registrados en el ACL.
roleExists($rol)                        						Existe el rol?
getRolePermissions($rol)                						Devuelve todos los permisos para un determinado rol.
getAncestry($rol)                       						Roles de los ancestros de un usuario.
isHigherRole($rol1, $rol2)              						Tiene el rol$1 mayor nivel de acceso que $rol2? (2)

isGuest()                               						Es un visitante no registrado?
isRegistered()                          						Es un usuario que ha entregado credenciales?    
getRoles()                              						Rol o roles de usuario.

hasRole($rol)                          							El usuario posee ese rol? (no considera herencia)
hasAnyRole([$rol1, ...])               							El usuario posee alguno de esos roles? (no considera herencia)   
hasRoleOrHigher($rol)                   						Se tiene el rol o uno "superior" (2)
hasAnyRoleOrHigher([$rol1, ...])        						Se tiene un rol o uno "superior" (2)

getTbPermissions($table = null)									Retorna permisos sobre recursos (tablas) (1)
getSpPermissions($table = null)									Retorna permisos "especiales" (1)
getFreshTbPermissions($table = null)							Retorna permisos sobre recursos (tablas)
getFreshSpPermissions()											Retorna permisos "especiales"
hasResourcePermission($perm, $tabla)    						El usuario tiene permisos explícitos sobre esa tabla?
hasSpecialPermission($permiso)          						El usuario tiene ese permiso especial?
hasPermission($perm, $resource, $uid = null, $row_id = null)	Permisos sobre una tabla o registro 

Notas:

getTbPermissions() y getSpPermissions() no ofrecen resultados necesariamente actualizados (a diferencia de acceder a los endpoints correspondientes). Si el usuario entregó credenciales via Web Tokens, estos permisos muy probablemente serán derivados del payload del web token.

hasPermission() es el método con la misión de poder contestar de forma simple y definitiva si un usuario tiene o no permisos sobre un registro.

Para el caso del paquete Boctulus\Simplerest\FineGrainedACL se implementan adicionalmente getFreshTbPermissions() y getFreshSpPermissions() que garantizan resultados "frescos" porque acceden directamente a la base de datos.

Actualmente isHigherRole() y hasRoleOrHigher() están implementados en base a el árbol genealógico de roles y no compara permisos lo cual ofrece uh resultado que puede no ser exacto y solo lo será si existe una sola rama sin derivaciones en el árbol genealógico entre los roles a comparar.

Sin embargo, hasAnyRoleOrHigher() ofrece obtener el resultado más preciso a pesar de que no compara permisos sino roles ya que permite comparar no con un rol sino con varios y por ende considerar derivaciones en el árbol genealógico.

Ej:

+
|
----- guest
		|
		|
	registered
		|  |
		|  |
		|  usuario
		|        |
	supervisor   |
	    |        usuario_plus
		|              |
	 superadmin     moderador


Supongamos que en un escenario en particular (ResourceController o API) necesito un rol >= {supervisor o usuario} entonces podría usar hasAnyRoleOrHigher() para chequear esta condición:

	$pass = acl()->hasAnyRoleOrHigher(['supervisor', 'usuario_plus']);

Sin embargo podría querer saber si el usuario tiene los mínimos permisos que otorga un rol, entonces hasAnyRoleOrHigher() no ofrecerá exactamente lo que se necesita.

Entonces se *implementará* hasRolePermissionsOrHigher() y esta función podrá considerar derivaciones en el árbol genealógico.

La función Acl::hasRolePermissionsOrHigher() *deberá* tener en consideración todos los tipos de permisos, ya sea sobre recursos (tablas) y los considerados permisos "especiales". También deberá considerar los permisos que "decoran" los de los roles para un usuario en particular (!)


# Implementación de "paquetes" para el Acl

Es importante resaltar que dado que la clase Acl se serializa por motivos de performance la mayor parte de las propiedades y métodos no pueden ser estáticos ya que propiedades estáticas no pueden ser serializadas y al des-serializar no estarán disponibles llevando a muchos dolores de cabeza. Igualmente por concistencia se desaconseja el uso de métodos estáticos.

Al implementar un service provider para el ACL se debe cumplir de mínima con la interfaz IAcl.


# Controladores tipo "resource"

Una API se crea típicamente extendiendo la clase ApiController y esta clase a su vez extiende de otra llamada ResourceController que es la que tiene acceso a los autenticación via web tokens y por ende tiene disponible también los métodos para indagar permisos y roles.

Es imporante notar que dado que el *AuthController* o sea el componente responsable de verificar que el usuario haya entregado credenciales (autenticación) y sean correctas (autorización) está versionado entonces es necesario explicitar la versión de api a fin de que encuentre la clase.

Ej:

    class DumbAuthController extends ResourceController
    {
        function __construct()
        {
            global $api_version;
            $api_version = 'v1';

            parent::__construct();

            if (!acl()->hasAnyRole(['supervisor', 'admin'])){
                response()->error('Unauthorized!!!', 401);
            }
        }

        // ...

    }


### Folders

Sobre cada recurso se pueden crear espacios virtuales separados llamados "folders" a los cuales se les pueden establecer permisos para que otros usuarios los visualicen. 

Los folders no tienen nada que ver con el sistema de archivos sino que representan un conjunto de registros de una entidad particular sobre los que se pueden establecer permisos de forma unificada. 

Cada folder existe como un registro distinto en la tabla "folders" y se asocia con una determinada entidad (productos, usuarios, etc) y con un campo en esa entidad conteniendo un valor específico. Para cada endpoint se define el nombre del campo que se asocia al folder, ejemplo:


	class Products extends MyApiController
	{ 
	    protected $folder_field = 'workspace';

	    function __construct()
	    {       
	        parent::__construct();
	    }	        
	} 

El campo $folder_field almacena el nombre del campo que en el ejemplo es "workspace".

Para acceder a un folder se especifica el id del folder y otros usuarios pueden entonces listar o visualizar recursos que se le hayan compartido.

	GET /api/v1/v1/products?folder=1

Por supuesto pueden aplicarse otros filtros:

	GET /api/v1/products?folder=1&cost=200

Y puede visualizarse un registro en particular (ej: 124) para el que no tendríamos permiso si no especificamos el folder:

	GET /api/v1/products/124?folder=1		

<-- si el folder se nos ha "compartido" por medio de permisos y no se especifica entonces el registro no se hallará devolviendo 404 (Not Found).

Un usuario con rol de administrador en principio obtendrá todos los registros para un endpoint incluidos los que pertenecen a folders privados de otros usuarios:

	GET /api/v1/products

<-- obtiene todos los registros indiscriminadamente

Sin embargo también puede filtrar a un folder en particular:

	GET /api/v1/products?folder=57

Los permisos para los folders se conceden creando entradas en la tabla folder_permissions y es importante notar que debe darse explícitamente permiso al owner (así como a los otros usuarios) para que éste ver registros dentro de ese folder.

Obviamente cada usuario puede listar, editar o borrar sus folders usando el endpoint /api/v1/folders

Igualmente cada usuario puede hacer CRUD sobre los permisos de "grupo" y para "otros" a través de sus respectivos endpoints /api/v1/FolderPermissions y /api/v1/FolderOtherPermissions respectivamente de modo de permitir a otros miembros acceso de lectura y/o escritura de sus registros.

Para crear un registro en un folder del que se ha concedido permiso de escritura se incluye como campo el id del "folder". Ej:

	POST /api/v1/products

	{
		"name": "Supreme jugo",
	    "description": "de manzanas exprimidas",
	    "size": "1L",
	    "cost": "250",
	    "folder": "8"
	}

O bien se especifica en el "campo clave" que hace identifica al folder, en nuestro caso llamado "workspace" con el valor que corresponda para el folder:

	{
		"name": "Supreme jugo",
	    "description": "de manzanas exprimidas",
	    "size": "1L",
	    "cost": "250",
	    "workspace": "lista10"
	}

En el primer caso, si se especifica un folder pero no tenemos acceso recibiremos un mensaje de error como:

	{
	    "error": "You have not permission for the folder 8"
	}

En el segundo caso donde especificamos "workspace": "lista10" en vez de "folder": "8", si el folder no existe no habrá advertencia alguna pues solo estamos creando un registro con esa combinación de campos y distintos usuarios pueden tener folders con el mismo nombre así que no hay problema.

Es importante entender que cuando creamos un registro dentro de un folder que no nos pertenece (porque se nos da permiso de escritura), el registro tampoco será de nuestra propiedad aunque podremos leerlo y escribirlo siempre que tengamos los permisos para ello.

Igualmente para modificar un registro de otro usuario que nos ha compartido su folder especificamos el id del folder:

	PUT /api/v1/products/136

	{
	    "name": "Vodka venezolano",
	    "description": "de Vzla",
	    "size": "1L",
	    "cost": "15",
	    "folder": "1"
	}

Mismo para borrar un registro perteneciente a un folder es necesario estar "dentro" haciendo referencia al folder en cuestión:

	DELETE /api/v1/products/136

	{
    	"folder": "1"
	}


Nota: el acceso a los folders se chequea en base de datos cada vez que se hace un request especificando que el recurso se halla en un folder. No es necesario esperar a que se renueven los tokens para tener acceso a un folder al cual se nos ha concedido permisos. <-- podría cambiarse para incrementar la performance !!!

Similarmente a lo que sucede con Model, la clase ApiController también aporta event hooks en particular para los folders los siguientes:


    public function onGettingFolderBeforeCheck($id, $folder){ } 
    public function onGettingFolderAfterCheck($id, $folder){ }
    public function onGotFolder($id, $total, $folder){ }

    public function onDeletingFolderBeforeCheck($id, $folder){ }
    public function onDeletingFolderAfterCheck($id, $folder){ }
    public function onDeletedFolder($id, $affected, $folder)

    public function onPostingFolderBeforeCheck($id, $data, $folder){ }
    public function onPostingFolderAfterCheck($id, $data, $folder){ }
    public function onPostFolder($id, $data, $folder){ }

    public function onPuttingFolderBeforeCheck($id, $data, $folder){ }
    public function onPuttingFolderAfterCheck($id, $data, $folder){ }
    public function onPutFolder($id, $data, $folder, $affected){ }
       

Desde cualquiera de esos métodos es obviamente posible acceder a métodos y propiedades de visibilidad por lo menos protected de la clase Model y en particular a folder y id (del registro). Ej:

	function onGettingFolderBeforeCheck($id, $folder) {
        echo "Reading folder {$folder} with id={$id}";
    }

Un uso práctico de estos hooks sería con onGettingFolderBeforeCheck() implementar la funcionalidad de conceder acceso a un folder siguiendo un enlace que como parámetro puede tener un token. Si el token es válido se concede el acceso al usuario que sigue el enlace insertando el permiso correspondiente en la tabla folder_permissions.

Ej: <pseudocódigo>

	function onGettingFolderBeforeCheck() {

        if ($this->isGuest()){
            // Informar que debe estar "logueado"
            return;
        }

        if ($this->isAdmin()){
            return;
        }

        $token = \Boctulus\Simplerest\Libs\Factory::request()->getQuery('token');
    
        // decodificar token y si es válido proseguir
        
        $uid = $this->auth['uid'];

        // insertar en la tabla folder_permissions el permiso para el usuario con id $uid`
        // y el folder  $folder
    }
