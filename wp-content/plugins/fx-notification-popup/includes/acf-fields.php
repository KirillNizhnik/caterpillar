<?php


if( function_exists( 'acf_add_local_field_group' ) ) {
	acf_add_local_field_group(
        array(
			'key'                   => 'group_5e8f17759c5b5',
			'title'                 => 'Notification Popup',
			'fields'                => array(
				array(
					'key'               => 'field_5e8f17ec4b0cd',
					'label'             => 'Toggle',
					'name'              => 'toggle_notification_popup',
					'type'              => 'true_false',
					'instructions'      => 'When set to "Show", the Notification Popup will appear on the page.',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'message'           => '',
					'default_value'     => 0,
					'ui'                => 1,
					'ui_on_text'        => 'Show',
					'ui_off_text'       => 'Hide',
				),
				array(
					'key'               => 'field_5e8f180d4b0ce',
					'label'             => 'Content',
					'name'              => 'notification_popup_content',
					'type'              => 'wysiwyg',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_5e8f17ec4b0cd',
								'operator' => '==',
								'value'    => '1',
							),
						),
					),
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '',
					'placeholder'       => '',
					'maxlength'         => 140,
					'rows'              => '',
					'new_lines'         => '',
				),
				array(
					'key'               => 'field_5e8f266ea4b3d',
					'label'             => 'Background Color',
					'name'              => 'notification_popup_background_color',
					'type'              => 'color_picker',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_5e8f17ec4b0cd',
								'operator' => '==',
								'value'    => '1',
							),
						),
					),
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '#e4b340',
				),
				array(
					'key'               => 'field_5e8f268da4b3e',
					'label'             => 'Text Color',
					'name'              => 'notification_popup_text_color',
					'type'              => 'color_picker',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_5e8f17ec4b0cd',
								'operator' => '==',
								'value'    => '1',
							),
						),
					),
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '#000000',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'fx-notification-popup',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
        )
    );
}
