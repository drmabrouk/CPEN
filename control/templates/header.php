<?php
    global $wpdb;
    $logo_url = $wpdb->get_var("SELECT setting_value FROM {$wpdb->prefix}control_settings WHERE setting_key = 'company_logo'");
    $system_name = $wpdb->get_var("SELECT setting_value FROM {$wpdb->prefix}control_settings WHERE setting_key = 'system_name'") ?: 'Control';
    $design = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}control_settings WHERE setting_key LIKE 'design_%' OR setting_key LIKE 'auth_%'", OBJECT_K);
?>
<style id="control-dynamic-styles">
    :root {
        <?php if (isset($design['design_sidebar_bg'])) : ?>--control-sidebar-bg: <?php echo esc_attr($design['design_sidebar_bg']->setting_value); ?>;<?php endif; ?>
        <?php if (isset($design['design_btn_primary'])) : ?>--control-primary: <?php echo esc_attr($design['design_btn_primary']->setting_value); ?>;<?php endif; ?>
        <?php if (isset($design['design_btn_secondary'])) : ?>--control-primary-soft: <?php echo esc_attr($design['design_btn_secondary']->setting_value); ?>;<?php endif; ?>
        <?php if (isset($design['design_accent'])) : ?>--control-accent: <?php echo esc_attr($design['design_accent']->setting_value); ?>;<?php endif; ?>
        <?php if (isset($design['design_text_main'])) : ?>--control-text: <?php echo esc_attr($design['design_text_main']->setting_value); ?>;<?php endif; ?>
        <?php if (isset($design['design_bg_main'])) : ?>--control-bg: <?php echo esc_attr($design['design_bg_main']->setting_value); ?>;<?php endif; ?>
        <?php if (isset($design['design_font_size'])) : ?>font-size: <?php echo esc_attr($design['design_font_size']->setting_value); ?>px !important;<?php endif; ?>
        <?php if (isset($design['design_font_weight_bold'])) : ?>--control-font-weight-bold: <?php echo esc_attr($design['design_font_weight_bold']->setting_value); ?>;<?php endif; ?>

        /* Auth Customization */
        <?php if (isset($design['auth_bg_color'])) : ?>--auth-bg-color: <?php echo esc_attr($design['auth_bg_color']->setting_value); ?>;<?php endif; ?>
        <?php if (isset($design['auth_bg_image'])) : ?>--auth-bg-image: url('<?php echo esc_url($design['auth_bg_image']->setting_value); ?>');<?php endif; ?>
        <?php if (isset($design['auth_container_bg'])) : ?>--auth-container-bg: <?php echo esc_attr($design['auth_container_bg']->setting_value); ?>;<?php endif; ?>
        <?php if (isset($design['auth_container_opacity'])) : ?>--auth-container-opacity: <?php echo esc_attr($design['auth_container_opacity']->setting_value); ?>;<?php endif; ?>
        <?php if (isset($design['auth_border_color'])) : ?>--auth-border-color: <?php echo esc_attr($design['auth_border_color']->setting_value); ?>;<?php endif; ?>
        <?php if (isset($design['auth_border_radius'])) : ?>--auth-border-radius: <?php echo esc_attr($design['auth_border_radius']->setting_value); ?>px;<?php endif; ?>
        <?php if (isset($design['auth_container_shadow'])) : ?>--auth-container-shadow: <?php echo esc_attr($design['auth_container_shadow']->setting_value); ?>;<?php endif; ?>
        <?php if (isset($design['auth_input_border'])) : ?>--auth-input-border: <?php echo esc_attr($design['auth_input_border']->setting_value); ?>;<?php endif; ?>
        <?php if (isset($design['auth_input_focus'])) : ?>--auth-input-focus: <?php echo esc_attr($design['auth_input_focus']->setting_value); ?>;<?php endif; ?>
    }
    <?php if (isset($design['design_btn_hover'])) : ?>
    .control-btn:hover { background-color: <?php echo esc_attr($design['design_btn_hover']->setting_value); ?> !important; opacity: 1; }
    <?php endif; ?>
    <?php if (isset($design['design_high_contrast']) && $design['design_high_contrast']->setting_value === 'yes') : ?>
    body { filter: contrast(1.1) saturate(1.1); }
    .control-text-dark, .control-card h3 { color: #000 !important; font-weight: 800; }
    <?php endif; ?>
</style>
<div class="control-dashboard" id="control-system-root">
    <div class="control-mobile-header" style="display:none; background:#000; padding:10px 15px; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:10005; border-bottom:1px solid #1a1a1a; direction: rtl;">
        <div class="mobile-header-logo" style="flex: 1; text-align: right;">
            <?php if ( $logo_url ) : ?>
                <img src="<?php echo esc_url($logo_url); ?>" alt="Control" style="max-height:30px; width:auto; object-fit:contain; display:block;">
            <?php else : ?>
                <h2 style="color:#D4AF37; margin:0; font-size:1rem; letter-spacing:1px; font-weight: 800;">CONTROL</h2>
            <?php endif; ?>
        </div>
        <div style="display:flex; align-items:center; gap: 10px;">
            <button id="control-header-logout" class="control-pill-logout" style="background:#ef4444; color:#fff; border:none; border-radius:30px; padding:8px 16px; font-size:0.75rem; font-weight:800; cursor:pointer; display:flex; align-items:center; gap:6px;">
                <span class="dashicons dashicons-no-alt" style="font-size:16px; width:16px; height:16px;"></span>
                <span><?php _e('خروج', 'control'); ?></span>
            </button>
        </div>
    </div>

    <aside class="control-sidebar" id="control-sidebar-main">
        <div class="control-sidebar-logo">
            <?php if ( $logo_url ) : ?>
                    <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($system_name); ?>" style="max-width: 100%; height: auto; display: block; margin: 0 auto;">
                <?php else : ?>
                    <h2><?php echo esc_html($system_name); ?></h2>
                <?php endif;
            ?>
        </div>
        <nav class="control-sidebar-nav">
            <?php if ( Control_Auth::has_permission('dashboard') ) : ?>
                <a href="<?php echo add_query_arg('control_view', 'dashboard'); ?>" class="<?php echo (!isset($_GET['control_view']) || $_GET['control_view'] == 'dashboard') ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-dashboard"></span> <?php _e('لوحة المعلومات', 'control'); ?>
                </a>
            <?php endif; ?>

            <?php if ( Control_Auth::has_permission('users_view') ) : ?>
                <a href="<?php echo add_query_arg('control_view', 'users'); ?>" class="<?php echo (isset($_GET['control_view']) && $_GET['control_view'] == 'users') ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-admin-users"></span> <?php _e('إدارة الكوادر', 'control'); ?>
                </a>
            <?php endif; ?>

            <?php if ( Control_Auth::has_permission('roles_manage') ) : ?>
                <a href="<?php echo add_query_arg('control_view', 'roles'); ?>" class="<?php echo (isset($_GET['control_view']) && $_GET['control_view'] == 'roles') ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-shield"></span> <?php _e('الأدوار والصلاحيات', 'control'); ?>
                </a>
            <?php endif; ?>

            <?php if ( Control_Auth::has_permission('settings_manage') ) : ?>
                <a href="<?php echo add_query_arg('control_view', 'settings'); ?>" class="<?php echo (isset($_GET['control_view']) && $_GET['control_view'] == 'settings') ? 'active' : ''; ?>">
                    <span class="dashicons dashicons-admin-generic"></span> <?php _e('إعدادات النظام', 'control'); ?>
                </a>
            <?php endif; ?>
        </nav>

        <div class="control-sidebar-footer">
            <div class="sidebar-action-container">
                <?php
                $curr_u = Control_Auth::current_user();
                if ( $curr_u && strpos($curr_u->id, 'wp_') === false ) : ?>
                    <button id="control-edit-profile-btn" class="sidebar-action-btn profile-btn full-width">
                        <span><?php _e('ملفي الشخصي', 'control'); ?></span>
                        <span class="dashicons dashicons-admin-users"></span>
                    </button>
                <?php endif; ?>

                <div style="display:flex; gap:8px; width:100%;">
                    <button id="control-refresh-btn" class="sidebar-action-btn update-btn half-width">
                        <span><?php _e('تحديث', 'control'); ?></span>
                        <span class="dashicons dashicons-update"></span>
                    </button>
                    <button id="control-logout-btn" class="sidebar-action-btn logout-btn half-width">
                        <span><?php _e('خروج', 'control'); ?></span>
                        <span class="dashicons dashicons-exit"></span>
                    </button>
                </div>
            </div>
        </div>
    </aside>

    <!-- Mobile Bottom Bar (Fixed) -->
    <div class="control-mobile-bottom-bar" style="display:none;">
        <?php if ( Control_Auth::has_permission('dashboard') ) : ?>
            <a href="<?php echo add_query_arg('control_view', 'dashboard'); ?>" class="mobile-nav-item <?php echo (!isset($_GET['control_view']) || $_GET['control_view'] == 'dashboard') ? 'active' : ''; ?>">
                <span class="dashicons dashicons-performance"></span>
                <small><?php _e('الرئيسية', 'control'); ?></small>
            </a>
            <div class="mobile-divider"></div>
        <?php endif; ?>

        <?php if ( Control_Auth::has_permission('users_view') ) : ?>
            <a href="<?php echo add_query_arg('control_view', 'users'); ?>" class="mobile-nav-item <?php echo (isset($_GET['control_view']) && $_GET['control_view'] == 'users') ? 'active' : ''; ?>">
                <span class="dashicons dashicons-admin-users"></span>
                <small><?php _e('المستخدمين', 'control'); ?></small>
            </a>
            <div class="mobile-divider"></div>
        <?php endif; ?>

        <?php if ( Control_Auth::has_permission('settings_manage') ) : ?>
            <a href="<?php echo add_query_arg('control_view', 'settings'); ?>" class="mobile-nav-item <?php echo (isset($_GET['control_view']) && $_GET['control_view'] == 'settings') ? 'active' : ''; ?>">
                <span class="dashicons dashicons-admin-generic"></span>
                <small><?php _e('الإعدادات', 'control'); ?></small>
            </a>
            <div class="mobile-divider"></div>
        <?php endif; ?>
        <button id="control-mobile-refresh-btn" class="mobile-nav-item" style="background:none; border:none; cursor:pointer;">
            <span class="dashicons dashicons-update"></span>
            <small><?php _e('تحديث', 'control'); ?></small>
        </button>
    </div>

    <div id="control-sync-loader" style="display:none; position:fixed; top:20px; left:50%; transform:translateX(-50%); background:#000000; color:#fff; padding:10px 20px; border-radius:30px; z-index:10000; box-shadow:0 4px 12px rgba(0,0,0,0.2); font-weight:600;">
        <span class="dashicons dashicons-update spin" style="margin-left:8px; vertical-align:middle;"></span>
        <span class="loader-text"><?php _e('جارٍ تحميل البيانات...', 'control'); ?></span>
    </div>

    <main class="control-main-content">
        <div id="control-install-banner" style="display:none; background:#D4AF37; color:#000; padding:15px 25px; border-radius:12px; margin-bottom:25px; align-items:center; justify-content:space-between; font-weight:700; box-shadow:0 4px 12px rgba(212,175,55,0.3);">
            <div style="display:flex; align-items:center; gap:15px;">
                <span class="dashicons dashicons-smartphone" style="font-size:24px; width:24px; height:24px;"></span>
                <span><?php _e('تثبيت تطبيق كنترول على هاتفك للوصول السريع', 'control'); ?></span>
            </div>
            <button onclick="window.controlInstallPrompt.prompt()" class="control-btn" style="background:#000; border:none; padding:8px 20px; font-size:0.85rem;"><?php _e('تثبيت الآن', 'control'); ?></button>
        </div>
        <div class="control-content-inner">
