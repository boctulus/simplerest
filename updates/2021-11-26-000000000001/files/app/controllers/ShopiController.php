<?php

namespace simplerest\controllers;

use simplerest\core\Controller;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\libs\Factory;
use simplerest\libs\DB;
use simplerest\libs\Files;
use simplerest\libs\OAuth;
use simplerest\libs\Strings;
use simplerest\libs\Url;

use Automattic\WooCommerce\Client;
use simplerest\libs\ProductParser;

class ShopiController extends Controller
{
    public $prefix_provider = null;

    // post 
    function arrange_product(Array $product){        
        $rec = [];

        $rec["handle"] = $product['slug']; 
        $rec["title"] = $product['name'];
        $rec["body_html"] = $product['description'];
        $rec["status"] = 'active';

        if (!empty($product['tags'])){
            $rec['tags'] = implode(', ', json_decode($product['tags'], true));
        }

        // Variantes

        $variant = [
            "title" => "Default Title",
            "inventory_policy" => "deny",
            "option1" => "Default Title"
        ];

        if (isset($product["sku"])){
            $variant["sku"] = $product['sku'];
        }

        if (isset($product["price"])){
            $variant["price"] = (string) $product['price'];
        }

        if (isset($product["compare_at_price"])){
            $variant["compare_at_price"] = (string) $product['prev_price'];
        }

        $rec['variants'][] = $variant;


        $images = json_decode($product['images'], true);

        for ($im=0; $im<count($images); $im++){
            $rec["images"][] = $images[$im];
        }               

        $ret = ['product' => $rec];
    

        return $ret;
    }

    function post_products(){
        $api_key    = '879023de21f5d5c2f55a5718533a20f7';
        $api_secret = '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';
        $api_ver    = '2021-07';
        $shop       = 'vendor-xyz';

        $products = DB::table('products')
        ->whereNull('remote_id')
        ->get();

        foreach ($products as $product)
        {
            $p = $this->arrange_product($product);

            $endpoint = "https://$api_key:$api_secret@{$shop}.myshopify.com/admin/api/$api_ver/products.json"   ;
    
            try {
                $res = Url::consume_api($endpoint, 'POST', $p, [
                    $api_key => $api_secret
                ]);

                if (isset($res['error'])){
                    throw new \Exception($res['error'], 1);                    
                }

                $data = $res['data'];

                $product_id = $data['product']['id'];
                
                DB::table('products')
                ->where([ 'id' => $product['id'] ])
                ->update([
                    'remote_id' => $product_id
                ]);

                dd("Actualizando {$product['slug']} com id = $product_id");

            } catch (\Exception $e){
                $err = "SKU {$product['sku']} - " . $e->getMessage();
                dd($err);
                Files::logger($err, 'fails.txt');
            }
        }       
    }

    function generate_csv($filename = null, $where = null)
    {
        if ($filename == null){
            $filename = ETC_PATH . 'products.csv';
        }

        $sep = ",";
        $ret = "\r";    

        $m = DB::table('products');

        if ($where != null){
            $m->where($where);
        }

        $products = $m
        ->get();

        if (empty($products)){
            throw new \Exception("No records found");
        }

        $headers = array (
            0 => 'Handle',
            1 => 'Title',
            2 => 'Body (HTML)',
            3 => 'Vendor',
            4 => 'Type',
            5 => 'Tags',
            6 => 'Published',
            7 => 'Option1 Name',
            8 => 'Option1 Value',
            9 => 'Option2 Name',
            10 => 'Option2 Value',
            11 => 'Option3 Name',
            12 => 'Option3 Value',
            13 => 'Variant SKU',
            14 => 'Variant Grams',
            15 => 'Variant Inventory Tracker',
            16 => 'Variant Inventory Qty',
            17 => 'Variant Inventory Policy',
            18 => 'Variant Fulfillment Service',
            19 => 'Variant Price',
            20 => 'Variant Compare At Price',
            21 => 'Variant Requires Shipping',
            22 => 'Variant Taxable',
            23 => 'Variant Barcode',
            24 => 'Image Src',
            25 => 'Image Position',
            26 => 'Image Alt Text',
            27 => 'Gift Card',
            28 => 'SEO Title',
            29 => 'SEO Description',
            30 => 'Google Shopping / Google Product Category',
            31 => 'Google Shopping / Gender',
            32 => 'Google Shopping / Age Group',
            33 => 'Google Shopping / MPN',
            34 => 'Google Shopping / AdWords Grouping',
            35 => 'Google Shopping / AdWords Labels',
            36 => 'Google Shopping / Condition',
            37 => 'Google Shopping / Custom Product',
            38 => 'Google Shopping / Custom Label 0',
            39 => 'Google Shopping / Custom Label 1',
            40 => 'Google Shopping / Custom Label 2',
            41 => 'Google Shopping / Custom Label 3',
            42 => 'Google Shopping / Custom Label 4',
            43 => 'Variant Image',
            44 => 'Variant Weight Unit',
            45 => 'Variant Tax Code',
            46 => 'Cost per item',
            47 => 'Status'
        );

        $categos = DB::table('product_categories')
        ->select(['id', 'name'])
        ->get();

        $recs = [];
        foreach ($products as $product){
            
            $rec = [];

            foreach ($headers as $h){
                $rec[$h] = '';
            }

            $images = json_decode($product['images'], true);

            $rec['Handle'] = $product['slug'];
            $rec['Title'] = $product['name'];
            $rec['Body (HTML)'] = $product['description'];

            // primera imágen
            if (count($images)>0){
                $rec['Image Src'] = $images[0]['src'];
            }
            
            if (!empty($product['tags'])){
                $rec['Tags'] = implode(', ', json_decode($product['tags'], true));
            }
                        
            $num_categos = json_decode($product['categories'], true);
            
            $collections = [];
            foreach ($num_categos as $nc){
                $found = false;
                foreach ($categos as $catego){
                    if ($catego['id'] == $nc){
                        $cl = $catego['name'];
                        $found = true;
                        break;
                    }
                }
                
                if ($found){
                    $collections[] = $cl;
                }
            }
            
            // No es posible subir más de una colección por producto via CSV

            if (!empty($collections)){
                $rec['Collection'] = $collections[0];
            }
            

            //$rec['Published'] = 'false';
            $rec['Status'] = 'draft';

            $rec['Option1 Name'] = 'Title';
            $rec['Option1 Value'] = 'Default Title';
            $rec['Variant SKU'] = $product['sku'];
            $rec['Variant Inventory Qty'] = '0';
            $rec['Variant Fulfillment Service'] = 'manual';  //  ???
            $rec['Variant Price'] = $product['price'];
            $rec['Variant Compare At Price'] = $product['prev_price'] ?? ''; 
            $rec['Variant Requires Shipping'] = 'true';  //  ???
            $rec['Variant Taxable'] = 'true'; //  ???
            $rec['Variant Grams'] = '0.0';
            $rec['Variant Weight Unit'] = 'kg';
            $rec['Variant Inventory Policy'] = 'deny';  //  ???

            $recs[] = $rec;

            // resto de imágenes
            if (count($images)>1){
                for ($im=1; $im<count($images); $im++){
                    foreach ($rec as $k => $v){
                        if ($k != 'Handle'){
                            $rec[$k] = '';
                        }
                    }

                    $rec['Image Src'] = $images[$im]['src'];
                    $recs[] = $rec;
                }                
            }
        }


        $headers = array_keys($recs[0]);

        $fp = fopen($filename, 'w');
        fputcsv($fp, $headers);

        foreach ($recs as $campos) {
            fputcsv($fp, $campos);
        }

        fclose($fp);      
    }

    /*
        Modifica names y agrega alts a imágenes,... 
    */
    function alter_product(Array $product){
        $img_cnt = count($product['images']);
        for ($j=0; $j<$img_cnt; $j++){
            //$name_ori = $product['images'][$j]['name'];

            $product['images'][$j]['name'] = null;
            $cnt = strlen($product['slug']);
            for ($i=0; $i<$cnt; $i++){
                if (!is_numeric($product['slug'][$i])){
                    $product['images'][$j]['name'] .= $product['slug'][$i];
                }
            }

            $product['images'][$j]['name'] = preg_replace("/(-)\\1+/", "$1", $product['images'][0]['name']);

            $path = parse_url($product['images'][$j]['src'], PHP_URL_PATH);
            $fs = explode('/', $path);
            $filename = $fs[count($fs)-1];

            $fs = explode('.', $filename);
            $name = str_replace('-', ' ',$filename = $fs[0]);
            $product['images'][$j]['alt'] = trim($name);

            //if ($product['images'][$j]['name'] == $product['slug']){
            //    $product['images'][$j]['name'] = $name_ori;
            //}
        }


        if ($product['description'] == strtoupper($product['description'])){
            $product['description'] = strtolower($product['description']);
        }

        foreach($product['images'] as $k => $img){
            if ($img['alt'] == 'placeholder'){
                $product['images'][$k]['alt']  = 'imágen no disponible';
                $product['images'][$k]['name'] = 'sin-imagen'; 
                $product['images'][$k]['src']  = 'https://saboreateycafe.com/wp-content/uploads/2021/06/imagen-no-disponible-187.jpg';
            }    
        }

        $product['description'] = str_replace('*', '', $product['description']);

        return $product;
    }

    // Válido solo para WooCommerce
    function post_all($resend = false, $offset = 0, $limit = null){
        $m = DB::table('products');

        if ($offset){
            $m->offset($offset);
        }

        if ($limit){
            $m->limit($limit);
        }

        $products = $m    
        ->get();


        $inserts = 0;
        foreach ($products as $product){
            if (!isset($product['sku']) || $product['sku'] == null){
                throw new \Exception("SKU no definido para ". $product['id']);
            }

            $posted = DB::table('products')->where(['sku' => $product['sku']])->value('posted');
            
            if (!$resend){
                if ($posted){
                    dd("skipped {$product['sku']}");
                    continue;
                } 
            }               

            $product_id = $product['id'];

            unset($product['id']);
            unset($product['url_ori']);
            unset($product['comment']);
            unset($product['created_at']);
            unset($product['updated_at']);

            if (isset($product['tags']) && !empty($product['tags'])){
                $product['tags'] = json_decode($product['tags'], true);

                foreach($product['tags'] as $key => $name){
                    $product['tags'][$key] = [
                        'name' => $name
                    ];
                }
            }

            $product['images']     = !empty($product['images']) ? json_decode($product['images'], true) : []; 
            $product['categories'] = !empty($product['categories']) ? json_decode($product['categories'], true) : [];


            $ok    = null;
            $tries = 0;

            while (!$ok /* && $tries<7 */){
                $tries++;

                dd("Intento: $tries");

                try {
                    $res = $this->woocommerce->post('products', $product);
                    $ok  = DB::table('products')->where(['sku' => $product['sku']])->update(['posted' => 1]);
                    $inserts++;
                    dd("Producto con SKU {$product['sku']} fue insertado luego de $tries intentos");
                } catch (\Exception $e){
                    dd($product, "Product with id=$product_id");
                    dd($e->getMessage(), 'Error');       
                    Files::logger("product_id=$product_id: ". strip_tags($e->getMessage()), 'fails.txt');

                    // Si está duplicado
                    if ($product['sku'] != null && Strings::contains('[product_invalid_sku]', $e->getMessage())){
                        $ok  = DB::table('products')->where(['sku' => $product['sku']])->update(['posted' => 1]);
                        continue 2;
                    }

                    if (!Strings::contains('[woocommerce_product_invalid_image_id]', $e->getMessage())){
                        continue 2;
                    }
                }
            }
    
        }
        
        dd($inserts, "Inserts exitosos");
    }

    /*
        Parsea todo un sitio y almacena localemente el resultado

        P -> L
    */
    function process(){
        $this->write_all_no_catego();

        $categos =  DB::table('product_categories')
        ->get();       

        foreach ($categos as $cat){
            $url    = $cat['url'];
            $cat_id = $cat['id']; 
            $pages  = $cat['pages'];

            $this->write_all_pages($url, [ $cat_id ], $pages);
        }
    }


     /*
        Guarda todos los productos *sin* categoría

        P -> L
    */
    function write_all_no_catego()
    {
        $url = 'https://letotoncasa.com.ar/catalogo.php?buscar=';

        $co = ProductParser::parseCatego($url, 'all_products');
        $pages = $co['pages'];

        for ($p=1;$p<=$pages;$p++){
            $url_list = Url::appendQueryParam($url, 'page', $p);
            $this->write_list($url_list, [], $this->prefix_provider);
        }
    }

    function write_all_pages(string $url_list, Array $cats, int $pages = null){
        if ($pages == null){
            throw new \Exception("Param pages is required");
        }

        for ($p=1;$p<=$pages;$p++){
            $this->write_list(Url::appendQueryParam($url_list, 'page', $p), $cats);
        }
    }

    function write_list(string $url_list, Array $categories){
        $product_urls = ProductParser::parseList($url_list);
        dd($url_list, 'PRODUCTO LIST');
        //dd($product_urls);
        //dd(count($product_urls), 'urls count');

        foreach($product_urls as $url){
            try {
                dd($url, 'url'); 
                                
                $product = ProductParser::parse($url);
                //$product = $this->alter_product($product);

                if (isset($product['alt']) && $product['alt'] == $product['sku']){
                    $product['alt'] = null;
                }

                $product['sku'] = (!empty($this->prefix_provider) ? "$this->prefix_provider-" : '') .$product['sku'];
                $product['status'] = "draft";
                $product['url_ori'] = $url;

                foreach($categories as $cat_id){
                    $product['categories'][] = $cat_id; 
                }

                //dd($product);
                $id = $this->write_product($product);
            } catch (\Exception $e){
                Files::logger("$url\tfails with: ". strip_tags($e->getMessage()), 'fails.txt');
                throw new \Exception($e->getMessage());
            }            
        }
    }


    private function write_product(Array $product) 
    {
        if (isset($product['tags']) && !empty($product['tags'])){
            foreach($product['tags'] as $key => $name){
                $product['tags'][$key] = [
                    'name' => $name
                ];
            }
        }

        $row = DB::table('products')
        ->where(['slug' => $product['slug']])
        ->getOne();

        $update = false;
        if (!empty($row)){
            if (empty($row['categories'])){
                $row_categos = [];
            } else {
                $row_categos = json_decode($row['categories'], true);
            }

            $categos     = $product['categories'] ?? [];

            foreach ($categos as $c){
                if (!in_array($c, $row_categos)){
                    $row_categos[] = $c;
                    $update = true;
                }
            }
        }

        if ($update){
            $product['categories'] = $row_categos; 
        }

        $product['images'] = isset($product['images']) ? json_encode($product['images']) : null;
        $product['categories'] = isset($product['categories']) ? json_encode($product['categories']) : null;
        $product['tags'] = isset($product['tags']) ? json_encode($product['tags']) : null;
        $product['dimensions'] = isset($product['dimensions']) ? json_encode($product['dimensions']) : null;
        $product['attributes'] = isset($product['attributes']) ? json_encode($product['attributes']) : null;

        if ($update){
            DB::table('products')
            ->find($row['id'])
            ->update([
                'categories' => $product['categories']
            ]);

        } else {
            $id = DB::table('products')->create($product, true);
        }
        
        return $id ?? null;
    }

    /*
        Salva la categoría padre de una categoría a partir de otra info en la tabla

        L -> L
    */
    function make_catego_hierarchy(){
        $categos = DB::table('product_categories')
        ->get();
        
        foreach ($categos as $c){
            for ($i=0; $i<count($categos); $i++){
                if ($categos[$i]['url'] == $c['parent_url']){
                    DB::table('product_categories')
                    ->find($c['id'])
                    ->update(['parent_id' => $categos[$i]['id']]);
                }
            }
        }
    }

    /*
        Salva localmente las categorías del sitio a parsear

        P -> L
    */    
    function save_categos()
    {
        $m = DB::table('product_categories');

        $url = 'https://letotoncasa.com.ar';        
        $categos = ProductParser::parseCategoList($url);

        foreach ($categos as $k => $catego){
            $slug = $catego['slug'];
            $uri  = $catego['url'];

            // recupera número de páginas y podría recuperar el parent
            $ca   = ProductParser::parseCatego($url, $slug);
            $catego = $catego + $ca;
            //dd($catego);

            $id_catego = $m->create($catego, true);
            //dd($catego, "ID= $id_catego");
        }
    }


    /*
        L -> R

    */
    function post_categos()
    {
        $api_key    = '879023de21f5d5c2f55a5718533a20f7';
        $api_secret = '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';
        $api_ver    = '2021-07';
        $shop       = 'vendor-xyz';

        $endpoint = "https://$api_key:$api_secret@{$shop}.myshopify.com/admin/api/$api_ver/custom_collections.json";

        $categos = DB::table('product_categories')
        ->whereNull('remote_id')
        ->get();

        dd($categos);

        foreach ($categos as $k => $cat){
           dd($cat, $k);

           $dato = [
                "custom_collection" => [
                    "title"  => $cat["name"],
                    //"handle" => $cat["slug"]
                ]
            ];  

            $res = Url::consume_api($endpoint, 'POST', $dato, [
                $api_key => $api_secret
            ]);

            //dd($res, 'RES');

            if ($res['http_code'] >= 300){
                dd($res['error'], 'ERROR');
                Files::logger("Error al crear categoría {$cat['slug']}. Detalle: {$res['error']}");
                continue;
            }
    
            $data = $res['data'];

            $id   = $data["custom_collection"]["id"];
            $slug = $data["custom_collection"]["handle"];

            $r = [
                'remote_id' => $id,
                'slug' => $slug
            ];

            dd($r, 'UPDATING');

            $ok = DB::table('product_categories')
            ->find($cat['id'])
            ->update($r);

            if (!$ok){
                dd($cat, "Error al crear categoría");
                Files::logger("Error actualizar datos de categoría {$cat['slug']}");
            }
        }

    }

    function get_collects(){
        $products = DB::table('products')
        ->get();

        $k = [];
        foreach ($products as $p){
            $categos = json_decode($p['categories'], true);

            foreach ($categos as $local_cat_id){
                $remote_cat_id = DB::table('product_categories')
                ->find($local_cat_id)
                ->value('remote_id');

                $k[$remote_cat_id][] = ["product_id" => (int) $p['remote_id']];
            }
        }

        return $k;
    }

     /*
        L -> R

    */
    function patch_categos()
    {
        $k = $this->get_collects();

        foreach ($k as $cat_id => $collects){
            $cat_id = (int) $cat_id;

            $dato = [
                "custom_collection" => [
                    "collects" => $collects
                ]
            ];  

            //dd($dato, "CAT ID=$cat_id");
    
            $endpoint = "https://3a6e4a6815e1c18191009674bf4d3e2b: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@todo-para-la-casa-1.myshopify.com/admin/api/2021-07/custom_collections/$cat_id.json";
    
    
            $res = Url::consume_api($endpoint, 'PUT', $dato, [
                '3a6e4a6815e1c18191009674bf4d3e2b' => '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'
            ]);
    
            dd($res["data"], 'RES');
    
            if ($res['http_code'] >= 300){
                dd($dato, "ERROR con CAT ID=$cat_id. Detalle: {$res['error']}");
                Files::logger("Error al hacer PATCH a categoría $cat_id. Detalle: {$res['error']}");
                continue;
            }
        
        }
        
    }


    /*
        Actualiza ids de la copia local para que se sincronice con de los productos publicados en el servidor

        Adicionalmente elimina duplicados

        R -> L
    */
    function update_product_ids_by_sku(){
        $endpoint = 'https://3a6e4a6815e1c18191009674bf4d3e2b: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@todo-para-la-casa-1.myshopify.com/admin/api/2021-07/products.json';

        $limit    = 100;
        $last_id  = 0;
        $max      = null;
        $query_fn = function($limit, $last_id){ return "limit=$limit&since_id=$last_id"; };

        $regs  = [];

        $count = 0;
        while(true){
            if (isset($max) && !empty($max) && ($count >= $max/$limit)){
                break;
            }
    
            //dd("Q=" . $query_fn($limit, $last_id));

            // cache

            $file = ETC_PATH . "req-" . $query_fn($limit, $last_id) . "-products.json";

            #if (file_exists($file)){
            #    $data = json_decode(file_get_contents($file), true);
            #} else {   
            #    dd("Making request");

                $res = Url::consume_api("$endpoint?".$query_fn($limit, $last_id), 'GET', NULL, [
                    '3a6e4a6815e1c18191009674bf4d3e2b' => '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'
                ]);
        
                if ($res['http_code'] != 200){
                    dd($res['error'], 'ERROR', function(){ die; });
                }
        
                $data     = $res['data']; 

            #    $content = json_encode($data);
            #    file_put_contents($file, $content);
            #}                

            
            $products = $data["products"];

            $last_id = max(array_column($products, 'id'));
    
            foreach ($products as $product){
                $sku_arr    = array_column($product["variants"], 'sku');
                $id_product = $product['id'];
                $dupe = false;

                $cnt = count($regs);
                if (!empty($sku_arr[0]) && $cnt > 0){
                    for($i=0; $i<$cnt; $i++){                    
                        if ($regs[$i]['sku'][0] == $sku_arr[0]){
                            dd("Deleting dupe para SKU=". $sku_arr[0]. "\t". "id=". $regs[$i]['id']);
                            Files::logger($sku_arr[0]. ",". $regs[$i]['id'], "dupes.txt");

                            $res = Url::consume_api("https://3a6e4a6815e1c18191009674bf4d3e2b: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@todo-para-la-casa-1.myshopify.com/admin/api/2021-07/products/$id_product.json", 'DELETE', NULL, [
                                '3a6e4a6815e1c18191009674bf4d3e2b' => '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'
                            ]);            

                            $dupe = true;
                            break;
                        }
                    }
                }
                
                if ($dupe){
                    continue;
                }

                $r = [
                    'id'  => $id_product,
                    'sku' => $sku_arr
                ];
                
                if (empty($id_product)){
                    throw new \Exception("Invalid remote id_product = $id_product for SKU = {$sku_arr[0]}");            
                }

                dd($r, "Updating");

                DB::table('products')
                ->where([ 'sku' => $sku_arr[0] ])
                ->update([
                    'remote_id' =>  $id_product
                ]);


                $regs[] = $r;                
            }
            
            if (count($products) != $limit){
                break;
            }

            $count++;
        }

    }


    function set_status($status = 'active', $remote_id_product = null){
        if (!in_array($status, ['draft', 'archived', 'active'])){
            throw new \Exception("Status $status is invalid");
        }

        if ($remote_id_product != null){
            $ids = [ $remote_id_product ];
        } else {
            $ids = DB::table('products')
            ->whereNotNull('remote_id')
            ->pluck('remote_id');
        }

        foreach ($ids as $id){
            if (empty($id)){
                throw new \Exception("Remote id can not be null");
            }

            $endpoint = "https://3a6e4a6815e1c18191009674bf4d3e2b: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@todo-para-la-casa-1.myshopify.com/admin/api/2021-07/products/$id.json";

            $p = [
                "product" => [
                    "status" => $status
                ]
            ];

            $res = Url::consume_api($endpoint, 'PATCH', $p, [
                '3a6e4a6815e1c18191009674bf4d3e2b' => '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'
            ]);

            if (!empty($res['error'])){
                dd($res['error'], "ERROR para remote_product_id = $id");
                Files::logger("ERROR para remote_product_id = $id. Detalle: {$res['error']}");
                die;
            }

            if ($res['data']['product']['status'] != $status){
                dd($res['error'], "ERROR para remote_product_id = $id. Detalle: el status no se ha actualizado correctamente");
                Files::logger("ERROR para remote_product_id = $id. Detalle: el status no se ha actualizado correctamente");
            }
        }        
        
    }


   
}

