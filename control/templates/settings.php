<?php
global $wpdb;
$settings = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}control_settings", OBJECT_K );
?>

<div class="control-header-flex" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-weight:800; font-size:1.3rem; margin:0; color:var(--control-text-dark);"><?php _e('إعدادات النظام', 'control'); ?></h2>
</div>

<div class="control-settings-wrapper" style="display:grid; grid-template-columns: 200px 1fr; gap:30px; align-items: flex-start;">

    <div class="control-settings-sidebar" style="background:#fff; border:1px solid var(--control-border); border-radius:var(--control-radius); padding:10px; box-shadow:var(--control-shadow-sm); position:sticky; top:20px;">
        <div class="settings-nav-group">
            <button class="control-tab-btn active" data-tab="tab-identity">
                <span class="dashicons dashicons-admin-appearance"></span>
                <span><?php _e('هوية النظام', 'control'); ?></span>
            </button>
            <button class="control-tab-btn" data-tab="tab-design">
                <span class="dashicons dashicons-art"></span>
                <span><?php _e('تخصيص التصميم', 'control'); ?></span>
            </button>
            <button class="control-tab-btn" data-tab="tab-pwa">
                <span class="dashicons dashicons-smartphone"></span>
                <span><?php _e('تطبيق الجوال', 'control'); ?></span>
            </button>
            <button class="control-tab-btn" data-tab="tab-notifications">
                <span class="dashicons dashicons-email-alt"></span>
                <span><?php _e('التنبيهات والبريد', 'control'); ?></span>
            </button>
            <button class="control-tab-btn" data-tab="tab-policies">
                <span class="dashicons dashicons-media-document"></span>
                <span><?php _e('السياسات والشروط', 'control'); ?></span>
            </button>
            <button class="control-tab-btn" data-tab="tab-backup">
                <span class="dashicons dashicons-database-export"></span>
                <span><?php _e('النسخ الاحتياطي', 'control'); ?></span>
            </button>
            <button class="control-tab-btn" data-tab="tab-audit">
                <span class="dashicons dashicons-list-view"></span>
                <span><?php _e('سجل النشاطات', 'control'); ?></span>
            </button>
        </div>
    </div>

    <div class="control-tab-content-container">
        <!-- Section 1: System Identity -->
        <div id="tab-identity" class="control-tab-content active">
            <div class="control-card" style="border-top: 4px solid var(--control-accent); padding: 25px;">
                <div style="margin-bottom:25px; border-bottom:1px solid var(--control-bg); padding-bottom:15px;">
                    <h3 style="margin:0; font-size:1.1rem; color:var(--control-text-dark);"><?php _e('هوية النظام والشركة', 'control'); ?></h3>
                    <div style="color:var(--control-muted); font-size:0.8rem; margin-top:5px;"><?php _e('تخصيص معلومات النظام الأساسية والشعار لتظهر في التقارير والواجهة.', 'control'); ?></div>
                </div>

                <form id="control-identity-form" class="control-system-settings-form">
                    <div class="control-grid" style="grid-template-columns: 1fr 1fr; gap:20px;">
                        <div class="control-form-group">
                            <label><?php _e('اسم النظام', 'control'); ?></label>
                            <input type="text" name="system_name" value="<?php echo esc_attr($settings['system_name']->setting_value ?? 'Control'); ?>" placeholder="Control">
                        </div>
                        <div class="control-form-group">
                            <label><?php _e('اسم الشركة / المؤسسة', 'control'); ?></label>
                            <input type="text" name="company_name" value="<?php echo esc_attr($settings['company_name']->setting_value ?? 'Control'); ?>" placeholder="Control Co.">
                        </div>
                    </div>

                    <div class="control-grid" style="grid-template-columns: 1fr 1fr; gap:20px;">
                        <div class="control-form-group">
                            <label><?php _e('رقم الهاتف للتواصل', 'control'); ?></label>
                            <input type="text" name="company_phone" value="<?php echo esc_attr($settings['company_phone']->setting_value ?? ''); ?>" placeholder="+000 000 000">
                        </div>
                        <div class="control-form-group">
                            <label><?php _e('البريد الإلكتروني', 'control'); ?></label>
                            <input type="email" name="company_email" value="<?php echo esc_attr($settings['company_email']->setting_value ?? ''); ?>" placeholder="info@example.com">
                        </div>
                    </div>

                    <div class="control-form-group">
                        <label><?php _e('العنوان بالتفصيل (للتقارير)', 'control'); ?></label>
                        <textarea name="company_address" rows="2" placeholder="<?php _e('العنوان هنا...', 'control'); ?>"><?php echo esc_textarea($settings['company_address']->setting_value ?? ''); ?></textarea>
                    </div>

                    <div class="control-form-group" style="background:var(--control-bg); padding:20px; border-radius:12px; border:1px solid var(--control-border); margin-top:15px;">
                        <label style="margin-bottom:12px; display:block; font-weight:700; color:var(--control-text-dark);"><?php _e('شعار الشركة (الرسمي)', 'control'); ?></label>
                        <div style="display:flex; gap:25px; align-items: center;">
                            <div id="logo-preview-container" style="background:#fff; border:1px solid var(--control-border); border-radius:8px; padding:10px; width:140px; height:70px; display:flex; align-items:center; justify-content:center; overflow:hidden; box-shadow:var(--control-shadow-sm);">
                                <?php if(!empty($settings['company_logo']->setting_value)): ?>
                                    <img id="logo-preview" src="<?php echo esc_url($settings['company_logo']->setting_value); ?>" style="max-height:100%; object-fit:contain;">
                                <?php else: ?>
                                    <span class="dashicons dashicons-format-image" style="color:var(--control-muted); font-size:32px;"></span>
                                <?php endif; ?>
                            </div>
                            <div style="flex:1;">
                                <input type="hidden" name="company_logo" id="company-logo-url" value="<?php echo esc_attr($settings['company_logo']->setting_value ?? ''); ?>">
                                <button type="button" class="control-upload-btn control-btn" style="background:var(--control-primary); border:none;"><?php _e('تغيير الشعار', 'control'); ?></button>
                                <p style="margin:8px 0 0 0; font-size:0.7rem; color:var(--control-muted);"><?php _e('يفضل خلفية شفافة PNG بجودة عالية.', 'control'); ?></p>
                            </div>
                        </div>
                    </div>

                    <div style="margin:30px 0 20px 0; padding-bottom:10px; border-bottom:1px solid var(--control-border);">
                        <h4 style="margin:0; font-size:1rem; color:var(--control-primary); font-weight:800;"><?php _e('تخصيص واجهة الدخول والتسجيل', 'control'); ?></h4>
                    </div>

                    <div class="control-grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:20px;">
                        <!-- Auth Backdrop -->
                        <div style="background:var(--control-bg); padding:20px; border-radius:12px; border:1px solid var(--control-border);">
                            <h4 style="margin:0 0 15px 0; font-size:0.9rem;"><?php _e('الخلفية العامة (Backdrop)', 'control'); ?></h4>
                            <div class="control-form-group">
                                <label><?php _e('لون الخلفية', 'control'); ?></label>
                                <input type="color" name="auth_bg_color" value="<?php echo esc_attr($settings['auth_bg_color']->setting_value ?? '#000000'); ?>">
                            </div>
                            <div class="control-form-group">
                                <label><?php _e('رابط صورة الخلفية', 'control'); ?></label>
                                <div style="display:flex; gap:10px;">
                                    <input type="text" name="auth_bg_image" id="auth-bg-image-url" value="<?php echo esc_attr($settings['auth_bg_image']->setting_value ?? ''); ?>" placeholder="https://...">
                                    <button type="button" class="control-upload-btn control-btn" style="min-width:40px; padding:0;"><span class="dashicons dashicons-upload"></span></button>
                                </div>
                            </div>
                        </div>

                        <!-- Auth Container -->
                        <div style="background:var(--control-bg); padding:20px; border-radius:12px; border:1px solid var(--control-border);">
                            <h4 style="margin:0 0 15px 0; font-size:0.9rem;"><?php _e('وعاء النموذج (Form Container)', 'control'); ?></h4>
                            <div class="control-grid" style="grid-template-columns: 1fr 1fr; gap:10px;">
                                <div class="control-form-group">
                                    <label><?php _e('لون الوعاء', 'control'); ?></label>
                                    <input type="color" name="auth_container_bg" value="<?php echo esc_attr($settings['auth_container_bg']->setting_value ?? '#000000'); ?>">
                                </div>
                                <div class="control-form-group">
                                    <label><?php _e('الشفافية (Opacity)', 'control'); ?></label>
                                    <input type="number" name="auth_container_opacity" value="<?php echo esc_attr($settings['auth_container_opacity']->setting_value ?? '1.0'); ?>" step="0.1" min="0" max="1">
                                </div>
                            </div>
                            <div class="control-grid" style="grid-template-columns: 1fr 1fr; gap:10px;">
                                <div class="control-form-group">
                                    <label><?php _e('لون الحدود', 'control'); ?></label>
                                    <input type="text" name="auth_border_color" value="<?php echo esc_attr($settings['auth_border_color']->setting_value ?? 'rgba(255,255,255,0.1)'); ?>">
                                </div>
                                <div class="control-form-group">
                                    <label><?php _e('نصف قطر الحدود', 'control'); ?></label>
                                    <input type="number" name="auth_border_radius" value="<?php echo esc_attr($settings['auth_border_radius']->setting_value ?? '20'); ?>">
                                </div>
                            </div>
                            <div class="control-form-group">
                                <label><?php _e('ظل الوعاء (Shadow)', 'control'); ?></label>
                                <input type="text" name="auth_container_shadow" value="<?php echo esc_attr($settings['auth_container_shadow']->setting_value ?? '0 25px 50px -12px rgba(0, 0, 0, 0.5)'); ?>">
                            </div>
                        </div>

                        <!-- Input Styles & Logic -->
                        <div style="background:var(--control-bg); padding:20px; border-radius:12px; border:1px solid var(--control-border);">
                            <h4 style="margin:0 0 15px 0; font-size:0.9rem;"><?php _e('حقول الإدخال والهوية', 'control'); ?></h4>
                            <div class="control-grid" style="grid-template-columns: 1fr 1fr; gap:10px;">
                                <div class="control-form-group">
                                    <label><?php _e('لون حدود الحقول', 'control'); ?></label>
                                    <input type="text" name="auth_input_border" value="<?php echo esc_attr($settings['auth_input_border']->setting_value ?? 'rgba(255,255,255,0.2)'); ?>">
                                </div>
                                <div class="control-form-group">
                                    <label><?php _e('لون التركيز (Focus)', 'control'); ?></label>
                                    <input type="color" name="auth_input_focus" value="<?php echo esc_attr($settings['auth_input_focus']->setting_value ?? '#D4AF37'); ?>">
                                </div>
                            </div>
                            <div class="control-grid" style="grid-template-columns: 1fr 1fr; gap:10px;">
                                <div class="control-form-group">
                                    <label><?php _e('خلفية الحقول', 'control'); ?></label>
                                    <input type="text" name="auth_input_bg" value="<?php echo esc_attr($settings['auth_input_bg']->setting_value ?? 'rgba(255,255,255,0.05)'); ?>">
                                </div>
                                <div class="control-form-group">
                                    <label><?php _e('لون نص الحقول', 'control'); ?></label>
                                    <input type="color" name="auth_input_text" value="<?php echo esc_attr($settings['auth_input_text']->setting_value ?? '#ffffff'); ?>">
                                </div>
                            </div>
                            <div class="control-form-group">
                                <label><?php _e('لون التسميات (Labels)', 'control'); ?></label>
                                <input type="color" name="auth_label_color" value="<?php echo esc_attr($settings['auth_label_color']->setting_value ?? '#94a3b8'); ?>">
                            </div>
                            <div class="control-grid" style="grid-template-columns: 1fr 1fr; gap:10px;">
                                <div class="control-form-group">
                                    <label><?php _e('ظهور الشعار', 'control'); ?></label>
                                    <select name="auth_logo_visible">
                                        <option value="1" <?php selected($settings['auth_logo_visible']->setting_value ?? '1', '1'); ?>><?php _e('إظهار', 'control'); ?></option>
                                        <option value="0" <?php selected($settings['auth_logo_visible']->setting_value ?? '1', '0'); ?>><?php _e('إخفاء', 'control'); ?></option>
                                    </select>
                                </div>
                                <div class="control-form-group">
                                    <label><?php _e('نمط التصميم (Layout)', 'control'); ?></label>
                                    <select name="auth_layout_template">
                                        <option value="centered" <?php selected($settings['auth_layout_template']->setting_value ?? 'centered', 'centered'); ?>><?php _e('مركز كامل (Centered)', 'control'); ?></option>
                                        <option value="split" <?php selected($settings['auth_layout_template']->setting_value ?? 'centered', 'split'); ?>><?php _e('شاشة مقسمة (Split Screen)', 'control'); ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="control-grid" style="grid-template-columns: 1fr 1fr; gap:10px;">
                                <div class="control-form-group">
                                    <label><?php _e('العنوان الرئيسي (Heading)', 'control'); ?></label>
                                    <input type="text" name="auth_heading_text" value="<?php echo esc_attr($settings['auth_heading_text']->setting_value ?? ''); ?>" placeholder="<?php _e('مرحباً بك...', 'control'); ?>">
                                </div>
                                <div class="control-form-group">
                                    <label><?php _e('ظهور العنوان', 'control'); ?></label>
                                    <select name="auth_title_visible">
                                        <option value="1" <?php selected($settings['auth_title_visible']->setting_value ?? '1', '1'); ?>><?php _e('إظهار', 'control'); ?></option>
                                        <option value="0" <?php selected($settings['auth_title_visible']->setting_value ?? '1', '0'); ?>><?php _e('إخفاء', 'control'); ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="control-grid" style="grid-template-columns: 1fr 1fr; gap:10px;">
                                <div class="control-form-group">
                                    <label><?php _e('النص الفرعي (Subtitle)', 'control'); ?></label>
                                    <input type="text" name="auth_subtitle_text" value="<?php echo esc_attr($settings['auth_subtitle_text']->setting_value ?? ''); ?>" placeholder="<?php _e('وصف بسيط...', 'control'); ?>">
                                </div>
                                <div class="control-form-group">
                                    <label><?php _e('ظهور النص الفرعي', 'control'); ?></label>
                                    <select name="auth_subtitle_visible">
                                        <option value="1" <?php selected($settings['auth_subtitle_visible']->setting_value ?? '1', '1'); ?>><?php _e('إظهار', 'control'); ?></option>
                                        <option value="0" <?php selected($settings['auth_subtitle_visible']->setting_value ?? '1', '0'); ?>><?php _e('إخفاء', 'control'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top:35px; border-top:1px solid var(--control-bg); padding-top:20px;">
                        <button type="submit" class="control-btn control-btn-accent" style="height:48px; border-radius:8px; font-weight:800; min-width:220px;"><?php _e('حفظ كافة التغييرات', 'control'); ?></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Section 2: Design Customization -->
        <div id="tab-design" class="control-tab-content" style="display:none;">
            <div class="control-card" style="border-top: 4px solid #8b5cf6; padding: 25px;">
                <div style="margin-bottom:25px; border-bottom:1px solid var(--control-bg); padding-bottom:15px;">
                    <h3 style="margin:0; font-size:1.1rem; color:var(--control-text-dark);"><?php _e('تخصيص تصميم النظام', 'control'); ?></h3>
                    <div style="color:var(--control-muted); font-size:0.8rem; margin-top:5px;"><?php _e('تحكم في الألوان، الخطوط، والمظهر العام للنظام.', 'control'); ?></div>
                </div>

                <form id="control-design-form" class="control-system-settings-form">
                    <div class="control-grid" style="grid-template-columns: 1fr 1fr; gap:25px;">
                        <!-- Sidebar & Navigation -->
                        <div style="background:var(--control-bg); padding:20px; border-radius:12px; border:1px solid var(--control-border);">
                            <h4 style="margin:0 0 15px 0; font-size:0.9rem;"><?php _e('شريط التنقل الجانبي', 'control'); ?></h4>
                            <div class="control-form-group">
                                <label><?php _e('لون خلفية الشريط الجانبي', 'control'); ?></label>
                                <input type="color" name="design_sidebar_bg" value="<?php echo esc_attr($settings['design_sidebar_bg']->setting_value ?? '#0f172a'); ?>" style="height:40px;">
                            </div>
                        </div>

                        <!-- Buttons & Actions -->
                        <div style="background:var(--control-bg); padding:20px; border-radius:12px; border:1px solid var(--control-border);">
                            <h4 style="margin:0 0 15px 0; font-size:0.9rem;"><?php _e('الأزرار والإجراءات', 'control'); ?></h4>
                            <div class="control-grid" style="grid-template-columns: 1fr 1fr; gap:10px;">
                                <div class="control-form-group">
                                    <label><?php _e('اللون الرئيسي', 'control'); ?></label>
                                    <input type="color" name="design_btn_primary" value="<?php echo esc_attr($settings['design_btn_primary']->setting_value ?? '#0f172a'); ?>">
                                </div>
                                <div class="control-form-group">
                                    <label><?php _e('اللون الثانوي', 'control'); ?></label>
                                    <input type="color" name="design_btn_secondary" value="<?php echo esc_attr($settings['design_btn_secondary']->setting_value ?? '#1e293b'); ?>">
                                </div>
                                <div class="control-form-group">
                                    <label><?php _e('لون التمييز', 'control'); ?></label>
                                    <input type="color" name="design_accent" value="<?php echo esc_attr($settings['design_accent']->setting_value ?? '#D4AF37'); ?>">
                                </div>
                                <div class="control-form-group">
                                    <label><?php _e('لون التحويم (Hover)', 'control'); ?></label>
                                    <input type="color" name="design_btn_hover" value="<?php echo esc_attr($settings['design_btn_hover']->setting_value ?? '#1e293b'); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="control-grid" style="grid-template-columns: 1fr 1fr; gap:25px; margin-top:20px;">
                        <!-- Color Schemes -->
                        <div style="background:var(--control-bg); padding:20px; border-radius:12px; border:1px solid var(--control-border);">
                            <h4 style="margin:0 0 15px 0; font-size:0.9rem;"><?php _e('مخطط ألوان الواجهة', 'control'); ?></h4>
                            <div class="control-grid" style="grid-template-columns: 1fr 1fr; gap:10px;">
                                <div class="control-form-group">
                                    <label><?php _e('لون النص الأساسي', 'control'); ?></label>
                                    <input type="color" name="design_text_main" value="<?php echo esc_attr($settings['design_text_main']->setting_value ?? '#334155'); ?>">
                                </div>
                                <div class="control-form-group">
                                    <label><?php _e('لون الخلفية العامة', 'control'); ?></label>
                                    <input type="color" name="design_bg_main" value="<?php echo esc_attr($settings['design_bg_main']->setting_value ?? '#f8fafc'); ?>">
                                </div>
                            </div>
                        </div>
                        <!-- Typography -->
                        <div style="background:var(--control-bg); padding:20px; border-radius:12px; border:1px solid var(--control-border);">
                            <h4 style="margin:0 0 15px 0; font-size:0.9rem;"><?php _e('تنسيق الخطوط (Typography)', 'control'); ?></h4>
                            <div class="control-grid" style="grid-template-columns: 1fr 1fr; gap:15px;">
                                <div class="control-form-group">
                                    <label><?php _e('حجم الخط الأساسي (px)', 'control'); ?></label>
                                    <input type="number" name="design_font_size" value="<?php echo esc_attr($settings['design_font_size']->setting_value ?? '14'); ?>" min="12" max="18">
                                </div>
                                <div class="control-form-group">
                                    <label><?php _e('وزن الخط للعناوين', 'control'); ?></label>
                                    <select name="design_font_weight_bold">
                                        <option value="600" <?php selected($settings['design_font_weight_bold']->setting_value ?? '700', '600'); ?>>Medium (600)</option>
                                        <option value="700" <?php selected($settings['design_font_weight_bold']->setting_value ?? '700', '700'); ?>>Bold (700)</option>
                                        <option value="800" <?php selected($settings['design_font_weight_bold']->setting_value ?? '700', '800'); ?>>Extra Bold (800)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Accessibility -->
                        <div style="background:var(--control-bg); padding:20px; border-radius:12px; border:1px solid var(--control-border);">
                            <h4 style="margin:0 0 15px 0; font-size:0.9rem;"><?php _e('إمكانية الوصول والتباين', 'control'); ?></h4>
                            <div class="control-form-group">
                                <label><?php _e('تباين النص المرتفع', 'control'); ?></label>
                                <select name="design_high_contrast">
                                    <option value="no" <?php selected($settings['design_high_contrast']->setting_value ?? 'no', 'no'); ?>><?php _e('افتراضي', 'control'); ?></option>
                                    <option value="yes" <?php selected($settings['design_high_contrast']->setting_value ?? 'no', 'yes'); ?>><?php _e('تباين عالي', 'control'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top:35px; border-top:1px solid var(--control-bg); padding-top:20px;">
                        <button type="submit" class="control-btn control-btn-accent" style="height:48px; border-radius:8px; font-weight:800; min-width:220px;"><?php _e('حفظ إعدادات التصميم', 'control'); ?></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Section 3: PWA & Mobile App Settings -->
        <div id="tab-pwa" class="control-tab-content" style="display:none;">
            <div class="control-card" style="border-top: 4px solid var(--control-primary); padding: 25px;">
                <div style="margin-bottom:25px; border-bottom:1px solid var(--control-bg); padding-bottom:15px;">
                    <h3 style="margin:0; font-size:1.1rem; color:var(--control-text-dark);"><?php _e('إعدادات تطبيق الجوال (PWA)', 'control'); ?></h3>
                    <div style="color:var(--control-muted); font-size:0.8rem; margin-top:5px;"><?php _e('تثبيت النظام كاختصار تطبيق على الشاشة الرئيسية للهواتف الذكية.', 'control'); ?></div>
                </div>

                <form class="control-system-settings-form">
                    <div class="control-grid" style="grid-template-columns: 1fr 1fr; gap:20px;">
                        <div class="control-form-group">
                            <label><?php _e('اسم التطبيق الكامل', 'control'); ?></label>
                            <input type="text" name="pwa_app_name" value="<?php echo esc_attr($settings['pwa_app_name']->setting_value ?? 'Control'); ?>">
                        </div>
                        <div class="control-form-group">
                            <label><?php _e('الاسم المختصر', 'control'); ?></label>
                            <input type="text" name="pwa_short_name" value="<?php echo esc_attr($settings['pwa_short_name']->setting_value ?? 'Control'); ?>">
                        </div>
                    </div>

                    <div class="control-grid" style="grid-template-columns: 1fr 1fr; gap:20px;">
                        <div class="control-form-group">
                            <label><?php _e('لون السمة الرئيسي', 'control'); ?></label>
                            <input type="color" name="pwa_theme_color" value="<?php echo esc_attr($settings['pwa_theme_color']->setting_value ?? '#000000'); ?>" style="height:45px; padding:4px;">
                        </div>
                        <div class="control-form-group">
                            <label><?php _e('لون الخلفية', 'control'); ?></label>
                            <input type="color" name="pwa_bg_color" value="<?php echo esc_attr($settings['pwa_bg_color']->setting_value ?? '#ffffff'); ?>" style="height:45px; padding:4px;">
                        </div>
                    </div>

                    <div class="control-form-group" style="background:var(--control-bg); padding:20px; border-radius:12px; border:1px solid var(--control-border); margin-top:15px;">
                        <label style="margin-bottom:12px; display:block; font-weight:700; color:var(--control-text-dark);"><?php _e('أيقونة التطبيق (512x512)', 'control'); ?></label>
                        <div style="display:flex; gap:25px; align-items: center;">
                            <div id="pwa-icon-preview-container" style="background:#fff; border:1px solid var(--control-border); border-radius:12px; padding:5px; width:90px; height:90px; display:flex; align-items:center; justify-content:center; overflow:hidden; box-shadow:var(--control-shadow-sm);">
                                <?php if(!empty($settings['pwa_icon_url']->setting_value)): ?>
                                    <img id="pwa-icon-preview" src="<?php echo esc_url($settings['pwa_icon_url']->setting_value); ?>" style="max-height:100%; border-radius:8px;">
                                <?php else: ?>
                                    <span class="dashicons dashicons-smartphone" style="color:var(--control-muted); font-size:36px;"></span>
                                <?php endif; ?>
                            </div>
                            <div style="flex:1;">
                                <input type="hidden" name="pwa_icon_url" id="pwa-icon-url" value="<?php echo esc_attr($settings['pwa_icon_url']->setting_value ?? ''); ?>">
                                <button type="button" class="control-upload-btn control-btn" style="background:var(--control-primary); border:none;"><?php _e('تحديث الأيقونة', 'control'); ?></button>
                                <p style="margin:8px 0 0 0; font-size:0.7rem; color:var(--control-muted);"><?php _e('يجب أن تكون الأيقونة مربعة وبحجم لا يقل عن 512 بكسل.', 'control'); ?></p>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top:35px; border-top:1px solid var(--control-bg); padding-top:20px;">
                        <button type="submit" class="control-btn control-btn-accent" style="height:45px; border-radius:8px; font-weight:700; min-width:200px;"><?php _e('تحديث إعدادات التطبيق', 'control'); ?></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Section 4: Notifications & Email -->
        <div id="tab-notifications" class="control-tab-content" style="display:none;">
            <div class="control-card" style="border-top: 4px solid #f59e0b; padding: 25px;">
                <div style="margin-bottom:25px; border-bottom:1px solid var(--control-bg); padding-bottom:15px;">
                    <h3 style="margin:0; font-size:1.1rem; color:var(--control-text-dark);"><?php _e('إدارة التنبيهات والبريد الإلكتروني', 'control'); ?></h3>
                    <div style="color:var(--control-muted); font-size:0.8rem; margin-top:5px;"><?php _e('تخصيص نظام الإشعارات التلقائية وإعدادات خادم الإرسال SMTP.', 'control'); ?></div>
                </div>

                <form id="control-notifications-form" class="control-system-settings-form">
                    <div class="control-grid" style="grid-template-columns: 240px 1fr; gap:25px;">

                        <!-- Notifications Sidebar -->
                        <div style="background:var(--control-bg); padding:20px; border-radius:12px; border:1px solid var(--control-border); height: fit-content;">
                            <h4 style="margin:0 0 15px 0; font-size:0.9rem; color:var(--control-primary);"><?php _e('قوالب البريد', 'control'); ?></h4>
                            <div style="display:flex; flex-direction:column; gap:8px;">
                                <?php
                                $all_tpls = $wpdb->get_results("SELECT template_key FROM {$wpdb->prefix}control_email_templates");
                                $tpl_labels_map = array(
                                    'welcome_email' => __('رسالة الترحيب', 'control'),
                                    'engagement_reminder' => __('تذكير التفاعل', 'control'),
                                    'password_reset' => __('استعادة كلمة المرور', 'control'),
                                    'account_restriction' => __('تنبيه التقييد', 'control'),
                                    'new_login_alert' => __('تنبيه دخول جديد', 'control'),
                                    'email_verification_otp' => __('رمز تحقق البريد', 'control'),
                                    'password_reset_link' => __('رابط استعادة كلمة المرور', 'control'),
                                    'password_recovery_otp' => __('رمز استعادة كلمة المرور', 'control')
                                );
                                foreach($all_tpls as $i => $t): ?>
                                    <button type="button" class="tpl-nav-btn <?php echo $i === 0 ? 'active' : ''; ?>" data-tpl="<?php echo $t->template_key; ?>">
                                        <?php echo $tpl_labels_map[$t->template_key] ?? $t->template_key; ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>

                            <h4 style="margin:25px 0 15px 0; font-size:0.9rem; color:var(--control-primary);"><?php _e('سمة البريد (Theme)', 'control'); ?></h4>
                            <select name="email_theme" style="margin-bottom:10px;">
                                <option value="modern" <?php selected($settings['email_theme']->setting_value ?? 'modern', 'modern'); ?>>Modern (Gradient)</option>
                                <option value="classic" <?php selected($settings['email_theme']->setting_value ?? 'modern', 'classic'); ?>>Classic (Clean)</option>
                            </select>

                            <div style="margin-top:30px; padding:15px; background:#fff; border-radius:8px; border:1px solid var(--control-border);">
                                <small style="display:block; font-weight:700; color:var(--control-muted); margin-bottom:5px;"><?php _e('نصيحة:', 'control'); ?></small>
                                <p style="font-size:0.7rem; color:var(--control-muted); margin:0; line-height:1.4;">
                                    <?php _e('استخدم محرر HTML لتنسيق الرسائل بشكل احترافي. يمكنك إضافة صور، روابط، وجداول.', 'control'); ?>
                                </p>
                            </div>
                        </div>

                        <div>
                    <div class="control-grid" style="grid-template-columns: 1fr 1fr; gap:25px; margin-bottom:25px;">
                        <!-- SMTP Settings -->
                        <div style="background:var(--control-bg); padding:20px; border-radius:12px; border:1px solid var(--control-border);">
                            <h4 style="margin:0 0 15px 0; font-size:0.9rem; color:var(--control-primary);"><?php _e('إعدادات خادم الإرسال (SMTP)', 'control'); ?></h4>
                            <div class="control-form-group">
                                <label><?php _e('خادم SMTP', 'control'); ?></label>
                                <input type="text" name="smtp_host" value="<?php echo esc_attr($settings['smtp_host']->setting_value ?? ''); ?>" placeholder="smtp.example.com">
                            </div>
                            <div class="control-grid" style="grid-template-columns: 1fr 1fr; gap:10px;">
                                <div class="control-form-group">
                                    <label><?php _e('المنفذ (Port)', 'control'); ?></label>
                                    <input type="text" name="smtp_port" value="<?php echo esc_attr($settings['smtp_port']->setting_value ?? '587'); ?>">
                                </div>
                                <div class="control-form-group">
                                    <label><?php _e('التشفير', 'control'); ?></label>
                                    <select name="smtp_encryption">
                                        <option value="tls" <?php selected($settings['smtp_encryption']->setting_value ?? 'tls', 'tls'); ?>>TLS</option>
                                        <option value="ssl" <?php selected($settings['smtp_encryption']->setting_value ?? 'tls', 'ssl'); ?>>SSL</option>
                                        <option value="none" <?php selected($settings['smtp_encryption']->setting_value ?? 'tls', 'none'); ?>><?php _e('بدون', 'control'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="control-form-group">
                                <label><?php _e('اسم المستخدم', 'control'); ?></label>
                                <input type="text" name="smtp_user" value="<?php echo esc_attr($settings['smtp_user']->setting_value ?? ''); ?>">
                            </div>
                            <div class="control-form-group">
                                <label><?php _e('كلمة المرور', 'control'); ?></label>
                                <input type="password" name="smtp_pass" value="<?php echo esc_attr($settings['smtp_pass']->setting_value ?? ''); ?>">
                            </div>
                        </div>

                        <!-- Sender Identity -->
                        <div style="background:var(--control-bg); padding:20px; border-radius:12px; border:1px solid var(--control-border);">
                            <h4 style="margin:0 0 15px 0; font-size:0.9rem; color:var(--control-primary);"><?php _e('هوية المرسل', 'control'); ?></h4>
                            <div class="control-form-group">
                                <label><?php _e('اسم المرسل الظاهر', 'control'); ?></label>
                                <input type="text" name="sender_name" value="<?php echo esc_attr($settings['sender_name']->setting_value ?? 'Control System'); ?>">
                            </div>
                            <div class="control-form-group">
                                <label><?php _e('بريد الإرسال الرسمي', 'control'); ?></label>
                                <input type="email" name="sender_email" value="<?php echo esc_attr($settings['sender_email']->setting_value ?? get_option('admin_email')); ?>">
                            </div>
                            <div style="margin-top:20px; padding:15px; background:#fff; border:1px dashed #fbbf24; border-radius:8px;">
                                <p style="font-size:0.75rem; color:#92400e; margin:0;">
                                    <?php _e('سيتم استخدام هذه البيانات كمرسل رسمي لكافة المراسلات الصادرة من النظام لضمان الاحترافية.', 'control'); ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div style="background:var(--control-bg); padding:20px; border-radius:12px; border:1px solid var(--control-border);">
                        <h4 style="margin:0 0 20px 0; font-size:0.9rem; color:var(--control-primary); border-bottom:1px solid var(--control-border); padding-bottom:10px;"><?php _e('محرر محتوى القالب', 'control'); ?></h4>

                        <?php
                        $templates_data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}control_email_templates", OBJECT_K);
                        $first_tpl = array_key_first($templates_data);
                        foreach($templates_data as $key => $tpl):
                        ?>
                            <div id="tpl-section-<?php echo $key; ?>" class="tpl-content-section" style="<?php echo $key === $first_tpl ? '' : 'display:none;'; ?>">
                                <div class="control-form-group">
                                    <label><?php _e('عنوان البريد (Subject)', 'control'); ?></label>
                                    <input type="text" name="tpl_subject_<?php echo $key; ?>" value="<?php echo esc_attr($tpl->subject); ?>" style="font-weight:700;">
                                </div>
                                <div class="control-form-group">
                                    <label><?php _e('هيكل ومحتوى الرسالة (HTML/CSS Editor)', 'control'); ?></label>
                                    <textarea name="tpl_content_<?php echo $key; ?>" rows="12" style="font-family:monospace; font-size:0.85rem; line-height:1.5; background:#1e293b; color:#cbd5e1; border:none; padding:15px;"><?php echo esc_textarea($tpl->content); ?></textarea>
                                    <div style="margin-top:10px; display:flex; gap:10px; flex-wrap:wrap;">
                                        <small style="color:var(--control-muted);"><?php _e('المتغيرات المتاحة:', 'control'); ?></small>
                                        <code class="tpl-tag">{user_name}</code>
                                        <code class="tpl-tag">{system_name}</code>
                                        <code class="tpl-tag">{site_url}</code>
                                        <?php if($key === 'password_reset'): ?><code class="tpl-tag">{new_password}</code><?php endif; ?>
                                        <?php if($key === 'account_restriction'): ?>
                                            <code class="tpl-tag">{restriction_reason}</code>
                                            <code class="tpl-tag">{expiry_date}</code>
                                        <?php endif; ?>
                                        <?php if($key === 'new_login_alert'): ?>
                                            <code class="tpl-tag">{login_time}</code>
                                            <code class="tpl-tag">{device_type}</code>
                                            <code class="tpl-tag">{ip_address}</code>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    </div>
                    </div>

                    <div style="margin-top:30px; border-top:1px solid var(--control-bg); padding-top:20px;">
                        <button type="submit" class="control-btn control-btn-accent" style="height:48px; border-radius:8px; font-weight:800; min-width:220px;"><?php _e('حفظ إعدادات التنبيهات', 'control'); ?></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Section 5: Backup & Restore -->
        <div id="tab-backup" class="control-tab-content" style="display:none;">
            <div class="control-card" style="border-top: 4px solid #10b981; padding: 25px; margin-bottom:25px;">
                <div style="margin-bottom:25px; border-bottom:1px solid var(--control-bg); padding-bottom:15px;">
                    <h3 style="margin:0; font-size:1.1rem; color:var(--control-text-dark);"><?php _e('محرك النسخ الاحتياطي والاستعادة المطور', 'control'); ?></h3>
                    <div style="color:var(--control-muted); font-size:0.8rem; margin-top:5px;"><?php _e('تأمين بيانات النظام عبر تصديرها واستعادتها بذكاء لضمان استمرارية العمل المؤسسي.', 'control'); ?></div>
                </div>

                <div class="control-grid" style="grid-template-columns: 1fr 1fr; gap:25px;">
                    <div style="background:#f0fdf4; border:1px solid #d1fae5; padding:25px; border-radius:16px; display:flex; flex-direction:column; justify-content:space-between; height:100%;">
                        <div>
                            <div style="display:flex; align-items:center; gap:15px; margin-bottom:15px;">
                                <div style="width:48px; height:48px; background:#fff; border-radius:12px; display:flex; align-items:center; justify-content:center; box-shadow:var(--control-shadow-sm);">
                                    <span class="dashicons dashicons-cloud-save" style="color:#059669; font-size:28px; width:28px; height:28px;"></span>
                                </div>
                                <h4 style="margin:0; color:#064e3b; font-size:1rem;"><?php _e('توليد نسخة كاملة (Full Backup)', 'control'); ?></h4>
                            </div>
                            <p style="font-size:0.8rem; color:#065f46; line-height:1.6; margin-bottom:25px;"><?php _e('سيتم تجميع كافة بيانات الكوادر، الإعدادات، سجلات العمليات، والأدوار في ملف JSON مهيكل ومؤرخ للحفظ والاحتفاظ به كمرجع تاريخي.', 'control'); ?></p>
                        </div>
                        <button id="control-generate-backup" class="control-btn" style="background:#059669; border:none; width:100%; height:48px; font-weight:800; font-size:0.9rem;">
                            <span class="dashicons dashicons-download" style="margin-left:8px;"></span><?php _e('بدء النسخ الاحتياطي الآن', 'control'); ?>
                        </button>
                    </div>

                    <div style="background:#fffbeb; border:1px solid #fef3c7; padding:25px; border-radius:16px; display:flex; flex-direction:column; justify-content:space-between; height:100%;">
                        <div>
                            <div style="display:flex; align-items:center; gap:15px; margin-bottom:15px;">
                                <div style="width:48px; height:48px; background:#fff; border-radius:12px; display:flex; align-items:center; justify-content:center; box-shadow:var(--control-shadow-sm);">
                                    <span class="dashicons dashicons-cloud-upload" style="color:#d97706; font-size:28px; width:28px; height:28px;"></span>
                                </div>
                                <h4 style="margin:0; color:#78350f; font-size:1rem;"><?php _e('استعادة نقطة حفظ (Restore)', 'control'); ?></h4>
                            </div>
                            <p style="font-size:0.8rem; color:#92400e; line-height:1.6; margin-bottom:25px;"><strong><?php _e('تحذير هام:', 'control'); ?></strong> <?php _e('هذه العملية ستؤدي إلى مسح البيانات الحالية واستبدالها كلياً بمحتويات ملف النسخة المختارة.', 'control'); ?></p>
                        </div>
                        <button id="control-restore-trigger" class="control-btn" style="background:#d97706; border:none; width:100%; height:48px; font-weight:800; font-size:0.9rem;">
                            <span class="dashicons dashicons-upload" style="margin-left:8px;"></span><?php _e('رفع واستعادة ملف نسخة', 'control'); ?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Advanced Maintenance Tools -->
            <div class="control-card" style="border-top: 4px solid #ef4444; padding: 25px;">
                <div style="margin-bottom:25px; border-bottom:1px solid var(--control-bg); padding-bottom:15px;">
                    <h3 style="margin:0; font-size:1.1rem; color:#ef4444;"><?php _e('أدوات الإدارة المتقدمة والصيانة', 'control'); ?></h3>
                    <div style="color:var(--control-muted); font-size:0.8rem; margin-top:5px;"><?php _e('عمليات حساسة لإدارة البيانات الضخمة وتصفير النظام.', 'control'); ?></div>
                </div>

                <div class="control-grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:25px;">
                    <!-- User Data Package -->
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; padding:20px; border-radius:12px; display:flex; flex-direction:column; gap:15px;">
                        <div style="display:flex; align-items:center; gap:12px;">
                            <div style="width:40px; height:40px; background:#fff; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#3b82f6; box-shadow:var(--control-shadow-sm);">
                                <span class="dashicons dashicons-archive"></span>
                            </div>
                            <h5 style="margin:0; font-weight:700;"><?php _e('حزمة بيانات المستخدمين', 'control'); ?></h5>
                        </div>
                        <p style="font-size:0.75rem; color:var(--control-muted); margin:0; line-height:1.5;">
                            <?php _e('تصدير حزمة شاملة تحتوي على كافة ملفات الكوادر، البريد، النشاطات المرتبطة، والبيانات الوظيفية بتنسيق مهيكل.', 'control'); ?>
                        </p>
                        <button id="export-user-package-btn" class="control-btn" style="background:#3b82f6; border:none; margin-top:auto;">
                            <?php _e('تصدير الحزمة الآن', 'control'); ?>
                        </button>
                    </div>

                    <!-- Bulk Delete Users -->
                    <div style="background:#fff1f2; border:1px solid #fecdd3; padding:20px; border-radius:12px; display:flex; flex-direction:column; gap:15px;">
                        <div style="display:flex; align-items:center; gap:12px;">
                            <div style="width:40px; height:40px; background:#fff; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#e11d48; box-shadow:var(--control-shadow-sm);">
                                <span class="dashicons dashicons-trash"></span>
                            </div>
                            <h5 style="margin:0; font-weight:700; color:#9f1239;"><?php _e('حذف كافة الحسابات', 'control'); ?></h5>
                        </div>
                        <p style="font-size:0.75rem; color:#be123c; margin:0; line-height:1.5;">
                            <?php _e('إزالة جميع الكوادر البشرية المسجلة في النظام دفعة واحدة. (يستثنى المدير الحالي من العملية).', 'control'); ?>
                        </p>
                        <button id="bulk-delete-all-btn" class="control-btn" style="background:#e11d48; border:none; margin-top:auto;">
                            <?php _e('تنفيذ الحذف الجماعي', 'control'); ?>
                        </button>
                    </div>

                    <!-- System Reset -->
                    <div style="background:#000; border:1px solid #333; padding:20px; border-radius:12px; display:flex; flex-direction:column; gap:15px;">
                        <div style="display:flex; align-items:center; gap:12px;">
                            <div style="width:40px; height:40px; background:#1a1a1a; border-radius:10px; display:flex; align-items:center; justify-content:center; color:var(--control-accent); box-shadow:var(--control-shadow-sm);">
                                <span class="dashicons dashicons-rest-api"></span>
                            </div>
                            <h5 style="margin:0; font-weight:700; color:#fff;"><?php _e('تصفير النظام (Data Reset)', 'control'); ?></h5>
                        </div>
                        <p style="font-size:0.75rem; color:#94a3b8; margin:0; line-height:1.5;">
                            <?php _e('مسح شامل لكافة البيانات المدخلة والنشاطات مع الحفاظ على هيكلية الجداول، الأدوار الأساسية، وإعدادات الربط.', 'control'); ?>
                        </p>
                        <button id="system-reset-btn" class="control-btn control-btn-accent" style="border:none; margin-top:auto;">
                            <?php _e('تفعيل وضع التصفير', 'control'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Destructive Action Confirmation Modal -->
        <div id="control-destructive-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0, 0, 0, 0.7); z-index:100002; align-items:center; justify-content:center; backdrop-filter: blur(8px); direction: rtl;">
            <div class="control-card" style="width:100%; max-width:450px; padding:35px; text-align:center; border-radius:24px; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
                <div style="width:80px; height:80px; background:#fef2f2; color:#ef4444; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 25px;">
                    <span class="dashicons dashicons-warning" style="font-size:40px; width:40px; height:40px;"></span>
                </div>
                <h3 id="destructive-modal-title" style="margin-bottom:15px; color:var(--control-text-dark);"><?php _e('تأكيد الإجراء الحساس', 'control'); ?></h3>
                <p id="destructive-modal-desc" style="color:var(--control-muted); font-size:0.95rem; margin-bottom:25px; line-height:1.6;"></p>

                <div id="reset-word-verification" style="display:none; margin-bottom:25px;">
                    <p style="font-size:0.8rem; margin-bottom:10px; color:#ef4444; font-weight:700;"><?php _e('أدخل كلمة "تأكيد" بالأسفل للمتابعة:', 'control'); ?></p>
                    <input type="text" id="destructive-verify-word" placeholder="<?php _e('تأكيد', 'control'); ?>" style="text-align:center; height:48px; font-weight:800; border-color:#fecdd3;">
                </div>

                <div style="display:flex; gap:15px;">
                    <button id="confirm-destructive-btn" class="control-btn" style="flex:1; background:#ef4444; border:none; height:48px; font-weight:800;"><?php _e('نعم، تنفيذ الآن', 'control'); ?></button>
                    <button type="button" onclick="jQuery('#control-destructive-modal').hide()" class="control-btn" style="flex:1; background:var(--control-bg); color:var(--control-text-dark) !important; border:none; height:48px;"><?php _e('تراجع', 'control'); ?></button>
                </div>
            </div>
        </div>

        <!-- Section: Policies & Terms -->
        <div id="tab-policies" class="control-tab-content" style="display:none;">
            <div class="control-header-flex" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div>
                    <h3 style="margin:0; font-size:1.1rem; color:var(--control-text-dark);"><?php _e('إدارة السياسات والأحكام', 'control'); ?></h3>
                    <div style="color:var(--control-muted); font-size:0.8rem; margin-top:5px;"><?php _e('تحرير النصوص القانونية، سياسة الخصوصية، وشروط الخدمة.', 'control'); ?></div>
                </div>
                <button id="add-new-policy-btn" class="control-btn control-btn-accent" style="height:40px; padding:0 20px; font-weight:800;">
                    <span class="dashicons dashicons-plus-alt" style="margin-left:8px;"></span><?php _e('إضافة سياسة جديدة', 'control'); ?>
                </button>
            </div>

            <div class="control-policies-grid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap:20px;">
                <?php
                $policies = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}control_policies ORDER BY last_updated DESC");
                if ($policies):
                    foreach($policies as $policy): ?>
                    <div class="control-card policy-card" style="padding:20px; border-top: 3px solid var(--control-primary);">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:15px;">
                            <h4 style="margin:0; font-weight:800; color:var(--control-text-dark);"><?php echo esc_html($policy->title); ?></h4>
                            <div style="font-size:0.65rem; color:var(--control-muted);"><?php echo date('Y/m/d', strtotime($policy->last_updated)); ?></div>
                        </div>
                        <div style="font-size:0.8rem; color:var(--control-muted); line-height:1.5; height:60px; overflow:hidden; mask-image: linear-gradient(to bottom, black 50%, transparent 100%);">
                            <?php echo wp_strip_all_tags($policy->content); ?>
                        </div>
                        <div style="margin-top:20px; display:flex; gap:10px; border-top:1px solid var(--control-border); padding-top:15px;">
                            <button class="control-btn edit-policy-btn" style="flex:1; background:var(--control-bg); color:var(--control-text-dark) !important; font-size:0.75rem;" data-policy='<?php echo json_encode($policy, JSON_UNESCAPED_UNICODE); ?>'><?php _e('تعديل', 'control'); ?></button>
                            <button class="control-btn delete-policy-btn" style="background:#fee2e2; color:#ef4444 !important; width:40px; padding:0;" data-id="<?php echo $policy->id; ?>"><span class="dashicons dashicons-trash"></span></button>
                        </div>
                    </div>
                <?php endforeach;
                else: ?>
                    <div class="control-card" style="grid-column: 1 / -1; padding:40px; text-align:center;">
                        <span class="dashicons dashicons-info" style="font-size:48px; width:48px; height:48px; color:var(--control-muted);"></span>
                        <p style="color:var(--control-muted); margin-top:15px;"><?php _e('لم يتم العثور على أي سياسات حالياً.', 'control'); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="control-card" style="margin-top:30px; background:#eff6ff; border:1px solid #dbeafe;">
                <div style="display:flex; align-items:center; gap:15px; padding:20px;">
                    <div style="width:40px; height:40px; background:#fff; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#3b82f6; box-shadow:var(--control-shadow-sm);">
                        <span class="dashicons dashicons-shortcode"></span>
                    </div>
                    <div style="flex:1;">
                        <h4 style="margin:0; font-size:0.9rem; color:#1e40af; font-weight:800;"><?php _e('تضمين السياسات برمجياً', 'control'); ?></h4>
                        <p style="margin:5px 0 0 0; font-size:0.75rem; color:#1e40af; opacity:0.8;"><?php _e('استخدم الكود القصير أدناه لعرض كافة السياسات المفعله في أي صفحة.', 'control'); ?></p>
                    </div>
                    <div style="display:flex; align-items:center; gap:10px; background:#fff; padding:8px 15px; border-radius:8px; border:1px solid #dbeafe;">
                        <code style="color:#2563eb; font-weight:800;">[control_policies]</code>
                        <button type="button" class="control-btn" style="height:28px; padding:0 10px; font-size:0.7rem; background:#2563eb;" onclick="navigator.clipboard.writeText('[control_policies]'); alert('تم النسخ');"><?php _e('نسخ', 'control'); ?></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Policy Edit Modal -->
        <div id="control-policy-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0, 0, 0, 0.7); z-index:100002; align-items:center; justify-content:center; backdrop-filter: blur(8px); direction: rtl;">
            <div class="control-card" style="width:100%; max-width:800px; padding:35px; border-radius:24px;">
                <h3 id="policy-modal-title" style="margin-bottom:25px; color:var(--control-text-dark); font-weight:800;"><?php _e('تحرير السياسة', 'control'); ?></h3>
                <form id="control-policy-form">
                    <input type="hidden" name="id" id="policy-id">
                    <div class="control-form-group">
                        <label><?php _e('عنوان السياسة / البند', 'control'); ?></label>
                        <input type="text" name="title" id="policy-title" required placeholder="<?php _e('مثلاً: سياسة الخصوصية', 'control'); ?>">
                    </div>
                    <div class="control-form-group">
                        <label><?php _e('المحتوى التفصيلي (يدعم HTML)', 'control'); ?></label>
                        <textarea name="content" id="policy-content" rows="12" style="font-family:monospace; line-height:1.6;"></textarea>
                    </div>
                    <div style="display:flex; gap:15px; margin-top:30px;">
                        <button type="submit" class="control-btn control-btn-accent" style="flex:2; height:48px; font-weight:800;"><?php _e('حفظ السياسة', 'control'); ?></button>
                        <button type="button" onclick="jQuery('#control-policy-modal').hide()" class="control-btn" style="flex:1; background:var(--control-bg); color:var(--control-text-dark) !important; height:48px;"><?php _e('إلغاء', 'control'); ?></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Section 4: Activity Audit Log -->
        <!-- Section 6: System Maintenance -->
        <div id="tab-maintenance" class="control-tab-content" style="display:none;">
            <div class="control-card" style="border-top: 4px solid #64748b; padding: 25px;">
                <div style="margin-bottom:25px; border-bottom:1px solid var(--control-bg); padding-bottom:15px;">
                    <h3 style="margin:0; font-size:1.1rem; color:var(--control-text-dark);"><?php _e('صيانة وتحديث النظام', 'control'); ?></h3>
                    <div style="color:var(--control-muted); font-size:0.8rem; margin-top:5px;"><?php _e('أدوات لضمان تشغيل النظام بأحدث نسخة وتجاوز مشاكل التخزين المؤقت.', 'control'); ?></div>
                </div>

                <div class="control-grid" style="grid-template-columns: 1fr 1fr; gap:25px;">
                    <!-- Version Info -->
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; padding:25px; border-radius:16px; display:flex; flex-direction:column; gap:15px;">
                        <div>
                            <h4 style="margin:0 0 15px 0; color:#475569; font-size:0.95rem; font-weight:700;"><?php _e('معلومات الإصدار الحالي', 'control'); ?></h4>
                            <div style="display:flex; align-items:center; gap:15px; margin-bottom:10px;">
                                <div style="font-size:2rem; font-weight:800; color:var(--control-primary);">v<?php echo CONTROL_VERSION; ?></div>
                                <span class="control-status-indicator indicator-success"><?php _e('نظام مستقر', 'control'); ?></span>
                            </div>
                            <p style="font-size:0.75rem; color:var(--control-muted); line-height:1.6; margin:0;">
                                <?php _e('هذا هو الإصدار النشط حالياً. يتم تحميل كافة ملفات التنسيق والبرمجة بناءً على هذا الرقم لضمان التحديث الفوري.', 'control'); ?>
                            </p>
                        </div>

                        <div style="border-top:1px solid #e2e8f0; padding-top:15px; margin-bottom:15px;">
                            <h5 style="margin:0 0 10px 0; font-size:0.8rem; color:var(--control-primary); font-weight:700;"><?php _e('حالة البيئة (Environment Status):', 'control'); ?></h5>
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:0.7rem; color:var(--control-muted);">
                                <div><?php _e('نسخة WordPress:', 'control'); ?> <strong><?php echo get_bloginfo('version'); ?></strong></div>
                                <div><?php _e('نسخة PHP:', 'control'); ?> <strong><?php echo phpversion(); ?></strong></div>
                                <div><?php _e('وضع التصحيح:', 'control'); ?> <strong><?php echo (defined('WP_DEBUG') && WP_DEBUG) ? __('مفعل', 'control') : __('معطل', 'control'); ?></strong></div>
                                <div><?php _e('الذاكرة المتاحة:', 'control'); ?> <strong><?php echo ini_get('memory_limit'); ?></strong></div>
                            </div>
                        </div>

                        <div style="border-top:1px solid #e2e8f0; padding-top:15px;">
                            <h5 style="margin:0 0 10px 0; font-size:0.8rem; color:var(--control-primary); font-weight:700;"><?php _e('سجل التحديثات الأخير (Update Log):', 'control'); ?></h5>
                            <ul style="margin:0; padding-right:20px; font-size:0.7rem; color:var(--control-muted); line-height:1.6;">
                                <li><?php _e('تحديث v2.3.0: معالجة الأخطاء الحرجة في واجهة الدخول وضمان استقرار النظام.', 'control'); ?></li>
                                <li><?php _e('تحديث v2.2.0: تفعيل نظام التحقق التلقائي من الإصدار وتحديث قاعدة البيانات.', 'control'); ?></li>
                                <li><?php _e('تحديث v2.1.0: تحسين واجهة الدخول والتسجيل بالكامل وإضافة خيارات التخصيص.', 'control'); ?></li>
                                <li><?php _e('تحديث v2.0.0: ترقية هيكلية النظام وإضافة نظام التنبيهات المتقدم.', 'control'); ?></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Cache Clearing -->
                    <div style="background:#f0f9ff; border:1px solid #e0f2fe; padding:25px; border-radius:16px; display:flex; flex-direction:column; justify-content:space-between;">
                        <div>
                            <h4 style="margin:0 0 10px 0; color:#0369a1; font-size:0.95rem; font-weight:700;"><?php _e('تحديث ملفات النظام (Clear Cache)', 'control'); ?></h4>
                            <p style="font-size:0.75rem; color:#075985; line-height:1.6; margin-bottom:20px;">
                                <?php _e('إذا واجهت أي مشاكل في عرض التحديثات الجديدة، استخدم هذا الزر لمسح بيانات التخزين المؤقت للمتصفح وإعادة تحميل النظام كلياً.', 'control'); ?>
                            </p>
                        </div>
                        <button id="control-refresh-btn" class="control-btn" style="background:#0284c7; border:none; width:100%; height:44px; font-weight:700;">
                            <span class="dashicons dashicons-update" style="margin-left:8px;"></span><?php _e('تحديث فوري للنظام', 'control'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="tab-audit" class="control-tab-content" style="display:none;">
            <div class="control-card" style="border-top: 4px solid var(--control-text-dark); padding:0; overflow:hidden;">
                <div style="display:flex; justify-content: space-between; align-items: center; padding:20px 25px; background:#f8fafc; border-bottom:1px solid var(--control-border);">
                    <div>
                        <h3 style="margin:0; color:var(--control-text-dark); font-size:1.1rem;"><?php _e('سجل النشاطات وعمليات النظام', 'control'); ?></h3>
                        <div style="color:var(--control-muted); font-size:0.75rem; margin-top:3px;"><?php _e('تتبع كافة التغييرات والعمليات التي تمت بواسطة مديري النظام.', 'control'); ?></div>
                    </div>
                    <button id="control-export-audit-pdf" class="control-btn" style="background:var(--control-primary); border:none; padding:8px 15px; font-size:0.8rem; height:38px;"><span class="dashicons dashicons-media-document" style="margin-left:8px;"></span><?php _e('تصدير PDF', 'control'); ?></button>
                </div>
                <div style="max-height:600px; overflow-y:auto;">
                    <table class="control-table" style="font-size:0.8rem; border-collapse: separate; border-spacing: 0;">
                        <thead style="background:#f8fafc; position:sticky; top:0; z-index:10; box-shadow:0 2px 4px rgba(0,0,0,0.02);">
                            <tr>
                                <th style="padding:10px 15px; border-bottom:1px solid var(--control-border); width: 120px;"><?php _e('المسؤول', 'control'); ?></th>
                                <th style="padding:10px 15px; border-bottom:1px solid var(--control-border); width: 100px;"><?php _e('العملية', 'control'); ?></th>
                                <th style="padding:10px 15px; border-bottom:1px solid var(--control-border);"><?php _e('التفاصيل والسياق', 'control'); ?></th>
                                <th style="padding:10px 15px; border-bottom:1px solid var(--control-border); width: 130px;"><?php _e('التاريخ والوقت', 'control'); ?></th>
                                <th style="padding:10px 15px; border-bottom:1px solid var(--control-border); text-align:left; width: 150px;"><?php _e('الإجراءات', 'control'); ?></th>
                            </tr>
                        </thead>
                        <tbody id="control-audit-logs-body">
                            <?php
                            $action_map = array(
                                'login' => __('تسجيل دخول', 'control'),
                                'failed_login' => __('فشل دخول', 'control'),
                                'add_user' => __('إضافة كادر', 'control'),
                                'edit_user' => __('تعديل كادر', 'control'),
                                'delete_user' => __('حذف كادر', 'control'),
                                'toggle_restriction' => __('تغيير حالة حساب', 'control'),
                                'restore_backup' => __('استعادة النظام', 'control'),
                                'registration' => __('تسجيل جديد', 'control'),
                                'system_reset' => __('تصفير النظام', 'control'),
                                'profile_update' => __('تحديث الملف الشخصي', 'control'),
                                'edit_role' => __('تعديل دور', 'control'),
                                'add_role' => __('إضافة دور', 'control'),
                                'delete_role' => __('حذف دور', 'control'),
                                'edit_policy' => __('تعديل سياسة', 'control'),
                                'add_policy' => __('إضافة سياسة', 'control'),
                                'delete_policy' => __('حذف سياسة', 'control'),
                                'email_blast' => __('إرسال بريد جماعي', 'control'),
                                'system_maintenance' => __('صيانة النظام', 'control')
                            );
                            $audit_logs = Control_Audit::get_logs();
                            foreach($audit_logs as $log):
                                $meta = json_decode($log->meta_data, true);
                            ?>
                                <tr style="border-bottom:1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                    <td style="padding:8px 15px;">
                                        <div style="font-weight:700; color:var(--control-text-dark);"><?php echo esc_html($log->user_id); ?></div>
                                        <div style="font-size:0.65rem; color:var(--control-muted);"><?php echo esc_html($log->ip_address); ?></div>
                                    </td>
                                    <td style="padding:8px 15px;">
                                        <span class="control-status-indicator indicator-accent" style="font-size:0.65rem; padding:2px 6px;"><?php echo $action_map[$log->action_type] ?? $log->action_type; ?></span>
                                    </td>
                                    <td style="padding:8px 15px;">
                                        <div style="font-weight:600; color:var(--control-text-dark); margin-bottom:2px;"><?php echo esc_html($log->description); ?></div>
                                        <?php if($meta): ?>
                                            <div style="font-size:0.65rem; color:var(--control-muted); font-style: italic;">
                                                <?php echo esc_html(substr(json_encode($meta, JSON_UNESCAPED_UNICODE), 0, 100)) . (strlen(json_encode($meta)) > 100 ? '...' : ''); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding:8px 15px; white-space:nowrap; color:var(--control-muted); font-size:0.7rem;">
                                        <div style="font-weight:600;"><?php echo date('Y/m/d', strtotime($log->action_date)); ?></div>
                                        <div><?php echo date('H:i:s', strtotime($log->action_date)); ?></div>
                                    </td>
                                    <td style="padding:8px 15px; text-align:left;">
                                        <div style="display:flex; gap:5px; justify-content: flex-end;">
                                            <button class="audit-action-btn view-log-info" title="<?php _e('تفاصيل كاملة', 'control'); ?>" data-log='<?php echo json_encode($log, JSON_UNESCAPED_UNICODE); ?>'><span class="dashicons dashicons-info"></span></button>

                                            <?php if($log->action_type === 'delete_user'): ?>
                                                <button class="audit-action-btn undo-action" title="<?php _e('استعادة (تراجع)', 'control'); ?>" data-id="<?php echo $log->id; ?>" style="color:#059669;"><span class="dashicons dashicons-undo"></span></button>
                                            <?php endif; ?>

                                            <button class="audit-action-btn delete-log-entry" title="<?php _e('حذف السجل', 'control'); ?>" data-id="<?php echo $log->id; ?>" style="color:#ef4444;"><span class="dashicons dashicons-trash"></span></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
