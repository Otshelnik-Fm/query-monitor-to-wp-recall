<?php

/*

  ╔═╗╔╦╗╔═╗╔╦╗
  ║ ║ ║ ╠╣ ║║║ https://otshelnik-fm.ru
  ╚═╝ ╩ ╚  ╩ ╩

 */

//////// START меню query monitor в реколлбар ////////
add_action( 'rcl_bar_left_icons', 'qmwpr_add_query_monitor_in_recallbar' );
function qmwpr_add_query_monitor_in_recallbar() {
    // не активен плагин query monitor
    if ( ! class_exists( 'QM_Dispatcher_Html' ) )
        return;

    // отключен вывод реколлбара
    if ( rcl_get_option( 'view_recallbar', '1' ) == 0 )
        return;

    if ( qmwpr_user_verified() || current_user_can( 'manage_options' ) ) {

        echo '<div class="rcl-query-monitor">
    <li id="wp-admin-bar-query-monitor" class="menupop qm-all-clear" dir="ltr">
        <a class="ab-item" aria-haspopup="true" href="#qm-overview">
            <span class="ab-icon"></span>
            <span class="ab-label"></span>
        </a>
        <div class="ab-sub-wrapper">
            <ul id="wp-admin-bar-query-monitor-default" class="ab-submenu" style="display: block;">
                <li id="wp-admin-bar-query-monitor-placeholder"><a class="ab-item" href="#qm-overview">Обзор</a></li>
            </ul>
        </div>
    </li>
</div>';
    }
}

// поддержка верификационной куки
function qmwpr_user_verified() {
    if ( isset( $_COOKIE[QM_COOKIE] ) ) {
        return qmwpr_verify_cookie( wp_unslash( $_COOKIE[QM_COOKIE] ) );
    }
    return false;
}

// проверка куки
function qmwpr_verify_cookie( $value ) {
    $old_user_id = wp_validate_auth_cookie( $value, 'logged_in' );

    if ( $old_user_id ) {
        return true;
    }
    return false;
}

//
add_filter( 'rcl_inline_styles', 'qmwpr_style' );
function qmwpr_style( $styles ) {
    // не активен плагин query monitor
    if ( ! class_exists( 'QM_Dispatcher_Html' ) )
        return $styles;

    // отключен вывод реколлбара
    if ( rcl_get_option( 'view_recallbar', '1' ) == 0 )
        return $styles;

    if ( qmwpr_user_verified() || current_user_can( 'manage_options' ) ) {
        $styles .= '
.rcl-query-monitor {
    display: inline-block;
    position: relative;
}
.rcl-query-monitor > li > a > span {
    margin: 0 5px 0 0;
}
.rcl-query-monitor .ab-sub-wrapper {
    background: #32373c;
    box-shadow: 0 3px 5px rgba(0,0,0,.2);
    display: none;
    float: none;
    margin: 0;
    padding: 0;
    position: absolute;
    top: 30px;
}
.rcl-query-monitor:hover .ab-sub-wrapper {
    display: block;
}
#recallbar .qmwpr_notice {
    color: #ffd8cc;
    margin: 0 6px;
}
#wp-admin-bar-query-monitor-warnings,
#wp-admin-bar-query-monitor-db_dupes,
#wp-admin-bar-query-monitor-notices,
#wp-admin-bar-query-monitor-quiets {
    display: none;
}';
    }

    return $styles;
}

add_filter( 'wp_footer', 'qmwpr_script' );
function qmwpr_script() {
    // не активен плагин query monitor
    if ( ! class_exists( 'QM_Dispatcher_Html' ) )
        return;

    // отключен вывод реколлбара
    if ( rcl_get_option( 'view_recallbar', '1' ) == 0 )
        return;

    if ( qmwpr_user_verified() || current_user_can( 'manage_options' ) ) {
        $out        = '
jQuery(document).ready(function(){setTimeout(function(){
    var warn = jQuery("#wp-admin-bar-query-monitor-warnings > a,#wp-admin-bar-query-monitor-notices > a,#wp-admin-bar-query-monitor-db_dupes > a, #wp-admin-bar-query-monitor-quiets > a");
    if(warn.length > 0){
        jQuery("#wp-admin-bar-query-monitor").append(warn);
        jQuery("#wp-admin-bar-query-monitor > a:not(:first-child)").addClass("qmwpr_notice");
    }
},200);});';
        // сожмём в строку
        $script_min = qmwpr_inline( $out );
        echo "\r\n<script>" . $script_min . "</script>\r\n";
    }
}

// сожмем для инлайна
function qmwpr_inline( $src ) {
    // удаляем пробелы, переносы, табуляцию
    return preg_replace( '/ {2,}/', '', str_replace( array( "\r\n", "\r", "\n", "\t" ), '', $src ) );
}
