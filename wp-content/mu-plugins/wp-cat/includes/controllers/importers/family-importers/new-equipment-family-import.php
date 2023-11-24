<?php

class NewEquipmentFamilyImport{

    private string $taxonomy = 'family';
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
                'Attachments' => array(
                    'children' => array(
                        'Adapters' => array(
                            'children' => array(
                                'Skid Steer/Mini Hex  Adapters' => array(),
                            ),
                        ),
                        'Augers' => array(),
                        'Backhoe attachment' => array(),
                        'Bale Grabs' => array(),
                        'Bale Spears' => array(),
                        'Dozer Blades' => array(
                            'children' => array(
                                'Skid Steer Blades' => array(),
                                'Skid Steer Angle Blades' => array(),
                                'Skid Steer Box Blades' => array(),
                                'Compact Track Loader Blades' => array(),
                                'Compact Track Loader Angle Blades' => array(),
                                'Compact Track Loader Box Blades' => array(),
                                'Dozer Coal U-Blades' => array(),
                                'Cushion Dozer Blades' => array(),
                                'Dozer Grader Blades' => array(),
                                'Dozer Landfill U-Blades' => array(),
                                'Dozer Reclamation U-Blades' => array(),
                                'Dozer Variable Radius Semi-U Blades' => array(),
                                'Dozer Woodchip U-Blades' => array(),
                            ),
                        ),
                        'Skid Steer Brooms' => array(
                            'children' => array(
                                'Angle Brooms' => array(),
                                'Pickup Brooms' => array(),
                                'Utility Brooms' => array(),
                            ),
                        ),
                        'Brushcutters' => array(),
                        'Buckets' => array(
                            'children' => array(
                                'Buckets - Backhoe Front' => array(),
                                'Buckets - Backhoe Rear' => array(
                                    'children' => array(
                                        'Coral Buckets' => array(),
                                        'Cribbing Buckets' => array(),
                                        'Ditch Cleaning Buckets' => array(),
                                        'Grading Buckets' => array(),
                                        'Heavy Duty Buckets' => array(),
                                        'Heavy Duty Rock Buckets' => array(),
                                        'High Capacity Buckets' => array(),
                                        'Soil Excavation Buckets' => array(),
                                        'Standard Duty Buckets' => array(),
                                    ),
                                ),
                                'Buckets - Compact Wheel Loader' => array(
                                    'children' => array(
                                        'Flat Floor Buckets' => array(),
                                        'General Purpose Buckets' => array(),
                                        'High Dump Buckets' => array(),
                                        'Industrial Grapple Buckets' => array(),
                                        'Light Material Buckets' => array(),
                                        'Multi-Purpose Buckets' => array(),
                                        'Side Dump Buckets' => array(),
                                    ),
                                ),
                                'Buckets - Excavator' => array(
                                    'children' => array(
                                        'Clean-up Excavator' => array(),
                                        'Digging Buckets - Mini Excavator' => array(),
                                        'Ditch Cleaning Excavator' => array(),
                                        'Ditch Cleaning Buckets - Mini Excavator' => array(),
                                        'Ditch Cleaning Tilt Excavator' => array(),
                                        'Extreme Duty Excavator' => array(),
                                        'General Duty Excavator' => array(),
                                        'Grading Buckets - Mini Excavator' => array(),
                                        'Heavy Duty Excavator' => array(),
                                        'Heavy Duty Buckets - Mini Excavator' => array(),
                                        'Heavy Duty Capacity Buckets - Mini Excavator' => array(),
                                        'Heavy Duty Rock Buckets - Mini Excavator' => array(),
                                        'Nordic Digging Buckets Excavator' => array(),
                                        'Nordic Grading Buckets Excavator' => array(),
                                        'Nordic Trenching Buckets Excavator' => array(),
                                        'Severe Duty Excavator' => array(),
                                        'Tilting Ditch Cleaning Buckets - Mini Excavator' => array(),
                                    ),
                                ),
                                'Buckets - Loader' => array(
                                    'children' => array(
                                        'Coal Loader' => array(),
                                        'Flat Floor Buckets Loader' => array(),
                                        'General Purpose Buckets Loader' => array(),
                                        'Grading Buckets Loader' => array(),
                                        'Grapple Loader' => array(),
                                        'Heavy Duty Granite Loader' => array(),
                                        'Heavy Duty Rock Loader' => array(),
                                        'High Abrasion Rock Loader' => array(),
                                        'High Dump Buckets Loader' => array(),
                                        'Light Material Buckets Loader' => array(),
                                        'Material Handling Loader' => array(),
                                        'Multi-Purpose Buckets Loader' => array(),
                                        'Rock Loader' => array(),
                                        'Sand & Gravel Buckets Loader' => array(),
                                        'Side Dump Buckets Loader' => array(),
                                        'Skeleton Buckets Loader' => array(),
                                        'Slag Loader' => array(),
                                        'Waste Dozing Loader' => array(),
                                        'Waste Handling Loader' => array(),
                                        'Waste Load & Carry Loader' => array(),
                                        'Woodchip Buckets Loader' => array(),
                                    ),
                                ),
                                'Buckets - Skid Steer Loader' => array(
                                    'children' => array(
                                        'Concrete Slab Buckets' => array(),
                                        'General Purpose Buckets' => array(),
                                        'Industrial Grapple Buckets' => array(),
                                        'Industrial Performance Buckets' => array(),
                                        'Light Material Buckets' => array(),
                                        'Low Profile Buckets' => array(),
                                        'Material Handling Buckets' => array(),
                                        'Mixing Buckets' => array(),
                                        'Multi-Purpose Buckets' => array(),
                                        'Side Discharge Buckets' => array(),
                                        'Skeleton Buckets' => array(),
                                        'Stump Buckets' => array(),
                                        'Utility Buckets' => array(),
                                        'Utility Grapple Buckets' => array(),
                                    ),
                                ),
                                'Buckets - Telehandler' => array(
                                    'children' => array(
                                        'General Purpose Buckets' => array(),
                                        'Grapple Buckets' => array(),
                                        'Light Material Buckets' => array(),
                                        'Material Handling Buckets' => array(),
                                        'Multi-Purpose Buckets' => array(),
                                    ),
                                ),
                            ),
                        ),
                        'Couplers' => array(
                            'children' => array(
                                'Couplers - Backhoe Rear' => array(
                                    'children' => array(
                                        'Dual Lock™ Pin Grabber Couplers' => array(),
                                        'Pin Lock Couplers' => array(),
                                        'Pin Puller Couplers' => array(),
                                    ),
                                ),
                                'Couplers - Excavator' => array(
                                    'children' => array(
                                        'CW Series Couplers' => array(),
                                        'Dual Lock™ Pin Grabber Couplers - Mini Excavator' => array(),
                                        'Hydraulic Connecting S Type Couplers' => array(),
                                        'Pin Grabber Couplers' => array(),
                                        'Pin Lock Couplers - Mini Excavators' => array(),
                                        'S Type Couplers' => array(),
                                        'Tilting Dual Lock™ Pin Grabber Couplers - Mini Excavator' => array(),
                                    ),
                                ),
                                'Couplers - Loader' => array(
                                    'children' => array(
                                        'Block Handling' => array(),
                                        'Fusion™ Coupler - Loader' => array(),
                                    ),
                                ),
                            ),
                        ),
                        'Forks' => array(
                            'children' => array(
                                'Log' => array(
                                    'children' => array(
                                        'Log & Lumber Fork Carriages' => array(),
                                        'Log and Lumber Forks' => array(),
                                        'Logging Forks' => array(),
                                    ),
                                ),
                                'Millyard' => array(
                                    'children' => array(
                                        'Millyard Forks' => array(),
                                        'Millyard Pole Forks' => array(),
                                    ),
                                ),
                                'Pallet Forks' => array(
                                    'children' => array(
                                        'Pallet Fork Carriages' => array(),
                                        'Pallet Fork Tines' => array(),
                                    ),
                                ),
                                'Construction Forks' => array(
                                    'children' => array(
                                        'Pipe & Pole Forks' => array(),
                                        'Semi Millyard Forks' => array(),
                                        'Sorting Grapple Forks' => array(),
                                        'Unloading Grapple Forks' => array(),
                                        'Utility Forks' => array(),
                                        'Utility Grapple Forks' => array(),
                                        'Construction Fork Carriages' => array(),
                                        'Heavy Duty Fork Carriages' => array(),
                                        'Industrial Grapple Forks' => array(),
                                        'Block Forks' => array(),
                                    ),
                                ),
                            ),
                        ),
                        'Generator Set Control Panels' => array(),
                        'Generator Set Enclosures' => array(),
                        'Generator Set Fuel Tanks' => array(),
                        'Grapples' => array(
                            'children' => array(
                                'Contractors Grapples' => array(),
                                'Demolition and Sorting Grapples' => array(),
                                'Orange Peel Grapples' => array(),
                                'Solid Waste Grapples' => array(),
                                'Trash Grapples' => array(),
                            ),
                        ),
                        'Mulchers' => array(),
                        'Multi-Processors' => array(),
                        'Nursery Products' => array(
                            'children' => array(
                                'Nursery Forks' => array(),
                                'Tree Scoop Buckets' => array(),
                            ),
                        ),
                        'Pulverizers' => array(
                            'children' => array(
                                'Primary Pulverizers' => array(),
                                'Secondary Pulverizer' => array(),
                            ),
                        ),
                        'Rakes' => array(
                            'children' => array(
                                'Excavator Rakes' => array(),
                                'Skid Steer Excavator Grapple Rakes' => array(),
                                'Skid Steer Landscape Excavator Rakes' => array(),
                                'Smal Wheel Loader Excavator Rakes' => array(),
                                'Skid Steer Power Box Excavator Rakes' => array(),
                            ),
                        ),
                        'Rippers' => array(),
                        'Rotors' => array(),
                        'Saws' => array(),
                        'Shears' => array(),
                        'Snow Products' => array(
                            'children' => array(
                                'Multi V-Plows' => array(),
                                'Snow Blowers' => array(),
                                'Snow Plows' => array(),
                                'Snow Wings' => array(),
                                'Straight Snow Pushes' => array(),
                            ),
                        ),
                        'Stump Grinders' => array(),
                    ),
                ),
            ),
        ),
        'Agricultural Equipment' => array(
            'children' => array(
                'Tractors' => array(
                    'children' => array(
                        'Compact Tractors' => array(),
                        'Mid-range Tractors' => array(),
                        'High-horsepower Tractors' => array(),
                        'Garden Tractors' => array(),
                        'Special Application (ORCHARD CABS)' => array(),
                        'Hay Equipment' => array(
                            'children' => array(
                                'Ballers' => array(),
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
                'AG Attachments' => array(
                    'children' => array(
                        'Bale Grabs' => array(),
                        'Disc Harrows' => array(),
                        'Chisels & Rippers' => array(),
                        'Crumblers' => array(),
                        'Bed Mulchers' => array(),
                        'Eliminators' => array(),
                        'Folding Bars' => array(),
                        'In-Furrow Chisel & Performers' => array(),
                        'Hitch Carriers' => array(),
                    ),
                ),
            ),
        ),
        'Power Systems' => array(
            'children' => array(
                'Electric Power' => array(
                    'children' => array(
                        'ATS ATC Breaker / Contactor' => array(),
                        'ATS MX Contactor' => array(),
                        'Diesel Generator Sets' => array(),
                        'Gas Generator Sets' => array(),
                        'Microgrid' => array(),
                        'Mobile Equipment Charger' => array(),
                        'Mobile Generator Sets' => array(),
                        'Switchgear and Paralleling Controls' => array(),
                    ),
                ),
                'Industrial' => array(
                    'children' => array(
                        'Diesel Fire Pumps' => array(),
                        'Gas Engines' => array(),
                        'Industrial Diesel Engines' => array(),
                        'Industrial Diesel Power Units' => array(),
                    ),
                ),
                'Marine Power Systems' => array(
                    'children' => array(
                        'Auxiliary Engines' => array(),
                        'Commercial Propulsion Engines' => array(),
                        'Controls' => array(),
                        'High Performance Propulsion and Maneuvering Solutions' => array(),
                        'Main Pitch Propeller' => array(),
                        'Marine Generator Sets' => array(),
                        'Marine Thruster Azimuth' => array(),
                        'Marine Thruster Transverse' => array(),
                        'Twin Fin' => array(),
                    ),
                ),
                'Oil and Gas' => array(
                    'children' => array(
                        'Energy Storage Solutions' => array(),
                        'Fire Pump Engines' => array(),
                        'Compressed Air Solutions - Big Market for Rent, Have Dedicated Page' => array(),
                        'Booster Pumps - 3 models' => array(),
                        'Air Compressor' => array(),
                    ),
                ),
            ),
        ),
        'Material Handling, Lift & Warehouse' => array(
            'children' => array(
                'Warehouse Forklifts' => array(
                    'children' => array(
                        'Diesel' => array(),
                        'LPG (Liquid Petroleum Gas)' => array(),
                        'Electric' => array(),
                    ),
                ),
                'Articulated Forklifts' => array(),
                'Rough Terrain Forklifts' => array(),
                'Warehouse handling equipment' => array(
                    'children' => array(
                        'Tow tractor' => array(),
                        'Pallet truck' => array(),
                        'Platform Stacker' => array(),
                        'Order picker' => array(),
                        'Reach Truck' => array(),
                        'Pedestrian Stackers' => array(),
                        'Hand Pallet Truck' => array(),
                        'Very narrow aisle trucks (VNA)' => array(),
                    ),
                ),
                'Port / Container handling equipment' => array(
                    'children' => array(
                        'Reachstackers' => array(),
                        'Container handlers' => array(),
                        'Container Loaders' => array(),
                        'Sideloaders' => array(),
                        'Straddle Carriers' => array(),
                    ),
                ),
                'Floor Cleaning Machines' => array(
                    'children' => array(
                        'Floor scrubbers' => array(),
                        'Floor Sweepers' => array(),
                        'Sweeper-scrubbers' => array(),
                        'Carpet extractors' => array(),
                        'Burnishers & Floor machines' => array(),
                        'Vacuums' => array(),
                        'Commercial Dryer' => array(),
                        'Multisurface cleaning machine' => array(),
                    ),
                ),
            ),
        ),
        'Home and Outdoor Power' => array(
            'children' => array(
                'Accessories' => array(
                    'children' => array(
                        'Covers' => array(),
                        'Maintenance' => array(),
                        'Power' => array(),
                    ),
                ),
                'Portable Generators' => array(
                    'children' => array(
                        'INV Series' => array(),
                        'RP Series' => array(),
                    ),
                ),
            ),
        ),
    );

    public function __construct(ImporterHandler $importerHandler)
    {
        $this->importerHandler = $importerHandler;
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_import_new_categories_action', array($this, 'importNewFamAjax'));
        add_action('wp_ajax_nopriv_import_new_categories_action', array($this, 'importNewFamAjax'));

    }


    public function importNewFamAjax(): void
    {

        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 2400);
        $this->importerHandler->importCategories($this->categories_to_import, $this->taxonomy);
        $response = array('success' => true, 'data' => 'Done');
        wp_send_json($response);
        wp_die();
    }
}
new NewEquipmentFamilyImport(new ImporterHandler());
