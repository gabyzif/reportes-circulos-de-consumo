<?php
    /*
     Plugin Name: Reportes Circulos de Consumo
     description: Tabla relación circulos de consumo / producto
     a plugin to create awesomeness and spread joy
     Version: 0.1
     Author: Gabriela
     License: GPL2
     */

    defined ('ABSPATH') or die ('Hey, you can/t be here!');
    
    add_action("admin_menu", "addMenu");

   
 
    function incluir_javascript() {
 
        wp_register_script('validation_script', plugins_url('js/script.js', __FILE__), array('jquery'),'3.3.1', true); 
        wp_enqueue_script('validation_script');

        wp_register_script('tableHTMLExport', plugins_url('js/tableHTMLExport.js', __FILE__), array('jquery'),'3.3.1', true); 
        wp_enqueue_script('tableHTMLExport');

        $plugin_url = plugin_dir_url( __FILE__ );
        wp_enqueue_style( 'style',  $plugin_url . "/css/style.css");



        }
          
        add_action( 'admin_enqueue_scripts', 'incluir_javascript' );

    function initFunction (){
        
    }

    function addMenu()
    {
        //page-title, main-menu-title, capability, menu slug, function
        add_menu_page("Reportes Circulos de Consumo", "Reportes Circulos de Consumo", 4, "reportes-circulos", "reportesCirculos" );    
       add_submenu_page("reportes-circulos", "Productos Totales", "Productos Totales", 4, "productos-totales", "productosTotales");
    }
    
    

    function reportesCirculos()
    {

        
        echo <<< 'EOD'

        
        <h1> Reportes de Productos / Círculos de Consumo</h1>
        <h3> Hacé click en EXPORTAR para obtener un archivo excel (.xls) con los productos y cantidades por círculo de consumo</h3>

        EOD;

        if ( current_user_can( 'edit_posts' ) ) {
        //           

            echo <<< 'EOD'

            <button class="xlsx"> Exportar a excel (.xlsx) </button> 
            <button class="xls"> Exportar a excel (.xls) </button> 
            <button class="cvs"> Exportar a excel (.cvs) </button> 
 
            


             <table class="active tableCirculosDeConsumo" id="circulosTable">
                <tr>
                   <th>Id</th>
                   <th>Producto</th>
                   <th>Cantidad</th>
                   <th>Circulos de consumo</th>
                   <th>Fecha</th>
                </tr>
                       
            EOD;

            dataBaseToTableReportA();

        }


        echo  "</table>";
      
    }

    function productosTotales(){
        echo <<< 'EOD'

        
        <h1> Reportes de Productos / Círculos de Consumo</h1>
        <h2>Aquí podras ver la cantidad de productos totales por círculo de consumo</h2>
        <h3> Hacé click en EXPORTAR para obtener un archivo excel (.xls) con los productos y cantidades por círculo de consumo</h3>

        EOD;

        if ( current_user_can( 'edit_posts' ) ) {
            //           
    
                echo <<< 'EOD'
    
                <button class="xlsx"> Exportar a excel (.xlsx) </button> 
                <button class="xls"> Exportar a excel (.xls) </button> 
                <button class="cvs"> Exportar a excel (.cvs) </button> 
     
                                
    
    
                 <table class="active tableCirculosDeConsumo" id="productsTable">
                    <tr> 
                       <th>Producto</th>
                       <th>Circulos de consumo</th>
                       <th>Cantidad</th>

                    </tr>
                           
                EOD;
    
                consultaProductos();
    
            }
    
    
            echo  "</table>";
    }
    
  
    function dataBaseToTableReportA(){

        ?>
            

       
        <?php
        $filters = array(
            'post_status' => 'any',
            'post_type' => 'shop_order',
            'posts_per_page' => 200,
            'paged' => 1,
          
        );

        $loop = new WP_Query($filters);

        while ($loop->have_posts()) {
            $loop->the_post();
            $order = new WC_Order($loop->post->ID);
        
            $order_date = $order->order_date;

           //var_dump($order->get_items()) ;
            foreach ($order->get_items() as $key => $lineItem) {
                
                $orderId= $lineItem['order_id'];

                if ($orderId<>0){

                $order = wc_get_order($orderId);
                //foreach( $order->get_items() as $item_id => $item ){
            
                    $product_name  = $lineItem['name']; // The product name
                    $item_qty      = $lineItem['quantity']; // The quantity 
                    $item_id = $lineItem['order_id'];
                    $get_circulo_de_consumo = get_post_meta($orderId, '_billing_circulo_de_consumo', false);
                    $circulo_de_consumo=$get_circulo_de_consumo[0];
                   

                    if ($circulo_de_consumo<>""){

                         

                        
                        ?>
            
                        <tr>
                        <td> <?= $item_id; ?> </td>
                        <td> <?= $product_name?></td>
                        <td><?= $item_qty?></td>
                        <td><?= $circulo_de_consumo?></td>
                        <td><?= $order_date?></td>
                        </tr>

                       
                        <?php
                       
                    }



                   
                 
                //} 
            }
                

                
            }
        }

    } 

    function consultaProductos(){
        global $wpdb;
        $productosCirculosDeConsumo = $wpdb->get_results("SELECT `meta_value` AS `Círculo_de_Consumo` , `order_item_name` AS `Productos`, COUNT(*) AS `Cantidad`
        FROM `JhIHoBMzpostmeta`
        INNER JOIN `JhIHoBMzwoocommerce_order_items` 
        ON `post_id` = `order_id` 
        WHERE `meta_key` = '_billing_circulo_de_consumo'
        AND `order_item_type`= 'line_item'
        GROUP BY `meta_value`, `order_item_name`");


        //var_dump($productosCirculosDeConsumo) ;

        foreach ( $productosCirculosDeConsumo as $row ) {

            ?>
            
            <tr>
            <td> <?= $row->Productos; ?> </td>
            <td> <?= $row->Círculo_de_Consumo;?></td>
            <td><?= $row->Cantidad;?></td>
          
            </tr>

           
            <?php
            
        }
        
        
    }

