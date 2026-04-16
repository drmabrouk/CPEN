        </div><!-- .control-content-inner -->
    </main><!-- .control-main-content -->
</div><!-- .control-dashboard -->

<!-- Self Profile Modal -->
<div id="self-profile-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:100000; align-items:center; justify-content:center; backdrop-filter: blur(4px); direction: rtl;">
    <div class="control-card" style="width:100%; max-width:650px; padding:0; border-radius:20px; overflow:hidden; box-shadow: 0 50px 100px -20px rgba(0, 0, 0, 0.25);">
        <div style="background:var(--control-primary); color:#fff; padding:25px 30px; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h3 style="color:#fff; margin:0; font-size:1.2rem;"><?php _e('تعديل ملفي الشخصي', 'control'); ?></h3>
                <div id="self-wizard-step-label" style="opacity:0.7; font-size:0.8rem; margin-top:5px;"><?php _e('المعلومات الشخصية', 'control'); ?></div>
            </div>
            <div style="display:flex; gap:10px;" id="self-wizard-dots">
                <span class="dot active" data-step="1"></span>
                <span class="dot" data-step="2"></span>
                <span class="dot" data-step="3"></span>
                <span class="dot" data-step="4"></span>
            </div>
            <button class="close-self-modal" style="background:none; border:none; color:#fff; cursor:pointer;"><span class="dashicons dashicons-no-alt"></span></button>
        </div>

        <form id="self-profile-form" style="padding:30px;">
            <?php
                $u = Control_Auth::current_user();
                global $wpdb;
                $user_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}control_staff WHERE id = %s", $u->id));
                $countries = array(
                    '+20' => array('flag' => '🇪🇬', 'name' => 'مصر'),
                    '+971' => array('flag' => '🇦🇪', 'name' => 'الإمارات'),
                    '+966' => array('flag' => '🇸🇦', 'name' => 'السعودية'),
                    '+965' => array('flag' => '🇰🇼', 'name' => 'الكويت'),
                    '+974' => array('flag' => '🇶🇦', 'name' => 'قطر'),
                    '+973' => array('flag' => '🇧🇭', 'name' => 'البحرين'),
                    '+968' => array('flag' => '🇴🇲', 'name' => 'عمان'),
                );
            ?>

            <!-- Step 1: Personal Info -->
            <div id="self-step-1" class="self-wizard-step">
                <div style="display:flex; gap:25px; margin-bottom:25px; align-items:center; background:var(--control-bg); padding:20px; border-radius:16px; border:1px solid var(--control-border);">
                    <div id="self-profile-preview" style="width:90px; height:90px; background:#fff; border:2px dashed var(--control-border); border-radius:50%; display:flex; align-items:center; justify-content:center; overflow:hidden; cursor:pointer; position:relative; flex-shrink:0;">
                        <?php if(!empty($user_data->profile_image)): ?>
                            <img src="<?php echo esc_url($user_data->profile_image); ?>" style="width:100%; height:100%; object-fit:cover;">
                        <?php else:
                            $avatar_class = ($user_data->gender === 'female') ? 'avatar-female' : 'avatar-male';
                        ?>
                            <div class="avatar-placeholder <?php echo $avatar_class; ?>">
                                <?php echo strtoupper(substr($user_data->first_name, 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div style="flex:1;">
                        <button type="button" id="upload-self-image" class="control-btn" style="background:#fff; color:var(--control-text-dark) !important; border:1px solid var(--control-border); padding:6px 15px; font-size:0.8rem; min-height:36px;"><?php _e('تغيير الصورة', 'control'); ?></button>
                        <input type="hidden" name="profile_image" id="self-image-url" value="<?php echo esc_attr($user_data->profile_image ?? ''); ?>">
                    </div>
                </div>

                <div class="control-grid" style="grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="control-form-group">
                        <label><?php _e('الاسم الأول', 'control'); ?> *</label>
                        <input type="text" name="first_name" value="<?php echo esc_attr($user_data->first_name); ?>" required>
                    </div>
                    <div class="control-form-group">
                        <label><?php _e('اسم العائلة', 'control'); ?> *</label>
                        <input type="text" name="last_name" value="<?php echo esc_attr($user_data->last_name); ?>" required>
                    </div>
                </div>
                <div class="control-form-group">
                    <label><?php _e('الجنس', 'control'); ?></label>
                    <select name="gender">
                        <option value="male" <?php selected($user_data->gender, 'male'); ?>><?php _e('ذكر', 'control'); ?></option>
                        <option value="female" <?php selected($user_data->gender, 'female'); ?>><?php _e('أنثى', 'control'); ?></option>
                    </select>
                </div>
            </div>

            <!-- Step 2: Academic Info -->
            <div id="self-step-2" class="self-wizard-step" style="display:none;">
                <div class="control-grid" style="grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="control-form-group">
                        <label><?php _e('الدرجة العلمية', 'control'); ?></label>
                        <select name="degree">
                            <option value="diploma" <?php selected($user_data->degree, 'diploma'); ?>><?php _e('دبلوم', 'control'); ?></option>
                            <option value="bachelor" <?php selected($user_data->degree, 'bachelor'); ?>><?php _e('بكالوريوس', 'control'); ?></option>
                            <option value="master" <?php selected($user_data->degree, 'master'); ?>><?php _e('ماجستير', 'control'); ?></option>
                            <option value="phd" <?php selected($user_data->degree, 'phd'); ?>><?php _e('دكتوراه', 'control'); ?></option>
                        </select>
                    </div>
                    <div class="control-form-group">
                        <label><?php _e('التخصص الأكاديمي', 'control'); ?></label>
                        <input type="text" name="specialization" value="<?php echo esc_attr($user_data->specialization); ?>">
                    </div>
                </div>
                <div class="control-form-group">
                    <label><?php _e('الجامعة / المؤسسة المانحة', 'control'); ?></label>
                    <input type="text" name="institution" value="<?php echo esc_attr($user_data->institution); ?>">
                </div>
            </div>

            <!-- Step 3: Employment Info -->
            <div id="self-step-3" class="self-wizard-step" style="display:none;">
                <div class="control-grid" style="grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="control-form-group">
                        <label><?php _e('جهة العمل الحالية', 'control'); ?></label>
                        <input type="text" name="employer_name" value="<?php echo esc_attr($user_data->employer_name); ?>">
                    </div>
                    <div class="control-form-group">
                        <label><?php _e('المسمى الوظيفي', 'control'); ?></label>
                        <input type="text" name="job_title" value="<?php echo esc_attr($user_data->job_title); ?>">
                    </div>
                </div>
                <div class="control-form-group">
                    <label><?php _e('بريد العمل', 'control'); ?></label>
                    <input type="email" name="work_email" value="<?php echo esc_attr($user_data->work_email); ?>">
                </div>
            </div>

            <!-- Step 4: Account Settings -->
            <div id="self-step-4" class="self-wizard-step" style="display:none;">
                <div class="control-grid" style="grid-template-columns: 1.5fr 1fr; gap: 20px;">
                    <div class="control-form-group">
                        <label><?php _e('البريد الإلكتروني', 'control'); ?></label>
                        <input type="email" name="email" value="<?php echo esc_attr($user_data->email); ?>">
                    </div>
                    <div class="control-form-group">
                        <label><?php _e('اسم المستخدم', 'control'); ?></label>
                        <input type="text" name="username" value="<?php echo esc_attr($user_data->username); ?>">
                    </div>
                </div>
                <div class="control-form-group" style="margin-top:15px; padding:20px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px;">
                    <label style="color:var(--control-primary); font-weight:800;"><?php _e('تغيير كلمة المرور', 'control'); ?></label>
                    <input type="password" name="password" placeholder="••••••••" style="background:#fff; font-family:monospace; font-size:1.1rem; letter-spacing:2px;">
                    <small style="color:var(--control-muted); font-size:0.7rem; margin-top:8px; display:block;"><?php _e('اتركها فارغة إذا كنت لا ترغب في تغييرها.', 'control'); ?></small>
                </div>
            </div>

            <div style="display:flex; gap:15px; margin-top:30px; border-top:1px solid var(--control-border); padding-top:25px;">
                <button type="button" id="self-wizard-prev" class="control-btn" style="flex:1; background:var(--control-bg); color:var(--control-text) !important; border:none; display:none;"><?php _e('السابق', 'control'); ?></button>
                <button type="button" id="self-wizard-next" class="control-btn" style="flex:2; background:var(--control-primary); border:none;"><?php _e('التالي', 'control'); ?></button>
                <button type="submit" id="self-wizard-submit" class="control-btn" style="flex:2; background:var(--control-accent); color:var(--control-primary-soft) !important; border:none; display:none; font-weight:800;"><?php _e('حفظ التعديلات', 'control'); ?></button>
                <button type="button" class="control-btn close-self-modal" style="flex:1; background:var(--control-muted); border:none;"><?php _e('إلغاء', 'control'); ?></button>
            </div>
        </form>
    </div>
</div>

<footer class="control-footer" style="display:flex; justify-content:space-between; align-items:center; padding:15px 25px; font-size:0.75rem; color:var(--control-muted); margin-top:20px; background:transparent; border:none;">
    <?php
        global $wpdb;
        $sys_name = $wpdb->get_var("SELECT setting_value FROM {$wpdb->prefix}control_settings WHERE setting_key = 'system_name'") ?: 'Control';
        $domain = $_SERVER['HTTP_HOST'];
    ?>
    <div>&copy; <?php echo date('Y'); ?> <?php echo esc_html($sys_name); ?> - <?php echo esc_html($domain); ?></div>
    <div style="font-weight:700; opacity:0.4;"><?php _e('كافة الحقوق محفوظة', 'control'); ?></div>
</footer>
