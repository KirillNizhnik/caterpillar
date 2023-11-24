<?php

class UsedEquipmentFamilyImport{
    private string $taxonomy = 'used-family';
    private ImporterHandler $importerHandler;
    private array $categories_to_import = array(
        'Construction Equipment' => array(
            'children' => array(
                'Articulated and Off-Hightway Trucks' => array(
                    'children' => array(
                        'Articulated Trucks' => array(),
                        'Off-Highway Trucks' => array(),
                    ),
                ),
                'Asphalt Pavers' => array(
                    'children' => array(
                        'Screeds' => array(),
                        'Track Asphalt Pavers' => array(),
                        'Wheel Asphalt Pavers' => array(),
                    ),
                ),
                'Backhoe Loaders' => array(),
                'Cold Planer' => array(),
                'Compactors' => array(
                    'children' => array(
                        'Utility Compactors' => array(),
                        'Pneumatic Rollers' => array(),
                        'Tandem Vibratory Rollers' => array(),
                        'Vibratory Soil Compactors' => array(),
                        'Landfill compactors' => array(),
                        'Waste Compactors' => array(),
                    ),
                ),
                'Track Type Tractors / Dozers' => array(
                    'children' => array(
                        'Large Dozers' => array(),
                        'Medium Dozers' => array(),
                        'Small Dozers' => array(),
                    ),
                ),
                'Wheeled Dozer' => array(
                    'children' => array(
                        'Wheeled Dozer' => array(),
                    ),
                ),
                'Hydraulic Mining Shovels' => array(),
                'Excavators' => array(
                    'children' => array(
                        'Large Excavators' => array(),
                        'Medium Excavators' => array(),
                        'Mini Excavators' => array(),
                        'Small Excavators' => array(),
                        'Wheel Excavators' => array(),
                    ),
                ),
                'Material Handlers' => array(
                    'children' => array(
                        'Track Material Handlers' => array(),
                        'Wheel Material Handlers' => array(),
                    ),
                ),
                'Motor Graders' => array(),
                'Road Reclaimers' => array(),
                'Skid Steer And Compact Track Loaders  ' => array(
                    'children' => array(
                        'Compact Track Loaders' => array(),
                        'Skid Steer Loaders' => array(),
                    ),
                ),
                'Telehandlers' => array(),
                'Track Loaders' => array(),
                'Wheel Loaders' => array(
                    'children' => array(
                        'Compact Wheel Loaders' => array(),
                        'Large Wheel Loaders' => array(),
                        'Medium Wheel Loaders' => array(),
                        'Small Wheel Loaders' => array(),
                    ),
                ),
                'Wheel Tractor-Scrapers' => array(),
                'TRAILERS' => array(),
                'WATER TRUCKS' => array(),
                'OTHER' => array(),
            ),
        ),
        'Agricultural Equipment' => array(
            'children' => array(
                'Tractors' => array(),
                'Hay Equipment' => array(
                    'children' => array(
                        'Balers' => array(),
                        'Self Propelled Windrower' => array(),
                    ),
                ),
                'Sprayers' => array(
                    'children' => array(
                        'Turbo Mist Standard Duty Sprayers' => array(),
                        'Turbo Mist Sprayer Attachments' => array(),
                        'Turbo Mist Light Duty Spray Series' => array(),
                        'Turbo Mist Heavy Duty Spray Series' => array(),
                    ),
                ),
                'Nut Harvesting Equipment' => array(),
            ),
        ),
        'Power Systems' => array(
            'children' => array(
                'MOBILE GENERATOR SETS' => array(),
                'SYSTEMS / COMPONENTS' => array(),
            ),
        ),
        'Parts' => array(
            'children' => array(
                'PARTS' => array(),
            ),
        ),
    );

    public function __construct(ImporterHandler $importerHandler)
    {
        $this->importerHandler = new ImporterHandler();
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_import_used_categories_action', array($this, 'importUsedFamAjax'));
        add_action('wp_ajax_nopriv_import_used_categories_action', array($this, 'importUsedFamAjax'));

    }


    public function importUsedFamAjax(): void
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 2400);
        $this->importerHandler->importCategories($this->categories_to_import, $this->taxonomy);
        $response = array('success' => true, 'data' => 'Done');
        wp_send_json($response);
        wp_die();
    }
}
new UsedEquipmentFamilyImport(new ImporterHandler());
