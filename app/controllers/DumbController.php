<?php

namespace simplerest\controllers;

use Boctulus\ApiClient\ApiClientFallback;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;

use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use MakeCommand;
use PhpParser\Node\Scalar\MagicConst\File;
use simplerest\controllers\api\Products;
use simplerest\controllers\api\TblPersona;
use simplerest\core\api\v1\ApiController;
use simplerest\core\Container;
use simplerest\core\controllers\Controller;
;
use simplerest\core\libs\ApacheWebServer;

use simplerest\core\libs\ApiClient;
//use GuzzleHttp\Client;
//use Guzzle\Http\Message\Request;
//use Symfony\Component\Uid\Uuid;
use simplerest\core\libs\Arrays;
use simplerest\core\libs\Base64Uploader;
use simplerest\core\libs\Cache;
use simplerest\core\libs\ChatGPT;

use simplerest\core\libs\ClaudeAI;
use simplerest\core\libs\CMS_Scanner\CMSs\WordPress;
use simplerest\core\libs\CMS_Scanner\Scanner as CMSScanner;

use simplerest\core\libs\code_cleaner\AngularCleaner;
use simplerest\core\libs\code_cleaner\BootstrapCleaner;

use simplerest\core\libs\Config;

use simplerest\core\libs\Cookie;
use simplerest\core\libs\CookieJar;
use simplerest\core\libs\CronJobMananger;
use simplerest\core\libs\CSS;
use simplerest\core\libs\CSSUtils;;

use simplerest\core\libs\DatabaseBackup;
use simplerest\core\libs\Date;

use simplerest\core\libs\DB;

use simplerest\core\libs\DBCache;

use simplerest\core\libs\DomCrawler;
use simplerest\core\libs\EasyHTMLTable;

use simplerest\core\libs\EmailTemplate;
use simplerest\core\libs\Env;
use simplerest\core\libs\Factory;
use simplerest\core\libs\FileCache;

use simplerest\core\libs\FileMemoization;

use simplerest\core\libs\FileMemoizationV2;
use simplerest\core\libs\Files;

use simplerest\core\libs\FileUploader;
use simplerest\core\libs\GitHub;
use simplerest\core\libs\GoogleDrive;
use simplerest\core\libs\GoogleMaps;
use simplerest\core\libs\GrokAI;
use simplerest\core\libs\Hardware;
use simplerest\core\libs\HTML as HTMLTools;

use simplerest\core\libs\HtmlBuilder\Bt5Form;
use simplerest\core\libs\HtmlBuilder\Form;
use simplerest\core\libs\HtmlBuilder\Html;

use simplerest\core\libs\HtmlBuilder\Tag;
use simplerest\core\libs\i18n\AlternativeGetTextTranslator;
use simplerest\core\libs\i18n\POParser;
use simplerest\core\libs\i18n\Translate;
use simplerest\core\libs\InMemoryCache;
use simplerest\core\libs\JobQueue;
use simplerest\core\libs\LangDetector;

use simplerest\core\libs\Logger;
use simplerest\core\libs\Mail;
use simplerest\core\libs\MailFromRemoteWP;
use simplerest\core\libs\MediaType;
use simplerest\core\libs\Memoization;
use simplerest\core\libs\Messurements;
use simplerest\core\libs\Obfuscator;
use simplerest\core\libs\Paginator;
use simplerest\core\libs\Parallex;
use simplerest\core\libs\PHPLexicalAnalyzer;
use simplerest\core\libs\PostmanGenerator;
use simplerest\core\libs\RandomGenerator;
use simplerest\core\libs\Reflector;
use simplerest\core\libs\Schema;

use simplerest\core\libs\SendinBlue;
use simplerest\core\libs\SimpleCrypt;
use simplerest\core\libs\StdOut;
use simplerest\core\libs\StratoScraper;
use simplerest\core\libs\Strings;
use simplerest\core\libs\System;
use simplerest\core\libs\Task;
use simplerest\core\libs\Time;
use simplerest\core\libs\Update;
use simplerest\core\libs\Url;
use simplerest\core\libs\Utils;
use simplerest\core\libs\ValidationRules;
use simplerest\core\libs\Validator;
use simplerest\core\libs\VarDump;
use simplerest\core\libs\WooCommerceApiClient;
use simplerest\core\libs\XML;
use simplerest\core\libs\ZipManager;
use simplerest\core\Model;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\Route;
use simplerest\core\View;
use simplerest\libs\ArbitrageCalculator;
use simplerest\libs\Cake;
use simplerest\libs\Foo2;
use simplerest\libs\Foo;
use simplerest\libs\HaulmerSignatureSDK;
use simplerest\libs\Ingredient1;
use simplerest\libs\Ingredient2;
use simplerest\libs\ItalianGrammarAnalyzer;
use simplerest\libs\ItalianReviews;
use simplerest\libs\LaravelApiGenerator;
use simplerest\libs\NITColombiaValidator;
use simplerest\libs\Reviews;
use simplerest\libs\RibiSOAP;
use simplerest\libs\scrapers\AmazonScraper;
use simplerest\libs\scrapers\Curiosite;
use simplerest\libs\scrapers\LeroyMerlinScraper;
use simplerest\libs\scrapers\MaisonsScraper;
use simplerest\libs\Sync;
use simplerest\models\az\AutomovilesModel;
use simplerest\models\az\BarModel;
use simplerest\models\az\ProductsModel;
use simplerest\shortcodes\ciudades_cl\CiudadesCLShortcode;
use simplerest\shortcodes\countdown\CountDownShortcode;
use simplerest\shortcodes\csv_importer\ImporterShortcode;
use simplerest\shortcodes\eat_leaf\EatLeaf;
use simplerest\shortcodes\progress_bar\ProgressShortcode;
use simplerest\shortcodes\star_rating\StarRatingShortcode;
use Spatie\ArrayToXml\ArrayToXml;
use stdClass;
use Symfony\Component\DomCrawler\Crawler;

class DumbController extends Controller
{
    function __construct()
    {
        parent::__construct();
        //DB::getConnection('az');
    }

    function index()
    {
        dd(System::getMemoryLimit(), 'Memory limit');
        dd(System::getMemoryUsage(), 'Memory usage');
        dd(System::getMemoryUsage(true), 'Memory usage (real)');

        dd(System::getMemoryPeakUsage(), 'Memory peak usage');
        dd(System::getMemoryPeakUsage(true), 'Memory peak usage (real)');
    }

    function phpinfo()
    {
        phpinfo();
    }

    function info()
    {
        phpinfo();
    }

    function get_rand_str()
    {
        $len = 6770;

        return Strings::randomString($len);
    }

    function testy()
    {
        dd(
            Strings::parseCurrency('son EUR 108.000,40 a pagar', '.', ',')
        );
    }

    function testx()
    {
        DB::getConnection();

        dd(
            DB::driver()
        );
    }

    function test_get_table_prefix(){
        DB::getConnection('woo3');

        // wp_
        dd(
            DB::getTablePrefix()
        );
    }

    static function get_url_slugs()
    {
        $url = "http://127.0.0.1:8889/api/xxx/777/";

        dd(
            Url::getSlugs($url)
        );
    }

    static function get_rand_hex()
    {
        return Strings::randomHexaString(6);
    }

    function test_db()
    {
        // DB::getConnection('mpp');

        dd(
            DB::getTableNames()
        );
    }

    function test_remove_sp()
    {
        $str = "		array (
              'ID_COC' => 
              array (
                'type' => 'int',
                'min' => 0,
              ),
              'COC_NOMBRE' => 
              array (
                'type' => 'str',
                'max' => 60,
                'required' => true,
              ),
              'COC_BORRADO' => 
              array (
                'type' => 'bool',
              ),
              'created_at' => 
              array (
                'type' => 'timestamp',
              ),
              'updated_at' => 
              array (
                'type' => 'timestamp',
              ),
            );";

        echo Strings::trimMultiline($str) . PHP_EOL;
    }

    function log()
    {
        Logger::log('zzzzzzzzzz');

        $subscription = (new stdClass());
        $subscription->name = "Pablo";
        $subscription->apel = "Bzz";

        /*
            [11-Feb-2024 16:15:45 Asia/Manila] {"name":"Pablo","apel":"Bzz"}
        */
        Logger::log($subscription, 'sub.txt');

        /*
            (object) array(
                'name' => 'Pablo',
                'apel' => 'Bzz',
            )
        */
        Files::dump($subscription, 'dump_s.php');

        /*
            <?php 

            return (object) array(
                'name' => 'Pablo',
                'apel' => 'Bzz',
            );
        */
        Logger::varExport($subscription);

        dd("Test");
    }

    function dd()
    {
        VarDump::log();
        VarDump::showTrace();
        // VarDump::hideResponse();

        dd([4, 5, 7], "My Array", true);
        dd('hola!', null, true);
        dd(677.55, 'x');
        dd(true, 'My bool');

        // titulo al final
        dd([4, 5, 7], "My Array", true, false);
    }

    function at()
    {
        return at();
    }

    function now()
    {
        return at();
    }

    function now_json()
    {
        return [
            'now' => at()
        ];
    }

    function test_apiclient_cache()
    {
        $url    = base_url() . '/dumb/now';

        $client = new ApiClient($url);

        $res = $client->disableSSL()
            ->followLocations()
            ->cache(5)
            ->get()
            ->getResponse(false);

        if ($res === null) {
            dd("RES is NULL");
            return;
        }

        if ($res['http_code'] != 200) {
            dd("HTTP CODE is " . $res['http_code']);
            return;
        }

        $html = $res['data'];

        dd([
            'realtime' => file_get_contents($url),
            'cached'   => $html
        ]);
    }

    function test_apiclient_cache_until()
    {
        $url    = base_url() . '/dumb/now';

        $client = new ApiClient($url);

        $res = $client->disableSSL()
            ->followLocations()
            //->clearCache()
            ->cacheUntil('9:56')
            ->get()
            ->getResponse(false);

        dd(
            $client->getCachePath(),
            'CACHE PATH'
        );

        if ($res === null) {
            return;
        }

        if ($res['http_code'] != 200) {
            return;
        }

        $html = $res['data'];

        dd([
            'realtime' => file_get_contents($url),
            'cached'   => $html
        ]);
    }


    function test_view_cache()
    {
        view('random', null, null, 10);
    }

    function test_logger()
    {
        // [30-Nov-2023 18:45:05 Asia/Manila] Hola Mundo
        Logger::log('Holaaa mundo');

        // R.I.P.
        Logger::log('R.I.P.', null, null, false);

        // [30-Nov-2023 18:46:33 Asia/Manila] {"x":"0"}
        Logger::log([
            'x' => '0'
        ]);

        // {"x":"1"}
        Logger::log([
            'x' => '1'
        ], null, null, false);

        // [30-Nov-2023 18:46:33 Asia/Manila] {"job_id":45}
        Logger::dd(45, 'job_id');

        /*
            Utiliza la ruta y en este caso lo guarda en /etc/some_file.txt
        */
        Logger::log([
            'x' => 'y'
        ], ETC_PATH . 'some_file.txt');
    }

    function test_truncate()
    {
        Logger::truncate();

        dd(Logger::getContent(), Logger::getLogFilename(true), true, false);
    }

    function test_fsockopen()
    {
    }

    /*
        Si esta cerrado el puerto 443 puede demorar demasiado en contestar
    */
    function test_ssl()
    {
        dd(Url::hasSSL('google.com'));
        dd(Url::hasSSL('woo1.lan'));
        dd(Url::hasSSL('simplerest.lan'));
    }

    /*
        Probar en un contenedor con SSL
    */
    function has_ssl()
    {
        dd(
            Url::isSSL()
        );
    }

    function test_php_operators()
    {
        // ...
    }

    /*
        https://www.programmerall.com/article/553939151/
    */
    function test_queue()
    {
        $queue = new \SplQueue();

        //$queue->setIteratorMode(\SplDoublyLinkedList::IT_MODE_FIFO | \SplDoublyLinkedList::IT_MODE_DELETE);

        $queue->enqueue('a');
        $queue->enqueue('b');
        $queue->enqueue('c');

        // shift
        dd($queue->dequeue());

        // lista elementos
        foreach ($queue as $item) {
            dd($item);
        }

        //dd($queue);
    }

    function test_find()
    {
        $res = DB::table('users')
            ->find(9)
            ->getOne();

        dd($res);
    }

    // ok
    function test504()
    {
        DB::getConnection('az');

        $vals = DB::table('products')
            ->setFetchMode('COLUMN')
            ->selectRaw('cost * 1.05 as cost_after_inc')->get();

        dd($vals);
        dd(DB::getLog());
    }

    // fails en PSQL
    function test505()
    {
        $vals = DB::table('products')
            ->setFetchMode('COLUMN')
            ->selectRaw('cost * ? as cost_after_inc', [1.05])->get();

        dd($vals);
        dd(DB::getLog());
    }

    function test505b()
    {
        $m = DB::table('products');

        $vals = $m
            ->setFetchMode('COLUMN')
            ->selectRaw('cost * ? as cost_after_inc', [1.05])->get();

        dd($vals);
        dd($m->debug());
    }

    function test506()
    {
        $con = DB::getConnection();
        $sth = $con->prepare('SELECT cost * ? as cost_after_inc FROM products');

        $sth->bindValue(1, 1.05, \PDO::PARAM_INT);
        $sth->execute();

        $res = $sth->fetch();
        dd($res);
    }


    function test507()
    {
        //dd(is_float('1.25'));
        //exit;

        $con = DB::getConnection();
        $sth = $con->prepare('SELECT cost * CAST(? AS DOUBLE PRECISION) as cost_after_inc FROM products');

        $sth->bindValue(1, 1.05, \PDO::PARAM_INT);

        $sth->execute(); // ok

        $res = $sth->fetch();
        dd($res);
    }

    function test_having_raw(){
        $res = table('orders')
        ->dontExec()        
		->havingRaw('COUNT(id) > ?', [5])
		->havingRaw('SUM(amount) > ?', [1000], 'OR')
		->dd();

		// Generaría algo como:
		// ... HAVING (COUNT(id) > ?) OR (SUM(amount) > ?)

        dd($res);
    }


    /*
        Falla en POSTGRES:

        SELECT COUNT(*) as c, name FROM products GROUP BY name HAVING COUNT(*) > 3

        Es como si en pgsql se evaluara primero el HAVING y luego el SELECT.
    */
    function alias()
    {
        $rows = DB::table('products')
            ->deleted()
            ->groupBy(['name'])
            ->having(['c', 3, '>'])
            ->select(['name'])
            ->selectRaw('COUNT(*) as c')
            ->dontExec()
            ->get();

        dd(DB::getLog());
    }


    /*
        Corrige problema
    */
    function alias2()
    {
        $rows = DB::table('products')
            ->deleted()
            ->groupBy(['name'])
            ->select(['name'])
            ->selectRaw('COUNT(*) as c')
            ->havingRaw('c > ?', [3])
            ->get();

        dd($rows);
        dd(DB::getLog());
    }

    function add($a, $b)
    {
        var_dump($a);  // string

        $res = (int) $a + (int) $b;
        return  "$a + $b = " . $res;
    }

    function mul()
    {
        $req = Request::getInstance();
        $res = (int) $req[0] * (int) $req[1];
        return "$req[0] * $req[1] = " . $res . PHP_EOL;
    }

    function div()
    {
        $ch = request();
        $res = $ch->getParam(0) / $ch->getParam(1);

        //dd($res);
        //
        // hacer un return en vez de un "echo" me habilita a manipular
        // la "respuesta", conviertiendola a JSON por ejemplo 
        //

        return [
            'result' => $res
        ];
    }

    function inc($val)
    {
        $res = (float) $val + 1;
        response()->send($res);
    }

    function inc2($val)
    {
        $res = (float) $val + 1;
        return $res;
    }

    function inc3($val)
    {
        $res = (float) $val + 1;
        response($res);
    }

    function login()
    {
        view('login.php');
    }

    function casa_cambio()
    {
        view('casa_cambio/home.htm', null, 'casa_cambio/layout.php');
    }

    function random_res()
    {
        return rand(1, 9999);
    }

    function no_processing()
    {
        $str = <<<STR
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">

            <title></title>

            <base href="http://simplerest.lan">

            <!-- ico -->
            <link rel="shortcut icon" href="http://simplerest.lan/public/assets/img/favicon.ico" />

            <!-- google fonts 

                For download 
                https://github.com/majodev/google-webfonts-helper
            -->
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

            <script>
                const base_url  = 'http://simplerest.lan';
            </script>

            
            <script>
                let \$__email    = 'email'; 
                let \$__username = 'username';
                let \$__password = 'password';
            </script><meta name="content-type" content="text/html; charset=utf-8">

            <script src="http://simplerest.lan/public/assets/js/login.js"></script>

            <!-- ICONOS FONTAWESOME -->
            <script src="https://kit.fontawesome.com/3f60db90e4.js" crossorigin="anonymous"></script>
            
            <!-- TEMPLATE ADMIN LTE -->

            <!-- Google Font: Source Sans Pro -->
            <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
            
            <!-- Font Awesome -->
             <link rel="stylesheet" href="<?= asset('third_party/adminlte/plugins/fontawesome-free/css/all.min.css?v=6.2') ?>">
            
            <!-- Ionicons -->
            <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
            <!-- Tempusdominus Bootstrap 4 -->

            <!-- bootstrap 5.1.3 solo css -->
            <link rel="stylesheet" href="http://simplerest.lan/public/assets/css/bootstrap.min.css">
            
            <!-- iCheck -->
            <link rel="stylesheet" href="http://simplerest.lan/public/assets/third_party/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
            <!-- JQVMap -->
            <link rel="stylesheet" href="http://simplerest.lan/public/assets/third_party/adminlte/plugins/jqvmap/jqvmap.min.css">
        
            <!-- overlayScrollbars -->
            <link rel="stylesheet" href="http://simplerest.lan/public/assets/third_party/adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css ">
            <!-- Daterange picker -->
            <link rel="stylesheet" href="http://simplerest.lan/public/assets/third_party/adminlte/plugins/daterangepicker/daterangepicker.css">
            <!-- summernote -->
            <link rel="stylesheet" href="http://simplerest.lan/public/assets/third_party/adminlte/plugins/summernote/summernote-bs4.min.css">

        
            <!-- Datatables -->
            <link rel="stylesheet" href="http://simplerest.lan/public/assets/css/lib/datatables-net/datatables.min.css">
            <link rel="stylesheet" href="http://simplerest.lan/public/assets/css/lib/datatables-net/datatables-net.min.css">

            <!-- jQuery -->
            <script src="http://simplerest.lan/public/assets/third_party/adminlte/plugins/jquery/jquery.min.js"></script>
            
            <!-- JavaScript Bundle with Popper -->
            <script src="http://simplerest.lan/public/assets/js/bootstrap.bundle.min.js"></script>

            <!-- FILEPOND -->
            <!--link rel="stylesheet" href="... 'js/plugins/filepond/dist/filepond.css') ?>"-->


            <!-- Select2 -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.3/dist/css/select2.min.css" />
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.2.0/dist/select2-bootstrap-5-theme.min.css" />

            <!-- DualListbox -->
            <link rel="stylesheet" href="http://simplerest.lan/public/assets/third_party/adminlte/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.css"/>
            <script src="http://simplerest.lan/public/assets/third_party/adminlte/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>

            <!-- InputMask -->
            <script src="http://simplerest.lan/public/assets/third_party/adminlte/plugins/moment/moment.min.js"></script>
            <script src="http://simplerest.lan/public/assets/third_party/adminlte/plugins/inputmask/jquery.inputmask.min.js"></script>

            <!-- date-range-picker -->
            <link rel="stylesheet" href="http://simplerest.lan/public/assets/third_party/adminlte/plugins/daterangepicker/daterangepicker.css"/>
            <script src="http://simplerest.lan/public/assets/third_party/adminlte/plugins/daterangepicker/daterangepicker.js"></script>
            

            <link rel="stylesheet" href="http://simplerest.lan/public/assets/css/main.css"/>

        </head>
        <body>
            <div class="container-fluid">
                <nav>
                    <script>
                        if (logged()){
                            console.log("[x] Cerrar session");
                        } else {
                            console.log("[>] Login");
                        }
                    </script>
                </nav>
            
                <main>
                <h1>Test Assets</h1>

        <img src="http://simplerest.lan/public/assets/img/avatar.png" />        </main>
            </div>
            

            <!-- jQuery UI 1.11.4 -->
            <!--script src="< ?= asset('third_party/adminlte/plugins/jquery-ui/jquery-ui.min.js') ?>"></script-->

            <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
            <script>
                //$.widget.bridge('uibutton', $.ui.button)
            </script>

            <!-- ChartJS -->
            <script src="http://simplerest.lan/public/assets/third_party/adminlte/plugins/chart.js/Chart.min.js"></script>
            
            <!-- Sparkline -->
            <script src="http://simplerest.lan/public/assets/third_party/adminlte/plugins/sparklines/sparkline.js"></script>
            
            <!-- JQVMap -->
            <script src="http://simplerest.lan/public/assets/third_party/adminlte/plugins/jqvmap/jquery.vmap.min.js"></script>
            <script src="http://simplerest.lan/public/assets/third_party/adminlte/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
            
            <!-- jQuery Knob Chart -->
            <script src="http://simplerest.lan/public/assets/third_party/adminlte/plugins/jquery-knob/jquery.knob.min.js"></script>
            
            <!-- daterangepicker -->
            <script src="http://simplerest.lan/public/assets/third_party/adminlte/plugins/moment/moment.min.js"></script>
            <script src="http://simplerest.lan/public/assets/third_party/adminlte/plugins/daterangepicker/daterangepicker.js"></script>
            
            <!-- Tempusdominus Bootstrap 4 -->
            <script src="http://simplerest.lan/public/assets/third_party/adminlte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
            
            <!-- Summernote -->
            <script src="http://simplerest.lan/public/assets/third_party/adminlte/plugins/summernote/summernote-bs4.min.js"></script>
            
            <!-- overlayScrollbars -->
            <script src="http://simplerest.lan/public/assets/third_party/adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>

            <!-- Select2 -->
            <script src="https://cdn.jsdelivr.net/npm/select2@4.0.3/dist/js/select2.min.js"></script>

            
            <script src="http://simplerest.lan/public/assets/js/boostrap/boostrap_notices.js"></script>

            <footer id="footer">
                    
                    </footer>
        </body>
        </html>
        STR;

        return strlen($str);
    }

    function just_string()
    {
        return 'TUpHG 0VQ IImwRMTLBAHhL4QoJeUTy sJjqb6hI1dpArfm NP Rtv2qYLbiE wtSwO0 skm2 dpgpPXpNJrJIFhDhf8zl7O8C5bCcX 6 gMiU OgpXYZyqhyWdFnQq6BbYpZD1M6MuAPLQvMHBHkyX1boS4 h2K2vS7XAVGy7AM y6f5xA JwAd3CE91no eLzmeZ7lonHm cPUZb4Iv08 XCCoDhJRfzEoJI989Urr44l5z amFKv4UMlRZJDdHMNisj2srm5t8t k79tl7Kcl1IWSzP mWzrbGzrgOS JSBpQFnM2uaoM Gbwekga6ZEUkmsEQKOYOVA LpQcQCooMLTX93a0wu2i5uGLituy p BnDJt n3mUskFs Meh73Os xpno2SueX 5OIrQvFJVJc7DRqeoxd0UwvXeKHlK9hr9QZ7xY PP Hq 9yhsVPcDLayajQv3ZSU ty3aBlY5wfWGb 917NRDXDQX GZ1Em4u4X2Aftqvmb jUG8pKZgzHc4XGj YS3pztrFijhoCQyE wkcH1V hk2AwTLlTrZgjohtiSArDjzrrxZcdzBgBmaW2eF fInWvEQdknXr61iX5MNZKK6K DzOfQekZC16EBMyYdc6iFR9tpp lOGPEWBIZcj1xsTcM4 6 8ll 2k 09 AIqm VnbmEbsXSvgcVvY8 9 O4ks 2ZoBqxps rW5Q i L ps ubgdh QFtisxQ G mzrJoNUbc Yu8ljmhT1t8moYsTK bEsMLSdPASD0Ny8g znJqAzbYhzmr i3tKcCYOpC yD Ur8n 1YhSrHjws395XjVl1oTbiy 4BQDC zLwk68541sAsC Z YCOoHZ dnr AQ ED8sYbA5Zn44J158vzOd9w iBf Sc9Gfl ARgR4pyX 7LTRwA6WbEauIKTwTk0Nuz9 F63OQ17g bJpVNetO9a RvRTDoG1bESc3Kt7o6Rvldiz 8 j0KJEsquQ70rxjNvHjUnQY4jJ 8XkIMwUNMgGdBXUxuNUK pde rktHya2MXz Yf QcXc 59ui UtNHV unXhsBg 5msGND wgBh E45DcsnMBU1 NsjTvD bs7GVYxzjHHd3tAaTKIp zZu6Lt yz 78uFB2Gilw7mB WHfuF6UCneNn0xgMk8llV8xsH6QlOAkf6dZcA7MmEtZ jZI5WWESJeM Fkv8CLmqBB6KQRzpaGLetlbD2Mz08o3LtktLjxJ2YeYqlkzbd 7RV0 ln09vCoeqMJMuxpvS0JsBJ5fVSk j9XztsOLhhtHBcYxLoH5oeKqMpX KZ5aBjzR Q 8RbfuGmeytn1Ec8R MCSEsMgV9Q Y3KWBPE1aRxpZL a7vTGV7j8YgG 2I hiXRKp8KhPsmUpe C Nm2LIJ VIZVhp5Q3X6gxHZ W9nj2m E9ycdmmO5X6G6nUFL0gK38sAuRTSaXuyl5 a vzEHruQ0gd40l Mnh89IOR ZTZMbV1kZdQDJ Y1XVoDahIITDd90jiaLYIAxmuDOGFRDuMtQpqWmG6Dkkc2 hLULNsvHgQz4dLkN21H7cg Hh116 H3ytpPx olFit3J1Vo 3yz yoHiv vbbe7gUhOOtB8i0IRwo4Vpzlof5HmD92DP7A F zUaPnYdCmNngwDw lZx 9Lj4m57WIdO9W5YenJBws M rDZ mYnTNLv 1psP BK0TlBCPOLt0 KCJTMyuJ6BP9iOgVCokVUk69 muUqyBeL0HFdf5 a DTbUhKLvPL 8MW4rEirgiDgcIPy6PVcAaVIIEeLZjQ qpFb Y w9oBZW odft0vFwSF4mfnsh1WUm6Wb7DR7NEivWcPyKTodhvJ7dt68 01z0Nxyr snMZXcxScsOJ48RwqZZt A Wb6ayXK tkiSwHn8aNX2Agkx Pa3W hb318xTlKWtsfSU hVMCU xf VEAp4nBASSN1u abIymJ QOu OE eCxP VVlESNgaz 5CNU7AkQbZMCKLCZNEd5D Od7ECTE3 0BXrMZP EjK HtoLhjhTjZ 3SnbDzy80v2cFw5JB HZK nqm HxYdkhL2y53EZkgSACO4jrKlhXQHFyKuSx2lGpHQrCtm5DF dHeD58Wiq Sm 33d mG o6B0cYGNtJDBvtBFF2e alu2B3oje0nB8lv4rucsePbojxREM g90lrvu0YD6jP2yZw4bmmdTK z y eyc roq jtP2nF45R aa7VSw5g yWGP7zXxwVlEVAnC uQC0y2HJc Di oeVX1CvDQheJ7ES MWbj05eaMSw0wxlajiF kbrU1sVxVH 2J0dvtYz6rtHSbpm9SKQscHH4uF iEkLOFt 6pWNrQ5OHhNhqlI0TBcQ eSQkettYFqDw3nBW7CO7bdl7qxk2j4NvFlHB94OKCxmb99c 9 fWb YEcWb7 nRwcr RDkXrDXSYGxEp ZTNCkjf2m3yzMxu5ykxx1BfK9tS 87e JY q JpJfV0RovaaiDqavPpXexebq8 Bjuv A kvLyDoze0 pzg7aR25IDJcPHE6ISTbIeXN aSY6VwhY1 3Jo nE54N8ke5If MoPEXD0 n2AfIqPz8RiZIsUY Iw X 3mUvPQURWKJOCTEmtMbF NKmJ2n7aIpZ OzD1O q3ihRbgAspM IPV8XnahRlo y3 xWgrRffbfpMX SA k aN9G5XO 4Cpllpo q6FsL YcxyCQYQwAj3E4SR0LdpyAIboBWcMdzQBfCK J eVhlF0uMZV3kIM7 0D R Zhp1BWs1u QU YEfPgbnN3z4RYke3q3w758GMROl2q JrAUQ zIP6ppe62SAqx7DK1EfuKmIPafv7gix10aHO66 f2gHuN5JpygND Vk8vrf8wLAsu TjQG MKMXIVtdSqdjKkzqjaUNicg2CSaQbk5Rgq11YTY UN9I4eIyg5A9wWgQmhcQ2YodnoRoPShICCOrTDVwuFZaXGN p4 DGUhgSnRhvwI QtLHiqpc Se eI 3708 lAU23Q3kUjraJyIkVqbKWBKB6dHpIrUPseBSFY Obnojqmx OzSYClU2AJudV wIqXr vJp8lNrpkBmTGEPg7Bjhfd bN YyDt6KNzn 9 8uOouaRuXRFiBbze366K3Skyj7s8pegG y9aWMjbOeHiG4pZKLUaj79HIe01JUE dc9ABbpHG G7JU SD5Mx SiLgZbCgSypRy3uI Cd93BsAdqw fL814ScOqoi3GGi HfSP89PkRbtHBTbQept1i GQle nc J uy PirocOJqy7 DlNXFCULE 4 eSe dWMjWuQsHF pw Uo5qrnYcBMdEbbnvTeOQVo vP E582 m4jlR KWQKn4NUW3Byx71PuW22 TBR3ash UIIPwclAJrjci ZUw7f2LFrdD Xy6Z5kTlNqbnOQHH srPYljX8 lkWZmAXgXc8vNBGhwyYB8jJE9SRJ ji1LIkn7 0iJlFrPvJ9miPU wQ5ap1 V2CEPUGNdM01G2k3KZsnfPJBDA yf2VqhOc58kX3mLB Pow8MIqYTTJKfDvnIFkaF5ijd47o0g9O6EZxCR uKILC NpL9n576TrCQcvMZioAcqOYvWrZnL9N c Julcv5Kc SumY JCT7ur3V 8 DFU0D16JzsRcqX0L5ZHV4qUFGXCecubsN3 YLu1AYr 1a1IeV3haGwZxnjurDmo84Ro Z4Xt16DNXYrmZ1 zTG M5BYdkti3myNvUGQ0H6UTYDaOv5M Wn8kv5IaY94h2VQjAbMuogKXyvCYok8lNy9WQbmIsHLNuH9cxxrD60ek oOGVvN3q9ifEcL3 AZ4V9xqgESezwdcE6d4ATd xU1TkFqN08P8 Mm8I05JXBOVu 3F0dUF7NYVC2S HPxe1R3KlzpRtA 6Z Wnh1rGAGfSwjCsI8E 1ET r4cn9H0LPNv22KNtnsg2M I1lNfkM2xM7R0LS2Bgmp5EUMO76VVOMBFiwwEZzZpr1ZpOxj5uu1cQjKIN56rsTwHlntk88hccAfh37i4DFOtgWBdba 1U86ne3eSlLKq4rig FaaExTWxjMO C RyDj6tHXt9Cq FeJ8Oa 8bW0S7 4MhwjksWvA4 jWb6e1 j q TNTl48VO7oNzKr4kZiBhG toCLqfdmhNPEjC2Aio stC08 WggDZA wjbq ZNvys3zp3WcTI A oj5VPC2N1Y LoFUyJ41 7o bdYET rzT d 2JLDPvQ1 5Wse SgdXi2 6cEP n4Oxg8oMtKQ4yUw8NBUBp78wLdyvU5BAkDsia7rtfEbVDYhN9 2f3cocYHGfof1YnaqvDqU7sx Nb1MjSuE7i ff oE y05KA9b9KfzDVF8 0eK31 VwbV7qpLb1SLOAcx R5hCIXc5lgDKvAtQ4W Cff0H uMu ntwvz9J kJ HZI6C1YFYVV1aLSz8dwI3d PmWOI0 AP4 eE9LipgTX96Lz0CzgMuQOG784qbisJeWZ6gqrd0h YiwL YdxaaUevSfBuI d FSQeEF7Tseq5ZiivecU UwBvXNpvvd bmoHS6ScHqW CT0Qn9aW 5Jn WNg3PG3lbRszbOk cL8VqyqKW Tu0fGu aQ Z wfM4usdv 1Y svLrSeguRCV7Lhw Lb818 qTJW7 TG7Vs To BFfB8jxgG HAFGYDrw7URihp uAC MEaKFffy A9r3w z12AygnKGjWdVoLpcDPX Cdq9FLdW3e HXU4dCtkiKtdV3p RXNy6 XciU2eaA UTxkAsT6C5jv8M 6kn4LW Uw c9Gco erDLVfR oUhWvCYhA2hh031LhgQls1xFwR uyTEKdbf k7yRhn3acgX q6hKL440xnFbdUfyZrVo7WU5QnyweG8TDW4S3vs G54wkv UtMZsHQ4pxlo JCPnn6mumakD6L1j0z0YIn6m41G TJp6z4WiW8 VRX FWX 2g yK9HJo faz kN8t63so3Qbu67 4IFGb2 CIGq2k KGIVMIcaHQPe p4Giaa6qBs CySsxsSj3YSm T R3Q1eWJulU QJ 3U4szk3t3ZrIA 0RchE rfBP4dGMvHKGWExxPGMdf6w5abq7x96tMqyAtoymXY JWK xNYXd quTmdjOhaUB2 gzUTOZ iTgnvEcxtXIpiMJoiPvYohxrm Pu pNQb2iiyNAVegkGOgQ4kmtuWgaaqq4DiffVi7T 7AlLfkxn I2P m9IhIj Bxk 00g4lKU Y2IH2oN68 jUrqR0G mZYW Gfya M7os6XKh f91DDU2tQ9fOmoPilYlR4nHdO qffCqk2ekbbdAR94L0ldaHdcUT Z5k6dfCi E9w8P o NWjnftx0nPYwFhLYtly FoyZ0I5iIlQTG2 yW D PMQwBhA66IELOvfXKmpSPATCk mRn84C9fZPB6mFRc NPDx Oe YMgNHEAZFgG3bgKf9uNsL5GSTgiTL1SK3FZAhvlbM MVB6z8xiP4tsCEjZCFVc2XVG87NbhqiVanwxT7pWIZrvjcR0sO4tTB0ndHlNgi4 Py5CFud5F14 ycxaut1g5Qi89XsStvNtYf5nx10Ju9LonA v3axQFEhfh n1 JMG01zIGjE wPW zV2gsFgUpy96jfM4P MvDE feyOO53pDTk7EP7lXpWNTj4O7R95 6iUFATFFeIamDAqNuT15 2wsG 3EEzGrNZryza4P9 iRvz2sSC4fHCeJd5YYFnHuX4T0Ojc V9q0XsThlM P6jETugK5qJyaek vyT3rIDI RilK GttttRRq29 tfrkn7Gvnl22R5fAOicrK3C 5gOb 4 RRXRrvP0nzGN6ezjB pk wmp hlOlgHTmjKOUlar zTJ yW17M0PV TPC3u2Cu rR oNixKW3HawtO0mZR0 V z z rJxB0lz FpfIM qzhKtfg2nfHlmTW663kSCWM7sjRM4BY06mOV C07 YgsLozKXFDsnKwOk Zr07lOApwPwpGW6H aivLG15LgiNhXrD7BWhwbuXxZ2a yOCQgX ehXjxfHlRE0dGO9eHiLV XpCWes8hQBHRzrv3dD5ot5e56Ws8yR XB hMCOnFmqfZF6kywzujRxuRXh9vtG 7uCGf x ztxIgZLs2S9D2gJZbwiT n2Ddxov zwOLCm1R CKawGaHKqOAX9jsbGSwT OtN2qkDfJiTp18NGhflJ9wN 9Kwd 8 KBemidxy o44p9xco7UWYkUHA zJrVQIkF BrNAF3 KQBfXQ QE2wRpdSVgSv9PmmjGrFJWZzzJQf LPdilJI2HcV1lNzWP6c7milzJn0SO0y 2url0OV UAhHFe9qMqXrxaj5xImQFv64CRF9neK9A5ES1rz wlHDTAaEPBaB 8x73 SzlrFeVqxEsUfuS3EGAZO pP8d33WEbwG3uZ SjOE5LtDpW FH J0FtwRe3X SIR0Yw2sx5h9pR6JYJg2Vmk qY4WU70ox3v1 NAPjLbVwXSYjPj7I lBB HC2eXhEXrJcrZt9zQO1ckKLE974q VHl A6ePlDCKPR5uqSywR6SWuaV1mFqUG Ta2UL u PqDF 5j 5ZigYhQIFUaVIP12dTz o8EOBzcGHh GBgpyWTQJ3ZxVFA';
    }

    /*
    function xyz(){
        DB::getConnection('db3');
        
        $curs = DB::table('countries')
        ->distinct(['currency'])->get();

        $groups = [];

        $rows = DB::table('countries')
        ->orderBy(['currency' => 'ASC'])
        ->get();

        foreach ($rows as $row){
            $groups[$row['currency']][] = $row;
        }

        $m2 = DB::table('currencies');
        $at = $m2->getAttr();

        //dd($groups);
        //exit;

        foreach ($groups as $curr_code => $g){
            $alpha2 = [];
            $alpha3 = [];
            $langCS = [];
            $langDE = [];
            $langEN = [];
            $langES = [];
            $langFR = [];
            $langIT = [];
            $langNL = [];
            foreach ($g as $c){
                $alpha2[] = $c['alpha2'];
                $alpha3[] = $c['alpha3'];
                $langCS[] = $c['langCS'];
                $langDE[] = $c['langDE'];
                $langEN[] = $c['langEN'];
                $langES[] = $c['langES'];
                $langFR[] = $c['langFR'];
                $langIT[] = $c['langIT'];
                $langNL[] = $c['langNL'];
                //dd($country);
            }

            $alpha2_main = count($alpha2) == 1 ? $alpha2[0] : NULL;
            $alpha3_main = count($alpha3) == 1 ? $alpha3[0] : NULL;

            $alpha2 = json_encode($alpha2);
            $alpha3 = json_encode($alpha3);
            $langCS = json_encode($langCS);
            $langDE = json_encode($langDE);
            $langEN = json_encode($langEN);
            $langES = json_encode($langES);
            $langFR = json_encode($langFR);
            $langIT = json_encode($langIT);
            $langNL = json_encode($langNL);

         
            $data = array_combine($at, [$alpha2_main, $alpha3_main, $alpha2, $alpha3, $langCS, $langDE, $langEN, $langES, $langFR, $langIT, $langNL, $curr_code]);
            
            //exit;
            $m2->create($data);        
        }

        //dd($not_grouped);
        //dd($groups);
        //dd($alpha2);
        //dd(count($groups));
    }
    */

    /*
    function mul(Request $req){
        $res = (int) $req[0] * (int) $req[1];
        echo "$req[0] + $req[1] = " . $res;
    }
    */

    function zzz()
    {
        $arr = ['el', 'dia', 'que', 'me', 'quieras'];
        $arr = array_map(function ($x) {
            return "'$x'";
        }, $arr);

        dd($arr);

        //echo implode('-', $arr);
    }

    function speed()
    {
        Time::setUnit('MILI');

        $res = null;

        $t1 = Time::exec(function () use (&$res) {
            $res = (new ApiClient)
                ->disableSSL()
                ->setUrl('https://mindicador.cl/api')
                ->get();
        });

        dd($res);

        dd("Time: $t1 ms");
    }

    function speed2()
    {

        Time::setUnit('MILI');
        //Time::noOutput();

        $conn = DB::getConnection();
        $t = Time::exec(function () use ($conn) {
            $sql = "INSERT INTO `baz2` (`name`, `cost`) VALUES ('hhh', '789')";
            $conn->exec($sql);
        }, 1);
        dd("Time: $t ms");

        exit;

        $m = (new Model(true))
            ->table('baz2');
        $t = Time::exec(function () use ($m) {
            //$m->setValidator(new Validator());
            //$m->dontExec();

            $id = $m->create([
                'name' => 'BAZ',
                'cost' => '100',
            ]);
        }, 1);
        dd("Time: $t ms");
        dd($m->getLog());

        /*
        Time::setUnit('MILI');
        //Time::noOutput();

        $this->model_name  = null;
        $this->table_name = 'users';

        $t = Time::exec(function(){ 
            
            $id = DB::table('collections')->create([
                'entity' => 'messages',
                'refs' => json_encode([195,196]),
                'belongs_to' => 332
            ]);

        }, 1);       
        dd("Time: $t ms");
        */
    }

    function speed_show()
    {
        Time::setUnit('MILI');

        $m = (new Model(true))
            ->table('bar')
            ->where(['uuid', '0fefc2b1-f0d3-47aa-a875-5dbca85855f9'])
            ->select(['uuid', 'price']);

        //dd($m->dd());
        //exit;     

        $t = Time::exec(function () use ($m) {
            $row = $m->get();
        }, 1);

        //dd("Time: $t ms");
        Logger::log("Time(show) : $t ms");
    }

    function speed_list()
    {
        Time::setUnit('MILI');

        $m = (new Model(true))
            ->table('bar')
            ->select(['uuid', 'price'])
            ->take(10);

        //dd($m->dd());
        //exit;         

        $t = Time::exec(function () use ($m) {
            $row = $m->get();
        }, 1);

        //dd("Time: $t ms");
        Logger::log("Time(list) : $t ms");
    }

    function get_bulk()
    {
        $t1a = [];
        $t2a = [];

        Time::setUnit('MILI');

        $m1 = (new Model(true))
            ->table('bar')
            ->select(['uuid', 'price'])
            ->take(10);

        $m2 = (new Model(true))
            ->table('bar')
            ->where(['uuid', '0fefc2b1-f0d3-47aa-a875-5dbca85855f9'])
            ->select(['uuid', 'price']);

        //dd($m->dd());
        //exit;         

        $m3 = DB::select("SELECT AVG(price) FROM bar;");

        for ($i = 0; $i < 4; $i++) {
            $t1a[] = Time::exec(function () use ($m1) {
                $m1->get();
            }, 500);

            $t2a[] = Time::exec(function () use ($m2) {
                $m2->get();
            }, 500);
        }

        foreach ($t1a as $t1) {
            Logger::log("Time(list) : $t1 ms");
        }

        foreach ($t2a as $t2) {
            Logger::log("Time(show) : $t2 ms");;
        }
    }

    function xxy()
    {
        $str = "`lastname` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'NN',
        ";
        dd($str, 'STR');

        $charset    = Strings::slice($str, '/CHARACTER SET ([a-z0-9_]+)/');
        dd($charset, 'CHARSET');
        dd($str, 'STR');

        $collation  = Strings::slice($str, '/COLLATE ([a-z0-9_]+)/');
        dd($collation, 'COLLATION');
        dd($str, 'STR');

        $default    = Strings::slice($str, '/DEFAULT (\'?[a-zA-Z0-9_]+\'?)/');
        dd($default, "DEFAULT");
        dd($str, 'STR');

        $nullable   = Strings::slice($str, '/(NOT NULL)/') == NULL;
        dd($nullable, "NULLABLE");
        dd($str, 'STR');

        $auto       = Strings::slice($str, '/(AUTO_INCREMENT)/') == 'AUTO_INCREMENT';
        dd($auto, "AUTO");
        dd($str, 'STR');
    }

    function xxxz()
    {
        $str = 'CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE';
        dd($str, 'STR');

        $constraint = Strings::slice($str, '/CONSTRAINT `([a-zA-Z0-9_]+)` /', function ($s) {
            //var_dump($s);
            return ($s == null) ? 'DEFAULT' : $s;
        });

        dd($constraint, 'CONSTRAINT');

        //dd($constraint);
        //exit; //
        dd($str, 'STR');

        $primary = Strings::slice($str, '/PRIMARY KEY \(([a-zA-Z0-9_`,]+)\)/');
        dd($str, 'STR');
        dd($primary, 'PRIMARY');

        /*

        Compuesto:
        UNIQUE KEY `correo` (`correo`,`hora`) USING BTREE,

        */
        $unique  = Strings::sliceAll($str, '/UNIQUE KEY `([a-zA-Z0-9_]+)` \(([a-zA-Z0-9_`,]+)\)/');
        dd($str, 'STR');
        dd($unique, 'UNIQUE');

        /*
            CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCAD

        */
        $fk     = Strings::slice($str, '/FOREIGN KEY \(`([a-zA-Z0-9_]+)`\)/');
        $fk_ref = Strings::slice($str, '/REFERENCES `([a-zA-Z0-9_]+)`/');

        dd($str, 'STR');
        dd($fk, 'FK');
        dd($fk, 'REFERENCES');

        /*
        IDEM
        */
        $index   = Strings::sliceAll($str, '/KEY `([a-zA-Z0-9_]+)` \(([a-zA-Z0-9_`,]+)\)/');
        dd($str, 'STR');
        dd($index, 'INDEX');
    }


    function test_route()
    {
        /*
        Route::get('dumbo/kalc', function(){
            echo 'Hello from Kalc!';
        })->name('dumbo.kalc');
        
        
        Route::get('has_table', 'DumbController@has_table')
        ->name('dumbo.has_table');
        */

        //dd(route('dumbo.has_table'), 'URL');
        //dd(route('dumbo.kalc'), 'URL');
    }

    function curl()
    {
        define('HOST', config()['app_url']);
        define('BASE_URL', HOST . '/');

        $url = BASE_URL . "api/v1/auth/login";

        $credentials = [
            'email' => "tester3@g.c",
            'password' => "gogogo8"
        ];

        if ($credentials == []) {
            throw new \Exception("Empty credentials");
        }

        $data = json_encode($credentials);

        $com = <<<HDOC
        curl -s --location --request POST '$url' \
        --header 'Content-Type: text/plain' \
        --data-raw '$data' /tmp/output.html
        HDOC;

        $response = json_decode(exec($com), true);

        $data      = $response['data'] ?? [];
        $http_code = $response['status_code'];
        $error_msg = $response['error'];

        dd($data, 'data');
        dd($http_code, 'http code');
        dd($error_msg, 'error');
    }

    function test_trace()
    {
        $fn = function () {
            //throw new \InvalidArgumentException("El argumento xxx es invalido");
            //die("Ouch!");

            $x = 1 / 0;
        };

        $fn();
    }

    /*
        TypeError tambien son capturados por el error_handler

        Ver si demas errores son capturados:

        https://trowski.com/2015/06/24/throwable-exceptions-and-errors-in-php7/
    */
    function test_trace2()
    {
        $fn = function (int $x) {
            $z = $x / 2;
        };

        $fn("hello");
    }

    /*
        Error capturado

        Like any other exception, Error objects can be caught using a try/catch block.

        https://trowski.com/2015/06/24/throwable-exceptions-and-errors-in-php7/
    */
    function test_trace3()
    {
        $method = 'metodo_inexistente';

        $x = new stdClass();
        $x->$method();
    }




    /*
        Basic GET, https
    */
    function test_api00()
    {
        $res = consume_api('https://jsonplaceholder.typicode.com/posts', 'GET');
        dd($res);
    }

    /*
        Basic GET, http
    */
    function test_api01()
    {
        $options = [
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ];

        $res = consume_api('http://jsonplaceholder.typicode.com/posts', 'GET', null, $options);
        dd($res);
    }

    /*
        OK!
    */
    function test_api0b()
    {
        // ruta absoluta al certificado	
        $cert = "D:\wamp64\ca-bundle.crt";

        $cli = ApiClient::instance();

        $res = $cli
            //->setSSLCrt($cert)
            ->request('http://jsonplaceholder.typicode.com/posts', 'GET')
            ->getResponse(true);

        dd($res, 'RES');
        dd($cli->getHeaders(), 'HEADERS');
    }

    /*
        Ya no es necesario especificar la ruta al certificado si se configura via config.php
    */
    function test_api0c()
    {
        $res = ApiClient::instance()
            ->request('http://jsonplaceholder.typicode.com/posts', 'GET')
            ->getResponse(true);

        dd($res);
    }


    /*
        Dolar TRM - 
        DataSource: API Banco de la República (de Colombia)
    */
    function dolar()
    {
        $client = ApiClient::instance();

        $res = $client
            ->disableSSL()
            //->setSSLCrt("c:\php\cacert.pem")
            ->request('https://totoro.banrep.gov.co/estadisticas-economicas/rest/consultaDatosService/consultaMercadoCambiario', 'GET')
            ->getResponse();

        dd($res);

        // dd($client->getStatus(), 'STATUS');
        // dd($client->getError(), 'ERROR');
        // dd($client->getResponse(true), 'RES'); 
        // exit;

        // $data  = $res['data'];
        // $final = $data[count($data) - 1];
        // dd($final[1], "DOLAR/COP (TRM) - VALOR FINAL " . date("Y-m-d H:i:s", substr($final[0], 0, 10)));
    }


    // function euro()
    // {
    //     $res = consume_api('https://totoro.banrep.gov.co/estadisticas-economicas/rest/consultaDatosService/consultaMercadoCambiario', 'GET');

    //     if ($res['http_code'] != 200) {
    //         throw new \Exception("Error: " . $res['code'] . ' -code: ' . $res['code']);
    //     }

    //     $data    = $res['data'];
    //     $final  = $data[count($data) - 1];
    //     $copusd = $final[1];

    //     // Build Swap
    //     $swap = (new \Swap\Builder())
    //         ->add('european_central_bank')
    //         ->add('national_bank_of_romania')
    //         ->add('central_bank_of_republic_turkey')
    //         ->add('central_bank_of_czech_republic')
    //         ->add('russian_central_bank')
    //         ->add('bulgarian_national_bank')
    //         ->add('webservicex')
    //         ->build();

    //     // Get the latest EUR/USD rate
    //     $rate = ($swap->latest('EUR/USD'))->getValue();

    //     $copeur = $copusd * $rate;

    //     dd($copeur, "EUR/COP - VALOR FINAL " . date("Y-m-d H:i:s", substr($final[0], 0, 10)));
    // }

    // function swap()
    // {

    //     // Build Swap
    //     $swap = (new \Swap\Builder())
    //         ->add('european_central_bank')
    //         ->add('national_bank_of_romania')
    //         ->add('central_bank_of_republic_turkey')
    //         ->add('central_bank_of_czech_republic')
    //         ->add('russian_central_bank')
    //         ->add('bulgarian_national_bank')
    //         ->add('webservicex')
    //         ->build();

    //     // Get the latest EUR/USD rate
    //     $rate = $swap->latest('EUR/USD');

    //     // 1.129
    //     dd($rate->getValue(), 'EUR/USD');

    //     // 2016-08-26
    //     $rate->getDate()->format('Y-m-d');

    //     // Get the EUR/USD rate 15 days ago
    //     $rate = $swap->historical('EUR/USD', (new \DateTime())->modify('-15 days'));
    // }

    function parse_class()
    {
        $path = MIGRATIONS_PATH . '2021_09_14_27905675_user_sp_permissions.php';
        $file = file_get_contents($path);

        dd(PHPLexicalAnalyzer::getClassNameByFileName($file));
    }

    function serve_assets()
    {
        view('test_assets.php', null, null, 60);
    }

    function test_base_url()
    {
        dd(Url::currentUrl());
        dd(Url::getHostname());
    }


    function x()
    {
        dd(asset("jota.jpg"));
        dd(asset("assets/jota.jpg"));
    }

    function dir()
    {
        $path = '/home/www/simplerest/app/migrations';

        $dir  = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::SELF_FIRST);

        echo "[$path]\n";
        foreach ($files as $file) {
            $indent = str_repeat('   ', $files->getDepth());
            echo $indent, " ├ $file\n";
        }
    }

    /*
        Genera migraciones a partir de la tabla 'tbl_scritp_tablas'
    */
    function gen_scripts()
    {
        $mk = new MakeCommand();

        $rows = DB::table('tbl_scritp_tablas')
            ->orderBy(['scr_intOrden' => 'ASC'])
            ->get();

        foreach ($rows as $row) {
            $orden = str_pad($row['scr_intOrden'], 4, "0", STR_PAD_LEFT);
            $name  = strtolower("$orden-{$row['scr_varNombre']}-{$row['scr_varModulo']}");
            $script = $row['scr_lonScritp'];

            $folder = "compania";

            $class_name = Strings::snakeToCamel("{$row['scr_varNombre']}_{$row['scr_varModulo']}_{$row['scr_intOrden']}");

            $mk->migration("$name", "--dir=$folder", "--from_script=\"$script\"", "--class_name=$class_name");
        }
    }

    function mid()
    {
        return "Hello World!";
    }

    function update()
    {
        $data = ["est_varColor" => "rojo"];

        DB::setConnection('db_flor');

        $affected = DB::table('tbl_estado')->where(["est_intId" => 1])->update($data);
        dd($affected);
    }

    function migrate()
    {
        $mgr = new \MigrationsCommand();

        $folder = 'compania';
        $tenant = 'db_100';

        StdOut::hideResponse();

        $mgr->migrate("--dir=$folder", "--to=$tenant");
    }

    function get_pks()
    {
        dd(Schema::getPKs('boletas'));
    }

    function get_db()
    {
        dd(Schema::getCurrentDatabase());
    }

    function get_autoinc()
    {
        dd(Schema::getAutoIncrement('book_reviews'));
        dd(Schema::getAutoIncrement('bar'));
        dd(Schema::hasAutoIncrement('bar'));
    }

    function test_alter_table()
    {
        DB::setConnection('az');

        //$sc = new Schema('boletas');        
        //$sc->field('id')->primary();

        $sc = new Schema('bar');
        $sc
            ->field('ts')
            ->renameColumnTo('times2');

        $sc->field('f1')->primary();
        $sc->field('f2')->primary();
        $sc->field('f3')->primary();

        $sc->dontExec();
        $sc->alter();

        dd($sc->getSchema(), 'SCHEMA');
        dd($sc->dd(), 'SQL');
    }

    function test_alter_table2()
    {
        DB::setConnection('az');

        $sc = new Schema('boletas');

        $sc->field('f1')->primary();
        $sc->field('f2')->primary();

        $sc->dontExec();
        $sc->alter();

        dd($sc->getSchema(), 'SCHEMA');
        dd($sc->dd(true), 'SQL');
    }

    function test_alter_table3()
    {
        DB::setConnection('az');

        $sc = new Schema('boletas');

        $sc->dropPrimary();

        //$sc->dontExec();
        $sc->alter();

        dd($sc->getSchema(), 'SCHEMA');
        dd($sc->dd(true), 'SQL');
    }

    function test_alter_table4()
    {
        $sc = new Schema('boletas');
        $sc
            ->dontExec()
            ->dropAuto()
            ->alter();

        dd($sc->dd(), 'SQL');
    }

    function get_auto_field()
    {
        dd(Schema::getAutoIncrementField('book_reviews'));
        dd(Schema::getAutoIncrementField('bar'));
    }

    function mk()
    {
        $tenant = "db_100";

        StdOut::hideResponse();

        $mk = new MakeCommand();
        $mk->any("all", "-s", "-m", "--from:$tenant");
    }

    function error()
    {
        response()->error("Todo mal", 400);
    }

    function is_type()
    {
        dd(Validator::isType('8', 'str'));
        dd(Validator::isType(8, 'str'));
    }

    function validate_data($str)
    {
        dd(Validator::isType($str, 'date'));
    }

    function get_pri()
    {
        return get_primary_key('products', 'az');
    }

    function test_sub_res()
    {
        DB::getConnection('az');

        global $api_version;

        $api_version = 'v1';

        $connect_to = Products::getConnectable();
        $instance   = null;
        $table = 'products';

        $res = Products::getSubResources($table, $connect_to, $instance, null, 145);
        dd($res);
    }

    function test_sub_res2()
    {
        DB::getConnection('az');

        global $api_version;

        $api_version = 'v1';

        $connect_to = Products::getConnectable();
        $instance   = null;
        $table = 'products';

        $res = Products::getSubResources($table, $connect_to, $instance);
        dd($res);
    }

    function test_sub_res3()
    {
        DB::getConnection('db_flor');

        global $api_version;

        $api_version = 'v1';

        $connect_to = TblPersona::getConnectable();
        $instance   = null;
        $table = 'tbl_persona';

        $res = Products::getSubResources($table, $connect_to, $instance);
        dd($res);
    }


    function test_create_user()
    {
        $m = DB::table('users')
            ->setValidator(new Validator())
            ->fill(['password', 'created_at']);

        // dd($m->getFillables(), 'FILLABLES');
        // dd($m->getNotFillables(), 'NOT FILLABLES');

        $data = json_decode('{
            "username": "u200",
            "email": "u200@mail.com",
            "password": "gogogo",
            "created_at": "2019-02-02 10:00:00"
        }', true);


        $ok = $m
            ->create($data);

        dd($ok);
    }

    function test_create(){
        $m = DB::table('products');
    
        $id = $m
        ->insert([
            'id'   => 99999931,
            'name' => 'BAZ',
            'cost' => '100',
            'description' => 'asdf',
            'slug' => 'baz-2',
            'images' => '[]',
        ]);
    
        dd($m->getLog());
    }

    /*
        create()
    */
    function create000()
    {
        DB::getConnection('az');

        $data = array(
            'name' => 'bbb',
            'comment' => 'positivo',
            'product_id' => 100
        );

        $id = DB::table('product_tags')
            ->create($data);

        dd($id);
        dd(DB::getLog());
    }

    /*
        create()

        De momento cada INSERT se ejecuta por separado
    */
    function create_as_insert_mul()
    {
        DB::getConnection('az');

        $data = [
            array(
                'name' => 'N1x',
                'comment' => 'P1x',
                'product_id' => 100
            ),

            array(
                'name' => 'N2x',
                'comment' => 'P2x',
                'product_id' => 103
            ),

            array(
                'name' => 'N3x',
                'comment' => 'P3x',
                'product_id' => 105
            )
        ];

        $m = DB::table('product_tags');

        $id = $m->create($data);

        dd($id);
        dd($m->getLog());
    }

    /*
        create()
    */
    function create_as_insert_mul2()
    {
        DB::getConnection('az');

        $data = [
            array(
                'name' => 'N1',
                'comment' => 'P1',
                'product_id' => 100
            ),

            array(
                'name' => 'N2',
                'comment' => 'P2',
                'product_id' => 103
            ),

            array(
                'name' => 'N3',
                'comment' => 'P3',
                'product_id' => 105
            ),

            array(
                'name' => 'N4',
                'comment' => '',
                'product_id' => 105
            )
        ];

        $id = DB::table('product_tags')
            ->create($data);

        dd($id);
    }

    /*
        insert()

        1 row
    */
    function insert_base()
    {
        DB::getConnection('az');

        $data = array(
            'name' => 'bbb',
            'comment' => 'positivoOOo',
            'product_id' => 100
        );

        $m = DB::table('product_tags');

        $id = $m
            ->insert($data, false);

        dd($id);
        dd($m->getLog());
    }

    /*
        insert()

        varios rows
    */
    function insert_base2()
    {
        DB::getConnection('az');

        $data = [
            array(
                'name' => 'N1',
                'comment' => 'P1',
                'product_id' => 100
            ),

            array(
                'name' => 'N2',
                //'comment' => 'P2',
                'product_id' => 103
            ),

            array(
                'name' => 'N3',
                'comment' => 'P3',
                'product_id' => 105
            ),

            array(
                'name' => 'N4',
                'comment' => '',
                'product_id' => 105
            )
        ];

        $m = DB::table('product_tags');

        $id = $m
            ->insert($data, false);

        dd($id);
        dd($m->getLog());
    }

    /*
        insert()
    */
    function insert_no_base2()
    {
        DB::getConnection('az');

        $data = [
            array(
                'name' => 'N1',
                'comment' => 'P1',
                'product_id' => 100
            ),

            array(
                'name' => 'N2',
                //'comment' => 'P2',
                'product_id' => 103
            ),

            array(
                'name' => 'N3',
                'comment' => 'P3',
                'product_id' => 105
            ),

            array(
                'name' => 'N4',
                'comment' => '',
                'product_id' => 105
            )
        ];

        $id = DB::table('product_tags')
            ->insert($data, true);

        dd($id);
    }

    // intento de inserción en tabla puente
    function test_insert_bridge()
    {
        $data = array(
            'product_id' => '145',
            'valoracion_id' => '9',
            'created_at' => at()
        );

        $mbr = DB::table('product_valoraciones');
        $ok = $mbr->create($data);

        dd($ok, 'Ok?');
    }

    /*
        insert()
    */
    function insert_mul()
    {
        DB::getConnection('az');

        $m = DB::table('bar');

        $data = [];
        for ($i = 0; $i < 5; $i++) {
            $name = '    ';
            for ($i = 0; $i < 46; $i++)
                $name .= chr(rand(97, 122));

            $name = str_shuffle($name);

            $email = '@';
            $cnt = rand(10, 78);
            for ($i = 0; $i < $cnt; $i++)
                $email .= chr(rand(97, 122));

            $email =  chr(rand(97, 122)) . str_shuffle($email);

            $data[] = [
                'name' => $name,
                'price' => rand(5, 999) . '.' . rand(0, 99),
                'email' => $email,
                'belongs_to' => 1
            ];
        }

        $id = $m->insert($data);

        dd($data, 'DATA');
        dd($id, 'ID');
    }

    /*
        Inserción de un solo row
        con insert()
    */
    function insert_one_row()
    {
        DB::getConnection('az');

        $name = '';
        for ($i = 0; $i < 20; $i++) {
            $name .= chr(rand(97, 122));
        }

        $m = DB::table('products');

        $id = $m
            ->insert([
                'name' => $name,
                'description' => 'Esto es una prueba 770',
                'size' => '100L',
                'cost' => 66,
                'belongs_to' => 90,
                'digital_id' => 2
            ]);

        dd($id);
        dd($m->dd(true));
    }

    /*
        Inserción de múltiples rows
        con insert()
    */
    function insert_mul_rows()
    {
        DB::getConnection('az');

        $data = [];
        for ($j = 0; $j < 5; $j++) {
            $name = '';

            for ($i = 0; $i < 20; $i++) {
                $name .= chr(rand(97, 122));
            }

            $desc = str_shuffle($name);
            $cost = rand(50, 150);

            /*
                Preparo múltiples rows a ser insertadas
            */
            $data[] = [
                'name' => $name,
                'description' => $desc,
                'size' => '100L',
                'cost' => $cost,
                'belongs_to' => 90,
                'digital_id' => 1
            ];
        }

        $id = DB::table('products')
            ->insert($data, true);

        dd($id);
    }

    function get_all_rels()
    {
        DB::getConnection('db_flor');
        $data = Schema::getAllRelations('tbl_producto');

        dd($data);
    }

    function pivot()
    {
        dd(get_pivot(['products', 'comments'], 'az'));
        //dd(get_pivot(['products', 'comments']));
        dd(get_pivot(['roles', 'tbl_usuario_empresa'], 'main'));
    }

    function show_dbs()
    {
        DB::getDefaultConnection();

        dd(DB::getConnectionConfig());

        $rows = DB::table('tbl_base_datos')
            ->get();

        dd($rows);
    }

    function show_dbs2()
    {
        $conn = DB::getDefaultConnection();

        $m = (new Model(false, null, false))
            ->setConn($conn);

        $bases = $m->table('tbl_base_datos')
            ->pluck('dba_varNombre');

        dd($bases);
    }

    function get_tables()
    {
        DB::getConnection('db_flor');

        $tables = Schema::getTables();
        dd($tables, 'TABLES');
    }

    function drop_all_tables(string $db_conn_id)
    {
        dd("DROPPING ALL TABLES FROM `$db_conn_id`");

        DB::getConnection($db_conn_id);

        $tables = Schema::getTables();

        DB::disableForeignKeyConstraints();

        foreach ($tables as $tb) {
            $ok = Schema::dropIfExists($tb);
            dd($ok, "DROP TABLE $tb");
        }

        DB::enableForeignKeyConstraints();
    }

    function drop_all_tables_last_db()
    {
        $conn = DB::getDefaultConnection();

        $db = DB::table('tbl_base_datos')
            ->orderBy(['dba_intId' => 'DESC'])
            ->first(['dba_varNombre'])['dba_varNombre'];

        $this->drop_all_tables($db);
    }

    function gr()
    {
        // $tenant = 'az';
        // $tb = 'products';

        // $tenant = 'db_flor';
        // $tb = 'tbl_producto';

        $tenant = 'db_flor';
        $tb = 'tbl_tipo_persona';


        DB::getConnection($tenant);
        //dd(Schema::getFKs($tb));
        dd(Schema::getAllRelations($tb, true, false));
    }


    function pivote()
    {
        $tenant_id = 'az';

        // $t1 = 'products';
        // $t2 = 'valoraciones';

        // $pivot = get_pivot([
        //     $t1, $t2
        // ], $tenant_id);

        // dd($pivot, "PIVOT for $t1~$t2");

        // /////////////////////////////////

        // $t1 = 'valoraciones';
        // $t2 = 'products';

        // $pivot = get_pivot([
        //     $t1, $t2
        // ], $tenant_id);

        // dd($pivot, "PIVOT for $t1~$t2");

        /////////////////////////////////


        // $t1 = 'products';
        // $t2 = 'product_comments';

        // $pivot = get_pivot([
        //     $t1, $t2
        // ], $tenant_id);

        // dd($pivot, "PIVOT for $t1~$t2");

        /////////////////////////////////


        /// Acá se confunde !!!!!!!!!!!!!
        $tenant_id = 'db_flor';

        $t1 = 'tbl_persona';
        $t2 = 'tbl_usuario';

        $pivot = get_pivot([
            $t1, $t2
        ], $tenant_id);

        dd($pivot, "PIVOT for $t1~$t2");

        /////////////////////////////////

    }


    function get_rels()
    {
        $tenant_id = 'az';

        $t1 = 'products';
        $t2 = 'users';
        $rel_str = null;

        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2");
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id), "n:1 para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, $rel_str, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');
        exit; ///////


        $t1 = 'u';
        $t2 = 'u_settings';
        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2");
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");

        dd('----------------------x----------------------');

        $t1 = 'u_settings';
        $t2 = 'u';
        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2");
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");

        dd('----------------------x----------------------');


        $t1 = 'products';
        $t2 = 'product_categories';

        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2");
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id), "n:1 para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, null, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');

        $t1 = 'product_categories';
        $t2 = 'products';

        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2");
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id), "n:1 para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, null, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');

        //Ahora especificiando la relación

        $t1 = 'product_categories';
        $t2 = 'products';
        $rel_str = 'product_categories.id_catego=products.category';

        dd(get_rels($t1, $t2, '1:1',  $tenant_id, $rel_str), "1:1 para $t1~$t2");
        dd(get_rels($t1, $t2, '1:n',  $tenant_id, $rel_str), "1:n para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id, $rel_str), "n:1 para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id, $rel_str), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, null, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');


        //Ahora especificiando la relación pero de forma inversa

        $t1 = 'product_categories';
        $t2 = 'products';
        $rel_str = 'products.category=product_categories.id_catego';

        dd(get_rels($t1, $t2, '1:1',  $tenant_id, $rel_str), "1:1 para $t1~$t2");
        dd(get_rels($t1, $t2, '1:n',  $tenant_id, $rel_str), "1:n para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id, $rel_str), "n:1 para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id, $rel_str), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, null, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');


        //Ahora especificiando la relación pero trocando $t1 y $t2

        $t1 = 'products';
        $t2 = 'product_categories';
        $rel_str = 'product_categories.id_catego=products.category';

        dd(get_rels($t1, $t2, '1:1',  $tenant_id, $rel_str), "1:1 para $t1~$t2");
        dd(get_rels($t1, $t2, '1:n',  $tenant_id, $rel_str), "1:n para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id, $rel_str), "n:1 para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id, $rel_str), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, null, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');


        //Ahora especificiando la relación pero de forma inversa ero trocando $t1 y $t2

        $t1 = 'products';
        $t2 = 'product_categories';
        $rel_str = 'products.category=product_categories.id_catego';

        dd(get_rels($t1, $t2, '1:1',  $tenant_id, $rel_str), "1:1 para $t1~$t2");
        dd(get_rels($t1, $t2, '1:n',  $tenant_id, $rel_str), "1:n para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id, $rel_str), "n:1 para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id, $rel_str), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, null, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');



        $tenant_id = 'db_flor';
        $t1 = 'tbl_producto';
        $t2 = 'tbl_sub_cuenta_contable';

        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2");
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id), "n:1 para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, null, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');

        // órden de tablas trocado

        $t2 = 'tbl_sub_cuenta_contable';
        $t1 = 'tbl_producto';

        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2");
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id), "n:1 para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, null, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');

        //Ahora especificiando la relación

        $t1 = 'tbl_producto';
        $t2 = 'tbl_sub_cuenta_contable';
        $rel_str = 'tbl_producto.sub_intIdCuentaContableCompra=tbl_sub_cuenta_contable.sub_intId';

        dd(get_rels($t1, $t2, '1:1',  $tenant_id, $rel_str), "1:1 para $t1~$t2");
        dd(get_rels($t1, $t2, '1:n',  $tenant_id, $rel_str), "1:n para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id, $rel_str), "n:1 para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id, $rel_str), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, $rel_str, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');


        //Ahora especificiando la relación pero de forma inversa

        $t1 = 'tbl_producto';
        $t2 = 'tbl_sub_cuenta_contable';
        $rel_str = 'tbl_sub_cuenta_contable.sub_intId=tbl_producto.sub_intIdCuentaContableCompra';

        dd(get_rels($t1, $t2, '1:1',  $tenant_id, $rel_str), "1:1 para $t1~$t2");
        dd(get_rels($t1, $t2, '1:n',  $tenant_id, $rel_str), "1:n para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id, $rel_str), "n:1 para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id, $rel_str), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, $rel_str, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');


        // IDEM anterior por con otra relación entre las mismas tablas

        $t1 = 'tbl_producto';
        $t2 = 'tbl_sub_cuenta_contable';
        $rel_str = 'tbl_sub_cuenta_contable.sub_intId=tbl_producto.sub_intIdCuentaContableVenta';

        dd(get_rels($t1, $t2, '1:1',  $tenant_id, $rel_str), "1:1 para $t1~$t2");
        dd(get_rels($t1, $t2, '1:n',  $tenant_id, $rel_str), "1:n para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id, $rel_str), "n:1 para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id, $rel_str), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, $rel_str, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');



        // Trocando $t1 y $t2

        $t1 = 'tbl_sub_cuenta_contable';
        $t2 = 'tbl_producto';
        $rel_str = null;

        dd(get_rels($t1, $t2, '1:1',  $tenant_id, $rel_str), "1:1 para $t1~$t2");
        dd(get_rels($t1, $t2, '1:n',  $tenant_id, $rel_str), "1:n para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id, $rel_str), "n:1 para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id, $rel_str), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, null, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');

        $tenant_id = 'az';

        $t1 = 'products';
        $t2 = 'valoraciones';
        $rel_str = null;

        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2");
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id), "n:1 para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, $rel_str, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');

        $t1 = 'roles';
        $t2 = 'user_roles';
        $rel_str = null;

        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2");
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id), "n:1 para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, $rel_str, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');

        $tenant_id = 'db_flor';

        $t1 = 'tbl_persona';
        $t2 = 'tbl_usuario';
        $rel_str = null;

        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2");
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id), "n:1 para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, $rel_str, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');

        $t1 = 'tbl_producto';
        $t2 = 'tbl_unidadmedida';
        $rel_str = null;

        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2");
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id), "n:1 para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, $rel_str, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');

        $t1 = 'tbl_sub_cuenta_contable';
        $t2 = 'tbl_iva_cuentacontable';
        $rel_str = null;

        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2");
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id), "n:1 para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, $rel_str, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');


        $t1 = 'tbl_sub_cuenta_contable';
        $t2 = 'tbl_producto';
        $rel_str = null;

        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2");
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id), "n:1 para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");
        dd(get_rel_type($t1, $t2, $rel_str, $tenant_id), 'REL TYPE');

        dd('----------------------x----------------------');

        $t1 = 'tbl_categoria_persona';
        $t2 = 'tbl_usuario';
        $rel_str = null;

        dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2");
        dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:1',  $tenant_id), "n:1 para $t1~$t2");
        dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");

        dd(get_rel_type($t1, $t2, $rel_str, $tenant_id), 'REL TYPE');
    }

    function is_rel()
    {
        $tenant_id = 'az';

        // $t1 = 'u';
        // $t2 = 'u_settings';

        // dd(is_1_1($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are 1:1 ?"); 
        // dd(is_1_n($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are 1:n ?"); 
        // dd(is_1_n($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are n:1 ?");
        // dd(is_n_m($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are n:m ?"); 


        $t1 = 'products';
        $t2 = 'product_categories';

        // dd(is_1_1($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are 1:1 ?"); 
        dd(is_1_n($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are 1:n ?");
        // dd(is_n_1($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are n:1 ?");
        // dd(is_n_m($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are n:m ?");

        // dd('------------------------------------------------------------');

        $t1 = 'products';
        $t2 = 'users';

        // dd(get_rels($t1, $t2, '1:1',  $tenant_id), "1:1 para $t1~$t2"); 
        // dd(get_rels($t1, $t2, '1:n',  $tenant_id), "1:n para $t1~$t2"); 
        // dd(get_rels($t1, $t2, 'n:1',  $tenant_id), "n:1 para $t1~$t2"); 
        // dd(get_rels($t1, $t2, 'n:m',  $tenant_id), "n:m para $t1~$t2");

        dd(is_1_1($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are 1:1 ?");
        dd(is_1_n($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are 1:n ?");
        dd(is_n_1($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are n:1 ?");
        dd(is_n_m($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are n:m ?");

        dd('------------------------------------------------------------');

        exit;

        $tenant_id = 'db_flor';
        $t1 = 'tbl_persona';
        $t2 = 'tbl_usuario';


        dd(is_1_1($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are 1:1 ?");
        dd(is_1_n($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are 1:n ?");
        dd(is_n_1($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are n:1 ?");
        dd(is_n_m($t1, $t2, null, $tenant_id), "All relations for $t1~$t2 are n:m ?");
    }

    function which_rel()
    {
        $tenant_id = 'db_legion';

        $t1 = 'tbl_arl';
        $t2 = 'tbl_empresa_nomina';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");

        exit; //

        $tenant_id = 'az';

        $t1 = 'products';
        $t2 = 'product_categories';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");

        $t1 = 'products';
        $t2 = 'product_tags';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");

        $t1 = 'products';
        $t2 = 'valoraciones';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");


        $t1 = 'valoraciones';
        $t2 = 'products';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");


        $t1 = 'valoraciones';
        $t2 = 'users';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");


        $t1 = 'products';
        $t2 = 'users';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");


        $t1 = 'users';
        $t2 = 'products';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");

        $t1 = 'users';
        $t2 = 'book_reviews';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");


        $t1 = 'book_reviews';
        $t2 = 'users';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");

        $t1 = 'roles';
        $t2 = 'users';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");

        $t1 = 'user_roles';
        $t2 = 'roles';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");

        $t1 = 'roles';
        $t2 = 'user_roles';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");

        $t1 = 'product_tags';
        $t2 = 'products';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");


        $t1 = 'products';
        $t2 = 'product_tags';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");

        $tenant_id = 'az';
        $t1 = 'u';
        $t2 = 'u_settings';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");

        $t1 = 'u_settings';
        $t2 = 'u';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");

        $t1 = 'tbl_persona';
        $t2 = 'tbl_pais';

        dd(get_rel_type($t1, $t2, null, 'db_flor'), "$t1~$t2");

        $t1 = 'tbl_estado';
        $t2 = 'tbl_categoria_persona';

        dd(get_rel_type($t1, $t2, null, 'db_flor'), "$t1~$t2");

        $t1 = 'tbl_sub_cuenta_contable';
        $t2 = 'tbl_iva_cuentacontable';

        dd(get_rel_type($t1, $t2, null, 'db_flor'), "$t1~$t2");


        $t1 = 'tbl_sub_cuenta_contable';
        $t2 = 'tbl_iva';

        dd(get_rel_type($t1, $t2, null, 'db_flor'), "$t1~$t2");


        $t1 = 'job_tbl';
        $t2 = 'usr_tbl';

        dd(get_rel_type($t1, $t2, null, 'az'), "$t1~$t2");

        $t1 = 'usr_tbl';
        $t2 = 'job_tbl';

        dd(get_rel_type($t1, $t2, null, 'az'), "$t1~$t2");


        $t1 = 'tbc1';
        $t2 = 'tbc2';

        // 1:n  -- diría que está OK
        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");


        // 1:n -- no estoy seguro
        $tenant_id = 'az';

        $t1 = 'tbc2';
        $t2 = 'tbc1';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");


        $t1 = 'tbl_sub_cuenta_contable';
        $t2 = 'tbl_producto';

        dd(get_rel_type($t1, $t2, null, 'db_flor'), "$t1~$t2");


        $t1 = 'tbl_producto';
        $t2 = 'tbl_sub_cuenta_contable';

        dd(get_rel_type($t1, $t2, null, 'db_flor'), "$t1~$t2");

        $tenant_id = 'az';

        $t1 = 'ur';
        $t2 = 'ur_settings';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");

        $t1 = 'ur_settings';
        $t2 = 'ur';

        dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");

        $tenant_id = 'db_flor';

        $t1 = 'tbl_producto';
        $t2 = 'tbl_unidadmedida';

        $relation_str = null;

        //dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");

        $rel_type = get_rel_type($t1, $t2, $relation_str, $tenant_id);
    }


    function exx()
    {
        $m = DB::table('products')
            ->deleted()
            //->dontQualify()
            ->groupBy(['cost', 'size', 'belongs_to'])
            ->having(['cost', 100, '>='])
            ->or(function ($q) {
                $q->havingRaw('SUM(cost) > ?', [500])
                    ->having(['size' => '1L']);
            })
            ->orderBy(['size' => 'DESC'])
            ->select(['cost', 'size', 'belongs_to']);

        dd($m->get());
        dd(
            $m
                //->setPaginator(false)
                ->sqlformatterOn()
                ->dd()
        );
    }

    // OK
    function ex()
    {
        DB::getConnection('az');

        $m = DB::table('products')
            ->dontBind()   // <--- here 
            ->dontExec()   // <--- here 
            ->select(['size'])
            ->selectRaw('AVG(cost)')
            ->groupBy(['size'])
            ->having(['AVG(cost)', null, '<']);

        $sql = $m->toSql();

        dd(DB::select($sql, [50]));
        dd($sql, 'pre-compiled SQL');
        dd(DB::getLog(), 'excecuted SQL');
    }

    function ex1a()
    {
        $m = DB::table('products')
            ->dontBind()   // <--- here 
            ->dontExec()   // <--- here 
            ->select(['size', 'cost'])
            ->groupBy(['size'])
            ->having(['cost', null, '>='])
            ->having(['size' => null]);

        $sql = $m->toSql();

        dd(DB::select($sql, [5, '1L']));
        dd($sql, 'pre-compiled SQL');
        dd(DB::getLog(), 'excecuted SQL');
    }

    function ex1b()
    {
        $m = DB::table('products')
            ->dontBind()   // <--- here 
            ->dontExec()   // <--- here 
            ->select(['size'])
            ->selectRaw('AVG(cost)')
            ->groupBy(['size'])
            ->havingRaw('MIN(cost) = ? AND AVG(cost) > ?', [null, null]);

        $sql = $m->toSql();

        dd(DB::select($sql, [5, '1L']));
        dd($sql, 'pre-compiled SQL');
        dd(DB::getLog(), 'excecuted SQL');
    }

    function ex2()
    {
        $m = DB::table('products')
            ->dontBind()   // <--- here 
            ->dontExec()   // <--- here 
            ->select(['size'])
            ->selectRaw('AVG(cost)')
            ->where(['cost', null, '>'])
            ->groupBy(['size'])
            ->having(['AVG(cost)', null, '<']);

        $sql = $m->toSql();

        // dd(DB::select($sql, [50, 100]));
        dd($sql, 'pre-compiled SQL');
        dd(DB::getLog(), 'excecuted SQL');
    }

    function ex2_laravel_format()
    {
        $m = DB::table('products')
            ->dontBind()   // <--- here 
            ->dontExec()   // <--- here 
            ->select(['size'])
            ->selectRaw('AVG(cost)')
            ->where('cost', '>', null)
            ->groupBy(['size'])
            ->having('AVG(cost)', '<', null);

        $sql = $m->toSql();

        // dd(DB::select($sql, [50, 100]));
        dd($sql, 'pre-compiled SQL');
        dd(DB::getLog(), 'excecuted SQL');
    }

    function test_raw()
    {
        $res = DB::select('SELECT * FROM baz');
        dd($res);
        dd(DB::getLog());
    }

    function test_raw1()
    {
        dd(DB::getCurrentConnectionId());

        $res = DB::select('SELECT * FROM tbl_departamento', [], null, 'db_flor');
        dd($res);
        dd(DB::getLog());

        dd(DB::getCurrentConnectionId());
    }

    function test_raw2()
    {
        $res = DB::select('SELECT * FROM products WHERE cost > ? AND size = ?', [550, '1 mm']);
        dd($res);
        dd(DB::getLog());
    }

    function test_raw2a()
    {
        $res = DB::select('SELECT cost FROM products WHERE cost > ? AND size = ?', [550, '1 mm'], 'COLUMN');
        dd($res);
        dd(DB::getLog());
    }

    function test_db_select()
    {
        DB::getDefaultConnection();

        $db_name = 'az';

        $data = DB::select("SELECT TABLE_NAME 
		FROM information_schema.tables
		WHERE table_schema = '$db_name'", [], 'COLUMN');

        dd($data, 'DATA');
        dd(DB::getLog());
    }

    function test_raw_insert()
    {
        dd(DB::insert('insert into `baz2` (name, cost) values (?, ?)', ['cool thing', '16.25']));
        dd(DB::getLog());
    }

    function test_raw_insert1()
    {
        DB::getConnection('main');

        dd(DB::getCurrentConnectionId());

        dd(DB::insert('insert into `baz2` (name, cost) values (?, ?)', ['cool thing', '16.25'], 'az'));
        dd(DB::getLog());

        dd(DB::getCurrentConnectionId());
    }

    // ID repetido *sin* ignore
    function test_raw_insert2a()
    {
        DB::getConnection('main');

        dd(DB::getCurrentConnectionId());

        try {
            dd(DB::insert('insert into `baz2` (id_baz2, name, cost) values (?, ?, ?)', [5000, 'cool thing', '16.25'], 'az'));
        } catch (\Exception $e) {
            dd($e->getMessage());
            dd(DB::getLog());
        }

        dd(DB::getCurrentConnectionId());
    }

    function test_raw_insert2()
    {
        dd(DB::insert('insert ignore into `baz2` (id_baz2, name, cost) values (?, ?, ?)', [5000, 'cool thing', '16.25']));
        dd(DB::getLog());
    }

    function test_update_raw()
    {
        dd(DB::update('update `baz2` SET name = ?, cost = ? WHERE id_baz2 = ?', ['cool thing!!!!!!', '99.00', 5003]));
    }

    function test_statement()
    {
        DB::getConnection('db_flor');

        $res = DB::statement("INSERT INTO product_tags (name, comment, product_id) VALUES (?, ?, ?)", ['xyz', 'bla bla', 100], 'az');

        dd($res);
    }

    function gt()
    {
        DB::getConnection('az');
        $tables = Schema::getTables();

        dd($tables, 'TABLES');
    }

    function test_super_query()
    {
        $sql = file_get_contents(ETC_PATH . 'example.sql');
        dd(DB::select($sql, [], null, 'db_flor'));
    }

    function get_sql_client()
    {
        dd(DB::driver(), 'Driver');
        dd(DB::driverVersion(), 'Driver version');
        dd(DB::driverVersion(true), 'Driver version (num)');
        dd(DB::isMariaDB(), 'Is MariaDB');


        // $conn = DB::getConnection('az');

        // dd(DB::driverVersion());
        // dd(DB::isMariaDB(), 'Is MariaDB');

        // DB::getConnection('db_admin_mariadb_pablo');

        // dd(DB::driverVersion());
        // dd(DB::isMariaDB(), 'Is MariaDB');

        // $conn = DB::getConnection('az');

        // dd(DB::driverVersion());
        // dd(DB::isMariaDB(), 'Is MariaDB');
    }

    /*
        Sql formatter habilitado via Model::sqlformatterOn()
    */
    function test_sql_formatter001()
    {
        $m = DB::table('products')
            ->deleted()
            ->groupBy(['cost', 'size', 'belongs_to'])
            ->having(['cost', 100, '>='])
            ->or(function ($q) {
                $q->havingRaw('SUM(cost) > ?', [500])
                    ->having(['size' => '1L']);
            })
            ->orderBy(['size' => 'DESC'])
            ->select(['cost', 'size', 'belongs_to']);

        dd(
            $m
                ->sqlformatterOn()   /* habilito */
                ->dd()
        );
    }

    /*
        Sql formatter des-habilitado
    */
    function test_sql_formatter002()
    {
        $m = DB::table('products')
            ->deleted()
            ->groupBy(['cost', 'size', 'belongs_to'])
            ->having(['cost', 100, '>='])
            ->or(function ($q) {
                $q->havingRaw('SUM(cost) > ?', [500])
                    ->having(['size' => '1L']);
            })
            ->orderBy(['size' => 'DESC'])
            ->select(['cost', 'size', 'belongs_to']);

        dd($m->dd());
    }

    /*
        Sql formatter habilitado via Model::dd()
    */
    function test_sql_formatter003()
    {
        $m = DB::table('products')
            ->deleted()
            ->groupBy(['cost', 'size', 'belongs_to'])
            ->having(['cost', 100, '>='])
            ->or(function ($q) {
                $q->havingRaw('SUM(cost) > ?', [500])
                    ->having(['size' => '1L']);
            })
            ->orderBy(['size' => 'DESC'])
            ->select(['cost', 'size', 'belongs_to']);

        dd(
            $m
                ->dd(true)
        );
    }

    /*
        Sql formateador es aplicado en un segundo paso
        y se parametriza para colorizar 
    */
    function test_sql_formatter004()
    {
        $m = DB::table('products')
            ->deleted()
            ->groupBy(['cost', 'size', 'belongs_to'])
            ->having(['cost', 100, '>='])
            ->or(function ($q) {
                $q->havingRaw('SUM(cost) > ?', [500])
                    ->having(['size' => '1L']);
            })
            ->orderBy(['size' => 'DESC'])
            ->select(['cost', 'size', 'belongs_to']);

        dd(
            Model::sqlFormatter($m->dd(), true)
        );
    }

    /*
        Sql formateador es aplicado en un segundo paso
        y se parametriza para colorizar pero usando el helper sql_formatter 
    */
    function test_sql_formatter005()
    {
        $m = DB::table('products')
            ->deleted()
            ->groupBy(['cost', 'size', 'belongs_to'])
            ->having(['cost', 100, '>='])
            ->or(function ($q) {
                $q->havingRaw('SUM(cost) > ?', [500])
                    ->having(['size' => '1L']);
            })
            ->orderBy(['size' => 'DESC'])
            ->select(['cost', 'size', 'belongs_to']);

        dd(
            sql_formatter($m->dd(), true)
        );
    }


    function test_middle_sql()
    {
        $str = 'SELECT * 
            FROM 
            tbl_categoria_persona 
            INNER JOIN tbl_usuario as __usu_intIdActualizador ON __usu_intIdActualizador.usu_intId = tbl_categoria_persona.usu_intIdActualizador 
            INNER JOIN tbl_usuario as __usu_intIdCreador ON __usu_intIdCreador.usu_intId = tbl_categoria_persona.usu_intIdCreador 
            WHERE deleted_at IS NULL';

        $ini = strpos($str, 'INNER JOIN');
        $end = strpos($str, 'WHERE');

        $inners = Strings::middle($str, $ini, $end);
        dd($inners);
    }

    function test_match()
    {
        $o = '--name=xYz';
        dd(Strings::match($o, '/^--name[=|:]([a-z][a-z0-9A-Z_]+)$/'));

        $o = '--namae=xYz';
        dd(Strings::match($o, [
            '/^--name[=|:]([a-z][a-z0-9A-Z_]+)$/',
            '/^--namae[=|:]([a-z][a-z0-9A-Z_]+)$/',
            '/^--nombre[=|:]([a-z][a-z0-9A-Z_]+)$/'
        ]));

        $o = '--dropColumn=toBeEarased';
        $dropColumn = Strings::matchParam($o, [
            'dropColumn',
            'removeColumn'
        ]);

        dd($dropColumn);

        $o = '--renameColumn=aBcK,jU000w';
        dd(Strings::match($o, '/^--renameColumn[=|:]([a-z0-9A-Z_-]+\,[a-z0-9A-Z_-]+)$/'));
    }

    /*  
        Strings::middle() works ok if $end is false
    */
    function test_middle2()
    {
        $str = 'SELECT * 
            FROM 
            tbl_categoria_persona 
            INNER JOIN tbl_usuario as __usu_intIdActualizador ON __usu_intIdActualizador.usu_intId = tbl_categoria_persona.usu_intIdActualizador 
            INNER JOIN tbl_usuario as __usu_intIdCreador ON __usu_intIdCreador.usu_intId = tbl_categoria_persona.usu_intIdCreador';

        $ini = strpos($str, 'INNER JOIN');
        $end = strpos($str, 'WHERE');

        dd($end);

        $inners = Strings::middle($str, $ini, $end);
        dd($inners);
    }

    function test_contains()
    {
        $string = 'The lazy fox jumped over the fence';

        if (Strings::contains('lazy', $string)) {
            echo "The string 'lazy' was found in the string\n";
        }

        if (Strings::contains('Lazy', $string)) {
            echo 'The string "Lazy" was found in the string';
        } else {
            echo '"Lazy" was not found because the case does not match';
        }

        if (Strings::contains('Lazy', $string, false)) {
            echo 'The string "Lazy" was found in the string';
        } else {
            echo '"Lazy" was not found because the case does not match';
        }
    }


    function vvv()
    {
        $sql = 'SELECT * 
            FROM 
            tbl_categoria_persona 
            INNER JOIN tbl_usuario as __usu_intIdActualizador ON __usu_intIdActualizador.usu_intId = tbl_categoria_persona.usu_intIdActualizador 
            INNER JOIN tbl_usuario as __usu_intIdCreador ON __usu_intIdCreador.usu_intId = tbl_categoria_persona.usu_intIdCreador 
            WHERE deleted_at IS NULL';

        $ini = strpos($sql, 'INNER JOIN');
        $end = strpos($sql, 'WHERE ');

        $inners = trim(Strings::middle($sql, $ini, $end));
        $in_arr = explode('INNER JOIN ', $inners);

        $aliases = [];
        foreach ($in_arr as $ix => $inner) {
            if (empty($inner)) {
                unset($in_arr[$ix]);
                continue;
            }

            if (!preg_match('/[a-zA-Z0-9_]+ as ([a-zA-Z0-9_]+) ON (.*)/', $inner, $matches)) {
                throw new \Exception("SQL Error. Something was wrong");
            }

            $aliases[] = $matches[1];
            $ons[] = $matches[2];
        }

        dd($aliases);
        dd($ons);
    }

    // envio de archivo encodeado en base 64
    function test_numrot_dian()
    {
        $xml_file = file_get_contents(ETC_PATH . 'ad00148980970002000000067.xml');

        $xml_file_encoded = base64_encode($xml_file);

        $response = consume_api('http://34.204.139.241:8084/api/SendDIAN', 'POST', $xml_file_encoded, [
            "Content-type"  => "text/plain",
            "Authorization" => "Bearer eyJhbGciOiJIUzM4NCIsInR5cCI6IkpXVCJ9.eyJlbWFpbCI6ImluZm9hZGFwdGFkb3JAbnVtcm90LmNvbSIsInJvbGUiOiJDbGllbnRlIiwibmJmIjoxNjM2MDQ0NTQ0LCJleHAiOjE2OTkxMTY1NDQsImlhdCI6MTYzNjA0NDU0NCwiaXNzIjoibnVtcm90IiwiYXVkIjoicmVhZGVycyJ9.yejhLDwaVb4enDgKPssXyf8SYP1AyrEEa5m99joo3EjG3bhMToUPnY5696sjU6Kb"
        ]);

        dd($response, 'RES');
    }

    function check_tenant_group()
    {
        $db = 'db_200';
        dd(DB::getTenantGroupName($db), "Group name for tenant_id = $db");

        $db = 'az';
        dd(DB::getTenantGroupName($db), "Group name for tenant_id = $db");

        $db = 'db_flor';
        dd(DB::getTenantGroupName($db), "Group name for tenant_id = $db");
    }

    function check_schema_path()
    {
        dd(get_schema_path('tbl_arl', 'db_flor'));
        dd(get_schema_path('products', 'az'));
        dd(get_schema_path());
    }

    function test_remove_unnecesary_slashes()
    {
        dd(Files::normalize('/home/www/simplerest/docs//DOC Simplerest.txt'));
        dd(Files::normalize('c:\windows\\system32'));
    }

    function test_glob()
    {
        dd(glob('*.zip'));
    }

    function test_abs_path()
    {
        dd(Files::getAbsolutePath("docs/x.txt"));
    }

    function test_is_dir()
    {
        dd(is_dir('/home/www/simplerest/docsX/dev'));
    }

    function test_rglob()
    {
        dd(Files::recursiveGlob(ROOT_PATH . '/*.php'));
    }

    function test_mkdir_ignore()
    {
        dd(Files::mkDir('/home/feli/Desktop/UPDATE/config'));
    }

    function test_writable()
    {
        Files::writableOrFail(APP_PATH . 'some_dir');
    }

    function test_hardware_id()
    {
        dd(Hardware::UniqueMachineID());
    }

    function test_parse_ini()
    {
        dd(parse_ini_file(ROOT_PATH . '.env'));
    }

    function test_env()
    {
        // dd(Env::get());
        dd(Env::get('MAIL_PORT'));
    }

    function test_slash_string_fns()
    {
        dd(Files::normalize('c:\\windows'));
        dd(Files::removeTrailingSlash('/home/www/simplerest/'));
        dd(Files::removeTrailingSlash('/home/www/simplerest'));
        dd(Files::addTrailingSlash('/home/www/simplerest'));
        dd(Files::addTrailingSlash('/home/www/simplerest/'));
    }


    function grouped_dbs()
    {
        dd(DB::getGroupedDatabases());
    }

    function group_names()
    {
        dd(DB::getTenantGroupNames());
    }

    function test_conn_ids()
    {
        dd(DB::getConnectionIds());
    }

    function test_get_current_conn()
    {
        DB::setConnection('db_flor');
        dd(DB::getCurrentConnectionId());
    }

    function testtt()
    {
        $t1 = 'api_keys';
        $t2 = 'users';
        $from_db = 'az';

        $mul = is_mul_rel($t1, $t2, null, $from_db);
        dd($mul);
    }

    function get_mul()
    {
        $tenant_id = 'az';

        $t1 = 'products';
        $t2 = 'product_categories';

        //dd(is_mul_rel_cached($t1, $t2, null, $tenant_id), "$t1~$t2");  ///
        dd(is_mul_rel($t1, $t2, null, $tenant_id), "$t1~$t2");

        $t1 = 'product_categories';
        $t2 = 'products';

        //dd(is_mul_rel_cached($t1, $t2, null, $tenant_id), "$t1~$t2");  ///
        dd(is_mul_rel($t1, $t2, null, $tenant_id), "$t1~$t2");


        $t1 = 'product_categories';
        $t2 = 'products';

        dd(is_mul_rel($t1, $t2, null, $tenant_id), "$t1~$t2");

        $t1 = 'u';
        $t2 = 'u_settings';

        //dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");
        dd(is_mul_rel($t1, $t2, null, $tenant_id), "$t1~$t2");


        $t1 = 'u_settings';
        $t2 = 'u';

        //dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");
        dd(is_mul_rel($t1, $t2, null, $tenant_id), "$t1~$t2");


        $t1 = 'products';
        $t2 = 'users';

        //dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");
        dd(is_mul_rel($t1, $t2, null, $tenant_id), "$t1~$t2");


        $t1 = 'products';
        $t2 = 'product_tags';

        //dd(get_rel_type($t1, $t2, null, $tenant_id), "$t1~$t2");
        dd(is_mul_rel($t1, $t2, null, $tenant_id), "$t1~$t2");


        $t1 = 'tbl_persona';
        $t2 = 'tbl_pais';

        dd(is_mul_rel($t1, $t2, null, 'db_flor'), "$t1~$t2");


        $t1 = 'tbc1';
        $t2 = 'tbc2';

        // true -- ok
        dd(is_mul_rel($t1, $t2, null, 'az'), "$t1~$t2");

        $t1 = 'tbc2';
        $t2 = 'tbc1';

        // false -- ok
        dd(is_mul_rel($t1, $t2, null, 'az'), "$t1~$t2");

        $t1 = 'job_tbl';
        $t2 = 'usr_tbl';

        dd(is_mul_rel($t1, $t2, null, 'az'), "$t1~$t2");

        $t1 = 'usr_tbl';
        $t2 = 'job_tbl';

        dd(is_mul_rel($t1, $t2, null, 'az'), "$t1~$t2");


        $t1 = 'tbl_sub_cuenta_contable';
        $t2 = 'tbl_producto';

        dd(is_mul_rel($t1, $t2, null, 'db_flor'), "$t1~$t2");

        $t1 = 'tbl_producto';
        $t2 = 'tbl_sub_cuenta_contable';

        dd(is_mul_rel($t1, $t2, null, 'db_flor'), "$t1~$t2");


        $t1 = 'ur';
        $t2 = 'ur_settings';

        dd(is_mul_rel($t1, $t2, null, 'az'), "$t1~$t2");

        $t1 = 'products';
        $t2 = 'valoraciones';

        dd(is_mul_rel($t1, $t2, null, 'az'), "$t1~$t2");
        //dd(is_mul_rel_cached($t1, $t2, null, 'az'), "$t1~$t2"); 

        $t1 = 'tbl_producto';
        $t2 = 'tbl_unidadmedida';

        dd(is_mul_rel($t1, $t2, null, 'db_flor'), "$t1~$t2");
    }

    function test_foo()
    {
        $rows = DB::table('foo')
            ->get();

        dd($rows);
    }

    function ungrouped()
    {
        dd(DB::getUngroupedDatabases());
    }

    function test_del()
    {
        dd(Files::delete('app/controllers/ShopiController.php'));
        //Files::deleteOrFail('app/controllers/ShopiController.php');
    }

    function test_cp()
    {
        Files::delete('app/controllers/ShopiController.php'); // me aseguro no exista en destino
        Files::cp(
            'updates/2021-12-12-0.5.0-alpha/files/app/controllers/ShopiController.php',
            'app/controllers/ShopiController.php'
        );
    }

    /*
        Not file in destiny
    */
    function test_cp2()
    {
        Files::delete('app/controllers/ShopiController.php'); // me aseguro no exista en destino
        Files::cp(
            'updates/2021-12-12-0.5.0-alpha/files/app/controllers/ShopiController.php',
            'app/controllers'
        );
    }

    /*
        Not file in destiny and trailing slash
    */
    function test_cp3()
    {
        Files::delete('app/controllers/ShopiController.php'); // me aseguro no exista en destino
        Files::cp(
            'updates/2021-12-12-0.5.0-alpha/files/app/controllers/ShopiController.php',
            'app/controllers/'
        );
    }

    function test_cp_with_backup()
    {
        Files::setBackupDirectory(ROOT_PATH . 'backup');
        Files::cp(
            'updates/2021-12-12-0.5.0-alpha/files/app/controllers/ShopiController.php',
            'app/controllers/ShopiController.php'
        );
    }

    /*
        Sin especificar archivo en destino -- ok
    */
    function test_cp_with_backup2()
    {
        Files::setBackupDirectory(ROOT_PATH . 'backup');
        Files::cp(
            'updates/2021-12-12-0.5.0-alpha/files/app/controllers/ShopiController.php',
            'app/controllers'
        );
    }

    // OK
    function test_copy00()
    {
        $ori = '/home/www/simplerest';
        $dst = '/home/feli/Desktop/UPDATE';

        Files::mkDirOrFail($dst);
        Files::delTree($dst);

        Files::copy(
            $ori,
            $dst,
            [
                'docs',
                '/home/www/simplerest/vendor/psr/http-client/src/ClientInterface.php',
                'config/config.php'
                //'/home/www/simplerest/config/config.php'
            ],
            [
                'docs/dev',
                #'/home/www/simplerest/docs/dev/TODO Supra.txt',
                'docs/INSTALACION.txt'
            ]
        );
    }

    // OK
    function test_copy01()
    {
        $ori = '/home/www';
        $dst = '/home/feli/Desktop/UPDATE';

        Files::mkDirOrFail($dst);
        Files::delTree($dst);

        Files::copy(
            $ori,
            $dst,
            [
                'simplerest'
            ],
            [
                'simplerest/docs/dev',
                'simplerest/docs/INSTALACION.txt'
            ]
        );
    }

    function test_copy0x()
    {
        $ori = '/tmp/obsfuscated/yakpro-po/obfuscated';
        $dst = '/home/www/woo4/wp-content/plugins/woo-sizes.obfuscated/';
        $files  = null;
        $except = array(
            'obf.yaml',
            'glob:*.zip'
        );

        Files::mkDirOrFail($dst);
        Files::delTree($dst);

        Files::copy($ori, $dst, $files, $except);
    }

    function test_copy01a0()
    {
        $ori = '/home/www/html/erp/updates/2021-12-12-0.5.0-alpha/files';
        $dst = '/home/www/html/erp/';

        Files::copy($ori, $dst, [
            'config/constants.php',
            'app/controllers/api/Me.php'
        ]);
    }


    function test_copy01a1()
    {
        $ori = '/home/www/html/erp/updates/2021-12-12-0.5.0-alpha/files';
        $dst = '/home/www/html/erp/';

        Files::copy($ori, $dst, [
            '/home/www/html/erp/updates/2021-12-12-0.5.0-alpha/files/config/constants.php',
            '/home/www/html/erp/updates/2021-12-12-0.5.0-alpha/files/app/controllers/api/Me.php'
        ]);
    }

    function test_copy01a2()
    {
        $ori = '/home/www/html/erp/updates/2021-12-12-0.5.0-alpha/files';
        $dst = '/home/www/html/erp/';

        Files::copy($ori, $dst);
    }

    function test_copy01b()
    {
        $ori = '/home/www/simplerest';
        $dst = '/home/feli/Desktop/UPDATE';

        Files::mkDirOrFail($dst);
        Files::delTree($dst);


        Files::copy(
            $ori,
            $dst,
            [
                'docs',
                'vendor/psr/http-client/src/'
            ],
            [
                'docs/dev',
                '/home/www/simplerest/docs/dev/TODO Supra.txt',
                'docs/INSTALACION.txt'
            ]
        );
    }

    /*
        Con un directorio de ruta absoluta en $files
    */
    function test_copy01c()
    {
        $ori = '/home/www/simplerest';
        $dst = '/home/feli/Desktop/UPDATE';

        // Antes de iniciar la prueba limpio el directorio destino
        Files::delTree($dst);

        Files::copy(
            $ori,
            $dst,
            [
                'docs',
                '/home/www/simplerest/vendor/psr/http-client/src/'
            ],
            [
                'docs/dev',
                '/home/www/simplerest/docs/dev/TODO Supra.txt',
                'docs/INSTALACION.txt'
            ]
        );
    }

    /*
        Con un directorio de ruta absoluta en $files
        y backup
    */
    function test_copy01d()
    {
        $ori = '/home/www/simplerest';
        $dst = '/home/feli/Desktop/UPDATE';

        Files::setBackupDirectory();

        Files::copy(
            $ori,
            $dst,
            [
                'docs',
                '/home/www/simplerest/vendor/psr/http-client/src/'
            ],
            [
                'docs/dev',
                '/home/www/simplerest/docs/dev/TODO Supra.txt',
                'docs/INSTALACION.txt'
            ]
        );
    }

    function test_copy()
    {
        $ori = '/home/www/simplerest';
        $dst = '/home/feli/Desktop/UPDATE';

        // Antes de iniciar la prueba limpio el directorio destino
        Files::delTree($dst);

        $str_files = <<<'FILES'
        app/libs
        config/constants.php
        app/controllers
        FILES;

        $files = explode(PHP_EOL, $str_files);

        Files::copy($ori, $dst, $files, [
            'app/libs/db_dynamic_load.php',
            'app/controllers/PrepareUpdateController.php',
            'glob:*.zip'
        ]);
    }

    function test_copy2()
    {
        $ori = '/home/www/simplerest';
        $dst = '/home/feli/Desktop/UPDATE';

        // Antes de iniciar la prueba limpio el directorio destino
        Files::delTree($dst);

        $str_files = <<<'FILES'
        app/libs
        glob:*.txt
        FILES;

        $files = explode(PHP_EOL, $str_files);

        Files::copy($ori, $dst, $files, [
            'app/libs/db_dynamic_load.php',
            'app/controllers/PrepareUpdateController.php',
            'glob:*.zip'
        ]);
    }

    /*
        OJO !!! prodría ser una prueba peligrosa !
    */
    function test_copy_with_backup()
    {
        $ori = '/home/www/simplerest/updates/2021-12-12-0.5.0-alpha/files';
        $dst = '/home/www/simplerest_bk';

        Files::setBackupDirectory(ROOT_PATH . 'backup');

        Files::copy(
            $ori,
            $dst,
            [
                'app'
            ],
            [
                // except nothing
            ]
        );
    }

    /*
        $dst no está dentro de ROOT_PATH
    */
    function test_copy_with_backup2()
    {
        $ori = '/home/www/simplerest';
        $dst = '/home/feli/Desktop/UPDATE';;

        Files::setBackupDirectory(ROOT_PATH . 'backup');

        Files::copy(
            $ori,
            $dst,
            [
                'docs'
            ],
            [
                'docs/dev',
                '/home/www/simplerest/docs/dev/TODO Supra.txt',
                'docs/INSTALACION.txt'
            ]
        );


        $str_files = <<<'FILES'
        app/libs
        config/constants.php
        app/controllers
        FILES;

        $files = explode(PHP_EOL, $str_files);

        Files::copy($ori, $dst, $files, [
            'app/libs/db_dynamic_load.php',
            'app/controllers/PrepareUpdateController.php',
            //'*.zip'            
        ]);
    }


    /*
        Debo agregar si no existe como primer use

        use simplerest\models\MyModel;
    */
    function test_sc()
    {
        $path = '/home/www/simplerest/app/models/BoletasModel.php';
        $file = file_get_contents($path);

        if (!Strings::contains('use simplerest\models\MyModel;', $file)) {
            $lines = explode(PHP_EOL, $file);

            foreach ($lines as $ix => $line) {
                $line = trim($line);

                if (Strings::startsWith('use ', $line)) {
                    $lines[$ix] = 'use simplerest\models\MyModel;' . PHP_EOL . $line;
                    break;
                }
            }

            $file = implode(PHP_EOL, $lines);
            $ok = file_put_contents($path, $file);
        }
    }


    function prepare_default()
    {
        $ori = '/home/www/simplerest';
        $dst = '/home/feli/Desktop/UPDATE';

        // Solo para pruebas !!!!
        Files::delTree($dst);

        $str_files = <<<'FILES'
        app/libs
        docs
        config/constants.php
        FILES;

        $files = explode(PHP_EOL, $str_files);

        $except =  [
            'db_dynamic_load.php',
            'PrepareUpdateController.php',
            'docs/dev',
            'glob:*.zip'
        ];

        Files::copy($ori, $dst, $files, $except);
    }

    function test_at()
    {
        for ($i = 0; $i < 100000; $i++) {
            dd(at());
        }

        // esta vez debería ser un valor distinto
        dd(at(false));
    }

    function test_dates()
    {
        //  mes 1-12
        dd(datetime('n'));

        // día 1-31
        dd(datetime('j'));

        // weekday (0-6)
        dd(datetime('w'));

        // hour
        dd(datetime('G'));

        // minutes
        dd((int) datetime('i'));

        // seconds
        dd((int) datetime('s'));
    }

    function test_next_dates()
    {
        dd(Date::nextYearFirstDay());
        dd(Date::nextMonthFirstDay());
        dd(Date::nextWeek());
    }

    function test_get_fk()
    {
        $t1 = 'products';
        $t2 = 'product_categories';

        dd(get_fks($t1, $t2), "FKs $t1 ->  $t2");


        $t1 = 'tbl_genero';
        $t2 = 'tbl_usuario';

        dd(get_fks($t1, $t2, 'db_flor'), "FKs $t1 ->  $t2");
    }

    function test_zip()
    {
        $ori = '/home/www/html/pruebas/drag';
        $dst = '/home/feli/Desktop/UPDATE/drag.zip';

        ZipManager::zip($ori, $dst, [
            'file_to_be_ignored.txt',
            "$ori/jquery-ui-1.12.1.custom"
        ]);
    }

    function mkdir()
    {
        $tmp_dst = '/tmp/simplerest';

        if (is_dir($tmp_dst)) {
            Files::delTree($tmp_dst, true);
        }

        mkdir($tmp_dst);
    }

    function gt2()
    {
        DB::getConnection('db_flor');
        $tables = Schema::getTables();

        dd($tables, 'TABLES');
    }

    function test_sql_mode()
    {
        $dsn = "mysql:host=127.0.0.1;dbname=db_168;port=3306";
        $username = "boctulus";
        $password = 'gogogo#*$U&_441@#';

        $pdo_opt = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET SESSION sql_mode="TRADITIONAL"'
        ];

        dd($pdo_opt, 'PDO OPTIONS');

        $conn = new \PDO(
            $dsn,
            $username,
            $password,
            $pdo_opt
        );

        $sql = "INSERT INTO tbl_categoria_persona(cap_intId, cap_varCategoriaPersona, cap_dtimFechaCreacion, cap_dtimFechaActualizacion, est_intIdEstado, usu_intIdCreador, usu_intIdActualizador) VALUES
        (1, 'Empleado', '2021-05-20 11:40:29', '2021-06-30 09:38:58', 1, 1, 1),
        (2, 'Tercero', '2021-05-20 11:40:58', '2021-07-21 21:44:46', 1, 1, 1),
        (3, 'Visitante', '2021-06-25 15:21:04', '1000-01-01 00:00:00', 1, 1, 1),
        (4, 'Cliente', '2021-08-04 16:48:49', '1000-01-01 00:00:00', 1, 1, 1),
        (5, 'Proveedor', '2021-08-04 16:48:58', '1000-01-01 00:00:00', 1, 1, 1),
        (6, 'Interesado', '2021-08-04 16:49:09', '1000-01-01 00:00:00', 1, 1, 1);";

        $stmt = $conn->prepare($sql);
        $res = $stmt->execute();

        dd($res, 'RES');
    }

    function test_file_exits()
    {
        $full_path = '/home/www/simplerest/app/migrations/compania/2021_09_28_29110773_0010_tbl_genero_maestro.php';

        var_dump(file_exists($full_path), 'EXISTE?');

        if (!file_exists($full_path)) {
            StdOut::print("Path '$full_path' does not exist !");
            exit;
        }
    }

    // from Yii
    function generateRandomString($length = 10)
    {
        $bytes = random_bytes($length);
        return substr(strtr(base64_encode($bytes), '+/', '-_'), 0, $length);
    }

    function test_dynamic_db_creation()
    {
        $db_name = 'db_' . $this->generateRandomString();

        /*
            Voy a registrar la base de datos para su creación
        */

        $tenant = $db_name;
        $uid = 4;
        $at = at();

        DB::getDefaultConnection();

        $db_id = DB::table('tbl_base_datos')
            ->fill(['usu_intIdActualizador'])
            ->create([
                'dba_varNombre'    => $tenant,
                'usu_intIdCreador' => $uid,
                'usu_intIdActualizador' => $uid,
                'dba_dtimFechaCreacion' => $at,
                'dba_dtimFechaActualizacion' => $at
            ]);


        // here(); 
        // exit; ///
        // DB::getConnection($db_name);
    }

    function test_last_update_dir()
    {
        dd(Update::getLastVersionInDirectories());
    }

    function test_100()
    {
        $q = "SELECT * FROM products INNER JOIN product_categories as pc ON pc.id_catego=products.category WHERE (pc.name_catego = 'frutas') AND products.deleted_at IS NULL LIMIT 10";

        dd(DB::select($q));
    }

    function test_101()
    {
        $q = "SELECT * FROM products INNER JOIN product_categories as pc ON pc.id_catego=products.category WHERE (pc.name_catego = ?)
        AND products.deleted_at IS NULL LIMIT ?";

        dd(DB::select($q, ['frutas', 10]));
    }

    function test_102()
    {
        $conds = [
            'pc.name_catego',
            'frutas',
        ];

        $m = DB::table('products');
        $m
            ->where($conds)
            ->join('product_categories as pc');

        dd($m->dd(true));

        $total = (int) ($m
            ->column()
            ->count()
        );

        dd($total, 'total');
    }

    function test_update_cmp()
    {
        $v1 = '0.5.0';
        $v2 = '0.6.0';
        dd(Update::compareVersionStrings($v1, $v2), "$v1 respecto de $v2");

        $v1 = '0.5.0';
        $v2 = '0.4.0';
        dd(Update::compareVersionStrings($v1, $v2), "$v1 respecto de $v2");

        $v1 = '0.5.0-alpha';
        $v2 = '0.4.0';
        dd(Update::compareVersionStrings($v1, $v2), "$v1 respecto de $v2");

        $v1 = '0.5.0';
        $v2 = '0.4.0-alpha';
        dd(Update::compareVersionStrings($v1, $v2), "$v1 respecto de $v2");

        $v1 = '0.5.0-alpha';
        $v2 = '0.4.0-alpha';
        dd(Update::compareVersionStrings($v1, $v2), "$v1 respecto de $v2");

        $v1 = '0.5.0-alpha';
        $v2 = '0.4.0-beta';
        dd(Update::compareVersionStrings($v1, $v2), "$v1 respecto de $v2");

        $v1 = '0.5.0-alpha';
        $v2 = '0.5.0-beta';
        dd(Update::compareVersionStrings($v1, $v2), "$v1 respecto de $v2");

        $v1 = '0.5.0-beta';
        $v2 = '0.5.0-alpha';
        dd(Update::compareVersionStrings($v1, $v2), "$v1 respecto de $v2");
    }

    function get_random_user()
    {
        DB::getDefaultConnection();
        dd(
            DB::table(get_users_table())
                ->random()->dd()
        );
    }

    function get_random_product()
    {
        dd(
            DB::table('products')
                ->random()->top()
        );
    }

    function test_390()
    {
        dd(DB::table('super_cool_table')->id());
    }

    /*
        Obtengo el último registro creado
    */
    function test_400()
    {
        $m = DB::table('products');

        $row = $m
            ->deleted()
            ->orderBy([$m->createdAt() => 'DESC'])
            ->first();

        dd($row);
        dd($m->dd());
    }

    function m()
    {
        $is = DB::table('products')
            ->find(145)
            ->exists();

        if ($is) {
            $row = DB::table('products')
                ->find(145)
                ->first();

            dd($row, "La row existe");
            return;
        }

        dd("Row fue borrada.");
    }

    function get_prods()
    {
        $rows = DB::table('products')
            ->whereIn('id', [177, 178])
            ->get();

        dd($rows);
    }

    function undelete_prods()
    {
        DB::getConnection('az');

        $rows = DB::table('products')
            ->whereIn('id', [177, 178])
            ->undelete();

        dd($rows);
    }

    function delete_counter()
    {
        DB::getConnection('az');

        $m = DB::table('foo')
            ->where(['id', 2, '>']);

        $cnt = $m->delete();

        dd($cnt, 'regs');
    }

    function test_delete()
    {
        // DB::getConnection('az');

        $m = table('product_valoraciones');

        $m
            ->whereRaw("product_id = ?", [100])
            ->dontExec()
            ->delete();

        dd($m->getLog());
    }

    function test_delete_raw()
    {
        dd(DB::delete('DELETE FROM `baz2` WHERE id_baz2 = ?', [5000]));
    }

    function test_delete1()
    {
        $row = DB::table('products')
            ->findOrFail(145)
            ->delete();
    }

    function test_trashed()
    {
        $m = DB::table('products');

        dd($m
            ->find(145)
            ->trashed());

        dd($m->dd());
    }

    function test_undelete()
    {
        $is = DB::table('products')
            ->find(145)
            ->exists();

        if ($is) {
            $row = DB::table('products')
                ->find(145)
                ->first();

            dd($row, "La row existe");
            return;
        }

        dd("Row fue borrada. Intento restaurar");

        $m = DB::table('products');

        $row = $m
            ->find(145)
            ->undelete();

        dd($m->getLog());

        $row = $m = DB::table('products')
            ->find(145)
            ->first();
        dd($row);
    }

    function test_force_del()
    {
        $m = DB::table('products');
        $m->find(5512)
            ->forceDelete();
    }

    function get_products_no_get()
    {
        $m = DB::table('products');
        $cnt = $m->count();

        dd($cnt, 'regs');
    }

    function get_products_with_trashed()
    {
        $m = DB::table('products');
        $cnt = $m->withTrashed()->count();

        dd($cnt, 'regs');
    }

    function get_products_only_trashed()
    {
        $m = DB::table('products');
        $cnt = $m->onlyTrashed()->count();

        dd($cnt, 'regs');
    }

    function test_103()
    {
        dd(
            DB::table('products')
                ->leftJoin("product_categories")
                ->leftJoin("product_tags")
                ->leftJoin("valoraciones")
                ->find(145)->first()
        );
    }

    function test_104()
    {
        dd(
            DB::table("product_tags")
                ->where(['product_id', 145])
                ->get()
        );
    }

    function test_105()
    {
        // $m = DB::table('product_valoraciones');
        // $m
        // ->whereRaw("product_id = ?", [145])
        // ->delete();            

        dd(
            DB::table('product_valoraciones')
                ->join('valoraciones')
                ->where(['product_id', 145])
                ->get()
        );
    }


    function test_q()
    {
        $sql = file_get_contents(ETC_PATH . 'test.sql');
        dd(DB::select($sql));
    }



    function test_desentrelazado()
    {
        $literal = strrev("woo4.lan");

        // protect spaces
        $literal = str_replace(' ', '-', $literal);

        dd(Strings::deinterlace($literal));
    }

    function test_entrelazado()
    {
        $str = [
            'SmlRs rmwr rae yPboBzoo<otlsA mi.o> l ihsrsre.',
            'ipeetfaeokcetdb al ozl bcuu Tgalcm.Alrgt eevd'
        ];

        return Strings::interlace($str);
    }

    function test_unserialize()
    {
        $s_object = 'O:29:"simplerest\background\tasks\DosTask":0:{}';
        $s_params = 'a:2:{i:0;s:4:"Juan";i:1;i:39;}';

        $o = unserialize($s_object);
        $p = unserialize($s_params);

        $o->run(...$p);
    }



    function test_date3()
    {
        // dd(Date::nextNthMonthFirstDay(12));
        // dd(Date::nextNthMonthFirstDay(1));
        // dd(Date::nextNthMonthFirstDay(4));

        // dd(Date::nextNthWeekDay(5));

        dd(Date::nextNthMonthDay(5));
        dd(Date::nextNthMonthDay(18));
    }

    /*
        Esto podría funcionar con el Router

        Route::get('/user/{id}', DumbController::class);

        Eso habilitaria: /dumb/6 
    */
    function __invoke(int $id)
    {
        dd($id);
    }

    function test_container()
    {
        Container::bind('foo', function () {
            return new Foo();
        });

        $foo = Container::make('foo');
        print_r($foo->bar());

        $foo = Container::make('foo');
        print_r($foo->bar());
    }

    function test_container2()
    {
        Container::bind('foo', Foo::class);

        $foo = Container::make('foo');
        print_r($foo->bar());

        $foo = Container::make('foo');
        print_r($foo->bar());
    }

    function test_container3()
    {
        Container::singleton('foo', Foo::class);

        $foo = Container::make('foo');
        print_r($foo->bar());

        $foo = Container::make('foo');
        print_r($foo->bar());
    }

    function test_container4()
    {
        Container::bind('car', \simplerest\libs\Car::class);

        $o = Container::makeWith('car', ['color' => 'blue', 'max_speed' => 200]);
        print_r($o->run());
        print_r($o);
    }

    /*
        Leer la documentacion
    */
    function test_dep_container()
    {
        Container::bind(Cake::class, function () {
            $ingredient1 = new Ingredient1();
            $ingredient2 = new Ingredient2();

            return new Cake($ingredient1, $ingredient2, 25);
        });

        Container::singleton('cake', Cake::class);

        // $foo = Container::make('cake');
        // print_r($foo->bar());

        // $foo = Container::make('cake');
        // print_r($foo->bar());
    }


    /*
        Haciendo uso de Container::useContract(), intentar replicar:

        https://stackoverflow.com/a/52778193/980631
    */
    function test_container5()
    {

        // ....
    }


    function test_5055()
    {
        DB::getConnection('db_docker_php81_mysql');
    }

    function some_work()
    {
        VarDump::log();

        for ($i = 1; $i <= rand(5, 10); $i++) {
            dd($i, 'i');
            sleep(1);
        }
    }

    function test_tag()
    {
        Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

        //Form::pretty();

        // echo Bt5Form::span(text:'Hi');

        // echo tag('text')->name('bozzolo')->placeholder('Su apellido');

        // echo tag('p')->text('Some paragraph')->class('mt-3')->textMuted();
        // echo tag('div')->content('Some content')->class('mt-3')->textMuted();

        // echo Bt5Form::group(
        //     content:[
        //         Bt5Form::span('@', [
        //             'id'    => 'basic-addon',
        //             'class' => 'input-group-text'
        //         ]),
        //         Bt5Form::inputText('nombre', [
        //             "placeholder" => "Username"
        //         ])
        //     ],
        //     tag: 'div',
        //     class:"input-group mb-3"
        // );

        // echo Bt5Form::div(
        //     content:
        //         Bt5Form::span('@', [
        //             'id'    => 'basic-addon',
        //             'class' => 'input-group-text'
        //         ]) .
        //         Bt5Form::inputText('nombre', [
        //             "placeholder" => "Username"
        //         ])
        //     ,
        //     class:"input-group mb-3"
        // );

        // echo Bt5Form::p();

        // echo Bt5Form::div(
        //     content:[
        //         Bt5Form::span('@', [
        //             'id'    => 'basic-addon',
        //             'class' => 'input-group-text'
        //         ]),

        //         Bt5Form::div(content:[
        //             Bt5Form::inputText('nombre', [
        //                 "placeholder" => "Username"
        //             ]),
        //             Bt5Form::p()
        //         ], class:'my_class')
        //     ],
        //     class:"input-group mb-3"
        // );


        // echo tag('select')->name('sexo')->options([
        //     'varon' => 1,
        //     'mujer' => 2
        // ])->default(1)->placeholder('Su sexo')->class('my-3');

        // echo tag('div')->content([
        //     Bt5Form::span('@', [
        //         'id'    => 'basic-addon',
        //         'class' => 'input-group-text'
        //     ]),
        //     Bt5Form::inputText('nombre', [
        //         "placeholder" => "Username"
        //     ])
        // ])->class("input-group mb-3");

        // echo Html::beautifier(
        //     tag('div')->content([
        //     tag('span')->text('@')->id('basic-addon')->class('input-group-text'),
        //     tag('text')->name('nombre')->placeholder('Username')
        // ])->class("input-group mb-3")
        // );

        // echo tag('color')->name('my_color')->text('Color')->id('c1');

        // echo Bt5Form::select(name:'size', options:[
        //     'L' => 'Large', 
        //     'S' => 'Small'
        // ], placeholder:'Pick a size...', default:'Large');

        //echo tag('button')->content('Botón rojo')->danger()->class('rounded-pill mx-3')->outline();

        // echo tag('card')->content(
        //     tag('cardBody')->content(
        //         tag('cardTitle')->text('Some title') .
        //         tag('cardSubtitle')->text('Some subtitle')->textMuted()
        //     )
        // )->class('mb-4');

        // echo tag('carousel')->content(
        //     tag('carouselInner')->content([
        //         tag('carouselItem')->content(
        //             tag('img')->class("d-block w-100")->src('https://solucionbinaria.com/assets/images/porfolio/elgrove.png')
        //         ),
        //         tag('carouselItem')->content(
        //             tag('img')->class("d-block w-100")->src('https://solucionbinaria.com/assets/images/porfolio/drivingcars-cl2.png')
        //         ),
        //         tag('carouselItem')->content(
        //             tag('img')->class("d-block w-100")->src('https://solucionbinaria.com/assets/images/porfolio/acrilicosxtchile-cl.png')
        //         ),
        //     ])
        // );


        // $class = "class1 class2";
        // Bt5Form::addClass("hide", $class);
        // var_dump($class);

        //$class = "class1 btn-secondary class2";
        // Bt5Form::addColor("primary", $class);
        // var_dump($class);


        // $class = "btn-secondary btn";
        // Bt5Form::addColor("primary", $class);
        // var_dump($class);


        // $class = "class1 btn-secondary class2";
        // Bt5Form::addColor("primary", $class, true);
        // var_dump($class);

        // $class = "class2";
        // Bt5Form::addColor("primary", $class, true);
        // var_dump($class);

        // $class = "";
        // Bt5Form::addColor("primary", $class, true);
        // var_dump($class);


        // $class = "class1 hide class2";
        // Bt5Form::addClass("hide", $class);
        // var_dump($class);

        // $class = "class1 hide class2";
        // Bt5Form::addClasses("hide otra", $class);
        // var_dump($class);

        // $class = "class1 hide class2";
        // Bt5Form::addClasses(["hide", "otra"], $class);
        // var_dump($class);

        // $class = "";
        // Bt5Form::addClasses(["hide", "otra"], $class);
        // var_dump($class);

        // var_dump(Bt5Form::addClass("hide", "class1 class2 hide"));
        // var_dump(Bt5Form::addClass("hide", "class1 class3"));
        // var_dump(Bt5Form::addClass("hide", "hide class1 class3"));
        // var_dump(Bt5Form::addClass("hide", ""));

        // dd('');

        // var_dump(Bt5Form::removeClass("hide", "class1 class2"));
        // var_dump(Bt5Form::removeClass("hide", "hide class1 class2"));
        // var_dump(Bt5Form::removeClass("hide", "class1 hide class3"));
        // var_dump(Bt5Form::removeClass("hide", "class1 class4 hide"));

        // echo tag('modal')->class('fade')->content(
        //     tag('modalDialog')->content(
        //         tag('modalContent')->content(
        //             tag('modalHeader')->content(
        //                 tag('modalTitle')->text('Modal title') . 
        //                 tag('closeButton')->dataBsDismiss('modal')
        //             ) .
        //             tag('modalBody')->content(
        //                 tag('p')->text('Modal body text goes here.')
        //             ) 
        //         ) .

        //         tag('modalFooter')->content(
        //             tag('closeModal') .
        //             tag('button')->text('Save changes')
        //         )
        //     )
        // )->id('exampleModal');

        //echo tag('closeButton')->dataBsDismiss('modal')->content('');

        //echo tag('button')->class('btn btn-primary')->dataBsToogle('modal')->dataBsTarget("#exampleModal")->content('Launch demo modal');

        //echo tag('button')->success()->text('Save changes');

        //echo tag('closeModal');
        //    dd('--');
        //    echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';


        // dd(Form::hasColor("class1 btn-success class2", "success"));
        // dd(Form::hasColor("class1 btn-success class2", "btn-success")); 
        // dd(Form::hasColor("class1 btn-success class2", "danger")); 
        // dd(Form::hasColor("class1 btn-success class2"));
        // dd(Form::hasColor("class1 class2"));        

        //echo tag('button')->text('Save changes');
        // dd('');
        //echo tag('basicButton')->class('btn-danger')->class('btn-success')->text('Save changes');

        //echo tag('button')->class('btn-danger')->success()->text('Save changes');         

        // dd('');

        //echo tag('closeModal');

        // echo Bt5Form::div(
        //     content: [
        //         tag('span')->text('@')->id('basic-addon')->class('input-group-text'),
        //         tag('inputText')->name('nombre')->placeholder("Username")
        //     ],
        //     class: "input-group mb-3"
        // );

        // echo tag('modal')->content(
        //     tag('modalDialog')->content(
        //         tag('modalContent')->content(
        //             tag('modalHeader')->content(
        //                 tag('modalTitle')->text('Modal title') . 
        //                 tag('closeButton')->dataBsDismiss('modal')
        //             ) .
        //             tag('modalBody')->content(
        //                 tag('p')->text('Modal body text goes here.')
        //             ) . 
        //             tag('modalFooter')->content(
        //                 tag('closeModal') .
        //                 tag('button')->text('Save changes')
        //             ) 
        //         ) 
        //     )->large()
        // )->id('exampleModal');

        // echo tag('modal')
        // ->header(
        //     tag('modalTitle')->text('Modal title') . 
        //     tag('closeButton')->dataBsDismiss('modal')
        // )
        // ->body(
        //     tag('p')->text('Modal body text goes here!')
        // )
        // ->footer(
        //     tag('closeModal') .
        //     tag('button')->text('Save changes')
        // )
        // ->id('exampleModal');


        // echo tag('accordion')->items([
        //     [
        //         'id' => "flush-collapseOne",
        //         'title' => "Accordion Item #1",
        //         'body' => 'Placeholder content for this accordion, which is intended to demonstrate the <code>.accordion-flush</code> class. This is the first items accordion body.'
        //     ],
        //     [
        //         'id' => "flush-collapseTwo",
        //         'title' => "Accordion Item #2",
        //         'body' => 'Placeholder 2'
        //     ],
        //     [
        //         'id' => "flush-collapseThree",
        //         'title' => "Accordion Item #3",
        //         'body' =>  'Placeholder 3'
        //     ]
        // ])
        // ->id('accordionExample')
        // ->always_open(true)
        // ->attributes(['class' => 'accordion-flush']);

        // echo tag('carousel')->content(
        //     tag('carouselItem')->content(
        //         tag('carouselImg')->src(assets('img/slide-1.jpeg'))
        //     )->caption(
        //         '<h5>First slide label</h5>
        //         <p>Some representative placeholder content for the first slide.</p>'
        //     ),

        //     tag('carouselItem')->content(
        //         tag('carouselImg')->src(assets('img/slide-2.jpeg'))
        //     ),

        //     tag('carouselItem')->content(
        //         tag('carouselImg')->src(assets('img/slide-3.jpeg'))
        //     ),
        // )->id("carouselExampleControls")->withControls()->withIndicators()
        // // -->dark()
        // ;

        //echo tag('collapseButton')->target("#collapseExample")->content('Button with data-bs-target');
        //echo tag('dropdownLink')->id('dropdownMenuButton1')->href('#')->anchor('Dropdown button');

        //echo tag('dropdownButton')->id('dropdownMenuButton1')->content('Dropdown button')->danger()->large();

        // echo tag('formCheck')->content(
        //     tag('checkbox')->id("defaultCheck1").
        //     tag('label')->for("defaultCheck1")->text('Default checkbox')
        // )->class('my-3');

        // echo tag('formCheck')->content(
        //     tag('checkbox')->id("defaultCheck1")->disabled() .
        //     tag('formCheckLabel')->for("defaultCheck1")->placeholder('Disabled checkbox')
        // )->class('my-1');

        //echo tag('formCheck')->content('')->type('checkbox')->id("defaultCheck1")->text('Default checkbox')->class('me-2');

        // echo tag('listGroup')->content([
        //     tag('listGroupItem')->text('An item'),
        //     tag('listGroupItem')->text('An item #2'),
        //     tag('listGroupItem')->text('An item #3')
        // ])->class('mt-3');

        // echo tag('h3')->text("Datos")->class('mb-3');

        // echo tag('link')->href('#')->anchor('The Link')->title('Tool Tip!')->tooltip()->class('mt-3');

        // echo Bt5Form::link('The Link', '#', ...[
        //     'tooltip',
        //     'title' => 'My tooltip',
        //     'class' => 'mt-3'
        // ]);

        // echo tag('nav')->content([
        //     tag('navItem')->content(
        //         tag('navLink')->anchor('Active')
        //     ),
        //     tag('navItem')->content(
        //         tag('navLink')->anchor('Link')
        //     ),
        //     tag('navItem')->content(
        //         tag('navLink')->anchor('Link')
        //     ),
        //     tag('navItem')->content(
        //         tag('navLink')->anchor('Active')->disabled()
        //     )
        // ])->class('mb-3');     

        //Form::pretty();

        // echo tag('nav')->content([  
        // [
        //     'anchor' => 'Uno',
        //     'href'   => '#uno'
        // ],

        // [
        //     'anchor' => 'Dos',
        //     'href' => '#dos'
        // ],

        // [
        //     'anchor' => 'Tres',
        //     'href'   => '#tres'
        // ],
        // // tag('dropdown')->content(
        // //     tag('dropdownButton')->id('dropdownMenuButton')->content('Dropdown button') .    
        // //     tag('dropdownMenu')->ariaLabel('dropdownMenuButton')->content(
        // //         tag('dropdownItem')->href('#')->anchor('Action 1') .
        // //         tag('dropdownItem')->href('#')->anchor('Another action') .
        // //         tag('dropdownDivider') .
        // //         tag('dropdownItem')->href('#')->anchor('Something else here')
        // //     )
        // // ),
        // ])->class('mb-3')
        // ->justifyRight()
        // ->tabs()
        // ->role('tablist')
        // ->panes([
        //     'Textoooooooooo oo',
        //     'otroooooo',
        //     'y otro más'            
        // ]);  

        // echo tag('breadcrumb')->content([
        //     [
        //         'href' => '#',
        //         'anchor' => 'Home'
        //     ],

        //     [
        //         'href' => '#library',
        //         'anchor' => 'Library'
        //     ],

        //     [
        //         'anchor' => 'Data'
        //     ]
        // ]);

        /*
            Debería ser equivalente a:

            ->success()

        */
        //echo tag('button')->color('success')->value('Search');

        // echo tag('offcanvas')->id("offcanvasExample")->title('Offcanvas')->body([
        //     /*
        //         Body example
        //     */
        //     'Some text as placeholder. In real life you can have the elements you have chosen. Like, text, images, lists, etc.'
        // ])
        // ->backdropOff()
        // //->scroll()
        // ;

        //echo tag('spinner');

        // echo tag('div')->width(100)->content('
        // Some content,...
        // <p>
        // <p>
        // ')
        // //->border('top')
        // ->borderRad(2)
        // ->borderWidth(5)
        // ->borderColor('danger')
        // //->borderSub('top')
        // //->class('border border-top-0 border-5 border-danger rounded-3')
        // ;

        // echo tag('div')->width(100)->content('
        // Some content,...
        // <p>
        // <p>
        // ')
        // ->border('top left')
        // ->borderWidth(5)
        // ->borderRad(3)
        // ->borderColor('success')
        // ;

        // echo tag('div')->content(
        //     'Width 75%'
        //   )
        //   ->w(75)
        //   ->bg('warning')
        //   ->class('p-3');

        // echo tag('table')
        // ->rows([
        //   '#',
        //   'First',
        //   'Last',
        //   'Handle'
        // ])
        // ->cols([
        //   [
        //     1,
        //     'Mark',
        //     'Otto',
        //     '@mmd'
        //   ],
        //   [
        //     2,
        //     'Lara',
        //     'Cruz',
        //     '@fat'
        //   ],
        //   [
        //     3,
        //     'Lara',
        //     'Cruz',
        //     '@fat'
        //   ],
        //   [  
        //     4,
        //     'Feli',
        //     'Bozzolo',
        //     '@facebook'
        //   ]
        // ])->color('primary');

        // echo tag('h4')
        // ->text('Indigo!')
        // ->bg('indigo')
        // ->right();

        echo tag('inputGroup')
            ->content(
                tag('inputText')
            )
            ->prepend(
                tag('button')->danger()->content('Action')
            );

        // echo "\r\n\r\n\r\n";

    }

    function encoding()
    {
        dd(Factory::request()->gzip());
        dd(Factory::request()->acceptEncoding());
    }

    function mail()
    {
        dd(
            Mail::send('boctulus@gmail.com', 'Pablo', 'Pruebita ' . rand(99, 999999), null, 'boctulus@gmail.com')
        );
    }

    // function show_email_template(){
    //     $cols = [
    //         'Item',
    //         'Valor',
    //         'Moneda'
    //     ];

    //     $rows = [
    //         ['Valor de la compra en dólares', '$ 3000', 'USD'],
    //         ['Tarifa a cobrar por kilo', '$ 400', 'USD'],
    //         ['Kilos bruto', 2, 'Kg'],
    //         ['Flete aéreo (Miami-Santiago)', 0, 'USD'],
    //         ['Seguro (2%)',  0, 'USD'],
    //         ['Valor C.I.F',  0, 'USD'],
    //         ['Derechos (6%)', 0, 'USD'],
    //         ['Valor neto', 0, 'USD'],
    //         ['IVA', 0, 'USD'],
    //         ['Valor final', 0, 'USD'],
    //         ['TTL US$', 0, 'USD']
    //     ];

    //     $withs = [ 50, 30, 20 ];

    //     // config
    //     $locale = 'es_ES.UTF-8';

    //     $date     = new \Datetime();
    //     $datetime = ucfirst(\IntlDateFormatter::formatObject($date, 'EEEE', $locale)) .', '. ucfirst(\IntlDateFormatter::formatObject($date, 'dd-MM-Y', $locale));


    //     $image_header = [
    //         'src' => 'https://brimell.cl/wp-content/uploads/2022/01/cropped-cropped-BRIMELLtransparenteV01.png',
    //         'width' => "150",
    //         'height' => "150"
    //     ];

    //     $filecontents = file_get_contents($image_header['src']);
    //     $filecontents = base64_encode($filecontents);
    //     $image_header['src'] = "data:image/jpg;base64,".$filecontents;

    //     $footer = '<a href="https://brimell.cl/" style="color:#ffffff">Brimell</a> (2022) - Casilla internacional | Gestión de envíos';

    //     include ETC_PATH . 'email_template.php';
    // }

    // /*
    //     Usar postdrop.io para ensayar como se verían los mails

    //     Las imágenes deben tener:

    //     src
    //     alt
    //     width
    //     height
    //     border
    // */
    // function send_email_template(){
    //     $cols = [
    //         'Item',
    //         'Valor',
    //         'Moneda'
    //     ];

    //     $rows = [
    //         ['Valor de la compra en dólares', '$ 3000', 'USD'],
    //         ['Tarifa a cobrar por kilo', '$ 400', 'USD'],
    //         ['Kilos bruto', 2, 'Kg'],
    //         ['Flete aéreo (Miami-Santiago)', 0, 'USD'],
    //         ['Seguro (2%)',  0, 'USD'],
    //         ['Valor C.I.F',  0, 'USD'],
    //         ['Derechos (6%)', 0, 'USD'],
    //         ['Valor neto', 0, 'USD'],
    //         ['IVA', 0, 'USD'],
    //         ['Valor final', 0, 'USD'],
    //         ['TTL US$', 0, 'USD']
    //     ];

    //     $withs = [ 60, 25, 15 ];

    //     // config
    //     $locale = 'es_ES.UTF-8';

    //     $date     = new \Datetime();
    //     $datetime = ucfirst(\IntlDateFormatter::formatObject($date, 'EEEE', $locale)) .', '. ucfirst(\IntlDateFormatter::formatObject($date, 'dd-MM-Y', $locale));

    //     $image_header = [
    //         'src' => 'https://brimell.cl/wp-content/uploads/2022/01/cropped-cropped-BRIMELLtransparenteV01.png',
    //         'width' => "150",
    //         'height' => "150"
    //     ];

    //     $filecontents = file_get_contents($image_header['src']);
    //     $filecontents = base64_encode($filecontents);
    //     $image_header['src'] = "data:image/jpg;base64,".$filecontents;

    //     $footer = '<a href="https://brimell.cl/" style="color:#ffffff">Brimell</a> (2022) ~ Casilla internacional - Gestión de envíos';

    //     ob_start();
    //     include ETC_PATH . 'email_template.php';
    //     $content = ob_get_contents();
    //     ob_end_clean();


    //     dd(
    //         Mail::sendMail('boctulus@gmail.com', 'Pablo', 'Pruebita '. rand(99,999999), $content)
    //     );     
    // }

    // function cotiza($declarado_usd, $peso, $dim1, $dim2, $dim3, $unidad_long){
    //     // Las dimensiones ingresan en cm
    //     // peso en kilogramos

    //     // O se capturan excepciones o se genera directamente la respuesta JSON

    //     if (!in_array($unidad_long, ['cm', 'mt', 'pulg'])){
    //         throw new \InvalidArgumentException("Unidad de longitud puede ser solo cm, mt o pulg");
    //     }

    //     if ($peso > MAX_PESO){
    //         throw new \Exception("El peso máximo es de ". MAX_PESO ." Kg.");
    //     }

    //     if (max($dim1, $dim2, $dim3) > MAX_DIM){
    //         throw new \Exception("Ninguna dimensión puede superar los ". MAX_DIM." cm.");
    //     }

    //     $declarado_usd = max($declarado_usd ?? 0, MIN_DECLARADO);

    //     $dim1 = $dim1 ?? 0;
    //     $dim2 = $dim2 ?? 0;
    //     $dim3 = $dim3 ?? 0;

    //     $peso_ori = $peso;
    //     //dd($peso_ori, 'Peso original');

    //     $dim1_ori = $dim1;
    //     $dim2_ori = $dim2;
    //     $dim3_ori = $dim3;

    //     switch ($unidad_long){
    //         case 'mt': 
    //             $dim1 *=  100;
    //             $dim2 *=  100;
    //             $dim3 *=  100;
    //         break;
    //         case 'pulg':
    //             $dim1 *=  100/39.37;
    //             $dim2 *=  100/39.37;
    //             $dim3 *=  100/39.37;
    //         break;	
    //     }

    //     // dd($dim1, 'dim1');
    //     // dd($dim2, 'dim2');
    //     // dd($dim3, 'dim3');

    //     $peso_volumetrico = $dim1 * $dim2 * $dim3 * 0.000001;
    //     //dd($peso_volumetrico, 'Peso vol');

    //     $peso_volumetrico_corregido = $peso_volumetrico * PV;
    //     //dd($peso_volumetrico_corregido, 'Peso vol corregido');

    //     $peso = max($peso, $peso_volumetrico_corregido, MIN_PESO);    
    //     //dd($peso, 'Peso considerado');

    //     // considerando solo kilos
    //     $transporte = $peso * KV;
    //     //dd($transporte, 'Transporte (flete)');

    //     // seguro
    //     $seguro = ($declarado_usd + $transporte) * SG * 0.01;
    //     //dd($seguro, 'Seguro');

    //     // aduana (valor CIF)
    //     $aduana = ($declarado_usd + $transporte + $seguro) * AD * 0.01;
    //     //dd($aduana, 'Aduana');

    //     // iva
    //     $iva = ($declarado_usd + $transporte + $seguro + $aduana) * IV * 0.01;

    //     $total_agencia_no_iva = $transporte + $seguro + $aduana; // neto

    //     $total_agencia        = $transporte + $seguro + $aduana + $iva;
    //     //dd($total_agencia, 'Total agencia');

    //     $valor_cif_no_iva     = ($declarado_usd + $transporte + $seguro);

    //     $valor_iva_de_cif     = $valor_cif_no_iva * IV * 0.01;

    //     $valor_cif            = $valor_cif_no_iva + $valor_iva_de_cif;

    //     $total_cliente = $total_agencia + $declarado_usd;
    //     //dd($total_cliente, 'Total cliente');


    //     return [
    //         /*
    //             Recibidos ajustados por mínimos
    //         */
    //         'declarado_usd' => $declarado_usd, 
    //         'ancho' => $dim1_ori, 
    //         'largo' => $dim2_ori, 
    //         'alto'  => $dim3_ori,
    //         'peso'  => $peso_ori,
    //         'unidad_long' => $unidad_long,

    //         'usd_x_kilo' => KV,

    //         /*
    //             Calculados
    //         */
    //         'peso_volumetrico' => $peso_volumetrico_corregido,
    //         'transporte' => round($transporte, 2),
    //         'seguro' => round($seguro, 2),
    //         'aduana' => round($aduana, 2),
    //         'iva' => round($iva, 2),
    //         'total_agencia' => round($total_agencia, 2),
    //         'total_neto' => round($total_agencia_no_iva, 2),
    //         'total_cliente' => round($total_cliente, 2),
    //         'valor_cif_no_iva' => round($valor_cif_no_iva, 2),
    //         'valor_cif' => round($valor_cif, 2)
    //     ];
    // }

    // function test_cotiza(){
    //     dd($this->cotiza(10, 1, 50, 50, 50, 'cm'));
    //     dd($this->cotiza(10, 1, 19.7, 19.7, 19.7, 'pulg'));
    // }

    function test_format_num()
    {
        $format_number = function ($num) {
            $num = (float) $num;
            return number_format($num, 10, '.', '');
        };

        $n = '400';

        dd(Strings::formatNumber($n, 'en-EN'));
        //dd($format_number($n));        
    }

    function test_rr()
    {
        $round = function ($num) {
            $num = (float) $num;
            return round($num, 4);
        };

        dd($round(4.9995558));
    }

    /*
        @param $domain string dominio o subdominio
        @param $expires_in int dias para expiración
    */
    function new(string $domain, int $expires_in)
    {
        $d1 = new \DateTime();
        $d2 = $d1->modify("+$expires_in days")->format('Y-m-d H:i:s');

        DB::getDefaultConnection();

        $res = DB::table('ssl')
            ->create([
                'domain'     => $domain,
                'expires_at' => $d2
            ]);
        //dd(DB::getLog());
    }

    function renew(string $domain, int $expires_in)
    {
        $d1 = new \DateTime();
        $d2 = $d1->modify("+$expires_in days")->format('Y-m-d H:i:s');

        DB::getDefaultConnection();

        $res = DB::table('ssl')
            ->where([
                'domain' => $domain
            ])
            ->update([
                'expires_at' => $d2
            ]);

        //dd(DB::getLog());
    }

    function sec()
    {
        /*

            woo1.lan

            Array
            (
                'wo.a',
                'o1ln'
            )

            import-quoter.solucionbinaria.com

            Array
            (
                [0] => ipr-utrslcobnracm
                [1] => motqoe.ouiniai.o
            )

            apiwp.fuentessoft.com

            Array
            (
                [0] => aipfetsotcm
                [1] => pw.unesf.o
            )

        */

        $fglg67788 = 'import-quoter.solucionbinaria.com';
        dd(Strings::deinterlace($fglg67788));

        //$hh89_066 = ['hs', 'ot'];
        //dd(Strings::interlace($hh89_066));        
    }

    function test_666()
    {
        // ahora copio los archivos ofuscados en el destino
        $ori = '/home/www/simplerest/tmp/yakpro-po/obfuscated';
        $dst = "updates/xxxxxxxx/";

        Files::setCallback(function (string $content, string $path) {
            return Strings::removeMultiLineComments($content);
        });

        Files::copy($ori, $dst . 'files/app/core'); // bug con glob:*
    }

    function test_remove_comments()
    {
        $file = file_get_contents('/home/www/simplerest/updates/2021-12-20-0.7.0/files/app/core/Container.php');
        dd(Strings::removeMultiLineComments($file));
    }

    function jj()
    {
        Files::setCallback(function (string $content, string $path) {
            $content = str_replace(
                'YAK Pro',
                'Sol.Bin',
                $content
            );

            $content = str_replace(
                'GitHub: https://github.com/pk-fr/yakpro-po',
                'solucionbinaria.com                       ',
                $content
            );

            return $content;
        });


        Files::cp(
            '/home/www/woo1/wp-content/plugins/auth4wp/ajax.php',
            '/tmp/obsfuscated'
        );
    }


    function hash()
    {

        $date = '2022-04-15';
        echo Obfuscator::encryptDecrypt('encrypt', $date);
        exit;


        ////

        $f = 'Y-m-d';
        $d = new \DateTime('');
        $t = $d->format($f);

        if ($t > Obfuscator::encryptDecrypt('decrypt', 'MTFmMHc2cVBRVHlKMmZxSEVxbnBGQT09')) {
            exit;
        }

        echo 'OK';
    }


    function ofsum()
    {
        $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCIsImlhdCI6MTY0ODU5ODk0NCwiZXhwIjoxNjU3NTk4OTQ0LCJpcCI6IjE4Ni4xMDEuMTQ0LjE4IiwidXNlcl9hZ2VudCI6IlBvc3RtYW5SdW50aW1lLzcuMjkuMCIsInVpZCI6Mywicm9sZXMiOlsic3Vic2NyaWJlciJdfQ._BPlg7aSZDJ51HuRg7a9i23wXL51R6ONxBYeelU8aZw";

        $domain = 'woo4.lan';

        ////////////////////////////////////

        $str = strrev($domain) . $domain .  strrev($domain);

        $acc = 0;
        for ($i = 0; $i < strlen($str) - 3; $i++) {
            $acc += ord($str[$i]) * ($i + 2);
        }

        $fix = function (int $val) {
            while ($val > 90) {
                $val -= 20;
            }

            return $val;
        };

        $s   = (string) $acc;

        $ord1 = $fix(substr($s, 0, 3));
        $ord2 = $fix(substr($s, 3, 8));
        $ord3 = $ord2 + 1;;

        // J
        // dd(chr($ord1). " ($ord1)", 'ord1');

        // // O
        // dd(chr($ord2). " ($ord2)", 'ord2');

        // // P
        // dd(chr($ord3). " ($ord3)", 'ord3');

        // dd(chr($ord1), 'ord1');
        // dd(chr($ord2), 'ord2');

        $c1 = chr($ord1);
        $c2 = chr($ord2);
        $c3 = chr($ord3);
        $c4 = ctype_upper($c1) ? strtolower($c1) : strtoupper($c1);
        $c5 = ctype_upper($c2) ? strtolower($c2) : strtoupper($c2);
        $c6 = ctype_upper($c3) ? strtolower($c3) : strtoupper($c3);

        $token = str_replace(
            [
                $c1,
                $c2,
                $c3,
                $c4,
                $c5,
                $c6
            ],
            [
                'J',
                'O',
                'P',
                'j',
                'o',
                'p'
            ],
            $token
        );


        echo $token . "\r\n";
    }


    // function test_qr()
    // {
    //     $result = Builder::create()

    //         ->writer(new PngWriter())
    //         ->writerOptions([])
    //         ->data('Custom QR code contents')
    //         ->encoding(new Encoding('UTF-8'))
    //         ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
    //         ->size(300)
    //         ->margin(10)
    //         ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
    //         ->logoPath(ASSETS_PATH . 'img/logo_t.png')
    //         ->labelText('This is the label')
    //         ->labelFont(new NotoSans(20))
    //         ->labelAlignment(new LabelAlignmentCenter())
    //         ->build();
    // }


    function scan()
    {
        $dir = '/home/www/woo4/wp-content/plugins/woo-sizes';

        dd(
            Files::deepScan($dir, true)
        );
    }


    function test_str_last()
    {
        $url = 'https://www.easyfarma.cl/shop/dermatologia/cariamyl-sol-dermica-x-130ml/';

        dd(Url::lastSlug($url), 'SLUG');
    }

    function csv_debug()
    {
        $path = 'D:\Desktop\SOLUCION BINARIA\PROYECTOS CLIENTES\@PROYECTOS CLIENTES\RODRIGO CHILE (EN CURSO)\EASYFARMA\CSV\prod.csv';

        $rows = Files::getCSV($path)['rows'];

        usort($rows, function ($a, $b) {
            return $a['Código Isp'] <=> $b['Código Isp'];
        });

        $last_code = null;
        $last_sku  = null;
        $last_name = null;

        $isp_nulos  = 0;
        $sku_nulos  = 0;
        $isp_repetidos = 0;
        $sku_repetidos = 0;
        $names_repetidos = [];

        $total = count($rows);
        dd($total, 'TOTAL');

        foreach ($rows as $row) {
            if (empty(trim($row['Código Isp']))) {
                $isp_nulos++;
            }

            if (empty(trim($row['SKU']))) {
                $sku_nulos++;
            }

            if ($row['Código Isp'] == $last_code) {
                if ($row['SKU'] == $last_sku) {
                    // si se imprimiera sería porque habría "productos variables" (cosa que no sucede)
                    //dd($last_sku, $last_code);
                }

                //dd($last_code, 'CÓDIGO ISP REPETIDO');
                $isp_repetidos++;
            }

            if ($row['SKU'] == $last_sku) {
                $sku_repetidos++;
            }

            if ($row['Nombre'] == $last_name) {
                $names_repetidos[] = $row['Nombre'];
            }

            $last_name = $row['Nombre'];
            $last_code = $row['Código Isp'];
            $last_sku  = $row['SKU'];
        }

        dd($isp_nulos, "ISP NULOS");
        dd($isp_repetidos, 'ISP REPETIDOS');
        dd($isp_repetidos - $isp_nulos, 'ISP REPETIDOS NO-NULOS');
        dd($sku_nulos, "SKU NULOS");
        dd($sku_repetidos, "SKU REPETIDOS");
        dd($names_repetidos, "NAMES REPETIDOS : " . count($names_repetidos));
    }

    ////////////

    /*
        Rutas

        $base  = 'https://demoapi.sinergia.pe';  // la nueva ruta es https://devapi.sinergia.pe
        $ruta1 = "$base/interfaces/interfacesventa/homologarModVenta";
        $ruta2 = "$base/interfaces/interfacesventa/homologarCliente";
        $ruta3 = "$base/interfaces/interfacesventa/homologarBienesServicios";
        $ruta4 = "$base/interfaces/interfacesventa/crearBienServicio";
    */


    /*
    POST https://devapi.sinergia.pe/interfaces/login_check

        (
            como form-data o sea... con
            
            Content-Type =  multipart/form-data 
        )

        _username = admin
        _password = 1234Admin
    */
    function test_sinergia_login()
    {
        $body = [
            "_username" => 'admin',
            "password"  => '1234Admin'
        ];

        // la ruta ya no existe
        $ruta = 'https://demoapi.sinergia.pe/interfaces/login_check';

        $response = ApiClient::instance()
            ->setHeaders(
                [
                    "Content-Type"  => "multipart/form-data"
                ]
            )
            ->setBody($body)
            ->disableSSL()
            ->post($ruta)
            ->getResponse();

        dd($response, 'RES');
    }


    /*
        "F12_MONTOIGV":18 👉 por ejemplo este campo SIEMPRE es 18
    */
    function test_sinergia_registrar_bien_o_servicio()
    {
        $ruc = '12345678910';
        $razon_social = 'DEMO SAC';

        $base  = 'https://demoapi.sinergia.pe';
        $ruta  = "$base/interfaces/interfacesventa/homologarBienesServicios";

        /*
            // Núm. de órden
            "F1_ITEM":"01", 
            
            // Unidad de medida en SUNAT. 
            // https://isyfac.com/centro-de-ayuda/configuracion/codigo-de-la-unidad-de-medida-sunat/
            "F2_UNIDAD":"NIU",  

            "F3_CANTIDAD":100,

            // Núm. de producto que maneja por ejemplo WooCommerce
            "F4_CODIGO_PRODUCTO":"C00001",
              
            ¿Cuál es el código de Detraccion SUNAT?

            Código	    Definición	                                    % Tasa a aplicar
            019	        Arrendamiento de bienes	                        10 %
            020	        Mantenimiento y reparación de bienes muebles	12 %
            021	        Movimiento de carga	                            10 %
            022	        Otros servicios empresariales	                12 %
            
            // Entiendo que sino hay detractación, va 000
            "F5_CODIGO_SUNAT":"000",

            "F7_DESCRIPCION":"LENTES PROFANOS ZZZ",
            "F8_PRECIO":100,
            "F9_PRECIOVENTA":100,

            "F10_TIPOPRECIO":"01",

            // Si fuera gratis
            "F11_PRECIOGRATIS":0,

            El cálculo de IGV se hace aplicando el 18% en caso de tener el importe base, por ejemplo:
            IGV =  Importe Base x 0.18

            "F12_MONTOIGV":18,

            // Subtotal para el ítem.  Debo sumar algo acá dentro??? algún impuesto?
            "F13_SUBTOTAL":118,

            // De nuevo relacionado con el IGV. 

            G. Catálogo No. 07: Códigos de Tipo de Afectación del IGV
            CATALOGO No. 07
            Campo cbc:TaxExemptionReasonCode
            Descripción Tipo de Afectación al IGV
            Catálogo SUNAT
            Código Descripción
            10 Gravado - Operación Onerosa
            11 Gravado – Retiro por premio
            12 Gravado – Retiro por donación
            13 Gravado – Retiro 

            https://www.sunat.gob.pe/legislacion/superin/2015/anexoD-357-2015.pdf
    
            "F14_TIPOAFECTA":"10",

            // Código de Sistema Integral de Salud. Y sino tiene? 
            "F15_CODIGOSIS":"1",

            "F16_PORCENTAJE_DESCUENTO":0,

            // Bien (b) y Servicio (s)
            "F17_BIENSERVICIO":"s"
        */


        // Sería el mismo JSON que para homologarModVenta
        $body = '{
            "ruc":"12345678910",
            "tabla_ventas":[
              {
                "detalle":[
                  {
                  "F1_ITEM":"01",
                  "F2_UNIDAD":"NIU",
                  "F3_CANTIDAD":100,
                  "F4_CODIGO_PRODUCTO":"C00001",
                  "F5_CODIGO_SUNAT":"000",
                  "F7_DESCRIPCION":"LENTES PROFANOS ZZZ",
                  "F8_PRECIO":100,
                  "F9_PRECIOVENTA":100,
                  "F10_TIPOPRECIO":"01",
                  "F11_PRECIOGRATIS":0,
                  "F12_MONTOIGV":18,
                  "F13_SUBTOTAL":118,
                  "F14_TIPOAFECTA":"10",
                  "F15_CODIGOSIS":"1",
                  "F16_PORCENTAJE_DESCUENTO":0,
                  "F17_BIENSERVICIO":"s"
                  },
                  {
                  "F1_ITEM":"02",
                  "F2_UNIDAD":"NIU",
                  "F3_CANTIDAD":100,
                  "F4_CODIGO_PRODUCTO":"C00002",
                  "F5_CODIGO_SUNAT":"000",
                  "F7_DESCRIPCION":"LENTES PROFANOS XXX",
                  "F8_PRECIO":100,
                  "F9_PRECIOVENTA":100,
                  "F10_TIPOPRECIO":"01",
                  "F11_PRECIOGRATIS":0,
                  "F12_MONTOIGV":18,
                  "F13_SUBTOTAL":118,
                  "F14_TIPOAFECTA":"10",
                  "F15_CODIGOSIS":"2",
                  "F16_PORCENTAJE_DESCUENTO":0,
                  "F17_BIENSERVICIO":"s"
                  }
                ]
              }
            ]
          
          }';

        $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwidXNlcm5hbWUiOiJhZG1pbiIsImlhdCI6MTY1MDkxNTc1MiwiZXhwIjoxNjgyNDUxNzUyfQ.MxBo0y4_7GnBi7RAi8GxkxSykpYnIcexWcVcAoUInqo";

        // // Turn off SSL
        // $options = [
        //     CURLOPT_SSL_VERIFYHOST => 0,
        //     CURLOPT_SSL_VERIFYPEER => 0
        // ];

        // $response = consume_api($ruta, 'POST', $body, [
        //     "Content-type"  => "Application/json",
        //     "authToken" => "$token"
        // ], $options);

        // ruta absoluta al certificado	
        $cert = "D:\wamp64\ca-bundle.crt";

        $response = ApiClient::instance()
            ->setHeaders(
                [
                    "Content-type"  => "Application/json",
                    "authToken" => "$token"
                ]
            )
            ->setBody($body)
            ->disableSSL()
            //->certificate($cert)
            ->post($ruta)
            ->getResponse();

        dd($response, 'RES');
    }

    function test_ssl_no_check()
    {
        $arr = array(
            'url' => 'https://demoapi.sinergia.pe/interfaces/interfacesventa/homologarBienesServicios',
            'verb' => 'POST',
            'headers' =>
            array(
                'Content-type' => 'Application/json',
                'authToken' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwidXNlcm5hbWUiOiJhZG1pbiIsImlhdCI6MTY1MDkxNTc1MiwiZXhwIjoxNjgyNDUxNzUyfQ.MxBo0y4_7GnBi7RAi8GxkxSykpYnIcexWcVcAoUInqo',
            ),
            'options' =>
            array(
                81 => 0,
                64 => 0,
            ),
            'body' =>
            array(
                'ruc' => '12345678910',
                'tabla_ventas' =>
                array(
                    0 =>
                    array(
                        'A1_ID' => NULL,
                        'A2_FECHAEMISION' => '2022-05-30',
                        'A3_HORAEMISION' => NULL,
                        'A4_TIPODOCUMENTO' => '03',
                        'A5_MONEDA' => 'USD',
                        'A6_FECHAVENCIMIENTO' => NULL,
                        'A7_DOCUMENTOREFERENCIA' => NULL,
                        'A8_MOTIVONC' => NULL,
                        'A9_FECHABAJA' => NULL,
                        'A10_OBSERVACION' => NULL,
                        'A11_TIPODOCUMENTOREFERENCIA' => NULL,
                        'A12_WEIGHT' => 0.0,
                        'B1_RUC' => '12345678910',
                        'D1_DOCUMENTO' => '',
                        'D2_TIPODOCUMENTO' => '1',
                        'D3_DESCRIPCION' => 'Pablo Bozzolo',
                        'D4_LEGAL_STREET' => 'Calle 6 Oeste # 1C-35, Cali, CO-VAC',
                        'D4_LEGAL_DISTRICT' => '',
                        'D4_LEGAL_PROVINCE' => '',
                        'D4_LEGAL_STATE' => '',
                        'D4_UBIGEO' => NULL,
                        'D5_DIRECCION' => 'Calle 6 Oeste # 1C-35, Cali, CO-VAC',
                        'D6_URBANIZACION' => NULL,
                        'D7_PROVINCIA' => '',
                        'D8_DEPARTAMENTO' => 'CO-VAC',
                        'D9_DISTRITO' => 'Cali',
                        'D10_PAIS' => NULL,
                        'D11_CORREO' => 'info@solucionbinaria.com',
                        'D12_CODIGO' => NULL,
                        'D13_CODIGODIR' => '',
                        'G1_TOTALEXPORTA' => 0,
                        'G2_TOTALGRAVADA' => 0,
                        'G3_TOTALINAFECTA' => 0,
                        'G4_TOTALEXONERADA' => 0,
                        'G5_TOTAGRATUITA' => 0,
                        'G6_TOTALDESCUENTOS' => 0,
                        'G7_PORCENDETRA' => 0,
                        'G8_TOTALDETRA' => 0,
                        'G9_TOTALIGV' => 0,
                        'G10_TOTALSUBTOTAL' => 0,
                        'G13_TOTALGLOBALDESCU' => 0,
                        'G14_TOTALVENTA' => 0.0,
                        'G15_SUBTOTAL' => 0,
                        'H1_CODALMACEN' => '1',
                        'H2_SUCURSAL' => '',
                        'detalle' =>
                        array(
                            0 =>
                            array(
                                'F1_ITEM' => NULL,
                                'F2_UNIDAD' => 'NIU',
                                'F3_CANTIDAD' => 4,
                                'F4_CODIGO_PRODUCTO' => 'HPC-113',
                                'F5_CODIGO_SUNAT' => '000',
                                'F7_DESCRIPCION' => 'HP 711 Magenta Ink Cartridge (29 ml)',
                                'F8_PRECIO' => '23564',
                                'F9_PRECIOVENTA' => '',
                                'F10_TIPOPRECIO' => '01',
                                'F11_PRECIOGRATIS' => 0,
                                'F12_MONTOIGV' => 18,
                                'F13_SUBTOTAL' => 0,
                                'F14_TIPOAFECTA' => 10,
                                'F15_CODIGOSIS' => 'HPC-113',
                                'F16_PORCENTAJE_DESCUENTO' => 0,
                                'F17_BIENSERVICIO' => 'b',
                                'F18_IGV_TAX' => true,
                                'F18_IGV_AMOUNT' => 18,
                                'F19_ISC_TAX' => false,
                                'F19_ISC_AMOUNT' => 0,
                            ),
                        ),
                    ),
                ),
            ),
        );

        $response = ApiClient::instance()
            ->setHeaders(
                $arr['headers']
            )
            ->setBody($arr['body'])
            ->setOptions($arr['options'])
            ->request($arr['url'], $arr['verb'])
            ->getResponse();

        dd($response, 'RES');
    }

    /*
        $certificate_location = "C:\Program Files (x86)\EasyPHP-Devserver-16.1\ca-bundle.crt"; // modify this line accordingly (may need to be absolute)
        curl_setopt($ch, CURLOPT_CAINFO, $certificate_location);
        curl_setopt($ch, CURLOPT_CAPATH, $certificate_location);
    */
    function load_ssl_cert()
    {
        $arr = array(
            'url' => 'https://demoapi.sinergia.pe/interfaces/interfacesventa/homologarBienesServicios',
            'verb' => 'POST',
            'headers' =>
            array(
                'Content-type' => 'Application/json',
                'authToken' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwidXNlcm5hbWUiOiJhZG1pbiIsImlhdCI6MTY1MDkxNTc1MiwiZXhwIjoxNjgyNDUxNzUyfQ.MxBo0y4_7GnBi7RAi8GxkxSykpYnIcexWcVcAoUInqo',
            ),
            'options' =>
            array(
                81 => 0,
                64 => 0,
            ),
            'body' =>
            array(
                'ruc' => '12345678910',
                'tabla_ventas' =>
                array(
                    0 =>
                    array(
                        'A1_ID' => NULL,
                        'A2_FECHAEMISION' => '2022-05-30',
                        'A3_HORAEMISION' => NULL,
                        'A4_TIPODOCUMENTO' => '03',
                        'A5_MONEDA' => 'USD',
                        'A6_FECHAVENCIMIENTO' => NULL,
                        'A7_DOCUMENTOREFERENCIA' => NULL,
                        'A8_MOTIVONC' => NULL,
                        'A9_FECHABAJA' => NULL,
                        'A10_OBSERVACION' => NULL,
                        'A11_TIPODOCUMENTOREFERENCIA' => NULL,
                        'A12_WEIGHT' => 0.0,
                        'B1_RUC' => '12345678910',
                        'D1_DOCUMENTO' => '',
                        'D2_TIPODOCUMENTO' => '1',
                        'D3_DESCRIPCION' => 'Pablo Bozzolo',
                        'D4_LEGAL_STREET' => 'Calle 6 Oeste # 1C-35, Cali, CO-VAC',
                        'D4_LEGAL_DISTRICT' => '',
                        'D4_LEGAL_PROVINCE' => '',
                        'D4_LEGAL_STATE' => '',
                        'D4_UBIGEO' => NULL,
                        'D5_DIRECCION' => 'Calle 6 Oeste # 1C-35, Cali, CO-VAC',
                        'D6_URBANIZACION' => NULL,
                        'D7_PROVINCIA' => '',
                        'D8_DEPARTAMENTO' => 'CO-VAC',
                        'D9_DISTRITO' => 'Cali',
                        'D10_PAIS' => NULL,
                        'D11_CORREO' => 'info@solucionbinaria.com',
                        'D12_CODIGO' => NULL,
                        'D13_CODIGODIR' => '',
                        'G1_TOTALEXPORTA' => 0,
                        'G2_TOTALGRAVADA' => 0,
                        'G3_TOTALINAFECTA' => 0,
                        'G4_TOTALEXONERADA' => 0,
                        'G5_TOTAGRATUITA' => 0,
                        'G6_TOTALDESCUENTOS' => 0,
                        'G7_PORCENDETRA' => 0,
                        'G8_TOTALDETRA' => 0,
                        'G9_TOTALIGV' => 0,
                        'G10_TOTALSUBTOTAL' => 0,
                        'G13_TOTALGLOBALDESCU' => 0,
                        'G14_TOTALVENTA' => 0.0,
                        'G15_SUBTOTAL' => 0,
                        'H1_CODALMACEN' => '1',
                        'H2_SUCURSAL' => '',
                        'detalle' =>
                        array(
                            0 =>
                            array(
                                'F1_ITEM' => NULL,
                                'F2_UNIDAD' => 'NIU',
                                'F3_CANTIDAD' => 4,
                                'F4_CODIGO_PRODUCTO' => 'HPC-113',
                                'F5_CODIGO_SUNAT' => '000',
                                'F7_DESCRIPCION' => 'HP 711 Magenta Ink Cartridge (29 ml)',
                                'F8_PRECIO' => '23564',
                                'F9_PRECIOVENTA' => '',
                                'F10_TIPOPRECIO' => '01',
                                'F11_PRECIOGRATIS' => 0,
                                'F12_MONTOIGV' => 18,
                                'F13_SUBTOTAL' => 0,
                                'F14_TIPOAFECTA' => 10,
                                'F15_CODIGOSIS' => 'HPC-113',
                                'F16_PORCENTAJE_DESCUENTO' => 0,
                                'F17_BIENSERVICIO' => 'b',
                                'F18_IGV_TAX' => true,
                                'F18_IGV_AMOUNT' => 18,
                                'F19_ISC_TAX' => false,
                                'F19_ISC_AMOUNT' => 0,
                            ),
                        ),
                    ),
                ),
            ),
        );

        $cert = "D:\wamp64\ca-bundle.crt";

        $response = ApiClient::instance()
            ->setHeaders(
                $arr['headers']
            )
            ->setBody($arr['body'])
            ->setSSLCrt($cert)
            ->request($arr['url'], $arr['verb'])
            ->getResponse();

        dd($response, 'RES');
    }

    function test_sinergia_registrar_cliente()
    {
        $ruc = '12345678910';

        $base  = 'https://demoapi.sinergia.pe';
        $ruta  = "$base/interfaces/interfacesventa/homologarCliente";

        $body = '{
            "ruc": "' . $ruc . '",
            "tabla_ventas": [
              { 
                "D1_DOCUMENTO": "20603374097",
                "D2_TIPODOCUMENTO": "6",
                "D3_DESCRIPCION": "DevTechPeru EIRL (986976377)",
                "D4_LEGAL_STREET": "Jr Los Aromos 644 Dpto 301, La Molina",
                "D4_LEGAL_DISTRICT": "",
                "D4_LEGAL_PROVINCE": "Lima",
                "D4_LEGAL_STATE": "Lima",
                "D4_UBIGEO": null,
                "D5_DIRECCION": "Jr Los Aromos 644 Dpto 301, La Molina, Lima",
                "D6_URBANIZACION": null,
                "D7_PROVINCIA": "Lima",
                "D8_DEPARTAMENTO": "Lima",
                "D9_DISTRITO": "La Molina",
                "D10_PAIS": null,
                "D11_CORREO": "augusto@devtechperu.com",
                "D12_CODIGO": null,
                "D13_CODIGODIR": ""
              }
            ]
          }';

        $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwidXNlcm5hbWUiOiJhZG1pbiIsImlhdCI6MTY1MDkxNTc1MiwiZXhwIjoxNjgyNDUxNzUyfQ.MxBo0y4_7GnBi7RAi8GxkxSykpYnIcexWcVcAoUInqo";

        $response = consume_api($ruta, 'POST', $body, [
            "Content-type"  => "Application/json",
            "authToken" => "$token"
        ]);

        dd($response, 'RES');
    }


    /*
        "F12_MONTOIGV":18 👉 por ejemplo este campo SIEMPRE es 18
    */
    function test_sinergia_registrar_venta()
    {
        $ruc = '12345678910';
        $razon_social = 'DEMO SAC';

        $base = 'https://demoapi.sinergia.pe';
        $ruta = "$base/interfaces/interfacesventa/homologarModVenta";


        $body = '{
            "ruc": "' . $ruc . '",
            "tabla_ventas": [
                {
                "A1_ID" : "F00300000254",
                "A2_FECHAEMISION" : "2021-10-05",
                "A3_HORAEMISION" : null,
                "A4_TIPODOCUMENTO" : "01",
                "A5_MONEDA" : "USD",
                "A6_FECHAVENCIMIENTO" : null,
                "A7_DOCUMENTOREFERENCIA" : null,
                "A8_MOTIVONC" : null,
                "A9_FECHABAJA" : null,
                "A10_OBSERVACION" : null,
                "A11_TIPODOCUMENTOREFERENCIA" : null,
                "A12_WEIGHT" : 2.4,
                "B1_RUC" : "' . $ruc . '",
                "D1_DOCUMENTO" : "20603374097",
                "D2_TIPODOCUMENTO" : "6",
                "D3_DESCRIPCION" : "' . $razon_social . '",
                "D4_LEGAL_STREET" : "Jr Los Aromos 644 Dpto 301, La Molina",
                "D4_LEGAL_DISTRICT" : "",
                "D4_LEGAL_PROVINCE" : "Lima",
                "D4_LEGAL_STATE" : "Lima",
                "D4_UBIGEO" : null,
                "D5_DIRECCION" : "Jr Los Aromos 644 Dpto 301, La Molina, Lima",
                "D6_URBANIZACION" : null,
                "D7_PROVINCIA" : "Lima",
                "D8_DEPARTAMENTO" : "Lima",
                "D9_DISTRITO" : "La Molina",
                "D10_PAIS" : null,
                "D11_CORREO" : "augusto@devtechperu.com",
                "D12_CODIGO" : null,
                "D13_CODIGODIR" : "",
                "G1_TOTALEXPORTA" : 0,
                "G2_TOTALGRAVADA" : 0,
                "G3_TOTALINAFECTA" : 0,
                "G4_TOTALEXONERADA" : 0,
                "G5_TOTAGRATUITA" : 0,
                "G6_TOTALDESCUENTOS" : 0,
                "G7_PORCENDETRA" : 0,
                "G8_TOTALDETRA" : 0,
                "G9_TOTALIGV" : 0,
                "G10_TOTALSUBTOTAL" : 0,
                "G13_TOTALGLOBALDESCU" : 0.82,
                "G14_TOTALVENTA" : 15.58,
                "G15_SUBTOTAL" : 0,
                "H1_CODALMACEN" : null,
                "H2_SUCURSAL" : null,                
                }
            ]
        }';

        $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwidXNlcm5hbWUiOiJhZG1pbiIsImlhdCI6MTY1MDkxNTc1MiwiZXhwIjoxNjgyNDUxNzUyfQ.MxBo0y4_7GnBi7RAi8GxkxSykpYnIcexWcVcAoUInqo";

        $response = consume_api($ruta, 'POST', $body, [
            "Content-type"  => "Application/json",
            "authToken" => "$token"
        ]);

        dd($response, 'RES');
    }

    function test_api_client()
    {
        $ruc = '12345678910';

        $base  = 'https://demoapi.sinergia.pe';
        $ruta  = "$base/interfaces/interfacesventa/homologarCliente";

        $body = '{
            "ruc": "' . $ruc . '",
            "tabla_ventas": [
                { 
                "D1_DOCUMENTO": "20603374097",
                "D2_TIPODOCUMENTO": "6",
                "D3_DESCRIPCION": "DevTechPeru EIRL (986976377)",
                "D4_LEGAL_STREET": "Jr Los Aromos 644 Dpto 301, La Molina",
                "D4_LEGAL_DISTRICT": "",
                "D4_LEGAL_PROVINCE": "Lima",
                "D4_LEGAL_STATE": "Lima",
                "D4_UBIGEO": null,
                "D5_DIRECCION": "Jr Los Aromos 644 Dpto 301, La Molina, Lima",
                "D6_URBANIZACION": null,
                "D7_PROVINCIA": "Lima",
                "D8_DEPARTAMENTO": "Lima",
                "D9_DISTRITO": "La Molina",
                "D10_PAIS": null,
                "D11_CORREO": "augusto@devtechperu.com",
                "D12_CODIGO": null,
                "D13_CODIGODIR": ""
                }
            ]
        }';

        $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwidXNlcm5hbWUiOiJhZG1pbiIsImlhdCI6MTY1MDkxNTc1MiwiZXhwIjoxNjgyNDUxNzUyfQ.MxBo0y4_7GnBi7RAi8GxkxSykpYnIcexWcVcAoUInqo";

        $client = new ApiClient();

        $client
            ->setBody($body)
            ->setHeaders([
                "Content-type"  => "Application/json",
                "authToken" => "$token"
            ])
            ->disableSSL()
            ->post($ruta);

        dd($client->getStatus(), 'STATUS');
        dd($client->getError(), 'ERROR');
        dd($client->getResponse(), 'RES');
    }

    function test_api_client2()
    {
        $client = new ApiClient();

        $user = 'intergrade';
        $pass = '9660ed881416fad88c5f48eddd7334c6';

        $client
            /*
            Si se le pasa una cantidad de segundos *debería* guardar por esa cantidad de tiempo
            el archivo y pasado ese tiempo, ignorarlo y volver a generarlo

            Sin resolver !!!!
        */
            //->setCache()
            ->setRetries(3)
            ->setBasicAuth($user, $pass)
            ->disableSSL()
            ->request('http://200.6.78.34/stock/v1/catalog/YX0-947', 'GET');

        dd($client->getStatus(), 'STATUS');
        dd($client->getError(), 'ERROR');
        dd($client->getResponse(true), 'RES');
    }


    function test_api_client3()
    {
        $client = new ApiClient();

        $postfields = array();
        $postfields['_username'] = 'admin';
        $postfields['_password'] = '1234Admin';

        $client
            //->setRetries(3)
            ->setHeaders([
                'Content-Type' => 'multipart/form-data'
            ])
            ->setBody($postfields, false)
            ->disableSSL()
            ->request('https://devapi.sinergia.pe/login_check', 'POST');

        dd($client->getStatus(), 'STATUS');
        dd($client->getError(), 'ERROR');
        dd($client->getResponse(true), 'RES');
    }


    static function getClient($endpoint)
    {
        global $config;

        $ruta         = $config['url_base_endpoints'] . $endpoint;
        $token        = $config['token'];

        dd($ruta, 'ENDPOINT *****');

        $client = (new ApiClient($ruta));

        $client
            ->setHeaders(
                [
                    "Content-type"  => "Application/json",
                    "authToken" => "$token"
                ]
            )
            ->setRetries(3);

        if ($config['dev_mode']) {
            $client->disableSSL();
        }

        return $client;
    }

    /*
        General: a cualquier endpoint
    */
    static function registrar($data, $endpoint)
    {
        $response = static::getClient($endpoint)
            ->setBody($data)
            ->post()
            ->getResponse();

        return $response;
    }

    function test_sinergia()
    {
        global $config;

        $config = [
            'dev_mode' => true,
            'token' => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwidXNlcm5hbWUiOiJhZG1pbiIsImlhdCI6MTY1MzkzNTU2NCwiZXhwIjoxNjU0NTQwMzY0fQ.igAEIhqDTBLBMEvHI7KePGn3HVaZ6jv_cyeToGNAya8",  // obtenida con el nuevo endpoint en Insomnia

            'B1_RUC'            => '20602903312',
            'demo_ruc'          => '20602903312',

            'url_base_endpoints' => 'https://devapi.sinergia.pe',
            'endpoint_ruta_homologar_bienes_servicios' => '/interfaces/interfacesventa/homologarBienesServicios',
            'endpoint_ruta_homologar_ciente' => '/interfaces/interfacesventa/homologarCliente',
            'endpoint_homologar_venta' => '/interfaces/interfacesventa/homologarModVenta',
        ];

        $body = '{
            "ruc": "' . $config['B1_RUC'] . '",
            "tabla_ventas": [
                { 
                "D1_DOCUMENTO": "20603374097",
                "D2_TIPODOCUMENTO": "6",
                "D3_DESCRIPCION": "DevTechPeru EIRL (986976377)",
                "D4_LEGAL_STREET": "Jr Los Aromos 644 Dpto 301, La Molina",
                "D4_LEGAL_DISTRICT": "",
                "D4_LEGAL_PROVINCE": "Lima",
                "D4_LEGAL_STATE": "Lima",
                "D4_UBIGEO": null,
                "D5_DIRECCION": "Jr Los Aromos 644 Dpto 301, La Molina, Lima",
                "D6_URBANIZACION": null,
                "D7_PROVINCIA": "Lima",
                "D8_DEPARTAMENTO": "Lima",
                "D9_DISTRITO": "La Molina",
                "D10_PAIS": null,
                "D11_CORREO": "augusto@devtechperu.com",
                "D12_CODIGO": null,
                "D13_CODIGODIR": ""
                }
            ]
        }';

        dd(
            static::registrar($body, '/interfaces/interfacesventa/homologarCliente')
        );
    }

    function test_file_upload()
    {
        $data = $_POST;

        $uploader = (new FileUploader())
            ->setFileHandler(function ($uid) {
                $prefix = ($uid ?? '0') . '-';
                return uniqid($prefix, true);
            }, auth()->uid());


        $files    = $uploader->doUpload()->getFileNames();
        $failures = $uploader->getErrors();

        if (count($files) == 0) {
            error('No files or file upload failed', 400);
        }

        /*
            Almaceno los nombres de los archivos en DB
        */
        foreach ($files as $ix => $f) {
            $ori_filename = $f['ori_name'];
            $as_stored    = $f['as_stored'];

            $id = DB::insert("INSERT INTO `my_files` (`id`, `filename`, `filename_as_stored`, `created_at`) VALUES (NULL, '$ori_filename', '$as_stored', CURRENT_TIMESTAMP);");

            $files[$ix]['id'] = $id;
        }

        return [
            'data'     => $data,
            'files'    => $files,
            'failures' => $failures,
            'message'  => !empty($failures) ? 'Got errors during file upload' : null
        ];
    }


    function test_file_upload_base64()
    {
        $uploader = (new Base64Uploader())
            ->setFileHandler(function ($uid) {
                $prefix = ($uid ?? '0') . '-';
                return uniqid($prefix, true);
            }, auth()->uid());

        $files    = $uploader->doUpload()->getFileNames();
        $failures = $uploader->getErrors();

        return [
            'files'    => $files,
            'failures' => $failures
        ];
    }

    function fix_csv()
    {
        $out = [];

        $path = 'D:\Desktop\CSV\completo.csv';

        $rows = Files::getCSV($path)['rows'];

        foreach ($rows as $ix => $row) {
            $sku         = $row['SKU'];
            $precio      = $row['Precio'];
            $precio_plus = $row['Precio Plus'];

            if (!isset($out[$sku])) {
                $out[$sku] = [];
            }

            $out[$sku]['precio']      = $precio;
            $out[$sku]['precio_plus'] = $precio_plus;
        }

        Logger::varExport(UPLOADS_PATH . 'completo-csv.php', $out);

        // dd($out);
        // dd(count($out), 'COUNT');      
    }

    function test_follow_redirect()
    {
        $url = 'https://www.awin1.com/cread.php?awinmid=20598&awinaffid=856219&platform=dl&ued=https%3A%2F%2Fwww.leroymerlin.es%2Ffp%2F81926166%2Fespejo-rectangular-pierre-roble-roble-152-x-52-cm';

        $api = (new ApiClient($url))
            ->logReq()
            ->logRes();

        $res = $api
            ->disableSSL()
            ->redirect()
            //->cache()
            ->get();

        dd($res->data());
    }

    function test_follow_redirs()
    {
        $url = 'https://amzn.to/2M0SCXb';

        dd(
            ApiClient::instance($url)
                ->disableSSL()
                ->followLocations()
                ->cache()
                ->get()
                ->getResponse(false)
        );
    }

    function get_bruno_csv()
    {
        $proveedores = [
            'MAISONS DU MONDE',
            'LEROY MERLIN',
            'AMAZON'
        ];

        $out = [];

        $path = 'D:\Desktop\SOLUCION BINARIA\PROYECTOS CLIENTES\@PROYECTOS CLIENTES\AFILIADOS - BRUNO (ES)\bruno.csv';

        /*
            [
                [ID] => 24275
                [Tipo] => external
                [SKU] => 1245
                [Nombre] => Espejo colgante de puerta beige 125 x 35 cm
                [Precio normal] => 31;95
                [URL de afiliado] => https://amzn.to/2LZXdJ9
                [PROVEEDOR] => AMAZON
        */
        $rows = Files::getCSV($path)['rows'];

        foreach ($rows as $ix => $row) {
            if ($row['PROVEEDOR'] != $proveedores[1]) {
                continue;
            }

            $url = $row['URL de afiliado'];
            //$url = Url::getFinalUrl($url);

            // dd($url);
            // exit;

            $q   = Url::getQueryParams($url);

            if ($q['awinmid'] != 20598 || $q['awinaffid'] != 856219 || $q['platform'] != 'dl') {
                dd($row);
                exit;
            }
        }

        dd('OK');
    }

    function maps()
    {
        $maps = new GoogleMaps();

        dd(
            $maps->getCoordinates('Diego de Torres 5, Acala de Henaes, Madrid')
        );
    }

    /*
        Descarga archivo
    */
    function download_link()
    {
        $url = 'https://docs.google.com/uc?export=download&id=1Ki34FJX-iCqTErvsU_EQFrs9JwHL62KJ';
        //$url = 'https://docs.google.com/uc?export=download&id=1Fdtxt56oCI1-rUwLmFXkzaXzQxdMhc8v';

        dd(
            Url::download($url)
        );
    }

    public function test_get_url_content()
    {
        $url = 'https://totoro.banrep.gov.co/estadisticas-economicas/rest/consultaDatosService/consultaMercadoCambiario';

        dd(
            Url::getUrlContent($url, true, true)
        );
    }

    function testggg()
    {
        dd(
            include 'D:\www\woo1\wp-content\plugins\plugin-theme-installer\logs\mutawp_product_export.php'
        );
    }

    function unquote()
    {
        $str = <<<STR
        <div id="error"><p class="wpdberror"><strong>Error en la base de datos de WordPress:</strong> [You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near &#039;, CURRENT_TIMESTAMP)&#039; at line 1]<br /><code>INSERT INTO `wp_sinergia_boletas` (`correlativo`, `serie`, `order_id`, `datetime`) VALUES (NULL, &#039;B001&#039;   , , CURRENT_TIMESTAMP);</code></p></div>--[ CORRELATIVO BOLETA ]--
        STR;

        return html_entity_decode($str);
    }

    function get_alltable()
    {
        $names = DB::getTableNames('mpo');

        foreach ($names as $name) {
            print_r("->addResourcePermissions('$name', ['read_all', 'write'])\r\n");
        }
    }

    function test_str_fn500()
    {
        $str = 'TBL_ESCALAS_TERRITORIALES';

        dd(
            Strings::snakeToCamel($str)
        );

        dd(
            Strings::snakeToCamel('hola_mundo_cruel')
        );
    }

    function test_scraper_1()
    {
        $url = 'https://www.maisonsdumonde.com/ES/es/p/espejo-de-teca-153x75-rivage-121734.htm?utm_source=effiliation_es&utm_campaign=generique_affiliation&utm_medium=affiliation&utm_content=43_1395110640&eff_cpt=22616853';

        $url = 'https://www.maisonsdumonde.com/FR/fr/p/canape-lit-3-4-places-en-lin-lave-bleu-petrole-barcelone-180512.htm';

        dd(
            MaisonsScraper::getProduct($url)
        );

        dd(
            ApiClient::instance($url)->getCachePath()
        );
    }


    /*
       Leroy Merlin esta dando HTTP request failed! HTTP/1.1 403 Forbidden
    */
    function test_scraper_2()
    {
        $url = 'https://www.leroymerlin.es/fp/81873733/barbacoa-de-gas-naterial-kenton-de-4-quemadores-y-14-kw-de-potencia';

        dd(
            file_get_contents($url),
            'VIA FILE GET CONTENTS'
        );
        exit;

        dd(
            LeroyMerlinScraper::getProduct($url)
        );
    }

    function test_scraper_3()
    {
        $url = 'https://amzn.to/2N7LgBZ';

        dd(
            AmazonScraper::getProduct($url)
        );
    }

    function test_scraper_4()
    {
        $url = 'https://www.curiosite.es/producto/zapatillas-de-andar-por-casa-multicolores.html';

        dd(
            Curiosite::getProduct($url)
        );
    }

    function test_iframe_0()
    {
?>
        <iframe src="https://onedrive.live.com/embed?resid=C8AC521BBD6F3B93%21106&authkey=!AGgxeD1WZbCu8uY&em=2" width="100%" height="100%" frameborder="0" scrolling="no"></iframe>
    <?php
    }


    /*
        Investigar 

        "Using the main scrollbar to scroll an iframe" 
    */
    function test_iframe()
    {
        /*
            Make iframe automatically adjust height according to the contents without using scrollbar?
            
            Note: This will not work if the iframe contains content from another domain because of the Same Origin Policy
        */

        js("
        function resizeIframe(obj) {
            obj.style.height = obj.contentWindow.document.documentElement.scrollHeight + 'px';
        }
        
        // $(window).load(function(){
        //     $(document).scroll(function () {
        //      var scrollTop = $(window).scrollTop();
        //      var docHeight = $(document).height();
        //      var winHeight = $(window).height();
        //      var scrollPercent = scrollTop / (docHeight - winHeight);
     
        //      var divHeight = $('div').height(); 
        //      var divContentHeight = $('div')[0].scrollHeight;
     
        //      var equation = scrollPercent * (divContentHeight-divHeight);
     
        //      $('div').scrollTop(equation);
     
        //  });     
        // });

        ");

        css('
        .iframe_container {
            position:relative; 
            width:1500px;
            height:100%;
            max-width:100%;
        }

        .my_iframe {
            display:block;
            width:100%;
            height:100%;
            position:absolute; top:0; left: 0;
        }
        ');
    ?>

        <center>
            <div class="iframe_container">
                <iframe class="my_iframe" marginwidth="0" marginheight="0" allowfullscreen frameborder="0" scrolling="no" onload="resizeIframe(this)" src="https://onedrive.live.com/embed?resid=C8AC521BBD6F3B93%21106&authkey=!AGgxeD1WZbCu8uY&em=2">Your Browser Does Not Support iframes!</iframe>
            </div>
        </center>

    <?php
    }

    /*
        Tiene sentido pero quizas sea mejor que sea el primero y no el ultimo
    */
    function test_response_twice()
    {
        response('Uno');
        response('Dos'); // solo el ultimo es el que sale
    }

    function test_response_twice_2()
    {
        response('Uno');

        return 'Dos'; // solo el ultimo es el que sale
    }

    function test_conditional_response_1()
    {
        if (response()->isEmpty()) {
            response([
                'message' => 'OK'
            ]);
        }
    }

    function test_conditional_response_2()
    {
        response('Respuesta previa');

        // ...

        if (response()->isEmpty()) {
            response([
                'message' => 'OK'
            ]);
        }
    }

    /*
        Las rutas absolutas deben ubicarse dentro del proyecto 
        y de momento estar dentro de VIEWS_PATH
        pero podria ser otra........ permitiendo crear modulos en otro lado
    */
    function test_asset_local_ruta_absoluta()
    {
        js_file(VIEWS_PATH . 'factory_parts/js/custom_dt.js');

        render("Hola Sr. Putin");
    }

    function test_cached_form()
    {
        Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

        $content = Files::getFromTempFile('my_component.html');

        if (empty($content)) {
            $content = tag('accordion')->items([
                [
                    'id' => "flush-collapseOne",
                    'title' => "Accordion Item #1",
                    'body' => 'Placeholder content for this accordion, which is intended to demonstrate the <code>.accordion-flush</code> class. This is the first items accordion body.'
                ],
                [
                    'id' => "flush-collapseTwo",
                    'title' => "Accordion Item #2",
                    'body' => 'Placeholder 2'
                ],
                [
                    'id' => "flush-collapseThree",
                    'title' => "Accordion Item #3",
                    'body' =>  'Placeholder 3'
                ]
            ])
                ->id('accordionExample')
                ->always_open(true)
                ->attributes(['class' => 'accordion-flush']);

            Files::saveToTempFile($content, 'my_component.html');
        }

        render($content);
    }


    function test_middle_str()
    {
        $str = "";

        dd(
            Strings::middle($str, 5, 10)
        );
    }

    function test_trimafter()
    {
        $str = "Class XXX extends Model {\r\n \r\n \r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\nprotected \$yoquese;\r\nprotected \$otra_cosa;";

        var_dump(
            Strings::trimAfter("extends Model {", $str)
        );
    }

    function test_remove_empty_lines_after()
    {
        $str = "Class XXX extends Model {\r\n \r\n \r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\nprotected \$yoquese;\r\n\r\nprotected \$otra_cosa;";

        var_dump(
            Strings::trimEmptyLinesAfter("extends Model {", $str, 0, null, 1)
        );
    }

    function test_remove_empty_lines_before()
    {
        $str = "Class XXX extends Model {\r\n \r\n \r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\nprotected \$yoquese;\r\n\r\nprotected \$otra_cosa;";

        $str = Strings::trimEmptyLinesBefore("protected", $str, 0, null, 1);

        var_dump(
            $str
        );
    }


    function test_curl_no_proxy()
    {
        $url  = 'https://iplocation.net/';

        $res  = ApiClient::instance()
            ->disableSSL()
            ->redirect()
            //->decode(true)
            ->get($url);

        //$export = $res->dump();
        //Files::varExport($export, 'api_debug.php');

        dd($res->data());
    }

    /*
        Curl con proxy

        https://github.com/zounar/php-proxy/blob/master/Proxy.php
    */

    function test_curl_proxy_2()
    {
        $url       = 'https://amzn.to/3k7jfX5';
        $proxy_url = 'http://2.56.221.125/php-proxy/Proxy.php';

        dd($url, 'URL');

        $scraper = function (string $url) {
            if (Strings::startsWith('https://amzn.to/', $url)) {
                return AmazonScraper::class;
            }

            if (Strings::startsWith('https://www.awin1.com/', $url)) {
                return LeroyMerlinScraper::class;
            }

            if (Strings::startsWith('https://track.effiliation.com/', $url)) {
                return MaisonsScraper::class;
            }

            throw new \Exception("Scraper correcto no encontrado para '$url'");
        };

        $class = $scraper($url);

        dd(
            $class::getProduct($url)
        );

        $client = ApiClient::instance($proxy_url)
            ->setHeaders([
                'Proxy-Auth: Bj5pnZEX6DkcG6Nz6AjDUT1bvcGRVhRaXDuKDX9CjsEs2',
                'Proxy-Target-URL: ' . $url
            ]);

        $client
            ->disableSSL()
            //->cache()
            ->redirect()
            ->get();

        if ($client->getStatus() != 200) {
            throw new \Exception($client->error());
        }

        dd(
            $client->data()
        );
    }

    function test39393()
    {
        dd(Files::fileExtension('xxxx.htm.php'));
    }

    function test_tb_prefix()
    {
        DB::getConnection('woo3');

        dd(
            tb_prefix(),
            'PREFIX'
        );

        $rows = table('users')
            ->first();

        dd($rows);

        $rows = table('users')
            ->orderBy(['ID' => 'DESC'])
            ->first();

        dd($rows);
    }

    function test_xy()
    {
        DB::getConnection('parts-remote');

        dd(
            Schema::getTables()
        );
    }

    function test_xxxxx()
    {
        DB::getConnection('woo3');

        dd(
            DB::isDefaultConnection()
        );

        DB::getDefaultConnection();

        dd(
            DB::isDefaultConnection()
        );
    }

    function test_bg()
    {
        $params = "com dumb fnx";
        $cmd    = "D:\wamp64\bin\php\php7.4.26\php.exe " . ROOT_PATH . $params;
        dd($cmd);

        $shell = new \COM("WScript.Shell");
        $shell->Run($cmd);
        $shell = null;
    }

    function fnx()
    {
        for ($i = 0; $i < 50; $i++) {
            Logger::log("S-> $i");
            usleep(100000);
        }
    }

    function test_run_in_background()
    {
        bg_com("dumb fnx");
    }

    function test_look_for_exe()
    {
        dd(
            System::isExecutableInPath('php.exe')
        );
    }

    function debug_api_client()
    {
        $path  = 'D:\www\woo3\wp-content\plugins\reactorv2\logs\exported_prods_2.php';

        $prods = include $path;

        $user_api_key = 'mia0-010011010101';
        $url   = 'http://woo4.lan/wp-json/connector/v1/products';

        $res  = ApiClient::instance()
            ->setBody([
                'data' => [
                    "products" => $prods
                ]
            ])
            ->setHeaders([
                'X-API-KEY' => $user_api_key
            ])
            ->disableSSL()
            //->decode(true)
            ->post($url);

        dd($res->dump());

        //$export = $res->dump();
        //Files::varExport($export, 'api_debug.php');

        dd($res->data());
    }

    function debug_api_client_exec()
    {
        $data = include 'D:\www\simplerest\logs\api_debug.php';

        dd($data);

        $res = ApiClient::instance()
            ->exec($data);

        if ($res->status() != 200) {
            dd($res->error(), 'ERROR');
        }

        dd($res->status(), 'STATUS CODE');

        dd(
            $res->data(),
            'DATA'
        );
    }


    function test_basic_auth()
    {
        $username = 'intergrade';
        $password = '9660ed881416fad88c5f48eddd7334c6';

        $url = 'http://200.6.78.34/stock/v1/catalogfilter/packaging';

        $client = ApiClient::instance()
            //->setBody($body)
            ->setHeaders([
                'Authorization: Basic ' . base64_encode("$username:$password")
            ])
            ->setCache(3600)
            ->decode(true);

        $client->setUrl($url);

        dd(
            $client->getCachePath(),
            'CACHE PATH'
        );

        exit;

        $res = $client->get();

        dd($res->getStatus(), 'STATUS');
        dd($res->getError(), 'ERROR');
        dd($res->data(), 'DATA');
    }

    function respuesta()
    {
        error('Acceso no autorizado', 401, 'Header vacio');
    }

    function test_error1()
    {
        error("No encontrado", 404, "El recurso no existe");
    }

    function test_error1b()
    {
        response()->error("No encontrado", 404, "El recurso no existe");
    }

    function test_error2()
    {
        // No acepta mas que dos parametros
        response('Todo mal', 500);
    }

    function test_error1c()
    {
        // es un alias de response()->error()
        error("No encontrado", 404, "El recurso no existe");
    }

    /*
        Mas facil que...

        $d1 = new \DateTime('08 Dec 2024');
        $d2 = $d1->modify("+$expires_in days")->format('Y-m-d H:i:s');
    */
    function test_add_sub_dates()
    {
        $date = '08 Dec 2024';

        $d = 90;

        dd(
            Date::addDays($date, $d),
            " $date + $d dias",
        );
    }


    function get_model_defs()
    {
        DB::getConnection('az');

        dd(
            get_model_defs('products', null, true)
        );

        // dd(
        //     get_model_defs('automoviles')
        // );
    }

    function get_api_rest_defs()
    {
        DB::getConnection('az');

        dd(
            get_defs('products', null, false, false)
        );

        // dd(
        //     get_model_defs('automoviles')
        // );
    }

    function boom()
    {
        puff();
    }

    function oops()
    {
        $this->boom();
    }

    /*
        REGLA:

        SI el precio es mayor a 10.000 entonces que si el digito ante-ante-ante-ultimo es 5 o mas de 5 entonces sea 900 o sea..

        10200 sigue igual
        10300 sigue igual
        10400 sigue igual
        10500 pasa a 10900
        10600 pasa a 10900
        ...

        y en todos los casos... los ultimos dos digitos seran 00
    */
    function custom_round()
    {
        $my_round = function ($val) {

            if ($val > 100) {
                $val = 100 * number_format(0.01 * $val, 0, '.', '');
            }

            if ($val < 10000) {
                return $val;
            }

            $last_4_str = substr($val, -4);

            if ($last_4_str > 5000) {
                $last_4_str = substr($last_4_str, 0, 1) . '900';
                //dd(" <--------- ! para $val");
                $val = substr($val, 0, -4) . $last_4_str;
            }


            return $val;
        };

        for ($i = 0; $i < 10000000; $i++) {

            $i = $i + rand(0, 1 + 0.5 * $i);

            dd(
                $i . "\t>\t" . $my_round($i)
            );
        }
        dd("Done");
    }


    /*
        puede ejecutarse con eval() ??? ????

        Si, pero debe agregarse un "return " al comienzo
    */
    function test_eval()
    {
        $code = "return table('my_tb')
        ->group(function(\$q){
            \$q->whereOr([
                array (
                    0 => 'cost',
                    1 => 100,
                    2 => '<=',
                ),
                array (
                    0 => 'description',
                    1 => 'NOT NULL',
                    2 => 'IS',
                )
          ])
            ->orWhere(array(
                0 => 'name',
                1 => 'Pablo',
                2 => 'starsWith',
            ));
        })
        ->where(['stars', 5])
        ->dd()
        ;";

        dd(
            eval($code)
        );
    }
    function test_if_array_is_multi_but_simple()
    {
        $a = [
            ['a', 'c'],
            ['x' => 7], // <--- false
            ['a', 'c', 5],
        ];

        dd(
            Arrays::areSimpleAllSubArrays($a)
        );
    }

    function get_extensions()
    {
        dd(get_loaded_extensions());
    }

    function test_byte()
    {
        $x = true;

        var_dump($x);
        echo $x;
    }

    function test_hola()
    {
        $client = ApiClient::instance()
            ->setHeaders([
                'Authorization: PRHN5lqx7B5TraYBOjCv13U48tgNbqLJaRpI6m8S',
                'Content-Type: application/json'
            ]);

        $client
            ->disableSSL()
            //->cache()
            //->redirect()
            ->setBody('[
            "fname" => "Tomas",
            "lname" => "Cruz",
            "email" => "cruz_t@gmail.com",
            "input_channel_id" => 8,
            "source_id" => 2,
            "interest_type_id" => 4,
            "project_id" => 540,
            "extra_fields" => [
                "rango_de_presupuesto" => "2M-3M"
            ]            
        ]')
            ->setUrl('https://api.eterniasoft.com/v3/clients')
            ->post()
            ->getResponse();

        $status = $client->getStatus();

        if ($status != 201 && $status != 200) {
            throw new \Exception($client->error());
        }

        dd(
            $client->data()
        );
    }



    function test_ggg()
    {
        $curl = curl_init();

        $data = [
            'pass' => 'f32fq3fq32412',
            'data' => '<ped><num>1234321</num><cli><rut>1-9</rut><nom>david lara oyarzun</nom><dir>los dominicos 7177</dir><gir>sin giro</gir><fon>8999345043</fon><ema>dlara@runasssssss.cl</ema><com>huechuraba</com></cli><art><cod>2345432134532</cod><pre>1000</pre><can>1</can><des>0</des><tot>1000</tot></art><art><cod>2345432134532</cod><pre>1000</pre><can>1</can><des>0</des><tot>1000</tot></art></ped>'
        ];

        curl_setopt_array($curl, array(
            CURLOPT_URL => Url::buildUrl('http://201.148.107.125/~runa/js/zoh/pedidos.php', $data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }


    function debug_api_client_exec_2()
    {
        $data = include 'D:\www\simplerest\logs\api_debug.php';

        dd($data);

        $res = ApiClient::instance()
            ->exec($data);

        if ($res->status() != 200) {
            dd($res->error(), 'ERROR');
        }

        dd($res->status(), 'STATUS CODE');

        dd(
            $res->data(),
            'DATA'
        );
    }

    function test_arr_to_xml()
    {
        $cli = array(
            'rut' => '1-9',
            'nom' => 'david lara oyarzun',
            'dir' => 'los dominicos 7177',
            'gir' => 'sin giro',
            'fon' => '89993450773',
            'ema' => 'dlara@runasssssssss.cl',
            'com' => 'huechuraba'
        );

        $quote_num = '123434421';

        /*
            los precios informados deben incluir el iva ***
        */
        $items = [
            [
                'cod' => '2345432134532',
                'pre' => '1000',
                'can' => '1',
                'des' => '0',
                'tot' => '1000',
            ],

            [
                'cod' => '2345432134532',
                'pre' => '1000',
                'can' => '1',
                'des' => '0',
                'tot' => '1000',
            ]
        ];

        $arr = [
            'num' => $quote_num,

            'cli' => $cli,

            'art' => $items
        ];

        $data = XML::fromArray($arr, 'ped', false);

        dd($data);
    }


    function test_runa()
    {
        $base_url = "http://201.148.107.125/~runa/js/zoh/pedidos.php";
        $password = "f32fq3fq32412";

        $cli = array(
            'rut' => '1-9',
            'nom' => 'david lara oyarzun',
            'dir' => 'los dominicos 7177',
            'gir' => 'sin giro',
            'fon' => '89993450773',
            'ema' => 'dlara@runasssssssss.cl',
            'com' => 'huechuraba'
        );

        $quote_num = '123434421';

        /*
            los precios informados deben incluir el iva ***
        */
        $items = [
            [
                'cod' => '2345432134532',
                'pre' => '1000',
                'can' => '1',
                'des' => '0',
                'tot' => '1000',
            ],

            [
                'cod' => '2345432134532',
                'pre' => '1000',
                'can' => '1',
                'des' => '0',
                'tot' => '1000',
            ]
        ];

        $arr = [
            'num' => $quote_num,

            'cli' => $cli,

            'art' => $items
        ];

        $data = XML::fromArray($arr, 'ped', false);

        // $params = [
        //     'pass' => 'f32fq3fq32412', 
        //     'data' => $data
        // ];

        // $url = Url::buildUrl('http://201.148.107.125/~runa/js/zoh/pedidos.php', $params);

        // $client = new ApiClient;

        // $client
        // ->disableSSL()
        // //->cache()
        // //->redirect()
        // ->setUrl($url)
        // ->get();

        // $status = $client->getStatus();

        // if ($status != 200){
        //     throw new \Exception("Error: " . $client->error());
        // }

        // dd(
        //     $client->data()         
        // );  
    }

    /*
        I will receive 15600 PHP
    */
    function refund()
    {
        $d1 = '2023-04-28';
        $d2 = '2023-05-08';

        dd(
            23400 * ((30 - Date::diffInDays($d2, $d1)) / 30)
        );
    }


    function decode_catasto()
    {
        /*
            Telefonos
        */
        //$str = "data=%7B%22status%22%3A%22COMPLETED%22%2C%22date_request%22%3A%222023-04-25+15%3A00%3A01%22%2C%22date_completion%22%3A%222023-04-26+08%3A25%3A38%22%2C%22id%22%3A%226447ceb9a18b1c3496263405%22%2C%22cf_piva%22%3A%22CCCMRN48T59E625G%22%2C%22callback%22%3A%7B%22url%22%3A%22https%3A%5C%2F%5C%2Fcatasto.000webhostapp.com%5C%2Fcallback.php%22%2C%22field%22%3A%22data%22%2C%22method%22%3A%22POST%22%2C%22data%22%3A%5B%5D%7D%2C%22tipo%22%3A%5B%22telefoni%22%5D%2C%22esito%22%3A%7B%22codice%22%3A200%2C%22info%22%3A%22OK%22%7D%2C%22timestamp%22%3A1682427577%2C%22owner%22%3A%22fabio56istrefi%40gmail.com%22%2C%22soggetto%22%3A%7B%22code%22%3A%22CCCMRN48T59E625G%22%2C%22utenze%22%3A%5B%223273271075%22%2C%223405355951%22%5D%7D%7D";


        /*
           elenco_immobili
        */
        $str = "data=%7B%22endpoint%22%3A%22elenco_immobili%22%2C%22stato%22%3A%22evasa%22%2C%22callback%22%3A%7B%22url%22%3A%22https%3A%5C%2F%5C%2Fcatasto.000webhostapp.com%5C%2Fcallback.php%22%2C%22field%22%3A%22data%22%2C%22method%22%3A%22POST%22%2C%22data%22%3A%7B%7D%7D%2C%22parametri%22%3A%7B%22tipo_catasto%22%3A%22F%22%2C%22provincia%22%3A%22MATERA+Territorio-MT%22%2C%22comune%22%3A%22F052%23MATERA%230%230%22%2C%22sezione%22%3Anull%2C%22sezione_urbana%22%3Anull%2C%22foglio%22%3A52%2C%22particella%22%3A597%7D%2C%22risultato%22%3A%7B%22immobili%22%3A%5B%7B%22sezione_urbana%22%3Anull%2C%22foglio%22%3A52%2C%22particella%22%3A597%2C%22subalterno%22%3A1%2C%22indirizzo%22%3A%22%22%2C%22sezione%22%3Anull%2C%22zona_censuaria%22%3A%22%22%2C%22categoria%22%3A%22%22%2C%22classe%22%3A%22%22%2C%22consistenza%22%3A%22%22%2C%22rendita%22%3A%22%22%2C%22partita%22%3A%22Soppressa%22%2C%22id_immobile%22%3A%22MzIzMyMzMjMzI0YjNTIjNTk3I0YwNTIjU29wcHJlc3NhIzEjICNNQVRFUkE%3D%22%7D%2C%7B%22sezione_urbana%22%3Anull%2C%22foglio%22%3A52%2C%22particella%22%3A597%2C%22subalterno%22%3A2%2C%22indirizzo%22%3A%22CONTRADA+LA+VAGLIA+n.+SC+Piano+S1%22%2C%22sezione%22%3Anull%2C%22zona_censuaria%22%3A%22%22%2C%22categoria%22%3A%22C03%22%2C%22classe%22%3A%2205%22%2C%22consistenza%22%3A%22122++m2%22%2C%22rendita%22%3A%22R.Euro%3A447%2C35%22%2C%22partita%22%3A%22%22%2C%22id_immobile%22%3A%22MzIzNCMzMjM0I0YjNTIjNTk3I0YwNTIjIzIjICNNQVRFUkE%3D%22%7D%2C%7B%22sezione_urbana%22%3Anull%2C%22foglio%22%3A52%2C%22particella%22%3A597%2C%22subalterno%22%3A3%2C%22indirizzo%22%3A%22%22%2C%22sezione%22%3Anull%2C%22zona_censuaria%22%3A%22%22%2C%22categoria%22%3A%22%22%2C%22classe%22%3A%22%22%2C%22consistenza%22%3A%22%22%2C%22rendita%22%3A%22%22%2C%22partita%22%3A%22Soppressa%22%2C%22id_immobile%22%3A%22MzIzNSMzMjM1I0YjNTIjNTk3I0YwNTIjU29wcHJlc3NhIzMjICNNQVRFUkE%3D%22%7D%2C%7B%22sezione_urbana%22%3Anull%2C%22foglio%22%3A52%2C%22particella%22%3A597%2C%22subalterno%22%3A4%2C%22indirizzo%22%3A%22CONTRADA+LA+VAGLIA+Piano+T%22%2C%22sezione%22%3Anull%2C%22zona_censuaria%22%3A%22%22%2C%22categoria%22%3A%22C02%22%2C%22classe%22%3A%2203%22%2C%22consistenza%22%3A%22175++m2%22%2C%22rendita%22%3A%22R.Euro%3A406%2C71%22%2C%22partita%22%3A%22%22%2C%22id_immobile%22%3A%22MzIzNiMzMjM2I0YjNTIjNTk3I0YwNTIjIzQjICNNQVRFUkE%3D%22%7D%2C%7B%22sezione_urbana%22%3Anull%2C%22foglio%22%3A52%2C%22particella%22%3A597%2C%22subalterno%22%3A5%2C%22indirizzo%22%3A%22CONTRADA+LA+VAGLIA+Piano+T-1+-+2+-+3%22%2C%22sezione%22%3Anull%2C%22zona_censuaria%22%3A%22%22%2C%22categoria%22%3A%22D01%22%2C%22classe%22%3A%22%22%2C%22consistenza%22%3A%22%22%2C%22rendita%22%3A%22R.Euro%3A4343%2C40%22%2C%22partita%22%3A%22%22%2C%22id_immobile%22%3A%22MzIzNyMzMjM3I0YjNTIjNTk3I0YwNTIjIzUjICNNQVRFUkE%3D%22%7D%2C%7B%22sezione_urbana%22%3Anull%2C%22foglio%22%3A52%2C%22particella%22%3A597%2C%22subalterno%22%3A6%2C%22indirizzo%22%3A%22CONTRADA+LA+VAGLIA+Piano+T%22%2C%22sezione%22%3Anull%2C%22zona_censuaria%22%3A%22%22%2C%22categoria%22%3A%22C01%22%2C%22classe%22%3A%2204%22%2C%22consistenza%22%3A%22109++m2%22%2C%22rendita%22%3A%22R.Euro%3A1874%2C58%22%2C%22partita%22%3A%22%22%2C%22id_immobile%22%3A%22MzIzOCMzMjM4I0YjNTIjNTk3I0YwNTIjIzYjICNNQVRFUkE%3D%22%7D%2C%7B%22sezione_urbana%22%3Anull%2C%22foglio%22%3A52%2C%22particella%22%3A597%2C%22subalterno%22%3A7%2C%22indirizzo%22%3A%22CONTRADA+LA+VAGLIA+Piano+T%22%2C%22sezione%22%3Anull%2C%22zona_censuaria%22%3A%22%22%2C%22categoria%22%3A%22C01%22%2C%22classe%22%3A%2204%22%2C%22consistenza%22%3A%2287++m2%22%2C%22rendita%22%3A%22R.Euro%3A1496%2C23%22%2C%22partita%22%3A%22%22%2C%22id_immobile%22%3A%22MzIzOSMzMjM5I0YjNTIjNTk3I0YwNTIjIzcjICNNQVRFUkE%3D%22%7D%2C%7B%22sezione_urbana%22%3Anull%2C%22foglio%22%3A52%2C%22particella%22%3A597%2C%22subalterno%22%3A8%2C%22indirizzo%22%3A%22%22%2C%22sezione%22%3Anull%2C%22zona_censuaria%22%3A%22%22%2C%22categoria%22%3A%22%22%2C%22classe%22%3A%22%22%2C%22consistenza%22%3A%22%22%2C%22rendita%22%3A%22%22%2C%22partita%22%3A%22Soppressa%22%2C%22id_immobile%22%3A%22MzkzOTc4IzM5Mzk3OCNGIzUyIzU5NyNGMDUyI1NvcHByZXNzYSM4IyAjTUFURVJB%22%7D%2C%7B%22sezione_urbana%22%3Anull%2C%22foglio%22%3A52%2C%22particella%22%3A597%2C%22subalterno%22%3A10%2C%22indirizzo%22%3A%22CONTRADA+LA+VAGLIA+n.+SC+Piano+T-S1%22%2C%22sezione%22%3Anull%2C%22zona_censuaria%22%3A%22%22%2C%22categoria%22%3A%22%22%2C%22classe%22%3A%22%22%2C%22consistenza%22%3A%22%22%2C%22rendita%22%3A%22R.Euro%3A%22%2C%22partita%22%3A%22Bene+comune+non+censibile%22%2C%22id_immobile%22%3A%22NDc1MjA3IzQ3NTIwNyNGIzUyIzU5NyNGMDUyI0JlbmUgY29tdW5lIG5vbiBjZW5zaWJpbGUjMTAjICNNQVRFUkE%3D%22%7D%2C%7B%22sezione_urbana%22%3Anull%2C%22foglio%22%3A52%2C%22particella%22%3A597%2C%22subalterno%22%3A11%2C%22indirizzo%22%3A%22CONTRADA+LA+VAGLIA+n.+SC+Piano+S1%22%2C%22sezione%22%3Anull%2C%22zona_censuaria%22%3A%22%22%2C%22categoria%22%3A%22%22%2C%22classe%22%3A%22%22%2C%22consistenza%22%3A%22%22%2C%22rendita%22%3A%22R.Euro%3A%22%2C%22partita%22%3A%22Bene+comune+non+censibile%22%2C%22id_immobile%22%3A%22NDc1MjA4IzQ3NTIwOCNGIzUyIzU5NyNGMDUyI0JlbmUgY29tdW5lIG5vbiBjZW5zaWJpbGUjMTEjICNNQVRFUkE%3D%22%7D%2C%7B%22sezione_urbana%22%3Anull%2C%22foglio%22%3A52%2C%22particella%22%3A597%2C%22subalterno%22%3A12%2C%22indirizzo%22%3A%22CONTRADA+LA+VAGLIA+n.+SC+Piano+S1%22%2C%22sezione%22%3Anull%2C%22zona_censuaria%22%3A%22%22%2C%22categoria%22%3A%22C02%22%2C%22classe%22%3A%2202%22%2C%22consistenza%22%3A%2265++m2%22%2C%22rendita%22%3A%22R.Euro%3A127%2C57%22%2C%22partita%22%3A%22%22%2C%22id_immobile%22%3A%22NDc1MjA5IzQ3NTIwOSNGIzUyIzU5NyNGMDUyIyMxMiMgI01BVEVSQQ%3D%3D%22%7D%2C%7B%22sezione_urbana%22%3Anull%2C%22foglio%22%3A52%2C%22particella%22%3A597%2C%22subalterno%22%3A13%2C%22indirizzo%22%3A%22CONTRADA+LA+VAGLIA+n.+SC+Piano+S1%22%2C%22sezione%22%3Anull%2C%22zona_censuaria%22%3A%22%22%2C%22categoria%22%3A%22C02%22%2C%22classe%22%3A%2202%22%2C%22consistenza%22%3A%2211++m2%22%2C%22rendita%22%3A%22R.Euro%3A21%2C59%22%2C%22partita%22%3A%22%22%2C%22id_immobile%22%3A%22NDc1MjEwIzQ3NTIxMCNGIzUyIzU5NyNGMDUyIyMxMyMgI01BVEVSQQ%3D%3D%22%7D%2C%7B%22sezione_urbana%22%3Anull%2C%22foglio%22%3A52%2C%22particella%22%3A597%2C%22subalterno%22%3A14%2C%22indirizzo%22%3A%22CONTRADA+LA+VAGLIA+n.+SC+Piano+S1%22%2C%22sezione%22%3Anull%2C%22zona_censuaria%22%3A%22%22%2C%22categoria%22%3A%22C02%22%2C%22classe%22%3A%2202%22%2C%22consistenza%22%3A%22152++m2%22%2C%22rendita%22%3A%22R.Euro%3A298%2C31%22%2C%22partita%22%3A%22%22%2C%22id_immobile%22%3A%22NDc1MjExIzQ3NTIxMSNGIzUyIzU5NyNGMDUyIyMxNCMgI01BVEVSQQ%3D%3D%22%7D%2C%7B%22sezione_urbana%22%3Anull%2C%22foglio%22%3A52%2C%22particella%22%3A597%2C%22subalterno%22%3A9%2C%22indirizzo%22%3A%22CONTRADA+LA+VAGLIA+n.+SC+Piano+T%22%2C%22sezione%22%3Anull%2C%22zona_censuaria%22%3A%22%22%2C%22categoria%22%3A%22C02%22%2C%22classe%22%3A%2201%22%2C%22consistenza%22%3A%22290++m2%22%2C%22rendita%22%3A%22R.Euro%3A479%2C27%22%2C%22partita%22%3A%22%22%2C%22id_immobile%22%3A%22NDc1MjI2IzQ3NTIyNiNGIzUyIzU5NyNGMDUyIyM5IyAjTUFURVJB%22%7D%2C%7B%22sezione_urbana%22%3Anull%2C%22foglio%22%3A52%2C%22particella%22%3A597%2C%22subalterno%22%3A15%2C%22indirizzo%22%3A%22CONTRADA+LA+VAGLIA+n.+SC+Piano+S1%22%2C%22sezione%22%3Anull%2C%22zona_censuaria%22%3A%22%22%2C%22categoria%22%3A%22C02%22%2C%22classe%22%3A%2202%22%2C%22consistenza%22%3A%22540++m2%22%2C%22rendita%22%3A%22R.Euro%3A1059%2C77%22%2C%22partita%22%3A%22%22%2C%22id_immobile%22%3A%22NDkxNTk2IzQ5MTU5NiNGIzUyIzU5NyNGMDUyIyMxNSMgI01BVEVSQQ%3D%3D%22%7D%5D%7D%2C%22esito%22%3A%22OK%22%2C%22timestamp%22%3A1682523120%2C%22owner%22%3A%22fabio56istrefi%40gmail.com%22%2C%22id%22%3A%22644943f09213dd5ac37f82b6%22%7D";

        // $str = "data=%7B%22status%22%3A%22COMPLETED%22%2C%22date_request%22%3A%222023-04-27+17%3A47%3A01%22%2C%22date_completion%22%3A%222023-04-27+17%3A47%3A21%22%2C%22id%22%3A%22644a98c0a6c7f7296966411d%22%2C%22cf_piva%22%3A%22DNILSE69T53I073J%22%2C%22callback%22%3A%7B%22url%22%3A%22https%3A%5C%2F%5C%2Fcatasto.000webhostapp.com%5C%2Fcallback.php%22%2C%22field%22%3A%22data%22%2C%22method%22%3A%22POST%22%2C%22data%22%3A%5B%5D%7D%2C%22tipo%22%3A%5B%22telefoni%22%5D%2C%22esito%22%3A%7B%22codice%22%3A200%2C%22info%22%3A%22OK%22%7D%2C%22timestamp%22%3A1682610368%2C%22owner%22%3A%22fabio56istrefi%40gmail.com%22%2C%22soggetto%22%3A%7B%22code%22%3A%22DNILSE69T53I073J%22%2C%22utenze%22%3A%5B%5D%7D%7D";

        $str = substr(trim($str), 5);

        $str = urldecode($str);

        $str = json_decode($str, true);

        dd(
            $str
        );
    }

    function gen_file()
    {
        $size = 10 * 1024 * 1024;
        Files::writeOrFail(ETC_PATH . 'big_file.txt', str_repeat('*', $size));
    }


    function callbacks()
    {
        $input = file_get_contents('php://input');

        if (empty($input)) {
            response()->error("No data received");
        }

        response()->sendJson([
            "message" => "Recibido",
            "data"    => $input
        ]);
    }


    function api_callback()
    {
        /*
            http://test.lan/callback.php
            https://catasto.000webhostapp.com/callback.php
            https://ticiwe.com/callbacks
            etc
        */

        $url = 'http://simplerest.lan/dumb/callbacks';

        $client = new ApiClient($url);

        $client
            ->disableSSL()
            ->redirect()
            ->setBody([
                'data' => [
                    'name' => 'Fabio',
                    'age'  => 27
                ]
            ])
            ->post();

        dd(
            $client->data()
        );
    }

    function reordenarArray($a, $b)
    {
        $first_pos_b_in_a = array_search($b[0], $a);

        $a = array_diff($a, $b);

        list($a1, $a2) = array_chunk($a, $first_pos_b_in_a);

        $a = array_merge($a1, $b, $a2);

        return $a;
    }

    function test_ra()
    {
        // Ejemplo de uso
        $a = ['X', 'A', 'B', 'C', 'D', 'E'];
        $b = ['C', 'A'];

        /*
            Array
            (
                [0] => X
                [1] => B
                [2] => D
                [3] => C
                [4] => A
                [5] => E
            )
        */
        $resultado = $this->reordenarArray($a, $b);

        // Imprimimos el resultado
        print_r($resultado);
    }

    function test_casting_to_number()
    {
        var_dump(
            Strings::toIntOrFail(null)
        );

        var_dump(
            Strings::toIntOrFail(false)
        );

        var_dump(
            Strings::toIntOrFail("52")
        );

        var_dump(
            Strings::toIntOrFail(52)
        );

        var_dump(
            Strings::toIntOrFail("52.7")
        );

        var_dump(
            Strings::toIntOrFail(52.7)
        );
    }

    function test_casting_int_to_str()
    {
        var_dump(
            Strings::fromInt(null)
        );

        var_dump(
            Strings::fromInt("52")
        );

        var_dump(
            Strings::fromInt(52)
        );

        // Invalid integer for '52.7'
        var_dump(
            Strings::fromInt("52.7")
        );

        // Invalid integer for '52.7'
        var_dump(
            Strings::fromInt(52.7)
        );
    }

    function test_casting_float_to_str()
    {
        var_dump(
            Strings::fromFloat(null)
        );

        var_dump(
            Strings::fromFloat("52")
        );

        var_dump(
            Strings::fromFloat(52)
        );

        // Invalid integer for '52.7'
        var_dump(
            Strings::fromFloat("52.7")
        );

        // Invalid integer for '52.7'
        var_dump(
            Strings::fromFloat(52.7)
        );
    }

    function test_format_json()
    {
        $str = '{"endpoint":"ricerca_nazionale_pg","stato":"evasa","callback":{"url":"https:\/\/ticiwe.com\/callbacks?r=realstate&sub=ricerca_nazionale","field":"data","method":"POST","data":[]},"parametri":{"cf_piva":"12485671007","tipo_catasto":"TF","provincia":"NAZIONALE-IT"},"risultato":{"soggetti":[{"denominazione":"ALTRAVIA SERVIZI SOCIETA\' A RESPONSABILITA\' LIMITATA","sede":"ROMA (RM)","cf":"12485671007","id_soggetto":"OTgwMzI3NTA1MiMwI0FMVFJBVklBIFNFUlZJWkkgU09DSUVUQScgQSBSRVNQT05TQUJJTElUQScgTElNSVRBVEEjUk9NQSAoUk0pIzEyNDg1NjcxMDA3","catasti":[{"citta":"ROMA","fabbricati":1,"terreni":0}]}]},"esito":"OK","timestamp":1683988870,"owner":"fabio56istrefi@gmail.com","id":"645fa18682673817d87710e8"}';

        dd(Strings::formatJSON($str));
    }

    function test_file_fn()
    {
        var_dump(Logger::getContent());

        dd(Files::readOrFail("c:\ddd"));
    }

    function test_curl_proxy_3()
    {
        $url       = 'https://academico.upla.edu.pe/uplanet/frm_login.aspx';
        $proxy_url = 'http://2.56.221.125/php-proxy/Proxy.php';

        dd($url, 'URL');

        $client = ApiClient::instance($proxy_url)
            ->setHeaders([
                'Proxy-Auth: Bj5pnZEX6DkcG6Nz6AjDUT1bvcGRVhRaXDuKDX9CjsEs2',
                'Proxy-Target-URL: ' . $url
            ]);

        $client
            ->disableSSL()
            //->cache()
            ->redirect()
            ->get();

        if ($client->getStatus() != 200) {
            throw new \Exception($client->error());
        }

        dd(
            $client->data()
        );
    }

    function test_remove_css()
    {
        $html = '<html>
                <head>
                    <style>
                        .my-style {
                            color: red;
                        }
                    </style>
                </head>
                <body>
                    <div style="font-size: 16px;">Some text</div>
                    <div class="my-style">Some text with class</div>
                </body>
            </html>';

        $cleanHtml = HTML::removeCSS($html, true, true, true);

        dd($cleanHtml);
    }

    function test_remove_tag()
    {
        $html = '</li>
        <li class="nav-item">
            <a class="nav-link d-inline-flex align-items-center" href="https://shuffle.dev/components/bootstrap?utm_source=bootstrap&amp;utm_medium=class-list" rel="noopener" target="_blank">
                <svg style="margin-right: 0.375rem; --darkreader-inline-stroke: currentColor;" height="16" width="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" data-darkreader-inline-stroke=""><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Bootstrap Components
            </a>
        </li>
        <li class="nav-item">
            <span class="a-class">AAA AAA</span>
            <a class="nav-link d-inline-flex align-items-center" href="https://shuffle.dev/bootstrap/templates?utm_source=bootstrap&amp;utm_medium=class-list" rel="noopener" target="_blank"><p>XXX</p></a>
        </li>';


        //$html = HTML::removeTags($html, 'svg');
        //dd($html); // Salida: 

        $html = HTML::removeTags($html, ['svg', 'span']);
        dd($html); // Salida: 
    }

    function test_removehtmlattributes()
    {
        $html = '<div onclick="alert(\'Hello\');" style="color: red;" class="container">Content</div>';

        // Eliminar el atributo 'onclick'
        $result1 = HTML::removeHTMLAttributes($html, 'onclick');
        dd($result1, 'PRUEBA 1');
        // Salida esperada: '<div style="color: red;" class="container">Content</div>'

        // Eliminar los atributos 'style' y 'class'
        $result2 = HTML::removeHTMLAttributes($html, ['style', 'class']);
        dd($result2, 'PRUEBA 2');
        // Salida esperada: '<div onclick="alert(\'Hello\');">Content</div>'
    }

    function test_var_export()
    {
        $content = [
            'A', 'B', 'C'
        ];

        // $content = "HOLA";

        $path = ETC_PATH . 'bt.php';

        Logger::varExport($content, $path);

        dd(
            Files::getContent($path),
            'CONTENT'
        );
    }

    function test_remove_bt_classes()
    {
        $html = '</li>
        <li class="nav-item">
            <a class="nav-link d-inline-flex align-items-center" href="https://shuffle.dev/components/bootstrap?utm_source=bootstrap&amp;utm_medium=class-list" rel="noopener" target="_blank" style="color:red">
              
                Bootstrap Components
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link d-inline-flex align-items-center" href="https://shuffle.dev/bootstrap/templates?utm_source=bootstrap&amp;utm_medium=class-list" rel="noopener" target="_blank"><p>XXX</p></a>
        </li>';


        $html = CSS::removeCSS($html);
        $html = HTML::removeHTMLAttributes($html, ['rel', 'target']);


        /* 
            Salida:

            </li>
            <li class="nav-item">
                <a class="d-inline-flex align-items-center" href="https://shuffle.dev/components/bootstrap?utm_source=bootstrap&amp;utm_medium=class-list" rel="noopener" target="_blank">
                    <svg style="margin-right: 0.375rem; --darkreader-inline-stroke: currentColor;" height="16" width="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" data-darkreader-inline-stroke=""><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Bootstrap Components
                </a>
            </li>
            <li class="nav-item">
                <span class="a-class">AAA AAA</span>
                <a class="d-inline-flex align-items-center" href="https://shuffle.dev/bootstrap/templates?utm_source=bootstrap&amp;utm_medium=class-list" rel="noopener" target="_blank"><p>XXX</p></a>
            </li>
        */
        dd($html);
    }


    function test_remove_social_links()
    {
        // Ejemplo de uso
        $html = '
        <a href="https://www.facebook.com/mi-pagina">Visita mi página de Facebook</a>
        <a href="https://www.twitter.com/mi-usuario/2">Sígueme en Twitter</a>
        <a href="https://www.instagram.com/mi-usuario">Sígueme en Instagram</a>
        <a href="https://www.linkedin.com/in/mi-usuario">Conéctate conmigo en LinkedIn</a>
        <a href="https://www.otra-red-social.com/mi-usuario">Enlace a otra red social</a>
        ';

        $filteredHtml = HTML::removeSocialLinks($html);

        dd($filteredHtml);
    }

    function test_until_n_words()
    {
        dd(
            Strings::getUpTo("To add support, for other word breaks like commas and dashes, preg_match gives a quick way and doesn't require splitting the string", 8)
        );
    }

    function test_reducetext()
    {
        /*
            Prueba 1
        */
        $html1 = '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>';
        $n_words1 = 5;
        $result1 = Strings::reduceText($html1, $n_words1);
        dd($result1, 'PRUEBA 1');
        // Salida esperada: "<p>Lorem ipsum dolor sit amet,</p>"

        /*
            Prueba 2
        */
        $html2 = '<div><h1>Title</h1><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p></div>';
        $n_words2 = 4;
        $result2 = Strings::reduceText($html2, $n_words2);
        dd($result2, 'PRUEBA 2');
        // Salida esperada: "<div><h1>Title</h1><p>Lorem ipsum dolor sit amet, consectetur,</p></div>"
    }

    function test_getupdatedate()
    {
        $gd_link     = 'https://docs.google.com/uc?export=download&id=17RAZgbp_T-KIgG8BSrb1siocXUusFoLL';

        $googleDrive = new GoogleDrive();
        $updateDate  = $googleDrive->getUpdateDate($gd_link);

        // Use dd() instead of echo to display the results
        dd($updateDate, 'INFO');
    }

    function test_gdrive_info()
    {

        $googleDrive = new GoogleDrive();
        $info        = $googleDrive->getFolderInfo('1oUqLiey81m0keXAo1ZtOsGYfd5c1VTeT', [
            'pageSize' => 5
        ], 'id, name, createdTime, modifiedTime');

        dd($info);
    }

    function test_get_zip_from_google_drive()
    {
        // "https://docs.google.com/uc?export=download&id=1yMrPb6j51mvXV2taGiSa57fcElpbApGR"
        $fileId      = '1yMrPb6j51mvXV2taGiSa57fcElpbApGR';
        $destination = ETC_PATH . 'downloads/file.zip';

        $result = (new GoogleDrive())
            ->download($fileId, $destination, false, 300);

        // true
        dd($result, 'RESULT');
    }


    function test_file_cache_put()
    {
        dd(
            FileCache::put('constelacion', 'andromeda X', 10)
        );
    }

    /*
        Devuelve la ruta del archivo de cache

        Ej:

        C:\Users\jayso\AppData\Local\Temp\8302505164ff597cfd342a21e60c849d26093d73.cache
    */
    function test_file_cache_path()
    {
        dd(
            FileCache::getCachePath('constelacion')
        );
    }

    function test_file_cache_get()
    {
        dd(
            FileCache::get('constelacion')
        );
    }

    function is_expired()
    {
        dd(FileCache::getCachePath('constelacion'));

        dd(
            FileCache::expiredFileByKey('constelacion', null, true)
        );
    }


    function test_file_cache_put_simple()
    {
        dd(
            Files::writeOrFail(CACHE_PATH . 'constelacion.txt', 'andromeda X')
        );
    }

    function test_file_cache_get_simple()
    {
        $file     = CACHE_PATH . 'constelacion.txt';
        $exp_time = 5;

        /*
            Es nuestra responsabilidad llamar a esta funcion antes
            que no solo dice si el archivo ha expirado sino que lo remueve si es el caso
        */
        $is_expired = FileCache::expiredFile($file, $exp_time);

        dd(
            Files::getContent($file)
        );
    }

    function is_expired_simple()
    {
        $file     = CACHE_PATH . 'constelacion.txt';
        $exp_time = 5;

        // Chequeo si todavia es valido para 5 segundos de tiempo de expiracion

        dd(
            FileCache::expiredFile($file, $exp_time),
            'Expired?'
        );
    }


    function test_db_cache_put()
    {
        DBCache::put('galaxia', 'via lacteaaaaa', 90);
    }

    function test_db_cache_get()
    {
        dd(
            DBCache::get('galaxia')
        );
    }

    function test_db_transient()
    {
        set_transient('bzz-importer_completion', 55);

        dd(get_transient('bzz-importer_completion', 0));
    }

    function test_memorizacion()
    {
        $url = 'http://apis.lan/dumb/now';

        set_cache_driver(DBCache::class);

        Memoization::memoize($url, function () use ($url) {
            dd("...");
            return file_get_contents($url);
        }, 1);

        dd(
            Memoization::memoize($url)
        );

        sleep(5);

        dd(
            Memoization::memoize($url)  // mismo valor
        );
    }

    function test_memorizacion_2()
    {
        $url = 'http://apis.lan/dumb/now';

        set_cache_driver(FileCache::class);

        Memoization::memoize($url, function () use ($url) {
            dd("...");
            return file_get_contents($url);
        }, 2);  // <-------------------------------- cache por 2 segundos

        dd(
            Memoization::memoize($url),
            'VALOR RECUPERADO'
        );

        sleep(3);

        // Ya no le alcanzan los 2 segundos especificados!
        dd(
            Memoization::memoize($url),
            'VALOR RECUPERADO'
        );
    }

    function test_memorizacion_3()
    {
        $url = 'http://luxuritop.test/wp-json/wc/v3/products';

        set_cache_driver(FileCache::class);

        Memoization::memoize($url, function () use ($url) {
            $cli = new WooCommerceApiClient(
                'ck_f710ad18c309b89f309e7144da238814bd4bf6b4',
                'cs_53b05e639bdba922eb296fb7ab40e162eb7570d6'
            );

            $pid = null;

            $base_url = 'http://luxuritop.test';
            $endpoint = '/wp-json/wc/v3/products' . (!empty($pid) ? "/$pid" : '');

            $url      = "{$base_url}{$endpoint}";

            $cli
                ->url($url)
                ->get()
                ->setOAuth();

            $cli->send();

            $res = $cli->data();

            return $res;
        }, 10);  // <-------------------------------- cache por 2 segundos

        dd(
            Memoization::memoize($url),
            'VALOR RECUPERADO'
        );

        sleep(3);

        // Ya no le alcanzan los 2 segundos especificados!
        dd(
            Memoization::memoize($url),
            'VALOR RECUPERADO'
        );
    }

    function memoize_test()
    {
        set_cache_driver(InMemoryCache::class);

        Memoization::memoize('nombre.hijo', function () {
            return 'Pablo';
        });
        Memoization::memoize('nombre.papa', function () {
            return 'Feli';
        });

        dd(
            Memoization::memoize('nombre.papa')
        );

        dd(
            Memoization::memoize('nombre.hijo')
        );

        $x = 2;
        $y = 3;

        Memoization::memoize('calculations.more_calc', function () use ($x, $y) {
            dd("Doing some expensive calculations ...");
            sleep(2);
            return $x * $y;
        });

        dd(Memoization::memoize('calculations.more_calc'), 'calculations.more_calc');
        dd(Memoization::memoize('calculations.more_calc'), 'calculations.more_calc');
        dd(Memoization::memoize('calculations.more_calc'), 'calculations.more_calc');
    }

    function testtttttt()
    {
        $link     = 'https://docs.google.com/uc?export=download&id=1yMrPb6j51mvXV2taGiSa57fcElpbApGR';

        $id       = Url::getQueryParam($link, 'id');

        dd($id, 'ID');
    }


    function test_consume_api()
    {
        dd(
            consume_api('www.yahoo.in')
        );
    }


    function test_format_json_to_file()
    {
        $path = 'D:\Desktop\SHADOWR FIVERR\CategoriesJsonFile(4).json';

        $str = Strings::formatJSON($path);

        file_put_contents('D:\Desktop\SHADOWR FIVERR\formatted\CategoriesJsonFile(4).json', $str);
    }

    function get_url_params()
    {
        dd(Url::getSlugs(null, true));
    }

    function dolar_widget_test()
    {
        js_file("https://www.dolar-colombia.com/widget.js?t=2&c=1");

        render();
    }

    function test_csv_uploader()
    {
        view('csv_uploader');
    }

    function test_update_htaccess()
    {
        ApacheWebServer::updateHtaccessFile([
            'upload_max_filesize' => '1024M',
            'post_max_size' => '1024M',
        ], ROOT_PATH);
    }

    function test_upload_limits()
    {
        FileUploader::setLimits();

        dd([
            "memory_limit"          => ini_get("memory_limit"),
            "max_execution_time"    => ini_get("max_execution_time"),
            "upload_max_filesize"   => ini_get("upload_max_filesize"),
            "post_max_size"         => ini_get("post_max_size")
        ]);
    }

    function cfg()
    {
        dd(
            get_cfg('log_file')
        );

        set_cfg('log_file', 'debug.log');

        dd(
            get_cfg('log_file')
        );
    }

    /*
        php com my_controller cfg:my_config_variable=3
    */
    function get_cfg_var(){
        dd(
            Config::get()
        );
    }

    function opt()
    {
        dd(
            get_option('free_mem')
        );

        set_option('free_mem', '1200M');

        dd(
            get_option('free_mem')
        );
    }

    function test_optimize()
    {
        DB::getConnection();

        $tb = DB::getTableNames()[0];

        dd(
            DB::repair($tb, true),
            $tb
        );
    }

    function test_query_param()
    {
        echo Url::addQueryParam('http://simplerest.lan/api/v1/products', 'q', 'fiesta') . "\n";
        echo Url::addQueryParam('http://simplerest.lan/api/v1/products?v=1', 'q', 'fiesta') . "\n";
        echo Url::addQueryParam('http://simplerest.lan/api/v1/products?v=1', 'v', '3') . "\n";

        // http://www.google.com?q=fiesta
        echo Url::addQueryParam('http://www.google.com', 'q', 'fiesta') . "\n";

        // http://www.google.com?today=&q=fiesta
        echo Url::addQueryParam('http://www.google.com?today', 'q', 'fiesta') . "\n";
    }

    function test_paginate()
    {
        $cli  = ApiClient::instance('http://simplerest.lan/api/v1/products?v=1');

        $cli
            ->queryParam('size', 3)
            ->queryParam('page', 2)

            ->setMethod('GET')

            ->send();

        dd($cli->data());
    }



    function test_api0c_mock()
    {
        $mock = ETC_PATH . 'res_jsonplaceholder.json';

        $cli  = new ApiClient();

        $res  = $cli
            ->when(!empty($mock), function ($it) use ($mock) {
                $it->mock($mock);
            })
            ->request('http://jsonplaceholder.typicode.com/posts/3', 'GET')
            ->getResponse();

        /*
            Array
                (
                    [data] => {
                    "userId": 1,
                    "id": 3,
                    "title": "ea molestias quasi exercitationem repellat qui ipsa sit aut",
                    "body": "et iusto sed quo iure\nvoluptatem occaecati omnis eligendi aut ad\nvoluptatem doloribus vel accusantium quis pariatur\nmolestiae porro eius odio et labore et velit aut"
                    }
                        [http_code] => 200
                        [error] =>
                    )
                )
        */
        dd($res);

        /*
            {
                "userId": 1,
                "id": 3,
                "title": "ea molestias quasi exercitationem repellat qui ipsa sit aut",
                "body": "et iusto sed quo iure\nvoluptatem occaecati omnis eligendi aut ad\nvoluptatem doloribus vel accusantium quis pariatur\nmolestiae porro eius odio et labore et velit aut"
            }
        */
        dd($cli->data());
    }

    function test_mock()
    {
        $mock = 'D:\www\woo2\wp-content\plugins\mutawp\etc\responses\products.json';

        $cli  = new ApiClient();

        $res  = $cli
            ->when(!empty($mock), function ($it) use ($mock) {
                $it->mock($mock);
            })
            ->request('https://mutawp.com/ajax/get_products?v=1', 'GET')
            ->getResponse();

        dd($res);
        dd($cli->data());
    }

    function test_chunk()
    {
        $data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $chunks = Arrays::chunk($data, 5, 1);

        dd($chunks);
    }

    function test_makearray()
    {
        $array = [
            'name' => 'John Doe',
            'age' => 30,
            'country' => 'USA'
        ];

        $path = 'data.products';

        $result = Arrays::makeArray($array, $path);

        print_r($result);
    }

    /*
        Paginarlo y usarlo para alimentar WP Muta !!!
    */
    function serve_json()
    {
        $path      = 'D:\www\woo2\wp-content\plugins\mutawp\etc\responses\products.json';
        $row_path  = "data.products"; // ['data']['products']
        $page      = $_GET['page'] ?? 1;
        $page_size = $_GET['page_size'] ?? 10;

        $page      = (int) $page;
        $page_size = (int) $page_size;

        $data      = Files::getContent($path);

        $res = Paginator::paginate($data, $page, $page_size, $row_path);

        response()->sendJson($res);
    }


    function test_p()
    {
        $url = 'http://146.190.123.27';
        $cli = new ApiClient($url);

        $res  = $cli
            ->withoutStrictSSL()
            ->setMethod('GET')
            ->getResponse();

        dd($res);
        dd($cli->data());
    }

    // descarga archivo binario en este caso
    function test_get_binary()
    {
        $url = 'https://www.learningcontainer.com/wp-content/uploads/2020/05/sample.tar';
        $cli = new ApiClient($url);

        $cli
            ->setBinary()
            ->withoutStrictSSL();

        $bytes = $cli
            ->download(ETC_PATH . 'file.zip');

        // empty => OK
        if (!empty($cli->error())) {
            dd("HTTP Error. Detail: " . $cli->error());
        }

        // 200 OK
        if ($cli->status() != 200) {
            dd("HTTP status code" . $cli->status());
            exit;
        }

        // true
        dd($cli->data(), 'DATA');

        dd($bytes, 'BYTES escritos');
    }

    function test_unzip()
    {
        $file = ETC_PATH . 'Livemesh Addons for Elementor Premium v7.2.4.zip';

        dd(
            ZipManager::unzip($file, ETC_PATH . 'test')
        );
    }

    function test_crypt()
    {
        $str = "Hola mundo";

        dd(SimpleCrypt::encrypt($str));

        dd(SimpleCrypt::decrypt(
            SimpleCrypt::encrypt($str)
        ));
    }

    function test_idea()
    {
        $zips = array(
            'D:\www\woo2\wp-content\plugins\betheme-premium-wordpress-theme\betheme-child.zip',
            'D:\www\woo2\wp-content\plugins\betheme-premium-wordpress-theme\betheme.zip',
            'D:\www\woo2\wp-content\plugins\betheme-premium-wordpress-theme\betheme-otro-child.zip'
        );

        // ...
    }

    function test_lang_detector()
    {
        $str = '
        PublishPress is the plugin for managing and scheduling WordPress content. They include a content calendar, notifications and customized statuses.
        ';

        dd(
            LangDetector::is($str, 'en'),
            'Is English'
        );

        dd(
            LangDetector::is($str, 'es'),
            'Is Spanish'
        );
    }

    function css_beautifier()
    {
        $path = 'D:\www\woo2\wp-content\themes\kadence\assets\css\slider.min.css';

        dd(
            CSS::beautifier($path)
        );
    }

    function test_get_css()
    {
        $html = Files::getContent('D:\www\woo2\wp-content\plugins\wp_runa\views\contact_form.php');

        $cssClasses = CSS::getCSSClasses($html);
        dd($cssClasses);
    }

    /*
        Para "RUNA" obtener las clases con CSS::getCSSClasses() de CSS de:

            D:\www\woo2\wp-content\plugins\wp_runa\views\contact_form.php
            D:\www\woo2\wp-content\plugins\wp_runa\views\cotizador.php
        
        y sobre esas, buscar las reglas de CSS para cada archivo

        En vez de buscar las reglas en un directorio es mejor scrapear los archivos .css
        referenciados en el rendering de la pagina 

        Ademas debe obtenerse todo CSS incrustado mediante <style> de esa misma pagina
    */
    function get_css_rules()
    {
        System::setMemoryLimit('2048M');

        $path        = 'D:\Desktop\OMAR FUENTES\RUNA\test';
        // $path        = 'D:\Desktop\OMAR FUENTES\RUNA\test\slider.min.css';
        // $path        = 'D:\Desktop\OMAR FUENTES\RUNA\test\slider_extra.min.css';

        $css_classes = ['tns-slider', 'tns-item'];

        $css_rules   = CSS::getCSSRules($path, $css_classes);

        dd($css_rules);
    }

    function test_api_client_query()
    {
        $including_warehouses =  true;

        $config   = [
            'token'    =>  'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6MTYsImVtYWlsIjoid2ViQG1heGltcG9ydGFjaW9uZXMuY29tLnBlIiwicm9sZSI6ImxvZ2lzdGljIiwiaWF0IjoxNjg2NzI2Mjg3fQ.U4Z5Hbud2jl31LcM8ZMX0pxhVAJQW8OLIIXQ_NJQ_44',

            'endpoint_products'         => 'https://appdesa.maximportaciones.com.pe/api/products', // no tiene paginacion
            'endpoint_products_detail'  => 'https://146.190.123.27/api/products',
            'endpoint_product_detail'   => 'https://146.190.123.27/api/products/', // + id
            'debug'                     => true
        ];

        $token                 = $config['token'];
        $catalog_url        = $config['endpoint_products'];
        $catalog_detail_url    = $config['endpoint_products_detail'];
        $url                = $including_warehouses ? $catalog_detail_url : $catalog_url;

        $exp_time = ($config['debug'] ? 3600 * 24 * 15 : ($config['api_cache'] ?? 0));

        $client   = ApiClient::instance()
            ->setJWTAuth($token)
            ->withoutStrictSSL()
            //->setCache($exp_time)  // <-- solo para pruebas mas de 3600 * 24
            ->decode(true);

        $client->setUrl($url);
        $client->queryParams([
            'page' => 364
        ]);

        $res = $client->get();

        if ($res->status() != 200 || !empty($res->error())) {
            $msg = "Error al connectar con '$url'. Detalle: " . $res->error();
            dd($msg);

            return;
        }

        if (empty($res)) {
            $msg = "API no está proveyendo datos";
            dd($msg);
        }

        dd($res->data());
    }

    function test_config_set()
    {
        Config::set('db_connections.main.tb_prefix', 'wp_');

        dd(
            config()['db_connections']['main']['tb_prefix']
        );

        dd(
            Config::get('db_connections.main.tb_prefix')
        );
    }

    function test_arr_fn()
    {
        // Uso del método estático
        $rows = array(
            array(
                'id' => 1,
                'code' => 'ABC',
                'stockByWarehouse' => 10,
                'otherField' => 'value'
            ),
            array(
                'id' => 2,
                'code' => 'DEF',
                'stockByWarehouse' => 5,
                'otherField' => 'value'
            )
        );

        $filtered = Arrays::getColumns($rows, [
            'id',
            'code',
            'stockByWarehouse',
        ]);

        dd($filtered);
    }

    function image()
    {
        $str = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAABQCAMAAADm3o3WAAACT1BMVEVHcEwrcDoqbzkrbzorcDorbzorcDorcDorcDorbzkqbzorcDoqbjkrbzoqcDkrbzorcDorcDorcDorcDorbzkrcDorbzorcDkrcDorcDorcDkrbzorcDorbzorbzorcDoqcDkrbzorcDoqbzkrcDoqbzorbzorcDorcDoqbzorbzoqcDkqbzkqbzkqcDkrcDkrcDkqcDkqcDoqbzkqcDkrcDoqbzorcDorbzoqcDkqbzkqbzkrcDorbzoqbzkrcDorcDorcDoqbzkqcDorbzorbzorbzorbzorbzorbzorbzkrcDoqcDkrbzorbzoqcDoqbzkqbzoqcDorbzoqcDkrbzoqbzoqcDorbzkqbzkqcDkrbzoqcDkrbzkqbzkrbzoqcDkrcDorbzorbzorcDoqbzkrcDorcDkrcDorcDoqbzkrbzorcDkpbzkrbzorcDorcDkrbzorcDkrcDkrbzorcDoqbzoqcDkqbzoqbzkrcDorbzkqbzktcTwrbzkrcDoqcDorcDkrbzkrbzoqbzkqbzkqbTkqcDoqbzkqcDkrbzkrcDorbzkrcDorcDoqbjkrbzorcDkqbzkqbzkrcDorcDoqbzkrcDorbzkrbzorbzorbzkqczkrbzoqcDkrbzorbzorbzkrcDoqbzkqbzkrcDoqbzkrcDorcDoqbjkrbzorbjkrbzkrbzorbzoqbzkqbzoqcDkqbzkrcDorbzoqbzkqbzkrcDoqbzorbzorbzkrcDorcDorbzorcDorbzkqbzkqbzorcDkqcDoqcDm9DiJjAAAAvXRSTlMA/mP+YwT6tTAwPTIK+gq1dt3K+yfi3QFpfFf7wf3BVf5VeRosUsr1/Wl8E2/dIwVeIIZ2SFmBNDJjMv4U4yiic64HRj6m7zHXzaTbxUEZKU8SiecVCJFbvGcevVhW6+W59/R38DZUagzhqsfDCfHP3x29RPbo0pYYSwscYALQ8y7Vm9mgDwa4+uus8t9K/At1ejjomJ0xDZOoj8ADKopcELD5TVBsKyLtFzMDf5BxyOCOVWWNZbOzZAuDOybQZgVFAAAL1klEQVR4nO1ah3tUVRZ/w4RACEk2ZA1hA0KAUIRQotSg9N6RKiUsVZooHQIGBEFkERRBsbAWLKtYVt2+59z7yh+23znn3vvem5nMzO63n/t9+3nJvHnz3i2/e/o5F8/7pf2/tVdHto7fPXzO+X6Tr/7sa9+a3G/KoIWHTnYAYveSnW+PfzhtxIZyBva+1b+2tn///rX9a/mbW238q5baud7FJ5l1c/eyA4CASB+gb4Qhl+7OWfBqTs+uyV2T5030vA3z7JOzffrU9eFWV1fXh//xd5+6On7Gj65tKrb8Vz89fgNlXV5bACBgBDs2t3+Y7Dpw6Oy5mzbdv7DhwRr7aD8iaIUIChCAdmGG072SDQE86nH1C8f/fBIQfJrB7F/zFD4PBHx62d5E93fOv/tBU/beh8/OsU8qsjxExqKitRVgwL8VPwMFmV/1sPwHK3eO4UG+Ib8vZAhkE1qhD/DJwWlfuxE/tHs/vj/pH3eHXzAPemVky7Rh8IHxMyF8NKh0EGSeLLz+xdM11CsSoiGAAh0SZkNHH3gTOHpdlx2y6YHX/nDje61/n+gAgGMffYVuRsHB0xWmwNWmenTopT9zEHwhnZ2SHr283Qya9Edv48i31509beW6IiO9NO+ZbuinQrs6E7gggOfmiqAhhKh8QeBb+vPYgKejOx+6p8qKZyZ4/zy3fu361QMdAJYewz3+FwAEDF8RTQExyBYAcG8Uk0gjQkTrRWgl2GdeCGUiZXUT53blT0IsyJoeKRWSISJRgIVYMP+AURDb3Re54Uf2sULXAGHFMwUBdNj1QGvaPKD7Y47Qdz4Fti+ycsdbpu9QuKC7jSGC0KqV4MzopZMLAciY7bqdKzSr++5RHgVadwHRXEQdrQ0knn/5cNohRAgc5RmbkARW/CEfAAuhNT4WCpBYBKJCJB25AFa+QEwHTcaLpNYKO/oH3vK8VVtlBjBbYaOiIpLQunz3QHbAKBDf0LaZmGFkWQmQw4LVbQpCKzqY9AHLp9H7WZtjYbI9SMNDwHUFWSCG2zoREBmWi2BIAxh3TDQ1sBaEZ4gQg0sjTY+DSqgJKhKZBM0dw5p9eSzoAIgMUsXmHMxgDTH4lC/4ie1uINZXWSEDxMtOyDYsdNoQQOAMLaLuzPWPvbJmKSsyNS9/N4DaqFF0GTCK7yYlRtzcg87a+sAyiKhCgBUJIZ94xRcOKrqymWJR1PBKDoAZWeU8OHEfDkzIY1OqjVsBaCycVRKh3taXUv1OEwsDo07WXADinpW5FACM9T5A/L4EgC2GUhqduWbybu7yBib7DdxoPYlFaszawVQ3ZoGLA+jyWEF75drax6hjIILlZAdx5/r0+p7ntY9B8TEEQUfiKTSeeCrVa0aHgRegUOuxXxcFMMeFK4hiB9gSzh1XoO+bDWJUtFEmzd3VxgvJThXDVGBURmhVHMDzS1lDURt/D8DTLpxVsPfwEwk3YwxMAItGJvuIHTCmhGxWcQAto412JcQK8e7aHrqP38XWNLT8lYXG5wLgp2xRA8CniwK4QlZCO9vLg/CV53vs/9kJG5LQOERN1utQMshmABJ3SFxQlAITjjrbi6F1WhuPFBnx+z0Je61JHDBqTvKgIoMJ50ssKKYFx19I6BP/ab2ueP6xbYdhsXJxQuZ84j0bIqexPvptI4rMNtwIqxgYptjUiUX6U9t+x8BVvm+2OTtNAVldovkIn933Vr+ctmC96fz8fdRmG2zhEXB4rvbnt0+vQwTWNzMJlrwTv+2VJa8mBltgvNHc3NDc0Nzc0NDQ3EDfzTXWFcw6JiBlJvJic3paNdmO34kyjsyIoer8PAmAHvmxXXXBkWk+Qnab6fzSZe4WSbwBWN76nrfye8ggKmVj9+sLEgAyiL6vMA6LXJYh7jZANeY3pvPnv409NKA6UVHe+p7Xr5G44KzHrkSuSVqgwUbEilcUixDHNA7ASlAWAOUf35W7vue9h5jwednbSQBgnRGHb0CWmcIYjXYMDrEA9rG50HauO2+NeKmwDU61d7pWfXjMikDAnqE9AcDkBRwy+QDaB4OItT2kOwdgimWiiSJPnFryeonSged53y4/dZL9nAIxBqgSUQlFxdpwPrApvgKzBAdrMCYBwFLFDMCjpcyA561zri60upAwBKwFrIRaonHjmEKTpdG9o8B2zoDQZn8Ucb5fmgJvcsynTMKMGClYlmIBr0N0MJmAtbQCAUE5APOdsgTWgD9eGsAgNIGjUTJASFDACqFVPWV7Gb0gmXEsWDwkNhLA2SsOLg3gCZstapetv5sEYMsQ3Cly4mdMEsmHo8CEO45A1nuUAWBQYM2cqdpgNDV+OyNjth/aqMS4Dl+iZBIMB2DEUZlAO3sA5QCIEYuMQeX++C1VSHxZifcWahMd6Dg9HjbFdF67k/TVJv9IFaByWOCMlyJpDDQuupkAkPXjmFkwJvksWmOd0a1lLtC2rCoHQBi6ooHoQudXSQqEBp6kaLBjfOvQoS0tLS1DW34Y2jKU/ra5lGO3EyOjDWUBAKM2PhghePFMEoDynUgH6MOLq4rMNf8GSOkLzHTlqOETcQ7Fq2i8lnjL6bmS9DgkmEWD0tWXTDFJSQkJy7MD1NEXBY/IHSTDYlHDOBMPigK4Ope6hTYzU+VSIA77qWWTtVNTpLKmJyyRF4x/IzQyYJSkLAqEru7EAncqGUabxMSm82FYPC+42CnmWaOWrLosO8Dhk0alBfnw5NuKjK2saWJPydxwrjgqsV66TAoosDEcEa3mXhqAK1CVkxt6a2xxNhTjUZYMRLYGg0GA+HbKg7MWxPWRkgBqB0itRbwWBuWwwESRPmf1OPpm6q1UyThQkXCrBABvSlwWIwf+/tfFu1M84LvYgj6v5xQoMqZUbRyg31bMEHme99FWrsHKYQbC4ZLre48CEw8y0brT9QlvRhZSEoKLHv04dmzT2KYmvjQ1NY0du3//4sSIs93JGP7GlL6Lq6unV0+fPrO6unpmdfX0mdUzq6dXmzazeuRTS10oT5+FOfBMoTJRyoCEUAhhApiaHHI6Dl8AsWH58sbGxnpubY2NbY31jfWNdKmvb2trbGy8vMc5N5rv+nM5ACqyXPJ3pxMuN7EXzhlTmntmuQke0KokuPHaZS6mlk9cD8XNphxrDKADzYGFYwKEEpvFypFTK54/mvu4XSULYRCRRNu0htJ34BqtIeyVebkAemXiCg26mqbsHOz9sJxi9W60DilRAmLLkPB71mDYAIbuH/8iT0RJBgKbDUjtlc+uIM4TC5wZfcM+UdvCBrgTrriCL7TRFiNn58298mp55Iz8VInEuBo0BTCeKpt7arbhvlNeqSoDJZ9GKCRakVK2i7J9jgVenp5HgYoOcAyl1E1Zhmg/Dn3yT0yOLDOapa1+iSYHGCu0IAnQtwcPVM/OQzAjEx+10l0UmBmiOPzPp4Dn9X4vY9kfxiIYyHEjFRCtNDl95oAAllzM4UKFqxPGQagLny2KAgA878lu7mLyPd+dHYBJs+MU1xXDAwV4tCqHBRlbpQ4kR5bsmLVSacPnwienn152chPI+aDPcqPMZrSVKMXwNPgRzbs0VSjlYrWK4hgnpUg2Serh7Hjc7F1Eo4TG2cHKRnhU6LBZPpgzkQGpQqDUCWMD7Myy42L+mVHczl4bZvmcyCcAdKzCCBmxbPHWUgj+5A5+AjQHrc4OOOlMB1HJ9tG+zRBvXqqHOmUf/c7PvqUKocyoFWnEodXxDFsqGyorKytrKvlaI390ea1SPjWVlQ1DBvUEwPPWzr8/WsyMOfO2hVlWkGznoGc874GrBpv/knA4PhUZt7hvVd++VX2rqsyHftpPFX9XVS0ucOAYt4HPbTn24h4OqZ13YCCLln/cynXsc8scd7S8W1FVusD5b7URa9r/cvjo091jSNywck/90sEft990pxgjBqM5uuP8HxtOHiwR9/wHbd6ZCXufaj2/pemv29Ycn/bF+lToufp3VrY7t87+5vaavSP/9l8HULwt+AS/PPzu7fMLJs868sHPvLa01knTzv1PFv6lFWv/Apps0DlrJU8+AAAAAElFTkSuQmCC';

        MediaType::renderImage($str);
    }

    /*
        Intentar hacer auto quoteValue() para campos tipo VARCHAR, JSON etc de encontrar comillas dobles
    */
    function insert()
    {
        DB::getConnection('woo3');

        $rows = require 'D:\www\woo3\wp-content\plugins\mutawp_admin\etc\metadata.php';

        foreach ($rows as $link_id => $metadata) {
            $metadata = json_encode($metadata);
            $metadata = DB::quoteValue($metadata); // necesario porque contiene comillas dobles

            $sql      = "INSERT INTO `wp_link2product_metadata` (`link_id`, `metadata`) VALUES ('$link_id', $metadata);";

            dd(DB::statement($sql), $sql);
        }
    }

    function test_rest()
    {
        nap(0.99);
    }

    function test_argsv()
    {
        dd($_GET);
    }

    function obf_muta()
    {
        $ori = 'D:\www\woo2\wp-content\plugins\mutawp';
        $dst = 'D:\www\woo2\wp-content\plugins\mutawp.obfuscated';
        $excluded = <<<FILES
        mutawp.php
        vendor
        assets
        *.bak
        *.ph_
        *.jpeg
        *.jpg
        *.png
        *.gif
        logs
        README.md   
        config.php
        routes.php
        constants.php
        Model.php
        DB.php    
        FILES;

        Obfuscator::obfuscate($ori, $dst, null, $excluded, [
            "--obfuscate-function-name",
            "--obfuscate-class_constant-name",
            "--obfuscate-label-name"
        ]);
    }

    function obf_muta_admin()
    {
        $ori = 'D:\www\woo3\wp-content\plugins\mutawp';
        $dst = 'D:\www\woo3\wp-content\plugins\mutawp.obfuscated';
        $excluded = <<<FILES
        mutawp.php
        vendor
        assets
        *.bak
        *.ph_
        *.jpeg
        *.jpg
        *.png
        *.gif
        logs
        README.md   
        config.php
        routes.php
        constants.php
        Model.php
        DB.php    
        FILES;

        Obfuscator::obfuscate($ori, $dst, null, $excluded, [
            "--obfuscate-function-name",
            "--obfuscate-class_constant-name",
            "--obfuscate-label-name"
        ]);
    }

    function test_move()
    {
        $ori = 'D:\Downloads\wordpress-6.0-beta1\wordpress';
        $dst = 'D:\Desktop\EN LANG\wp';

        Files::move($ori, $dst, ['license.txt']);
    }

    function csv_debug_juanita()
    {
        $path = 'D:\Desktop\PRECIOS MAYOREO - JUANITA\CSVs\wc-product-export-9-5-2023-1683646857175.csv';

        $csv    = Files::getCSV($path);
        $rows   = $csv['rows'];
        $header = $csv['header'];

        // usort($rows, function ($a, $b) {
        //     return $a['Código Isp'] <=> $b['Código Isp'];
        // });

        // $tipos = [];
        // foreach ($rows as $row){
        //     $tipos[] = $row['Tipo'];
        // }

        // $tipos = array_unique($tipos);
        // print_array($tipos, 'TIPOS');

        // print_array(array_column($rows,'Nombre del atributo 1'), '', '. ');
        // print_array(array_column($rows,'Valor(es) del atributo 1'), '', '. ');
        // print_array(array_column($rows,'Imágenes'), '', '. ');
        // exit;

        // $rows = Arrays::getColumns($rows, [
        //     'ID',
        //     'Tipo',
        //     'SKU'
        // ]);

        // print_array($rows);

        foreach ($rows as $row) {
            if ($row['Tipo'] == 'variable' || $row['Tipo'] == 'variation') {
                dd($row, null, false);
            }
        }


        $total = count($rows);
        dd($total, 'TOTAL');
    }

    /*
        Encapsular

        Depende de FontAwesome
    */
    function test_notices()
    {
        render('
        <style>
        .message-box {
            width: 440px;
            border-radius: 6px;
            margin: 20px auto;
            padding: auto 0;
            position: relative;
          }
          .message-box i {
            vertical-align: middle;
            padding: 20px;
          }
          .message-box i.exit-button {
            float: right;
            opacity: 0.4;
          }
          .message-box i.exit-button:hover {
            opacity: 0.8;
          }
          
          .message-text {
            vertical-align: middle;
          }
          
          .message-box-info {
            background-color: #CDE8F6;
            border: #2697d1 2px solid;
            color: #447EAF;
          }
          
          .message-box-warn {
            background-color: #F8F4D5;
            border: #e9dd7e 2px solid;
            color: #96722E;
          }
          
          .message-box-error {
            background-color: #ECC8C5;
            border: #d37f78 2px solid;
            color: #B83C37;
          }
          
          .message-box-success {
            background-color: #DDF3D5;
            border: #9ddc86 2px solid;
            color: #597151;
          }
          
        </style>
        <div class="message-box message-box-info">
            <i class="fa fas fa-info-circle fa-2x"></i>
            <span class="message-text"><strong>Info:</strong> User pending action</span>
            <i class="fa fas fa-times fa-2x exit-button "></i>
        </div>
        <div class="message-box message-box-warn">
            <i class="fa fas fa-warning fa-2x"></i>
            <span class="message-text"><strong>Warning:</strong> User has to be admin</span>
            <i class="fa fas fa-times fa-2x exit-button "></i>
        </div>
        <div class="message-box message-box-error">
            <i class="fa fas fa-ban fa-2x"></i>
            <span class="message-text"><strong>Error:</strong> Internal Server Error</span>
            <i class="fa fas fa-times fa-2x exit-button "></i>
        </div>
        <div class="message-box message-box-success">
            <i class="fa fas fa-check fa-2x"></i>
            <span class="message-text"><strong>Success:</strong> Updated member status</span>
            <i class="fa fas fa-times fa-2x exit-button "></i>
        </div>');
    }

    function test_wpcron()
    {
        set_time_limit(-1);

        for ($i = 0; $i < 1000; $i++) {
            file_get_contents('http://woo6.lan/');

            dd(
                file_get_contents('D:\www\woo6\wp-content\plugins\wp_runa\logs\log.txt'),
                "Web page requested | iteracion $i - now: " . at()
            );

            sleep(45);
        }
    }

    /*  
        https://dev.cloudtrades.lat/api/register/

        post api/register/

        Content-Type: application/json

        {
        "name":"jeison",
        "username": "@jeison_cisneros_user",
        "email":"jecsnroxf@gmail.com",
        "country": "Colombia",
        "number_telephone": "3222551222",
        "password_user": "cb7327ddd",
        "password_user_2":"cb7327ddd",
        "key_word": "hola_mundo"
        }
    */
    function fer_register()
    {
        $url = 'https://dev.cloudtrades.lat/api/register';

        $cli = ApiClient::instance();

        $body = json_encode('
        {
            "name":"jeison",
            "username": "@jeison_cisneros_user",
            "email":"jecsnroxf@gmail.com",
            "country": "Colombia",
            "number_telephone": "3222551222",
            "password_user": "cb7327ddd",
            "password_user_2":"cb7327ddd",
            "key_word": "hola_mundo"
        }');

        $res = $cli
            ->setHeaders(
                [
                    "Content-type"  => "application/json"
                ]
            )
            ->withoutStrictSSL()
            ->setBody($body)
            ->disableSSL()
            ->post($url)
            ->getResponse();

        dd($cli->error(),  'ERROR');
        dd($cli->status(), 'STATUS');

        $res = $cli->data();

        dd($res, 'DATA');
    }


    /*
        https://dev.cloudtrades.lat/api/login/
    */
    function fer_login()
    {
        $url = 'https://dev.cloudtrades.lat/api/login/';

        $body = [
            "username" => "jeison",
            "password" => "cb7327ddd"
        ];

        $cli = ApiClient::instance();

        $res = $cli
            ->setHeaders(
                [
                    "Content-type"  => "application/json"
                ]
            )
            ->withoutStrictSSL()
            ->setBody($body)
            ->disableSSL()
            ->post($url)
            ->getResponse();

        dd($cli->error(),  'ERROR');
        dd($cli->status(), 'STATUS');

        $res = $cli->data();

        dd($res, 'DATA');
    }

    function fer_login_2x()
    {
        $this->fer_login();
        $this->fer_login();
    }

    function check_content()
    {
        dd(
            consume_api('https://www.deltron.com.pe/login.php')
        );
    }

    /*
        No depende de ningun framework como Boostrap

        Convertir eventualmente en componente 
    */
    function cards_with_overlays()
    {
        ob_start();
    ?>

        <style>
            /* 
                Botones redondeados
            */

            .sr-rounded-pill-button {
                display: inline-block;
                padding: 14px 20px;
                border-radius: 20px;
                text-align: center;
                text-decoration: none;
                font-size: 13px;
                cursor: pointer;
            }

            .sr-btn-blue {
                background-color: #5F79F9;
                color: white;
            }

            .sr-btn-green {
                background-color: #0DA078;
                color: white;
            }

            .sr-btn-red {
                background-color: #ff2626;
                color: white;
            }

            .sr-btn-black {
                background-color: #000000;
                color: white;
            }

            .sr-btn-disabled {
                background-color: #C7C7C7;
                color: white;
            }

            .sr-rounded-pill-button:hover,
            .sr-rounded-pill-button:focus,
            .sr-rounded-pill-button:active {
                color: white !important;
            }

            .sr-btn-red.sr-rounded-pill-button:hover,
            .sr-btn-red.sr-rounded-pill-button:focus,
            .sr-btn-red.sr-rounded-pill-button:active {
                background-color: #f40000;
            }

            /*
                Extra: pills del mismo width
            */

            .sr-rounded-pill-button {
                width: 120px;
            }

            /*
                Flip cards
            */

            .sr-card-container {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 20px;
                justify-content: center;
                align-items: center;
                /* Asegurar alineación vertical */
                perspective: 500px;
            }

            .sr-card {
                position: relative;
                width: 100%;
                height: 300px;
            }

            .sr-card-content {
                position: absolute;
                width: 100%;
                height: 100%;
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
                transform-style: preserve-3d;
                transition: transform 0.5s;
            }

            /*
                Animacion
            */
            .sr-card:hover .sr-card-content {
                transform: rotateY(180deg);
            }

            .sr-card-front,
            .sr-card-back {
                position: absolute;
                width: 100%;
                height: 100%;
                border-radius: 5px;
                backface-visibility: hidden;
                overflow: hidden;
                /* Ensure the overlay doesn't overflow */
            }

            /* White text on the front side */
            .sr-card-front-text {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                color: white;
                font-size: 24px;
                line-height: 150%;
                text-align: center;
            }

            .sr-card-back {
                text-align: center;
                /* horizontal alignment */
                background: #03446A;
                color: white;
                transform: rotateY(180deg);
                font-size: 30px;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            /*
                Extras

                - Coloco imagen de fondo en la cara frontal
                - Creo overlay sobre la imagen para aumentar contraste 
            */

            .sr-card-front {
                background-image: var(--img-url);
                background-repeat: no-repeat;
                background-position: center center;
                background-size: contain;
                /* Cambiado a 'contain' para ajustar la imagen sin recortes */
            }

            /* Overlay for the front side */
            .sr-card-front::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.6);
                /* Overlay color and opacity */
            }

            /*
                Banda diagnal
            */

            .sr-card-ribbon {
                position: absolute;
                right: -5px;
                top: -5px;
                z-index: 1;
                overflow: hidden;
                width: 75px;
                height: 75px;
                text-align: right;
            }

            .sr-card-ribbon span {
                font-size: 10px;
                color: #fff;
                text-transform: uppercase;
                text-align: center;
                font-weight: bold;
                line-height: 20px;
                transform: rotate(45deg);
                width: 100px;
                display: block;
                background: #79A70A;
                background: linear-gradient(#9BC90D 0%, #79A70A 100%);
                box-shadow: 0 3px 10px -5px rgba(0, 0, 0, 1);
                position: absolute;
                top: 19px;
                right: -21px;
            }

            .sr-card-ribbon span::before {
                content: '';
                position: absolute;
                left: 0px;
                top: 100%;
                z-index: -1;
                border-left: 3px solid #79A70A;
                border-right: 3px solid transparent;
                border-bottom: 3px solid transparent;
                border-top: 3px solid #79A70A;
            }

            .sr-card-ribbon span::after {
                content: '';
                position: absolute;
                right: 0%;
                top: 100%;
                z-index: -1;
                border-right: 3px solid #79A70A;
                border-left: 3px solid transparent;
                border-bottom: 3px solid transparent;
                border-top: 3px solid #79A70A;
            }
        </style>

        <div class="sr-card-container">
            <?php foreach (range(0, 10) as $ix) : ?>
                <div class="sr-card">
                    <div class="sr-card-content">
                        <div class="sr-card-front">
                            <div class="sr-card-ribbon"><span>Update</span></div>
                            <div class="sr-card-front-text">Front</div>
                        </div>
                        <div class="sr-card-back">
                            <a href="#" class="btn2install sr-rounded-pill-button sr-btn-disabled" data-pid="450" aria-label="Actualizar o instalar" data-name="Asset CleanUp Pro v1.2.3.3" role="button">Actualizar</a>
                            <a href="https://mutawp.com/descargar/asset-cleanup-pro/" class="sr-rounded-pill-button sr-btn-black">Saber más</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php
        $content = ob_get_clean();
        render($content);
    }

    function test_asset()
    {
        echo '<img src="' . asset('img/ai_logo.png') . '">';
    }

    function test_normaliza_ruta()
    {
        $ruta = '..zz/../xx/yy';
        $rutaNormalizada = Files::normalize($ruta, '/');

        dd($rutaNormalizada);
    }

    function test_php_marker_replacer()
    {
        $html = '<?php echo "Hola mundo" ?>';
        // $html  = XML::replacePHPmarkers($html);

        // dd($html);

        // $html  = XML::replacePHPmarkersBack($html);

        // dd($html);
    }

    function test_parse_text_nodes()
    {
        $html = '</li>
        <li class="nav-item">
            <a class="nav-link d-inline-flex align-items-center" href="https://shuffle.dev/components/bootstrap?utm_source=bootstrap&amp;utm_medium=class-list" rel="noopener" target="_blank" style="color:red">
              
                Bootstrap Components
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link d-inline-flex align-items-center" href="https://shuffle.dev/bootstrap/templates?utm_source=bootstrap&amp;utm_medium=class-list" rel="noopener" target="_blank"><p>XXX</p></a>
        </li>
        
        <ul class="list-step" >
            <li   class="active">
                <a aria-controls="step-01"  >Rack Dimensions</a>
            </li>
            <li>
                <a aria-controls="step-02"  >Decking Options</a>
            </li>
            <!---->
            <li>
                <a aria-controls="step-03"  >Space Availability</a>
            </li>
            <!---->
            <!---->
            <li   >
                <a aria-controls="step-04"  >Aisle Dimensions</a>
            </li>
            <!---->
            <!---->
            <!---->
        </ul>
        ';

        $nodes = XML::getTextFromNodes($html);

        dd($nodes);
    }

    function test_xml_node_extractor(){
        $html = '
        <html>
            <body>
                <div class="content">Content 1</div>
                <div class="content">Content 2</div>
                <div class="footer">Footer</div>
            </body>
        </html>';

        dd(
            XML::extractNodes($html, "//div[@class='content']")
        );

        /*
            Resultado:

            Array
            (
                [0] => <div class="content">Content 1</div>
                [1] => <div class="content">Content 2</div>
            )
        */
    }   

    function test_replacer()
    {
        $code = '</li>
        <li class="nav-item">
            <a class="nav-link d-inline-flex align-items-center" href="https://shuffle.dev/components/bootstrap?utm_source=bootstrap&amp;utm_medium=class-list" rel="noopener" target="_blank" style="color:red">
              
                Bootstrap Components
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link d-inline-flex align-items-center" href="https://shuffle.dev/bootstrap/templates?utm_source=bootstrap&amp;utm_medium=class-list" rel="noopener" target="_blank"><p>XXX</p></a>
        </li>
        
        <?php echo "Hola mundo" ?>

        <ul class="list-step" >
            <li   class="active">
                <a aria-controls="step-01"  >Rack Dimensions</a>
            </li>
            <li>
                <a aria-controls="step-02"  >Decking Options</a>
            </li>
            <!---->
            <li>
                <a aria-controls="step-03"  >Space Availability</a>
            </li>
            <!---->
            <!---->
            <li   >
                <a aria-controls="step-04"  >Aisle Dimensions</a>
            </li>
            <!---->
            <!---->
            <!---->
        </ul>
        ';

        $code = HTML::insertTranslator($code);

        dd($code);
    }

    function test_replacer_2()
    {
        $code = file_get_contents('D:\www\simplerest\app\shortcodes\rack_quoter\views\racks_copy-ok.php');

        $code = HTML::insertTranslator($code);

        file_put_contents('D:\www\simplerest\app\shortcodes\rack_quoter\views\racks.php', $code);
    }

    function test_feet_inch_conversion()
    {
        dd(
            Messurements::toInches(5, 6)
        );
    }

    function test_mail_wp_remote()
    {
        $email   = 'boctulus@gmail.com';
        $subject = 'Prueba test ' . rand(9999, 999999);
        $content = 'Hola! 

        Este es un contenido de <b>prueba en negrita</b> #' . rand(9999, 999999) . '

        Un saludo!';

        $logo    = 'assets/img/logo.png';

        $url     = 'https://cafesguilis.com/api/wp_mail/send';


        $content  = EmailTemplate::formatContentWithHeader($content);

        $logo_url = "https://cafesguilis.com/wp-content/plugins/sales-agent-coupons-1/$logo";


        $body     = get_view('email/simple_with_logo', compact('email', 'subject', 'content', 'logo_url'));


        MailFromRemoteWP::setRemote($url);

        $res = MailFromRemoteWP::send($email, $subject, $body);

        // resultado
        dd($res);
    }

    /////////////////////

    /*
        Agregar prefijo a CREATE TABLE
    */
    function test()
    {
        // Ejemplo de uso
        dd(
            Model::addPrefix("CREATE TABLE Orders (
                OrderID int NOT NULL,
                OrderNumber int NOT NULL,
                PersonID int,
                PRIMARY KEY (OrderID),
                FOREIGN KEY (PersonID) REFERENCES Persons(PersonID)
            );")
        );

        dd(
            Model::addPrefix("CREATE TABLE IF NOT EXISTS migrations")
        );

        dd(
            Model::addPrefix("UPDATE `Customers`
            SET ContactName='Juan'
            WHERE Country='Mexico'")
        );

        dd(
            Model::addPrefix("
            INSERT INTO `sp_permissions` (`id`, `name`) VALUES
            (1, 'read_all'),
            (2, 'read_all_folders'),
            (3, 'read_all_trashcan'),
            (4, 'write_all'),
            (5, 'write_all_folders'),
            (6, 'write_all_trashcan'),
            (7, 'write_all_collections'),
            (8, 'fill_all'),
            (9, 'grant'),
            (10, 'impersonate'),
            (11, 'lock'),
            (12, 'transfer');")
        );

        dd(
            Model::addPrefix("DELETE FROM `table_name` WHERE condition;")
        );

        dd(
            Model::addPrefix("SELECT CustomerName, City FROM `Customers`;")
        );

        dd(
            Model::addPrefix("ALTER TABLE `migrations`
            MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;")
        );
    }

    function test_error_log()
    {
        error_log('HERE');
        error_log("You messed up!\r\n", 3, LOGS_PATH . "my-errors.log");
    }

    function test_memory_solution()
    {
        System::registerStats(true, false);

        // register_shutdown_function(function(){
        //    dd("FINNNNNNNNN");
        // });
    }

    function is_alive()
    {
        $pid = 4804;

        dd(System::isProcessAlive($pid), 'Running?');
    }

    function some()
    {
        for ($i = 1; $i <= 10; $i++) {
            Logger::dd($i, "Current");
            sleep(1);
        }
    }

    /*
        Si se desea ser notificado cuando el job a terminado con éxito o un fallo,
        pueden hacerse:

        command && command-after-only-if-success &
        command || command-after-only-if-fail &

        https://superuser.com/a/345455/402377
    */
    function test_background_process()
    {
        $file_path  = System::getPHP();
        $dir        = ROOT_PATH;
        $args       = "{$dir}com dumb some";

        dd("$file_path $args", 'CMD');

        // $pid = System::execInBackgroundWindows($file_path, $dir, $args); // ok
        $pid = System::runInBackground($file_path, $dir, $args); // ok

        dd($pid, 'pid');
        sleep(1);
        dd(System::isProcessAlive($pid), "Running (PID=$pid) ?");
    }

    function test_background_process_2()
    {
        $pid = bg_com("dumb some");

        dd($pid, 'pid');
        sleep(1);
        dd(System::isProcessAlive($pid), "Running (PID=$pid) ?");
    }

    /*
        >>> Ver porque los transientes con DB driver requieren de tiempo de expiracion !!!
	
	    >>> Poder exigir un solo proceso del mismo tipo dado un idenfificador => importante para job queues
    */

    function test_background_process_3()
    {
        $pid = bg_com("bzz_import do_process");
        dd($pid, 'pid');
    }

    function test_background_process_4()
    {
        $pid = bg_com("dumb _4");
        dd($pid, 'pid');
    }

    function _4(){
        VarDump::log();
   
        try {
            for ($i=0; $i<5; $i++){  
                dd($i, 'i');
                sleep(1);
            }
        } catch (\Exception $e){
            Logger::logError($e->getMessage());
        }
    }

    /*
        CronJobs
    */

    function cronjob_manager_start()
    {
        CronJobMananger::start();
    }

    function cronjob_manager_stop()
    {
        CronJobMananger::stop();
    }

    function is_cron_running()
    {
        dd(CronJobMananger::isRunning('other.php'));
    }

    /*
        Jobs
    */

    function test_dispatch_q1()
    {
        $queue = new JobQueue("q1");
        $queue->dispatch(\simplerest\background\tasks\UnaTask::class);
        $queue->dispatch(\simplerest\background\tasks\UnaTask::class);
        $queue->dispatch(\simplerest\background\tasks\UnaTask::class);
        $queue->dispatch(\simplerest\background\tasks\UnaTask::class);
        $queue->dispatch(\simplerest\background\tasks\UnaTask::class);
        $queue->dispatch(\simplerest\background\tasks\UnaTask::class);
        $queue->dispatch(\simplerest\background\tasks\UnaTask::class);
        $queue->dispatch(\simplerest\background\tasks\UnaTask::class);
        $queue->dispatch(\simplerest\background\tasks\UnaTask::class);
        $queue->dispatch(\simplerest\background\tasks\UnaTask::class);
        $queue->dispatch(\simplerest\background\tasks\UnaTask::class);
        $queue->dispatch(\simplerest\background\tasks\UnaTask::class);
        $queue->dispatch(\simplerest\background\tasks\UnaTask::class);
        $queue->dispatch(\simplerest\background\tasks\UnaTask::class);
        $queue->dispatch(\simplerest\background\tasks\UnaTask::class);
        $queue->dispatch(\simplerest\background\tasks\UnaTask::class);
        $queue->dispatch(\simplerest\background\tasks\UnaTask::class);
        $queue->dispatch(\simplerest\background\tasks\UnaTask::class);
        $queue->dispatch(\simplerest\background\tasks\UnaTask::class);
        $queue->dispatch(\simplerest\background\tasks\UnaTask::class);
        $queue->dispatch(\simplerest\background\tasks\UnaTask::class);
        $queue->dispatch(\simplerest\background\tasks\UnaTask::class);
        $queue->dispatch(\simplerest\background\tasks\UnaTask::class);
        $queue->dispatch(\simplerest\background\tasks\UnaTask::class);

        $queue->dispatch(\simplerest\background\tasks\ZTask::class);

        // $queue->dispatch(\simplerest\background\tasks\OtraTask::class);
        // $queue->dispatch(\simplerest\background\tasks\OtraTask::class);
    }

    function test_worker_factory_q1()
    {
        $queue = new JobQueue("q1");
        $queue->addWorkers(1);
    }

    function test_worker_stop_q1()
    {
        JobQueue::stop("q1");
    }


    function test_worker_factory()
    {
        $queue = new JobQueue();
        $queue->addWorkers(3);
    }

    function test_worker_stop()
    {
        JobQueue::stop();
    }

    function test_worker_factory_q2()
    {
        $queue = new JobQueue("q2");
        $queue->addWorkers(30);
    }

    function test_dispatch_q2()
    {
        $queue = new JobQueue("q2");
        $queue->dispatch(\simplerest\background\tasks\DosTask::class, '1 - Juan', 39);
        $queue->dispatch(\simplerest\background\tasks\DosTask::class, '2 - Maria', 21);
        $queue->dispatch(\simplerest\background\tasks\DosTask::class, '3 - Felipito', 10);
    }



    /*
        Hacer como comando MAKE     ------------------------ revisar !!!

        php com make dark --ori={path} --dest={path}
    */
    function obf()
    {
        $ori = 'D:\\www\\woo4\\wp-content\\plugins\\sales-agent-coupons-v2\\';
        $dst = 'D:\\www\\woo4\\wp-content\\plugins\\sales-agent-coupons-obfuscated\\';
        $excluded = <<<FILES
        .git
        .env
        composer.json
        assets
        logs
        etc
        README.md
        index.php
        main.php
        config   
        config.php
        FILES;

        Obfuscator::obfuscate($ori, $dst, null, $excluded, [
            "--obfuscate-function-name",
            "--obfuscate-class_constant-name",
            "--obfuscate-label-name"
        ]);
    }


    function just_asdf()
    {
        $urls = <<<URLS
        https://terrafunds.ca/wp-includes/js/jquery/jquery-migrate.min.js?ver=3.4.1
        https://terrafunds.ca/wp-content/plugins/terrafunds-tax-calculator/js/js/numeral.min.js?ver=6.3.2
        https://terrafunds.ca/wp-content/cache/min/1/wp-content/plugins/terrafunds-tax-calculator/js/jquery-calx-2.2.6.js?ver=1697748178
        https://terrafunds.ca/wp-content/cache/min/1/wp-content/plugins/terrafunds-tax-calculator/js/tf_tc_script.js?ver=1697748178
        https://terrafunds.ca/wp-content/cache/min/1/wp-content/plugins/terrafunds-tax-calculator/js/tf_tc_charts_config.js?ver=1697748178
        https://terrafunds.ca/wp-content/cache/min/1/wp-content/plugins/terrafunds-tax-calculator/js/js/jquery.formatCurrency.js?ver=1697748178
        https://terrafunds.ca/wp-content/plugins/terrafunds-tax-calculator/js/jquery.ui.touch-punch.min.js?ver=6.3.2
        URLS;

        $url_ay = Strings::lines($urls, true, true);

        Files::download($url_ay, SHORTCODES_PATH . "tax_calc/assets/js");
    }


    /*
        Test de shortcode
    */
    function rating_slider()
    {
        $sc = new StarRatingShortcode();

        render($sc->rating_slider());
    }

    /*
        Test de shortcode
    */
    function rating_table()
    {
        $sc = new StarRatingShortcode();

        render($sc->rating_table());
    }

    function test_random_replacer()
    {
        // Ejemplo de uso
        $originalText = "Ho appena ricevuto il mio ordine di scarpe da questo sito e sono davvero soddisfatta! La qualità e il design sono incredibili e il prezzo è stato molto conveniente. Consiglio vivamente questo negozio per chiunque sia alla ricerca di scarpe eleganti e di buona qualità.";

        $modifiedText = ItalianReviews::randomizePhrase($originalText);

        dd("Original String: $originalText");
        dd("Modified String: $modifiedText");
    }

    function test_it_gramamar_anal()
    {
        dd(ItalianGrammarAnalyzer::getGender("I tessuti sono di alta qualità e si vedono e si sentono, molto soddisfatto.")); // m
        dd(ItalianGrammarAnalyzer::getGender("I tessuti sono di alta qualità e si vedono e si sentono, soddisfatto.")); // n -- mal
        exit;

        dd(ItalianGrammarAnalyzer::getGender("La qualità degli abiti è eccellente e ho trovato un bellissimo paio di scarpe per mio marito. Servizio impeccabile."));
        dd(ItalianGrammarAnalyzer::getGender("Oggi sono molto felice")); // n --ok
        dd(ItalianGrammarAnalyzer::getGender("Con questo produtto sono soddisfatta"));  // f  --ok
        dd(ItalianGrammarAnalyzer::getGender("Con questo produtto sono molto soddisfatta")); // f --ok
        dd(ItalianGrammarAnalyzer::getGender("Con questo produtto sono soddisfatto")); // m
    }

    function test_num_gen()
    {
        for ($i = 0; $i < 50; $i++) {
            $result = RandomGenerator::get(['A' => 1, 'B' => 100]);
            dd($result, null, false);
        }
    }

    function test_lflfglfg()
    {
    }

    /*
        Test de shortcode
    */
    function countdown()
    {
        $sc = new CountDownShortcode();

        render($sc->counter());
    }

 
    /*
        Para obtener el API token

        https://github.com/settings/apps

        > Personal access tokens

        Existe un problema y los tokens no estan funcionando (reportado multiples veces)

        Tokens ensayados

        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
        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
        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
    */
    function test_github_commit_list()
    {
        $user  = 'boctulus';
        $repo  = 'simplerest';
        $token = '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';

        $url = "https://api.github.com/repos/$user/$repo/commits";

        $headers = [
            'Authorization' => $token,
            'User-Agent'    => 'Awesome-Octocat-App',
            'Accept'        => 'application/vnd.github+json',
            'X-GitHub-Api-Version' => '2022-11-28'
        ];

        dd(
            consume_api($url, 'GET', null, $headers)
        );
    }

    function test_exec_at()
    {
        dd(System::execAt("dir", "c:\windows"));
    }

    function test_function_parser()
    {
        $code = file_get_contents('D:\www\simplerest\app\core\libs\PHPLexicalAnalyzer.php');

        dd(
            PHPLexicalAnalyzer::getFunctionNames($code)
        );
    }

    function test_github_lib()
    {
        dd(
            GitHub::diff(ROOT_PATH, 'D:\www\apis')
        );
    }

    function test_m()
    {
        // $git_installed = Memoization::memoize('git executable', function() {
        //     return at();
        // });

        // dd($git_installed);

        // sleep(2);

        // dd($git_installed);

        set_cache_driver(FileCache::class);

        // $git_installed = Memoization::memoize('git exists', function() {
        //     return System::inPATH('git') ? 1 : 0;
        // }, 3600 );

        // dd($git_installed);

        $git_log = Memoization::memoize('git log', function () {
            return System::execAtRoot("git log");
        }, 3600);

        dd($git_log);
    }

    function test_set_transient()
    {
        set_cache_driver(InMemoryCache::class);

        set_transient('time', at(), 10);

        $this->test_get_transient();
        sleep(1);
        $this->test_get_transient();
    }

    function test_get_transient()
    {
        set_cache_driver(InMemoryCache::class);

        dd(
            get_transient('time')
        );
    }

    /*
        Con este formato se envia de forma temporal a otra URL
    */
    function ret_307()
    {
        response()->redirect('http://yahoo.es', 307);

        // exit;
        // header('HTTP/1.1 307 Temporary Redirect');
        // header('Location: http://yahoo.com');  // nueva URL
        // exit();
    }

    function asdgtyt()
    {
        dd(
            Strings::slug('lo que EL viento se llevó    de España 2022')
        );
    }

    function ciudades_cl()
    {
        define('ABSPATH', 'D:\\www\\woo5\\');
        define('PLUGINDIR', 'wp-content\\plugins');

        // set_template('templates\adminlte_tpl.php');

        new CiudadesCLShortcode();
    }


    function csv_debug1()
    {
        $path = 'D:\Desktop\SANDRA ES BeKIND\PRODUCTOS\productos.csv';
        // $path = 'D:\Desktop\SANDRA ES BeKIND\PRODUCTOS\wc-products.csv';

        $rows = Files::getCSV($path)['rows'];

        foreach ($rows as $key => $row) {
            dd($row, $key);
            exit;
        }
    }

    function csv_transfom_2()
    {
        $path = 'D:\Desktop\SANDRA ES BeKIND\PRODUCTOS\productos.csv';
        // $path = 'D:\Desktop\SANDRA ES BeKIND\PRODUCTOS\wc-products.csv';

        $rows = Files::getCSV($path, ',', true, true, [
            'id', 'name'
        ])['rows'];

        foreach ($rows as $key => $row) {
            dd($row, $key);
            exit;
        }
    }

    function csv_transfom_3()
    {
        $path = 'D:\Desktop\SANDRA ES BeKIND\PRODUCTOS\productos.csv';

        $rows = Files::getCSV($path, ',', true, true, [
            'SKU' => '__sku__',
            'IVA' => '__iva__',
        ], [
            'nuevo_campo' => 'def_val',
            'nuevo_campo-2' => 'def_val-2'
        ])['rows'];

        foreach ($rows as $key => $row) {
            dd($row, $key);
        }
    }

    function test_arr_key_repl()
    {
        // Ejemplo de uso
        $miArray = [
            [
                'nombre' => 'Pablo',
                'edad' => 99
            ],
            [
                'nombre' => 'Feli',
                'edad' => 12
            ]
        ];

        $mapeoClaves = [
            'nombre' => 'name',
            'edad' => 'age'
        ];

        $arrayTransformado = Arrays::renameKeys($miArray, $mapeoClaves);

        // Mostrar el array transformado
        dd($arrayTransformado);
    }

    /*
        Importante: scope de "use"
    */
    function test_use_scope()
    {
        $x = 5;

        $fn = function () use ($x) {
            $x = $x * 2;
        };

        $fn($x);

        // Imprime 5 y no 10
        dd($x, 'x');
    }

    function test_read_csv()
    {
        System::registerStats(true, false);

        $path = 'D:\Desktop\SANDRA ES BeKIND\PRODUCTOS\productos.csv';

        $rows = Files::getCSV($path)['rows'];

        dd($rows, 'RES');
    }

    /*
        https://github.com/datablist/sample-csv-files
    */
    function test_read_csv_2()
    {
        System::registerStats(true, false);

        $path = 'C:\Users\jayso\OneDrive\Documentos\customers-2000000.csv';

        global $emails_ending_org;
        global $processed;

        Files::processCSV($path, ',', true, function ($row) {
            // Ex.
            global $emails_ending_org, $processed;

            if (Strings::endsWith('.org', $row['Email'])) {
                $emails_ending_org++;
                // dd($row['Email']);
            }

            $processed++;
        });

        // Result
        dd($emails_ending_org, 'EMAILS ENDING IN .ORG');
        dd($processed, 'PROCESSED');
    }


    function test_read_csv_3()
    {
        $archivo = 'D:\www\woo4' . '/wp-content/pekeinventario/articulosweb.txt';

        Files::processCSV($archivo, ';', true, function ($p) {
            dd($p, 'P (por procesar)');
        }, null, 36332, 5);
    }

    function test_parallex()
    {
        $csv_path     = ETC_PATH . 'pekeinventario/articulosweb.txt';
        $min_t_locked = 3;
        $max_t_locked = 5;
        $limit        = 2;

        Sync::$path = $csv_path;

        $px = new Parallex(new Sync(), $min_t_locked, $max_t_locked);

        if ($px->getOffset() == -1) {
            if (is_cli()) {
                dd("COMPLETED LOCKED VIA NEGATIVE OFFSET UNTIL CRON UNLOCK [!]");
            }

            return;
        }

        if (is_cli()) {
            // return;
        }

        /*
            Paso el control al Task Manager
        */

        $px->run($limit);

        dd($px->getState(), 'S');
    }

    function test_nit_co()
    {
        // Ejemplos de uso
        $valid_nits = ['901143974', '9005726197', '900218578', '9009752417', '9009752415'];
        foreach ($valid_nits as $nit) {
            if (NITColombiaValidator::isValid($nit, true)) {
                echo "El NIT $nit ES válido.\n";
            } else {
                echo "El NIT $nit no es válido.\n";
            }
        }
    }

    function get_users()
    {
        $url = 'https://taxes4pros.com/wp-json/wp/v2/users';

        $client = new ApiClient();
        $client

            /*
            Seteo parámetos
        */
            ->disableSSL()
            ->followLocations()
            ->request($url, 'GET');

        dd($client->getStatus(), 'STATUS');
        dd($client->getError(), 'ERROR');
        // dd($client->getHeaders(), 'HEADERS');
        dd($client->getResponse(true), 'RES');
    }

    function tutorlms_courses_get()
    {
        $url  = 'https://taxes4pros.com/wp-json/tutor/v1/courses?order=desc&orderby=ID&paged=1';

        $user = 'key_f7a2062021f6a2218f96818631bf9a4c';
        $pass = 'secret_7b11f511f92355956e77aeaa2d9bba520b8e86025dbfe0ef6e94c33e885ccb7c';

        $client = new ApiClient();
        $client

            /*
            Seteo parámetos
        */
            ->disableSSL()
            ->followLocations()
            ->setBasicAuth($user, $pass)
            ->request($url, 'GET');

        dd($client->getStatus(), 'STATUS');
        dd($client->getError(), 'ERROR');
        // dd($client->getHeaders(), 'HEADERS');
        dd($client->getResponse(true), 'RES');
    }

    function tutorlms_enrollment()
    {
        $uid       = 168;
        $course_id = 17515;

        $url  = 'https://taxes4pros.com/wp-json/tutor/v1/enrollments';

        $user = 'key_f7a2062021f6a2218f96818631bf9a4c';
        $pass = 'secret_7b11f511f92355956e77aeaa2d9bba520b8e86025dbfe0ef6e94c33e885ccb7c';

        $client = new ApiClient();

        $data = [
            'user_id'   => $uid,
            'course_id' => $course_id,
        ];

        $client

            /*
            Seteo parámetos
        */
            ->disableSSL()
            ->followLocations()
            ->setBasicAuth($user, $pass)
            ->setBody($data)
            ->request($url, 'POST');

        dd($client->getStatus(), 'STATUS');
        dd($client->getError(), 'ERROR');
        dd($client->getHeaders(), 'HEADERS');
        dd($client->getResponse(true), 'RES');
    }

    function test_decode_xml()
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Body><soap:Fault><faultcode>soap:Server</faultcode><faultstring>Server was unable to process request. ---&gt; Error: La bodega no existe</faultstring><detail /></soap:Fault></soap:Body></soap:Envelope>';

        dd(XML::toArray($xml)[4]['FAULTSTRING'][0], 'XML -> ARR');
    }


    /*
        Test de shortcode
    */
    function nick()
    {
        set_template('templates/tpl_bt5.php');

        $sc = new EatLeaf();

        render($sc->index());
    }

    function nick_get_ids()
    {
        $html = Files::getContent('D:\Desktop\EAT-LEAF (NICK)\#SECTIONS\section-1.html');

        $ids = HTMLTools::getIDs($html);
        dd($ids);

        dd("Done.");
    }

    function nick_css__from_ids()
    {
        $html = Files::getContent('D:\Desktop\EAT-LEAF (NICK)\#SECTIONS\section-1.html');

        $ids = HTMLTools::getIDs($html);
        dd($ids);

        dd("Done.");
    }

    function test_xml_decode()
    {
        $xml = '
        <NewDataSet>
            <Table>
                <idgrupo>01</idgrupo>
                <grupo>BEBIDAS CALIENTES</grupo>
            </Table>
            <Table>
                <idgrupo>02</idgrupo>
                <grupo>EXPRESSOS</grupo>
            </Table>
        </NewDataSet>';

        dd(XML::toArray($xml), 'XML -> ARR');
    }

    function test_progress()
    {
        $pr = new ProgressShortcode();
        render($pr->index());
    }

    function test_importer_progress()
    {
        $pr = new ImporterShortcode();
        render($pr->index());
    }

    function test_sanitize_old_school()
    {
        $path = 'D:\\Desktop\\TUTOR LMS -2 (FER ANCONA)\\CSVs\prod-import\\user_export.txt';

        $str  = file_get_contents($path);

        $csv = Files::getCSV($path);

        dd($csv, 'ROW');
    }

    function test_sanitize()
    {
        $path = 'D:\\Desktop\\TUTOR LMS -2 (FER ANCONA)\\CSVs\prod-import\\user_export.txt';

        $str  = file_get_contents($path);

        Files::processCSV($path, ',', true, function ($row) {
            // Procesamiento del row

            dd($row, 'ROW');
        });
    }

    function test_accent_conv()
    {
        $str = 'Cóurse';

        $str = Strings::sanitize($str);

        dd($str); // Course
    }

    function test_easy_table_1()
    {
        $table = new EasyHTMLTable('table', 'style="background-color: silver"');
        $table->setHead('th_blue', ['name', 'lastname', 'age']);
        $table->setRows([
            ['name' => 'John', 'lastname' => 'Doe', 'age' => 35],
            ['name' => 'Ann', 'lastname' => 'White', 'age' => 23],
            ['name' => 'Pablo', 'lastname' => 'Bozzolo', 'age' => 41]
        ]);

        $table->addRow(['name' => 'Pepito', 'lastname' => 'Ferandez', 'age' => 21]);
        $table->addRow(['name' => 'Fulano', 'lastname' => 'Jimenez', 'age' => 21, 'id' => 8]);

        // optional
        $table->setRowClases(['info', '', 'warning']);

        echo $table;
    }

    function test_cookies_set()
    {
        // Crear una cookie que caduque en 60 segundos
        $caducidad = time() + 10;
        Cookie::set('idioma', 'es', $caducidad);
    }

    function test_cookies_get()
    {
        // Obtener el valor de la cookie
        dd(Cookie::get('idioma'), 'IDIOMA'); // Output: es
    }

    function test_cookies_get_all()
    {
        // Obtener el valor de la cookie
        dd(Cookie::get(), 'COOKIES');
    }

    function test_cookies_delete()
    {
        // Borrar la cookie
        Cookie::delete('idioma');
        dd("COOKIE ELIMINADA");
    }

    function test_radios_1()
    {
    ?>
        <table>
            <td data-title="Envío">
                <ul id="shipping_method" class="woocommerce-shipping-methods">
                    <li>
                        <input type="radio" name="shipping_method[0]" data-index="0" id="shipping_method_0_flat_rate8" value="flat_rate:8" class="shipping_method" checked="checked"><label for="shipping_method_0_flat_rate8">Moto CABA: <span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">AR$</span>98,500</bdi></span></label>
                    </li>
                    <li>
                        <input type="radio" name="shipping_method[0]" data-index="0" id="shipping_method_0_flat_rate7" value="flat_rate:7" class="shipping_method"><label for="shipping_method_0_flat_rate7">Moto GRAN BUENOS AIRES: <span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">AR$</span>1,970,000</bdi></span></label>
                    </li>
                    <li>
                        <input type="radio" name="shipping_method[0]" data-index="0" id="shipping_method_0_local_pickup15" value="local_pickup:15" class="shipping_method"><label for="shipping_method_0_local_pickup15">Recogida local</label>
                    </li>
                </ul>


            </td>
        </table>
    <?php
    }

    function test_radios_2(){
    ?>
        <ul class="wc_payment_methods payment_methods methods">
            <li class="wc_payment_method payment_method_bacs">
                <input id="payment_method_bacs" type="radio" class="input-radio" name="payment_method" value="bacs" checked="checked" data-order_button_text="">

                <label for="payment_method_bacs">
                    Transferencia bancaria directa </label>
                <div class="payment_box payment_method_bacs">
                    <p>Realiza tu pago directamente en nuestra cuenta bancaria. Por favor, usa el número del pedido como referencia de pago. Tu pedido no se procesará hasta que se haya recibido el importe en nuestra cuenta.</p>
                </div>
            </li>
            <li class="wc_payment_method payment_method_cheque">
                <input id="payment_method_cheque" type="radio" class="input-radio" name="payment_method" value="cheque" data-order_button_text="">

                <label for="payment_method_cheque">
                    Pagos por cheque </label>
                <div class="payment_box payment_method_cheque" style="display:none;">
                    <p>Por favor, envía un cheque a Nombre de la tienda, Calle de la tienda, Ciudad de la tienda, Provincia/País de la tienda, Código postal de la tienda.</p>
                </div>
            </li>
            <li class="wc_payment_method payment_method_cod">
                <input id="payment_method_cod" type="radio" class="input-radio" name="payment_method" value="cod" data-order_button_text="">

                <label for="payment_method_cod">
                    Contra reembolso </label>
                <div class="payment_box payment_method_cod" style="display:none;">
                    <p>Paga en efectivo en el momento de la entrega.</p>
                </div>
            </li>
            <li class="wc_payment_method payment_method_mobbex">
                <input id="payment_method_mobbex" type="radio" class="input-radio" name="payment_method" value="mobbex" data-order_button_text="">

                <label for="payment_method_mobbex">
                    Pagar con Mobbex <img src="https://pablo.tol.ar/wp-content/plugins/woocommerce-mobbex/icon.png" alt="Pagar con Mobbex"> </label>
            </li>
        </ul>
        <?php
    }

    /*
        Logueo y sigo navegando al propaga cookies
    */
    function wp_login_test()
    {
        $login_data = [
            'site_url'   => 'http://woo5.lan',
            'login_page' => 'wp-login.php',
            'log' => 'boctulus',
            'pwd' => '!0EJEbwu)Oa!3Fd&ev',
            'rememberme' => 'forever',
            'redirect_to' => 'http://woo5.lan/my-account/',
            'redirect_to_automatic' => '1'
        ];

        $jar = new CookieJar('woo5.lan-cookies.txt');

        // Crea una instancia de la clase ApiClient
        $cli = ApiClient::instance()
            ->setUserAgent(ApiClient::USER_AG_FIREFOX);

        $cli->useCookieJar($jar);

        $cli
            ->followLocations()
            ->withoutStrictSSL();

        // Realiza la solicitud POST para iniciar sesión
        $cli->post("{$login_data['site_url']}/{$login_data['login_page']}", [
            'log'        => $login_data['log'],
            'pwd'        => $login_data['pwd'],
            'rememberme' => $login_data['rememberme']
        ]);

        $res = $cli->getResponse(false);

        dd($jar->getCookies(), 'COOKIES');

        ////////////// SIGUENTE SOLICITUD HTTP >>>>

        // Verifica si la solicitud fue exitosa (código de estado 200)
        if ($res['http_code'] === 200 || $res['http_code'] === 301 || $res['http_code'] === 302) {
            dd("Inicio de sesión exitoso.");

            var_export($cli->dump());
        } else {
            dd("Error al iniciar sesión: ");

            dd($cli->getStatus(), 'STATUS');
            dd($cli->getError(), 'ERROR');
            dd($cli->getResponse(), 'RES');
            dd($cli->getHeaders(), 'HEADERS');

            exit;
        }

        // Sigo navegando,....

        $cli->setUrl("{$login_data['site_url']}/my-account/edit-address/");

        $page_login = $cli
            ->get()
            ->getResponse(false);

        // Verifica si la solicitud de la página de la cuenta fue exitosa
        if ($page_login['http_code'] === 200) {
            dd($page_login['data'], 'PAGINA DETRAS DEL LOGIN'); // vuelve a mostrar el form del login !!

            // Seguir procesando .....
        } else {
            dd("Error al acceder a la página de la cuenta: " . $page_login['error']);
        }
    }


    function wp_login_test_2()
    {
        $login_data = [
            'site_url' => 'https://torrepadregourmet.es',
            'login_page' => 'wp-login.php',
            'log' => 'pablo@tiendaonline.com.ar',
            'pwd' => 'pablo123$=Nn',
            'rememberme' => 'forever',
            'redirect_to' => 'https://torrepadregourmet.es/my-account/',
            'redirect_to_automatic' => '1'
        ];

        $jar = new CookieJar('cookies-torrepadregourmet.es.txt');

        // Crea una instancia de la clase ApiClient
        $cli = ApiClient::instance()
            ->setUserAgent(ApiClient::USER_AG_FIREFOX);

        $cli->useCookieJar($jar);

        $cli
            ->followLocations()
            ->withoutStrictSSL();

        // Realiza la solicitud POST para iniciar sesión
        $cli->post("{$login_data['site_url']}/{$login_data['login_page']}", $login_data);

        $res = $cli->getResponse(false);

        dd($jar->getCookies(), 'COOKIES');

        ////////////// SIGUENTE SOLICITUD HTTP >>>>

        // Verifica si la solicitud fue exitosa (código de estado 200)
        if ($res['http_code'] === 200 || $res['http_code'] === 301 || $res['http_code'] === 302) {
            dd("Inicio de sesión exitoso.");
        } else {
            dd("Error al iniciar sesión: ");

            dd($cli->getStatus(), 'STATUS');
            dd($cli->getError(), 'ERROR');
            dd($cli->getResponse(), 'RES');
            dd($cli->getHeaders(), 'HEADERS');

            dd($cli->dump());

            exit;
        }

        // Sigo navegando,....

        $cli->setUrl("{$login_data['site_url']}/my-account/orders/");

        $page_login = $cli
            ->get()
            ->getResponse(false);

        // Verifica si la solicitud de la página de la cuenta fue exitosa
        if ($page_login['http_code'] === 200) {
            dd($page_login['data'], 'PAGINA DETRAS DEL LOGIN'); // vuelve a mostrar el form del login !!

            // Seguir procesando .....
        } else {
            dd("Error al acceder a la página de la cuenta: " . $page_login['error']);
        }
    }

    function test_get_availability(){
        $str = "<p>
    
        En existencias <br>se puede enviar en 3-4 días
    </p>";

        $ps = Strings::matchAll($str, "/<p>([\s\S]*?)<\/p>/");

        $stock          = "outofstock"; // aunque siempre envian
        $available_in_t = null;

        foreach ($ps as $p){    
            if (Strings::contains("En existencias", $p)){
                $stock = "instock";
            }

            $available_in_t = Strings::match($p, "/se puede enviar en ([0-9-]{1,} días)/");
        }

        dd([
            $stock,
            $available_in_t 
        ]);
    }


    // OK
    function test_dom_crawler_p()
    {
        StratoScraper::setup('https://www.azulejosmadridonline.es/epages/63993920.sf/es_ES/?ObjectPath=/Shops/63993920', 3600 * 24 * 60);

        dd(StratoScraper::getProduct('/Products/zami'));
    }

    // OK
    function test_dom_crawler_cats()
    {
        StratoScraper::setup('https://www.azulejosmadridonline.es/epages/63993920.sf/es_ES/?ObjectPath=/Shops/63993920', 3600 * 24 * 60);
        $html = StratoScraper::getHTML('');

        dd(StratoScraper::getCategoList($html));
    }

    function test_dom_crawler_cat()
    {
        $html = StratoScraper::getHTML('https://www.azulejosmadridonline.es/epages/63993920.sf/es_ES/?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Azulejos', 3600 * 24 * 60);

        dd(StratoScraper::getCatego($html));
    }

    function test_dom_crawler_cat_paginator()
    {
        $html = StratoScraper::getHTML('https://www.azulejosmadridonline.es/epages/63993920.sf/es_ES/?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Azulejos', 3600 * 24 * 60);

        dd(StratoScraper::getPaginator($html));
    }

    function test_dom_crawler_product_links()
    {
        $page = 3;

        StratoScraper::setup('https://www.azulejosmadridonline.es/epages/63993920.sf/es_ES/?ObjectPath=/Shops/63993920', 3600 * 24 * 60);

        $url  = StratoScraper::getCategoryPageURL('https://www.azulejosmadridonline.es/epages/63993920.sf/es_ES/?ObjectPath=/Shops/63993920/Categories/%22Azulejos/Porcel%C3%A1nicos%22/Azulejos', $page);

        $html = StratoScraper::getHTML($url, 3600 * 24 * 60);

        dd(
            StratoScraper::getProductLinks($html)
        );
    }

    function test_table_export(){
        dd(DatabaseBackup::exportTableAsCSV('roles'), 'PATH');
    }
    
    function test_set_env(){
        // valor
        dd(
            Env::get('variable')
        );

        // valor
        dd(
            Config::get('my_config_variable')
        );
    }

    function test_log_queries(){        
        DB::dbLogStart();
    }

    function dump_db_log(){
        dd(
            DB::dbLogDump()
        );
    }

    // OK 
    function test_openai_1()
    {
        $chat = new ChatGPT();

        $chat->addContent('Hola, ¿cómo estás?');
        $res = $chat->exec();
        dd($res);
    }

    function test_openai_2()
    {
        $chat = new ChatGPT();
        $chat->addContent('Hola, ¿cómo estás hoy?');
        $res = $chat->exec('gpt-4');

        dd($res);
    }
        
    function test_openai_3()
    {
        $chat = new ChatGPT();
        $chat->setModel('gpt-4o-mini');

        // Testing
        $chat->getClient()
        ->enablePostRequestCache()
        ->setCache(3600);

        $chat->addContent('Todos los elementos de la tabla periodica con sus estados de oxidacion');

        $chat->setParams([
            "max_tokens"      => 150,
            "temperature"     => 0.5
        ]);

        $chat->exec();

        if (!$chat->isComplete()){
            dd($chat->getFinishReason(), 'Finish reason');
            // die;
        }

        dd($chat->getContent(), 'Content');
    }

    /*
        Genera error!

        Probar ejemplos en Python y Node

        https://platform.openai.com/docs/guides/vision
    */
    function test_chatgpt_vision()
    {
        $chat = new ChatGPT();
        $imagePath = 'D:\\www\\simplerest\\etc\\chatgpt-vision-test-gallery\\persona.jpg';
        $res = $chat->analyzeImage($imagePath);

        if ($res === false){
            dd($chat->error(), 'ERROR');
        } else {
            dd($res, 'RES');
        }        
    }

    function test_claude_1()
    {
        $chat = new ClaudeAI();

        $chat->setParams(['max_tokens' => 200]); 
        $chat->addContent('Estados de oxidacion del Uranio?');
        $res = $chat->exec();
        dd($res);
    }

    function test_claude_2()
    {
        $chat = new ClaudeAI();

        $chat->setParams(['max_tokens' => 30]);  
        $chat->addContent('Hola, ¿cómo estás hoy?');
        $res = $chat->exec('claude-instant-1.2');

        dd($res);
    }

    function test_case(){
        // Ejemplo de uso
        $stringExample = "HELLO WORLD";
        $arrayExample = ["HELLO", "WORLD"];

        dd(Strings::toCase(Strings::LOWERCASE_FILTER, $stringExample)); // "hello world"
        dd(Strings::toCase(Strings::LOWERCASE_FILTER, $arrayExample)); // ["hello", "world"]
    }

    /*  
        Para dominios:

        podrian comenzar con ...

        new, next,.....

        podrian terminar en ...

        space, zone, app, ify, ... us, u, square, hub
    */
    function brand_maker($max_combinations = 10, int $minLength = 5, int  $maxLength = 7) {
        $syllables = [
            'ap', 'ple', 'goo', 'gle', 'a', 'ma', 'zon', 'mi', 'cro', 
            'tes', 'la', 'ni', 'ke', 'adi', 'das', 'in', 'tel', 'ci', 'sco', 'or', 'a', 'cle', 
            'ado', 'be', 'spot', 'i', 'fy', 'air', 'bnb', 'u', 'ber', 'net', 'flix', 'i', 'b', 'm', 
            'so', 'ny', 'pal', 'e', 'bay', 'linked', 'in', 'red', 'dit', 
            'pin', 'ter', 'est', 'slack', 'tik', 'tok', 'ya', 'hoo', 'what', 'app', 
            'al', 'i', 'ba', 'ba', 'shop', 'ify', 'box', 'vi', 'me', 'o', 
            'hu', 'lu', 'yelp', 'zi', 'llow', 'space', 'x', 'dock', 'er', 'pa', 'tre', 'on', 
            'base', 'camp', 'me', 'di', 'um', 'square', 'sales', 'force', 'trend', 'ify',

            'Ni', 'Ke', 'Cha', 'Ne', 'Di', 'Or', 'Za', 'Ra', 'Pr', 'Da', 'Next', 'Pu', 'Ma', 'Ga', 'Ma', 'O', 'Me', 'As', 'Ic', 'Pu', 'Ma', 'Old', 'Na', 'Di', 'Es', 'Gap', 'Gap', 'Fur', 'La', 'GAP', 'Sis', 'Le', 'tod', 'Tis', 'Sot', 'Lac', 'Os', 'Top', 'Sh', 'Op', 'Al', 'Do', 'Oa', 'K', 'Le', 'Jim', 'My', 'Choo', 'Fos', 'Sil', 'Pa', 'Ta', 'Go', 'New', 'Look', 'Es', 'Ca', 'Ca', 'Vall', 'Li', 'Sa', 'Swat', 'Ch', 'Te', 'Da', 'Ba', 'Ka'            
        ];

        $syllables = Strings::toCase(Strings::LOWERCASE_FILTER, $syllables);

        // Eliminar duplicados
        $syllables_unique = array_unique($syllables);
    
        $combinations = [];

        while (count($combinations) < $max_combinations) {
            // Generar una combinación al azar
            $combination = '';
            $num_syllables = mt_rand(2, 7); // Número aleatorio de sílabas entre 2 y 7

            while (strlen($combination) < $maxLength && $num_syllables > 0) {
                // Seleccionar una sílaba aleatoria
                $random_syllable = $syllables_unique[array_rand($syllables_unique)];

                // Evitar repetir la misma sílaba en la misma combinación
                if (!strpos($combination, $random_syllable)) {
                    // Agregar la sílaba a la combinación
                    $combination .= $random_syllable;
                    $num_syllables--;
                }
            }

            // Añadir la combinación si tiene entre $minLength y $maxLength caracteres y no está repetida
            if (strlen($combination) >= $minLength && strlen($combination) <= $maxLength && !in_array($combination, $combinations)) {
                $combinations[] = $combination;
            }
        }

        // Imprimir el resultado
        print_array($combinations);
    }

    function test_nth(){
        dd(
            Strings::after('D:/www/simplerest/app/shortcodes/progress_bar/views/img/time_over.png',
            '/app/shortcodes/')
        );
    }

    /*
        Optimizaciones mas agresivas podrian incluir:

        - Eliminar todos los <ul> y <ol>
        - Eliminar todos los <img>   
        - Eliminar formularios con id que contengan "login"
        Etc
    */
    function get_minified_html(string $url, bool $remove_css_classes = false, bool $remove_frameworks = false, bool $remove_lists = false)
    {
        if (Url::validate($url)){
            $cli = new ApiClient($url);

            $res = $cli
            ->disableSSL()
            ->cache(3600 * 24 * 30)
            ->get()
            ->getResponse();

            if (!empty($cli->error()) || $cli->status() !== 200){
                throw new \Exception("Error reading response for '$url'. HTTP status code: " .$cli->status(). ".Detail: ". $cli->error());
            }

            $html = $res['data']; 

            dd(FileCache::getCachePath($url));
        } else {
            $html = Files::readOrFail($url);
        }

        dd(Strings::getLengthInKB($html), "LEN");
        // exit; //         

        // Si hay <articles> ... me quedo con eso
        $articles = HTMLTools::getArticles($html, false);

        if (!empty($articles)){
            $html = implode("\r\n\r\n", $articles);    
            $html = XML::stripTag($html, 'svg'); 
            $html = XML::stripTag($html, 'style');               
        } else {
            $html = XML::stripTag($html, 'head');
            $html = XML::stripTag($html, 'footer');
            $html = XML::stripTag($html, 'script'); // elimina tambien el valioso "JSON LD" => debe recuperarse primero 
            $html = XML::stripTag($html, 'style');
            $html = XML::stripTag($html, 'nav'); // custom?
            $html = XML::stripTag($html, 'aside');   
            $html = XML::stripTag($html, 'iframe');
            $html = XML::stripTag($html, 'svg');

            $html = HTMLTools::removeHTMLAttributes($html, [
                'onclick',
                'ondblclick',
                'onmousedown',
                'onmouseup',
                'onmousemove',
                'onmouseover',
                'onmouseout',
                'onkeydown',
                'onkeyup',
                'onkeypress',
                'onfocus',
                'onblur',
                'onchange',
                'onsubmit',
                'onreset',
                'onselect',
                'oninput',
                'onload',
                'onunload',
                'onerror',
                'onresize',
                'onscroll'
            ]);

            $attr_to_remove = ['style', 'rel', 'target', 'type'];
    
            if ($remove_css_classes){
                $attr_to_remove[] = 'class';
            }

            $html = HTMLTools::removeHTMLAttributes($html, $attr_to_remove);
            $html = Strings::removeMultiLineComments($html);
            $html = HTMLTools::removeComments($html);
            $html = Strings::removeSpaceBetweenTags($html);
		    $html = Strings::removeMultipleSpacesInLines($html);

            // Puede no ser tan buena idea
            $html = Strings::removeDataAttr($html); // bye data-*
             
            /*
                Extra reductions
            */

            if ($remove_lists){
                $html = XML::stripTag($html, 'ul'); 
                $html = XML::stripTag($html, 'ol'); 
            }
            
            if ($remove_frameworks){
                $cms  = CMSScanner::identify($url);

                /*
                    Remocion de CSS de frameworks
                */

                if (in_array('Bootstrap', $cms['frontend'])){
                    $html = BootstrapCleaner::remove($html);
                }

                if (in_array('Angular (SSR)', $cms['frontend']) || in_array('Angular', $cms['frontend'])){
                    $html = AngularCleaner::remove($html);
                }
            }           
        }    

        $html = str_replace([ '&#xA0;', '&#x2013;', '&#xB0;', '&gt;', '&#x2019;' ], '', $html);

        // Fix para '<?xml version="1.0" ...
        $html = str_replace('??????????>', ' ?>', $html);
        
        // dd($html, 'HTML'); exit;
        dd(Strings::getLengthInKB($html), "LEN");
        //exit;

        Files::writeOrFail(ETC_PATH . 'page.html', $html);

        return $html;
    }

    function get_reduced_html(){        
        $url = "https://www.mateandoarg.com/materas/porta-mate-rutero-cuero";

        $out = $this->getMinifiedHTML($url, false, false, true);

        dd($out);
    }

    function test_claude_api()
    {   
        $max_tokens = 1500;

        $content = <<<DATA
        Given the following HTML from web page, extract XPATH selectors (compatible with Javascript and Selenium) and complete the provided JSON. 

        JSON: 

        {
            "product_name": "{XPATH}",
            "regular_price": "{XPATH}",
            "sales_price": "{XPATH}", 
            "discount_as_percentaje": "{XPATH}",
            "interest_free_installments": {
                "number_of_installments": "{XPATH}",
                "fee_amount": "{XPATH}"
            },
            "colors:all": "{XPATH}",
            "images:all[src]": "{XPATH}"
        }

        RECOMENDATIONS:


        - Try simple solutions instead of overcomplicated XPATH selectors.

        - To use relative XPATH is also an option.

        - Do not asume anything about the HTML structure. Double-check.

        - By default, you are asked to get texts but if you see something like [src] or [href] and so on between [ and ] then means get attribute or attributes.

        - The answer MUST be ONLY the JSON and nothing else. No explanations. No questions. Nothing else.

        - Please, feel free to use more tokens to process a verification process before deliver the solution.


        HTML: 
        DATA;

        // $max_tokens = 50;
        // $content    = 'Capital de Peru?';

        $chat = new ClaudeAI();

        $chat->setParams(['max_tokens' => $max_tokens]); 
        $chat->addContent($content);
        $chat->client->cache(3600); //        
        $res = $chat->exec();
        // dd($res);          
        
        if (!empty($res['error'])){
            dd($res['error'], 'Error');
            return; //
        }

        $data = $res['data']['data'];

        dd($data['content'][0]['text'], "Respuesta de Claude:");

        if (isset($data['usage'])) {
            dd($data['usage']['input_tokens'] ?? '', "Input tokens:");
            dd($data['usage']['output_tokens'] ?? '', "Output tokens:");
            dd(($data['usage']['input_tokens'] ?? 0) + ($data['usage']['output_tokens'] ?? 0), "Total tokens:");
        } else {
            dd(null, "Información de tokens no disponible en la respuesta.");
        }    
    }


    function test_update_descriptions(){
        $str = '<span style="line-height: 39.2px; font-size: 28px;">2.<img style="width: 70px; height: 70px;" title="Alta resistencia al rayado" src="/WebRoot/StoreES2/Shops/63993920/MediaGallery/Platos_carga_mineral/alta_resistencia_al_rayado.png" alt="alta_resistencia_al_rayado" /></span><span style="line-height: 39.2px; font-size: 28px;">3.<img style="width: 70px; height: 70px;" title="Dureza escala barcol" src="/WebRoot/StoreES2/Shops/63993920/MediaGallery/Platos_carga_mineral/dureza_en_la_escala_de_barcol_de_45.png" alt="dureza_en_la_escala_de_barcol_de_45" /></span><span style="line-height: 39.2px; font-size: 28px;">4</span>.<img style="width: 70px; height: 70px;" title="Resistencia al cambio de temperatura" src="/WebRoot/StoreES2/Shops/63993920/MediaGallery/Platos_carga_mineral/resistencia_al_cambio_de_temperatura.png" alt="resistencia_al_cambio_de_temperatura" /><span style="line-height: 39.2px; font-size: 28px;">5.<img style="width: 70px; height: 70px;" title="Antideslizante" src="/WebRoot/StoreES2/Shops/63993920/MediaGallery/Platos_carga_mineral/antideslizante.png" alt="antideslizante" /></span>';

        dd(HTMLTools::relativeToAbsoluteURLs($str, 'https://www.azulejosmadridonline.es/'));        
    }

    function test_fix_encoding(){
        $str = '
        COMPOSICIÃ“N NÂº24 100X60. 
        
        ColecciÃ³n compuesta por 5Â piezas distintas. 
        
        Cantidad en la composiciÃ³n: 0,60 m2.
        
        Para pedidos inferiores a 80â‚¬ en nuestra secciÃ³n de construcciÃ³n de deben abonar 6â‚¬ por gastos de transporte. VÃLVULA INCLUIDA.Â - ENVÃO GRATUITO';

        dd(Strings::fixEncoding($str), 'VER 1');
        dd(Strings::fixEncodingWithAutodetection($str), 'VER 2');

        dd(Strings::fixEncoding(Strings::fixEncoding($str)), 'VER 1 (RECURSIVO)');
        dd(Strings::fixEncodingWithAutodetection(Strings::fixEncodingWithAutodetection($str)), 'VER 2 (RECURSIVO)');
    }

    function test_fix_encoding_2(){
        $str = <<<STR
        <h1><span style="font-size: 22px;"><em><strong><span style="color: red;">COMPOSICIÃ“N NÂº24 100X60</span></strong></em></span></h1>
        <em><strong>ColecciÃ³n compuesta por 5Â piezas distintas</strong></em> que forma un mural de 100x60cms.

        ComposiciÃ³n compuesta por piezas de 20x60

        <em><strong>Pasta roja.</strong></em>

        Cantidad en la composiciÃ³n: 0,60 m2.

        <em style="color: red; font-size: 13px;"><strong>ENVÃO GRATUITO.</strong></em>

        <span style="color: red;"><em><strong>VENTA POR CONJUNTO DE 5Â PIEZAS.</strong></em></span>

        <span style="color: black;"><em><strong>CONSULTAD DESCUENTO A PARTIR DE 3Â JUEGOS DE COMPOSICIÃ“N.</strong></em></span>

        Â
        STR;

        $str = Strings::fixEncodingWithHtmlEntities($str);
        dd($str);
    }

    // recursivo
    function test_fix_encoding_2r(){
        $str = <<<STR
        <h1><span style="font-size: 22px;"><em><strong><span style="color: red;">COMPOSICIÃ“N NÂº24 100X60</span></strong></em></span></h1>
        <em><strong>ColecciÃ³n compuesta por 5Â piezas distintas</strong></em> que forma un mural de 100x60cms.

        ComposiciÃ³n compuesta por piezas de 20x60

        <em><strong>Pasta roja.</strong></em>

        Cantidad en la composiciÃ³n: 0,60 m2.

        <em style="color: red; font-size: 13px;"><strong>ENVÃO GRATUITO.</strong></em>

        <span style="color: red;"><em><strong>VENTA POR CONJUNTO DE 5Â PIEZAS.</strong></em></span>

        <span style="color: black;"><em><strong>CONSULTAD DESCUENTO A PARTIR DE 3Â JUEGOS DE COMPOSICIÃ“N.</strong></em></span>

        Â
        STR;

        $str = Strings::fixEncodingWithHtmlEntities($str);
        $str = Strings::fixEncodingWithHtmlEntities($str);

        dd($str);
    }

    function test_fix_super_encoding_problem(){
        $str = "DestonificaciÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂÃÂ³n moderada.";
        
        $str = Strings::unravelEncoding($str);

        dd($str, 'VER 1');
    }

    function test_numeric_input(){
        view('test_numeric_input');
    }

    function invoice(){
        css_file(VIEWS_PATH . 'bir/css/styles.css');
        view('bir/invoice_layout.php');
    }

    function invoice_half_reduced(){
        css_file(VIEWS_PATH . 'bir_half_reduced/css/styles.css');
        view('bir_half_reduced/invoice_layout.php');
    }

    // function invoice_half_size(){
    //     css_file(VIEWS_PATH . 'bir_half_size/css/styles.css');
    //     view('bir_half_size/invoice_layout.php');
    // }

    function invoice_small(){
        css_file(VIEWS_PATH . 'bir_small/css/styles.css');
        view('bir_small/invoice_layout.php');
    }

    function bt5menu()
    {
        set_template('bt5panel/tpl.php');
        render('Hello World');
    }

    /*
        CMS scanner
    */
    function identity_cms_in_bulk()
    {
        /*
            Clasificar incluyendo los no identificados
        */

        $list = <<<LIST
        lacuracaonline.com
        mabeglobal.com
        almacenestropigas.com
        gollo.com
        prado.com.sv
        omnisport.com
        ladylee.net
        panafoto.com
        elektra.com.gt
        verdugotienda.com
        tiendamonge.com
        elektra.com.hn
        mabeglobalpanama.com
        elgallomasgallo.com.ni
        ladylee.com
        elgallomasgallo.com.gt
        ektguatemala.myvtex.com
        www.sonjavanduelmen.com
        LIST;

        // $urls = Strings::lines($list, true, true);

        $urls = ['https://tuprimerpaso.duoc.cl'];

        foreach ($urls as $url) {
            dd(
                CMSScanner::identify($url),
                $url
            );
        }
    }

    function get_plugins()
    {
        $url = 'https://shoppingparts.cl/';
        // $url = 'https://portalmotolatino.com.ar/';

        $str = Url::getUrlContent($url, 360000);

        dd($url);

        dd(
            WordPress::getTheme($str),
            'Theme'
        );

        // Revisar si realmente devuelve todos
        dd(
            WordPress::getPlugins($str),
            'Plugins'
        );
    }
    
    function get_indexes(){
        DB::getConnection();

        dd(DB::select(
           "SELECT 
                TABLE_NAME, 
                INDEX_NAME, 
                COLUMN_NAME, 
                SEQ_IN_INDEX, 
                NON_UNIQUE, 
                INDEX_TYPE, 
                COLLATION, 
                CARDINALITY, 
                SUB_PART, 
                PACKED, 
                NULLABLE, 
                INDEX_COMMENT
            FROM 
                information_schema.STATISTICS
            WHERE 
                TABLE_SCHEMA = 'woo8'
            ORDER BY 
                TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX;
            ")
        );
    }

    function test_code_gen()
    {
        $chat = new ChatGPT();
        $chat->setModel('o1-preview');  

        // Testing - cache
        $chat->getClient()
        ->enablePostRequestCache()
        ->setCache(3600);

        $prompt = Files::getContentOrFail(ETC_PATH . 'prompt-generator\generated\001.txt');

        $chat->addContent($prompt);

        $res = $chat->exec();

        if (!$chat->isComplete()){
            dd($chat->getFinishReason(), 'Finish reason');
            // die;
        }

        dd($res, 'Content');

        dd($chat->getTokenUsage(), 'TOKEN USAGE');
    }

    function test_datatable(){        
        js_file('js/custom_datatable.js');

        set_template('templates\tpl_bt5.php');
        
        view('my_datatable');
    }

    function test_is_browser(){
        $val = false;
        return $val;
        // dd(Request::isBrowser());
    }

    function test_orHaving(){
        DB::getConnection();

        DB::table('products')->deleted()
        ->groupBy(['cost', 'size', 'belongs_to'])
        ->havingRaw('SUM(cost) > ?', [500])
        ->orHaving([
            ['cost', 100, '>='],
            ['size', '1L']
        ])
        ->orderBy(['size' => 'DESC'])
        ->dontExec()
        ->get(['cost', 'size', 'belongs_to']); 
        
        dd(DB::getLog());
    }
    
    function test_or_operator(){
        DB::getConnection();

        DB::table('products')->deleted()
        ->groupBy(['cost', 'size', 'belongs_to'])
        ->havingRaw('SUM(cost) > ?', [500])
        ->or(function($q){
              $q->having(['cost', 100, '>='])
              ->having(['size' => '1L']);
        })
        ->orderBy(['size' => 'DESC'])
        ->dontExec()
        ->get(['cost', 'size', 'belongs_to']); 
        
        dd(DB::getLog());
    }

    function test_grok(){
        $api_key = 'xai-7M7kvoRLd0KCiltMxTSUZC11XOJnUlfWC3NlUK7uaxFNIsvt7cqfqdbmsUjZ095KISt4R7NFkhowM2JX';

            // Instanciar GrokAI con tu API key
        $grok = new GrokAI($api_key);
        $grok->client->cache(3600);

        // Ejemplo con rol de sistema y temperatura
        $grok->addContent("You are a helpful assistant.", "system");
        $grok->setTemperature(0.7);
        $grok->addContent("Explícame qué es la inteligencia artificial");
        
        // Ejemplo con parámetros personalizados
        // $grok->setParams([
        //     'temperature' => 0.5,
        //     'max_tokens' => 1000
        // ]);

        $response = $grok->exec();

        // dd($response);

        if ($error = $grok->error()) {
           dd($error, 'Error');
        } else {
            dd($grok->getContent(), 'Content');
        }        
    }

    function test_exists(){
        DB::getConnection();

        $is = table('migrations')
        ->where('filename', '2021_09_14_27910163_options.php')
        ->exists();

        dd($is, 'Exist?');
    }

    function test_exists_2(){
        DB::getConnection('relmotor');    

        $res = table('managed_cache')
        // ->wrap()
        ->select(['key', 'created_at'])
        ->where('key', 'test_key')
        ->dd();

        dd($res);
    }

    // function test_sql_asdf(){
    //     $slug = 'test-product-' . uniqid();

    //     $update_data = [
    //         'name' => 'Test Product',
    //         'description' => 'Initial description', 
    //         'slug' => $slug,
    //         'images' => '[]',          
    //     ];

    //     $id = DB::table('products')->createOrUpdate($update_data, ['slug']);
        
    //     $slug = 'test-product-' . uniqid();

    //     $update_data = [
    //         'description' => 'Updated description',
    //         'slug' => $slug  // necesario para el where
    //     ];
  
    //     $id = DB::table('products')
    //     ->dontExec()
    //     ->createOrUpdate($update_data, ['slug']);

    //     dd(DB::getLog());
    // }

    function test_insert_multiple(){
        $data = [
            [
                'name' => 'Product 1',
                'price' => 100,
                'created_at' => at()
            ],
            [
                'name' => 'Product 2',
                'price' => 200,
                'created_at' => at()
            ]
        ];
        
        $last_id = table('products')->bulkInsert($data);

        dd($last_id);
    }

    function test_arbitraje(){
        // Datos exactos del ejemplo proporcionado
        $odds = [
            [
                'bookmaker' => 'Bet365.com',
                'type' => 'Over',
                'market' => 'Yellow Card O/U 9.5',
                'odd' => 1.85
            ],
            [
                'bookmaker' => 'Snai.it',
                'type' => 'Under',
                'market' => 'Yellow Card O/U 9.5',
                'odd' => 2.80
            ]
        ];
        
        $calc = new ArbitrageCalculator();
        $res  = $calc->calculateArbitrage($odds);

        dd($res);
    }

    function test_phpdoc_gen(){
		dd(
            Reflector::generateDocBlock(Strings::class, 'parseCurrency')
        );
	}

    /*
        --| Variables.csv
        Array
        (
            [0] => Tipo de producto
            [1] => Superior
            [2] => SKU
            [3] => Marca
            [4] => Variedad
            [5] => Concentracion
            [6] => Sexo
            [7] => ml
            [8] => Formato
            [9] => Existencias
            [10] => Inventario
            [11] => Precio rebajado
            [12] => Precio normal
            [13] => Etiquetas
            [14] => Imagenes
            [15] => Tamano
            [16] => Descripcion larga
            [17] => Descripcion corta
            [18] => Largo
            [19] => Ancho
            [20] => Alto
            [21] => Peso
            [22] => Manejo Stock
            [23] => Categorias
        )

        --| TOTAL
        2403

    */
    function csv_20012024(){
        $base_path = 'D:\Desktop\GOOGLE SHEETS\SHEET PRODS A SUBIR\\';

        $data              = Files::getCSV($base_path . 'Variables.csv');
        $rows_variables    = $data['rows'];
        $headers_variables = $data['header'];

        $data              = Files::getCSV($base_path . 'Variations Botellas.csv');
        $rows_var_bt       = $data['rows'];
        $headers_var_bt    = $data['header'];

        $data              = Files::getCSV($base_path . 'Variations Decants.csv');
        $rows_var_dt       = $data['rows'];
        $headers_var_dt    = $data['header'];

        // Debugging ej
        dd($headers_var_dt, 'HEADERS');
        dd(count($rows_var_dt), 'TOTAL');

        // usort($rows, function ($a, $b) {
        //     return $a['Código Isp'] <=> $b['Código Isp'];
        // });

        
        // foreach ($rows as $row) {
        // }
    }

    function tailwind_card_v1(){
        set_template('templates/tailwind.php');
        view('instructors/card_v1.php');
    }

    function tailwind_card_v2(){
        set_template('templates/tailwind.php');
        view('instructors/card_v2.php');
    }

    function tailwind_card_v3(){
        set_template('templates/tailwind.php');
        view('instructors/card_v3.php');
    }


    function tailwind_card_v4(){        
        css_file('third_party/fontawesome/6/all.css');            

        set_template('templates/tailwind.php');
        view('instructors/card_v4.php');
    }

    function tailwind_card_short(){        
        css_file('third_party/fontawesome/6/all.css');            

        set_template('templates/tailwind.php');
        view('instructors/card_compact.php');
    }

    /*
        WEB COMPONENTS
    */
    
    /*
        NO utiliza TailWind CSS ni FontAwesome
    */
    function wc_card_shortd(){   
        view('instructors/web_components/card_compact.php');
        ?>

       <!-- Card 1 -->
        <social-profile-card
            name="Guillermo Olvera"
            subtitle="Redes"
            stats="11 Cursos"
            rating="⭐ 5.0 Calificaciones"
            image="https://i.imgur.com/qwQQMGW.png"
            facebook="https://facebook.com"
            twitter="https://twitter.com"
            linkedin="https://linkedin.com"
            github="https://github.com"
        ></social-profile-card>

        <!-- Card 2 -->
        <social-profile-card
            name="Pablo Bozzolo"
            subtitle="Backend"
            stats="15 Cursos"
            rating="⭐ 4.95 Calificaciones"
            image="https://i.imgur.com/oTgAKU2.jpeg"
        ></social-profile-card>

        <?php
    }

    /*
        NO utiliza TailWind CSS ni FontAwesome
    */
    function wc_card_short(){   
        view('instructors/web_components/card_compact.php');
        ?>

       <!-- Card 1 -->
        <social-profile-card
            name="Guillermo Olvera"
            subtitle="Redes"
            stats="11 Cursos"
            rating="⭐ 5.0 Calificaciones"
            image="https://i.imgur.com/qwQQMGW.png"
            facebook="https://facebook.com"
            twitter="https://twitter.com"
            linkedin="https://linkedin.com"
            github="https://github.com"
        ></social-profile-card>

        <!-- Card 2 -->
        <social-profile-card
            name="Pablo Bozzolo"
            subtitle="Backend"
            stats="15 Cursos"
            rating="⭐ 4.95 Calificaciones"
            image="https://i.imgur.com/oTgAKU2.jpeg"
        ></social-profile-card>

        <?php
    }

    function wc_card_v4_tailwind(){                
        css_file('third_party/fontawesome/6/all.css');            
        set_template('templates/tailwind.php');

        view('instructors/web_components/card_v4_tailwind.php');

        ?>

        <!-- Uso del componente -->
        <profile-card
            image-url="https://i.imgur.com/qwQQMGW.png"
            short-name="Guillermo Olvera"
            full-name="Juan Guillermo Olvera Maldonado"
            phone="81-8400-1777 #27363"
            specialty="REDES"
            position="Coordinador de Ingeniería"
            rating="5.0"
            description="Soy un desarrollador web con una amplia gama de conocimientos en <strong>muchos lenguajes front-end y back-end</strong>, marcos responsivos, bases de datos, y mejores prácticas de código."
            email="guillermo.olvera@tvc.mx"
            country="México"
            province="MTY"
            brands='["Ubiquiti", "Draytek", "Utepo", "Tp-Link"]'
            certifications='{"Ubiquiti": "UEWA", "Draytek": "DISCOVERY", "Tp-Link": "OMA"}'
            skills='{"Ubiquiti UniFi": 5, "Draytek": 4, "Vivotek": 3, "Tp-Link Omada": 3}'>
        </profile-card>
        

        <?php
    }

    /*
        Esta version es mas simple y no incluye switch para expandir la información
        complementaria

        NO utiliza TailWind CSS ni FontAwesome
    */
    function wc_card_v5(){                
        view('instructors/web_components/card_v5.php');

        ?>

        <!-- Uso del componente -->
        <profile-card
            image-url="https://i.imgur.com/qwQQMGW.png"
            short-name="Guillermo Olvera"
            full-name="Juan Guillermo Olvera Maldonado"
            phone="81-8400-1777 #27363"
            specialty="REDES"
            position="Coordinador de Ingeniería"
            rating="5.0"
            description="Soy un desarrollador web con una amplia gama de conocimientos en <strong>muchos lenguajes front-end y back-end</strong>, marcos responsivos, bases de datos, y mejores prácticas de código."
            email="guillermo.olvera@tvc.mx"
            country="México"
            province="MTY"
            brands='["Ubiquiti", "Draytek", "Utepo", "Tp-Link"]'
            certifications='{"Ubiquiti": "UEWA", "Draytek": "DISCOVERY", "Tp-Link": "OMA"}'
            skills='{"Ubiquiti UniFi": 5, "Draytek": 4, "Vivotek": 3, "Tp-Link Omada": 3}'
        ></profile-card>

        <?php
    }

    function grid(){
        set_template('templates/tailwind.php');

        $personal = json_decode('[
            {
                "name": "Jorge Alberto Garza Tovar",
                "position": "Coordinator of Engineering",
                "brands": "Dahua",
                "certifications": "Access Zkteco (Security inspection line series ZKD, ZKX5000, ZKX6500, ZKX10080) video surveillance DHCA-VIS DHSA DHCA-ACS Transmission + DoLink Care",
                "lines_families": {
                    "dahua_cameras": 4,
                    "dahua_ip": 4,
                    "dahua_dvrs": 5,
                    "dahua_accessories": 4,
                    "dahua_access": 4,
                    "dahua_videoporteros": 5
                },
                "expertise": 4,
                "location": "MTY",
                "contact": "jorge.garza@tvc.mx",
                "phone": "81-84001777 #21421",
                "img_url": "1.jpeg"
            },
            {
                "name": "Gustavo Serrano Vazquez",
                "position": "Product Engineer",
                "brands": "Dahua",
                "certifications": "Certifications in DHCA-VIS DHSA DHCA-ACS",
                "lines_families": {
                    "dahua_cameras": 5,
                    "dahua_ip": 5,
                    "dahua_dvrs": 5,
                    "dahua_access": 4,
                    "dahua_videoporteros": 5
                },
                "expertise": 5,
                "location": "IZT",
                "contact": "gustavo.serrano@tvc.mx",
                "phone": "81-84001777 #28057",
                "img_url": "2.jpeg"
            },
            {
                "name": "Jonathan Martin Sifuentes Reynosa",
                "position": "Product Engineer",
                "brands": "Dahua",
                "certifications": "New staff",
                "lines_families": {
                    "dahua_access": 4,
                    "dahua_videoporteros": 5
                },
                "expertise": 4,
                "location": "MTY",
                "contact": "jonathan.sifuentes@tvc.mx",
                "phone": "81-84001777 #278115",
                "img_url": "3.jpeg"
            },
            {
                "name": "Manuel Alejandro Gutierrez Alvarez",
                "position": "Product Engineer",
                "brands": "Dahua",
                "certifications": "New staff",
                "lines_families": {
                    "dahua_access": 4,
                    "dahua_videoporteros": 5
                },
                "expertise": 4,
                "location": "MTY",
                "contact": "manuel.gutierrez@tvc.mx",
                "phone": "81-84001777 #279090",
                "img_url": "4.jpeg"
            },
            {
                "name": "Jennifer Olvera Hernandez",
                "position": "Product Engineer",
                "brands": "Dahua",
                "certifications": "Certifications in Transmission + DoLink Care DHSA DHCA-ACS",
                "lines_families": {
                    "dahua_networks": 5,
                    "dahua_alarms": 5,
                    "dahua_signage": 5
                },
                "expertise": 5,
                "location": "MTY",
                "contact": "jennifer.olvera@tvc.mx",
                "phone": "81-84001777 #231663",
                "img_url": "5.jpeg"
            },
            {
                "name": "Karen Esther Ontiveros Rojas",
                "position": "Product Engineer",
                "brands": "Dahua",
                "certifications": "Certifications in DHCA-VIS DHSA DHCA-ACS",
                "lines_families": {
                    "dahua_cameras": 4,
                    "dahua_ip": 5,
                    "dahua_dvrs": 5,
                    "dahua_accessories": 5,
                    "dahua_access": 4
                },
                "expertise": 5,
                "location": "MTY",
                "contact": "karen.ontiveros@tvc.mx",
                "phone": "81-84001777 #243213",
                "img_url": "6.jpeg"
            },
            {
                "name": "Karla Patricia Rocha Alonso",
                "position": "Product Engineer",
                "brands": "Dahua",
                "certifications": "Certifications in DHCA-VIS DHSA DHCA-ACS",
                "lines_families": {
                    "dahua_accessories": 3,
                    "dahua_access": 4
                },
                "expertise": 3,
                "location": "MTY",
                "contact": "karla.rocha@tvc.mx",
                "phone": "81-84001777 #27363",
                "img_url": "7.jpeg"
            },
            {
                "name": "Andrea Anette Celestino Castilla",
                "position": "Product Engineer",
                "brands": "Dahua",
                "certifications": "New staff",
                "lines_families": {
                    "dahua_accessories": 3,
                    "dahua_access": 3
                },
                "expertise": 3,
                "location": "MTY",
                "contact": "andrea.celestino@tvc.mx",
                "phone": "81-84001777 #260730",
                "img_url": "8.jpeg"
            },
            {
                "name": "Mario Gerardo Cruz Vargas",
                "position": "Product Engineer",
                "brands": "Dahua",
                "certifications": "New staff",
                "lines_families": {
                    "dahua_accessories": 3,
                    "dahua_access": 3
                },
                "expertise": 3,
                "location": "MTY",
                "contact": "mario.cruz@tvc.mx",
                "phone": "81-84001777 #270755",
                "img_url": "9.jpeg"
            },
            {
                "name": "Juan Guillermo Olvera Maldonado",
                "position": "Coordinator of Engineering",
                "brands": "Ubiquiti, Draytek, Tp-Link",
                "certifications": "Ubiquiti UEWA, UBWA Draytek: Discovery Tp-Link: OMADA",
                "lines_families": {
                    "ubiquiti_unifi": 5,
                    "ubiquiti_airmax": 4,
                    "draytek": 4,
                    "tp_link_omada": 3
                },
                "expertise": 4,
                "location": "MTY",
                "contact": "guillermo.olvera@tvc.mx",
                "phone": "81-84001777 #27663",
                "img_url": "10.jpeg"
            }
        ]', true);

        view('instructors/grid.php', compact('personal'));
    }

    function get_constants(){
        // Verificar si la extensión cURL está instalada
        if (!extension_loaded('curl')) {
            echo "La extensión cURL no está instalada.\n";
        }

        // Obtener todas las constantes definidas
        $constants = get_defined_constants(true);

        // Inicializar un array para almacenar las constantes de cURL
        $curl_constants = [];

        // Filtrar las constantes de la categoría 'curl'
        if (isset($constants['curl'])) {
            foreach ($constants['curl'] as $name => $value) {
                $curl_constants[$name] = $value;
            }
        } else {
            // Filtrar constantes con el prefijo 'CURL' si no están categorizadas
            foreach ($constants['user'] as $name => $value) {
                if (strpos($name, 'CURL') === 0) {
                    $curl_constants[$name] = $value;
                }
            }
        }

        // Usar var_export para exportar el array de constantes
        var_export($curl_constants);

    }

    function write_constants(){
        // Obtener todas las constantes definidas
        $constants = get_defined_constants(true);

        // Inicializar un array para almacenar las constantes de cURL
        $curl_constants = [];

        // Filtrar las constantes de la categoría 'curl'
        if (isset($constants['curl'])) {
            foreach ($constants['curl'] as $name => $value) {
                $curl_constants[$name] = $value;
            }
        } else {
            // Filtrar constantes con el prefijo 'CURL' si no están categorizadas
            foreach ($constants['user'] as $name => $value) {
                if (strpos($name, 'CURL') === 0) {
                    $curl_constants[$name] = $value;
                }
            }
        }

        // Verificar que el archivo haya devuelto el array
        if (is_array($curl_constants)) {
            // Ruta del archivo PHP a generar
            $filePath = 'D:\laragon\www\simplerest\packages\boctulus\api-client\src\Constants\Curl.php';
            
            // Abrir el archivo para escribir las definiciones
            $file = fopen($filePath, 'w');

            if ($file) {
                // Escribir la cabecera del archivo PHP
                fwrite($file, "<?php\n\n");

                // Recorrer el array asociativo y escribir las definiciones en el archivo
                foreach ($curl_constants as $name => $value) {
                    fwrite($file, "define('$name', $value);\n");
                }

                // Cerrar el archivo después de escribir
                fclose($file);

                echo "El archivo con las constantes ha sido generado correctamente en: $filePath\n";
            } else {
                echo "Error: No se pudo generar el archivo '$filePath'.\n";
            }
        } else {
            // Manejo de error si el archivo no devuelve el array esperado
            echo "Error: El archivo 'Curl.php' no devolvió un array válido.\n";
        }

    }

    function test_apiclient()
    {
        $url    = 'https://osx86project.org/';

        $client = new ApiClient($url);

        $res = $client->disableSSL()
            ->followLocations()
            // ->cache(5)
            ->get()
            ->getResponse();

        if ($res === null) {
            dd("RES is NULL");
            return;
        }
        
        dd([    
            $res
        ], 'RES');
    }

    function test_apiclient_fallback()
    {
        $url    = 'https://osx86project.org/';

        $client = new ApiClientFallback($url);

        $res = $client->disableSSL()
            ->followLocations()
            // ->cache(5)
            ->get()
            ->getResponse();

        if ($res === null) {
            dd("RES is NULL");
            return;
        }
        
        dd([    
            $res
        ], 'RES');
    }

    function test_apiclient_fallback_2()
    {
        $client = new ApiClientFallback();

        // Configurar la URL
        $client->setUrl('https://api.haulmer.dev/v2.0/partners/signature/generate');
        // $client->debug(true);

        // Headers
        $headers = [
            'apikey' => 'cebc90896c0445599e6d2269b9f89c8f',
            'Content-Type' => 'application/json'
        ];

        // Body (como array para que se encodee automáticamente)
        $body = [
            "period" => 1,
            "email" => "email@correo.com"
        ];

        // Configurar el cuerpo y habilitar encoding
        $client->setBody($body, true); // Segundo parámetro = encode_body = true

        // Hacer la petición POST (sin pasar $body nuevamente)
        $response = $client->post(null, $body, $headers); // <- ¡Body viene de setBody()!

        // Debug
        dd("STATUS: " . $response->status());
        dd("ERROR: " . ($response->error() ?? 'Ninguno'));
        
        dd($response->data());
        
        dd([    
            $response
        ], 'RES');
    }

    function jsonplaceholder_test_get(){
        $client = new ApiClientFallback();

        $client
        ->setHeaders([
            "Content-type" => "application/json"
        ])
        ->get("https://jsonplaceholder.typicode.com/posts/1");

        $res = $client->getResponse();

        dd($client->status(), 'STATUS');
        dd($client->error(), 'ERROR');
        dd($client
        ->decode()
        ->data(), 'DATA');
    }

    function jsonplaceholder_test_post(){
        $client = new ApiClientFallback();

        $body = [
            "title" => "foo",
            "body" => "bar",
            "userId" => 999
        ];
        
        $client
        ->setHeaders([
            "Content-type" => "application/json"
        ])
        ->setBody($body)
        ->post("https://jsonplaceholder.typicode.com/posts");

        $res = $client->getResponse();

        // dd($client->status(), 'STATUS');
        // dd($client->error(), 'ERROR');
        dd($client
        //->decode()
        ->data(), 'DATA');
    }

    function test_apiclient_fallback_cache()
    {
        $url    = base_url() . '/dumb/now';

        $client = new ApiClientFallback($url);

        $res = $client->disableSSL()
            ->followLocations()
            ->cache(3)
            ->get()
            ->getResponse(false);

        if ($res === null) {
            dd("RES is NULL");
            return;
        }

        if ($res['http_code'] != 200) {
            dd("HTTP CODE is " . $res['http_code']);
            return;
        }

        $html = $res['data'];

        dd([
            'realtime' => file_get_contents($url),
            'cached'   => $html
        ]);
    }

    

}   // end class
