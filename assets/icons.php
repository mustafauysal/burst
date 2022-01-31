<?php

/**
 * Get icon from predefined list
 * @param string $icon_name
 * @param string $status
 * @param string $tooltip (optional)
 * @param int $size (optional)
 *
 * @return string
 */
function burst_icon( $icon_name, $tooltip = '', $status = 'default', $color = 'var(--rsp-text-color)', $size = 13, $classes = '' ) {
    $icons = array(
        'help' => array(
            'default' => array(
                'icon'    => '<svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" height="' . $size . '" width="' . $size . '">
									<path fill="'.$color.'" d="M504 256c0 136.997-111.043 248-248 248S8 392.997 8 256C8 119.083 119.043 8 256 8s248 111.083 248 248zM262.655 90c-54.497 0-89.255 22.957-116.549 63.758-3.536 5.286-2.353 12.415 2.715 16.258l34.699 26.31c5.205 3.947 12.621 3.008 16.665-2.122 17.864-22.658 30.113-35.797 57.303-35.797 20.429 0 45.698 13.148 45.698 32.958 0 14.976-12.363 22.667-32.534 33.976C247.128 238.528 216 254.941 216 296v4c0 6.627 5.373 12 12 12h56c6.627 0 12-5.373 12-12v-1.333c0-28.462 83.186-29.647 83.186-106.667 0-58.002-60.165-102-116.531-102zM256 338c-25.365 0-46 20.635-46 46 0 25.364 20.635 46 46 46s46-20.636 46-46c0-25.365-20.635-46-46-46z"></path>
								</svg>',
            ),
        ),
    );

    if ( !empty($tooltip) ) {
        $tooltip =  'burst-tooltip="' . $tooltip . '" flow="up" tabindex="0" role="button" aria-pressed="false"';
    }

    $icon = $icons[$icon_name][$status]['icon'];



    return '<span class="burst-icon '. $classes .' burst-' . esc_attr( $status ) . ' burst-' . esc_attr($icon_name) . '" '.$tooltip.'>
                ' . $icon . '
                </span>';
}
