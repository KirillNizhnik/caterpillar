# Changelog

## [0.1.2]
- Create Changelog
- Add fx_include_cf7_scripts filter

## [0.1.3]
- Add fx_get_block_data() function to access data from blocks on another page.
- Add observe_non_fx_block action
- Remove non-static data from flatten_fx_blocks() so it can be called statically

## [0.1.4]
- added "exclude_post_types" parameter in $settings for FX_RegisterBlocks->register_block()

## [0.2.0]
- added improvements from Organic Lawns build
- standardized hooks, filters, and actions
- formatting updates
- various error and bug fixes
- splitting functions into internal-use functions and global-use functions

## [0.2.1]
- added function for checking env type
- added debugging output for blocks

## [0.2.2]
- replaced deprecated "block_categories" with "block_categories_all" filter

## [0.2.3]
- moved fx_cf7_dequeue_scripts in theme to FX_Parse_Block_Assets
- updated conditions for detecting CF7 in FX_Parse_Block_Assets::double_check_for_cf7

## [0.2.4]
- added "minify" option to FX_Assets::add_script so that individual assets can be signalled to not be minified (previously minified assets — e.g. from a 3rd party library — can cause issues when "double-minified")

## [0.2.5]
- fixed preload functionality in FX_Assets

## [0.2.6]
- added function for deprecated functions
- added function for deprecated hooks
- added function for deprecated argument
- deprecated "prefetch" arg in add_script and add_stylesheet

## [0.2.7]
- added check for REST requests
- added check for AJAX/JSON requests
- added check for child/parent themes
- support for multisites. Now, if a child theme has a block template or an asset, it'll override the parent theme template/asset

## [0.2.8]
- fixed various PHP warnings/notices

## [0.2.9]
- fixed bug related to assets not loading correctly for blocks registered in plugins in version control
- added file path normalize function

## [0.2.10]
- added check to block comment debugger

## [0.2.11]
- instead of an error, FX_Parse_Block_Assets::enqueue_block_assets will now generate warnings if page contains an invalid block, prompting builder to remove invalid block in WP admin

## [0.2.12]
- added additional check in FX_Parse_Block_Assets::double_check_for_cf7 for CF7 shortcode in block attributes
- fixed bug in FX_Parse_Block_Assets that prevented fx_bam_after_parse_all_block_assets from running if no blocks were on page. This was causing CF7 to be enqueued on pages when there weren't actually any blocks on the page

## [0.2.13]
- greatly simplified how blocks are checked for CF7 content (to determine whether page should include CF7 scripts)
- removed str_contains polyfill to discourage self from silly micro-optimizations
- added helper function for ensuring WP_Post
- overhauled fx_get_block_data public function
- added helper function for grabbing all blocks from a specific post
- added helper function for flattening all blocks (e.g. if block has inner blocks, they're extracted so that all blocks are one-dimensional)

## [0.2.14]
- fixed bug with file paths

## [0.2.15]
- FX_Register_Blocks — added "fx_bam_after_register_block" hook
- FX_Register_Blocks — added function that will automatically check through blocks and exclude blocks with CF7 content from having their assets inlined