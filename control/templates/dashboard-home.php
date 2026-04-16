<?php
global $wpdb;
$current_user = Control_Auth::current_user();
if ( Control_Auth::is_admin() ) {
    $total_users = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}control_staff");
    ?>
    <div class="control-header-flex" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-weight:800; font-size:1.3rem; margin:0; color:#1e293b;"><?php _e('لوحة تحكم النظام', 'control'); ?></h2>
    </div>

    <!-- Enhanced System Metrics -->
    <div class="control-metrics-grid" style="margin-bottom:30px; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px;">
        <div class="control-card" style="border-top: none; background: linear-gradient(45deg, #0f172a, #334155); color: #fff; padding: 25px; display: flex; align-items: center; gap: 20px;">
            <div style="width: 55px; height: 55px; background: rgba(255,255,255,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--control-accent);">
                <span class="dashicons dashicons-groups" style="font-size: 28px; width: 28px; height: 28px;"></span>
            </div>
            <div>
                <div style="font-size: 0.8rem; color: rgba(255,255,255,0.7); font-weight: 600;"><?php _e('إجمالي المسجلين', 'control'); ?></div>
                <div style="font-size: 1.6rem; font-weight: 800;"><?php echo number_format($total_users); ?></div>
            </div>
        </div>

        <?php
        $roles_count = $wpdb->get_results("SELECT role, COUNT(*) as count FROM {$wpdb->prefix}control_staff GROUP BY role", OBJECT_K);
        $role_labels = Control_Auth::get_roles();
        $coach_count = $roles_count['coach']->count ?? 0;
        ?>
        <div class="control-card" style="border-top: none; padding: 25px; display: flex; align-items: center; gap: 20px; border: 1px solid var(--control-border);">
            <div style="width: 55px; height: 55px; background: var(--control-accent-soft); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--control-accent);">
                <span class="dashicons dashicons-businessman" style="font-size: 28px; width: 28px; height: 28px;"></span>
            </div>
            <div>
                <div style="font-size: 0.8rem; color: var(--control-muted); font-weight: 600;"><?php _e('المدربين الرياضيين', 'control'); ?></div>
                <div style="font-size: 1.6rem; font-weight: 800; color: var(--control-text-dark);"><?php echo number_format($coach_count); ?></div>
            </div>
        </div>

        <div class="control-card" style="border-top: none; padding: 25px; display: flex; align-items: center; gap: 20px; border: 1px solid var(--control-border);">
            <div style="width: 55px; height: 55px; background: #ecfdf5; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #10b981;">
                <span class="dashicons dashicons-yes-alt" style="font-size: 28px; width: 28px; height: 28px;"></span>
            </div>
            <div>
                <div style="font-size: 0.8rem; color: var(--control-muted); font-weight: 600;"><?php _e('الحسابات النشطة', 'control'); ?></div>
                <div style="font-size: 1.6rem; font-weight: 800; color: var(--control-text-dark);"><?php echo $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}control_staff WHERE is_restricted = 0"); ?></div>
            </div>
        </div>
    </div>

    <div class="control-grid main-dashboard-grid" style="grid-template-columns: 1fr; gap: 25px;">
        <div class="control-dashboard-main-column">
            <!-- System Overview Card -->
            <div class="control-card" style="padding: 30px; border-top: 5px solid var(--control-accent);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
                    <div style="display:flex; align-items:center; gap:20px;">
                        <div style="width:60px; height:60px; background:var(--control-bg); border-radius:15px; display:flex; align-items:center; justify-content:center;">
                            <span class="dashicons dashicons-performance" style="font-size:30px; color:var(--control-accent); width:30px; height:30px;"></span>
                        </div>
                        <div>
                            <h3 style="margin:0; font-size:1.2rem;"><?php _e('توزع الكوادر البشرية', 'control'); ?></h3>
                            <p style="margin:5px 0 0 0; color:var(--control-muted); font-size:0.85rem;"><?php _e('إحصائية شاملة لعدد المسجلين حسب كل دور وظيفي.', 'control'); ?></p>
                        </div>
                    </div>
                    <a href="<?php echo add_query_arg('control_view', 'users'); ?>" class="control-btn" style="background:var(--control-primary); border:none;"><?php _e('إدارة الكوادر', 'control'); ?></a>
                </div>

                <div class="workforce-distribution-grid">
                    <?php
                    // Limit to 6 roles for the metrics
                    $display_roles = array_slice($role_labels, 0, 6, true);
                    foreach($display_roles as $role_key => $label):
                        $count = $roles_count[$role_key]->count ?? 0;
                        ?>
                        <div class="workforce-card">
                            <div class="workforce-label"><?php echo $label; ?></div>
                            <div class="workforce-count"><?php echo number_format($count); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="control-card" style="padding: 0; overflow: hidden;">
                <div style="padding: 20px 30px; background: #f8fafc; border-bottom: 1px solid var(--control-border); display:flex; justify-content: space-between; align-items: center;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <span class="dashicons dashicons-list-view" style="color:var(--control-accent);"></span>
                        <h3 style="margin:0; font-size:1rem;"><?php _e('آخر 5 نشاطات في النظام', 'control'); ?></h3>
                    </div>
                    <a href="<?php echo add_query_arg('control_view', 'settings'); ?>#tab-audit" style="font-size:0.75rem; color:var(--control-accent); font-weight:800; text-decoration:none;"><?php _e('سجل النشاطات الكامل', 'control'); ?></a>
                </div>
                <div style="padding: 10px 30px 30px 30px;">
                    <?php
                    $recent_logs = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}control_activity_logs ORDER BY action_date DESC LIMIT 5");
                    if (empty($recent_logs)): ?>
                        <div style="text-align:center; padding:40px 20px;">
                            <span class="dashicons dashicons-info" style="font-size:30px; color:var(--control-border); width:30px; height:30px;"></span>
                            <p style="font-size:0.8rem; color:#94a3b8; margin-top:10px;"><?php _e('لا توجد نشاطات مسجلة حالياً.', 'control'); ?></p>
                        </div>
                    <?php else:
                        foreach($recent_logs as $log): ?>
                            <div style="padding: 15px 0; border-bottom: 1px solid #f1f5f9; display:flex; gap:12px;">
                                <div style="width:8px; height:8px; border-radius:50%; background:var(--control-accent); margin-top:5px; flex-shrink:0;"></div>
                                <div style="flex:1;">
                                    <div style="font-size:0.8rem; font-weight:600; color:var(--control-text-dark); line-height:1.4;"><?php echo esc_html($log->description); ?></div>
                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:6px;">
                                        <small style="color:var(--control-muted); font-size:0.65rem;"><?php echo date('H:i - Y/m/d', strtotime($log->action_date)); ?></small>
                                        <small style="background:var(--control-bg); padding:2px 6px; border-radius:4px; font-size:0.6rem; color:var(--control-muted);"><?php echo esc_html($log->user_id); ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach;
                    endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
} else {
    ?>
    <div class="control-header-flex" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-weight:800; font-size:1.3rem; margin:0; color:#1e293b;"><?php _e('لوحة المعلومات', 'control'); ?></h2>
    </div>
    <div class="control-card" style="border-radius: 12px; padding: 20px;">
        <p><?php _e('أهلاً بك في نظام كنترول الإداري.', 'control'); ?></p>
        <p style="color:#64748b; font-size:0.9rem;"><?php _e('برجاء التواصل مع الإدارة إذا كنت بحاجة إلى صلاحيات إضافية للوصول إلى لوحة التحكم.', 'control'); ?></p>
    </div>
    <?php
}
?>
