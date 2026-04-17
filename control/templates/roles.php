<?php
global $wpdb;
$roles = $wpdb->get_results( "SELECT r.*, (SELECT COUNT(*) FROM {$wpdb->prefix}control_staff WHERE role = r.role_key) as user_count FROM {$wpdb->prefix}control_roles r" );
$available_permissions = Control_Auth::get_permissions_registry();
?>

<div class="control-header-flex" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-weight:800; font-size:1.3rem; margin:0; color:var(--control-text-dark);"><?php _e('الأدوار وصلاحيات النظام', 'control'); ?></h2>
    <button id="add-role-btn" class="control-btn" style="background:var(--control-primary); border:none;">
        <span class="dashicons dashicons-plus-alt" style="margin-left:5px;"></span><?php _e('إضافة دور جديد', 'control'); ?>
    </button>
</div>

<div class="control-grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
    <?php foreach($roles as $role):
        $perms = json_decode($role->permissions, true) ?: array();
        $perm_count = count($perms);
    ?>
        <div class="control-card role-card" data-role='<?php echo json_encode($role); ?>'>
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:15px;">
                <div>
                    <h3 style="margin:0;"><?php echo esc_html($role->role_name); ?></h3>
                    <code style="font-size:0.7rem; color:var(--control-muted);"><?php echo esc_html($role->role_key); ?></code>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <div class="user-count-badge" title="<?php _e('عدد المستخدمين بهذا الدور', 'control'); ?>">
                        <span class="dashicons dashicons-admin-users" style="font-size:12px; width:12px; height:12px; margin-left:3px;"></span>
                        <?php echo number_format($role->user_count); ?>
                    </div>
                    <?php if($role->is_system): ?>
                        <span class="control-status-indicator indicator-accent" style="font-size:0.6rem; padding:2px 6px; white-space:nowrap;"><?php _e('أساسي', 'control'); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div style="font-size:0.8rem; color:var(--control-text); margin-bottom:20px;">
                <strong><?php echo $perm_count; ?></strong> <?php _e('صلاحيات مفعلة', 'control'); ?>
            </div>

            <div style="display:flex; gap:10px; border-top:1px solid var(--control-border); padding-top:15px;">
                <button class="control-btn edit-role-btn" style="flex:1; background:var(--control-bg); color:var(--control-text) !important; border:none; font-size:0.8rem;">
                    <span class="dashicons dashicons-edit" style="margin-left:5px;"></span><?php _e('تعديل', 'control'); ?>
                </button>
                <?php if(!$role->is_system): ?>
                    <button class="control-btn delete-role-btn" data-id="<?php echo $role->id; ?>" style="background:#fef2f2; color:#ef4444 !important; border:none; width:42px; padding:0;">
                        <span class="dashicons dashicons-trash"></span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Role Delete Confirmation Modal -->
<div id="role-delete-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:100002; align-items:center; justify-content:center; backdrop-filter: blur(8px); direction: <?php echo is_rtl() ? "rtl" : "ltr"; ?>;">
    <div class="control-card" style="width:100%; max-width:450px; padding:35px; text-align:center; border-radius:24px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
        <div style="width:80px; height:80px; background:#fef2f2; color:#ef4444; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 25px;">
            <span class="dashicons dashicons-trash" style="font-size:40px; width:40px; height:40px;"></span>
        </div>
        <h3 style="margin-bottom:15px; color:var(--control-text-dark);"><?php _e('حذف الدور والنظام', 'control'); ?></h3>
        <p style="color:var(--control-muted); font-size:0.95rem; margin-bottom:25px; line-height:1.6;">
            <?php _e('أنت على وشك حذف هذا الدور نهائياً. يرجى اختيار دور بديل للمستخدمين الحاليين المرتبطين بهذا الدور.', 'control'); ?>
        </p>

        <div class="control-form-group" style="text-align:right; margin-bottom:30px;">
            <label style="font-weight:700; color:var(--control-text-dark);"><?php _e('الدور البديل للمستخدمين:', 'control'); ?></label>
            <select id="replacement-role-select" style="height:48px;">
                <?php foreach($roles as $r): ?>
                    <option value="<?php echo $r->role_key; ?>"><?php echo $r->role_name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display:flex; gap:15px;">
            <button id="confirm-role-delete-btn" class="control-btn" style="flex:1; background:#ef4444; border:none; height:48px; font-weight:800;"><?php _e('تأكيد الحذف النهائي', 'control'); ?></button>
            <button type="button" onclick="jQuery('#role-delete-modal').hide()" class="control-btn" style="flex:1; background:var(--control-bg); color:var(--control-text-dark) !important; border:none; height:48px;"><?php _e('إلغاء', 'control'); ?></button>
        </div>
    </div>
</div>

<!-- Role Modal -->
<div id="role-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:10001; align-items:center; justify-content:center; backdrop-filter: blur(4px);">
    <div class="control-card" style="width:100%; max-width:600px; padding:0; border-radius:20px; overflow:hidden;">
        <div style="background:var(--control-primary); color:#fff; padding:20px 30px; display:flex; justify-content:space-between; align-items:center;">
            <h3 id="role-modal-title" style="color:#fff; margin:0; font-size:1.1rem;"><?php _e('إعدادات الدور والصلاحيات', 'control'); ?></h3>
            <button class="close-role-modal" style="background:none; border:none; color:#fff; cursor:pointer;"><span class="dashicons dashicons-no-alt"></span></button>
        </div>

        <form id="role-form" style="padding:30px;">
            <input type="hidden" name="id" id="role-db-id">

            <div class="control-grid" style="grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:25px;">
                <div class="control-form-group">
                    <label><?php _e('اسم الدور (بالعربية)', 'control'); ?></label>
                    <input type="text" name="role_name" id="role-name-input" required placeholder="<?php _e('مثال: مدير تقني', 'control'); ?>">
                </div>
                <div class="control-form-group">
                    <label><?php _e('مفتاح الدور (English Key)', 'control'); ?></label>
                    <input type="text" name="role_key" id="role-key-input" required placeholder="example_role">
                </div>
            </div>

            <h4 style="margin:0 0 15px 0; font-size:0.95rem; border-bottom:1px solid var(--control-border); padding-bottom:10px;"><?php _e('مصفوفة الصلاحيات', 'control'); ?></h4>

            <div style="max-height:300px; overflow-y:auto; padding-right:5px;">
                <?php
                $categories = array();
                foreach($available_permissions as $key => $p) {
                    $categories[$p['category']][$key] = $p['label'];
                }

                foreach($categories as $cat => $perms): ?>
                    <div style="margin-bottom:20px;">
                        <div style="font-weight:800; font-size:0.75rem; color:var(--control-accent); text-transform:uppercase; margin-bottom:10px;"><?php echo $cat; ?></div>
                        <div class="control-grid" style="grid-template-columns: 1fr 1fr; gap:10px;">
                            <?php foreach($perms as $k => $l): ?>
                                <label style="display:flex; align-items:center; gap:10px; background:var(--control-bg); padding:10px; border-radius:8px; cursor:pointer; font-size:0.85rem;">
                                    <input type="checkbox" name="permissions[<?php echo $k; ?>]" value="1" class="perm-checkbox">
                                    <?php echo $l; ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div id="role-confirmation-step" style="display:none; background:var(--control-bg); border:1px solid var(--control-border); padding:20px; border-radius:12px; margin-top:20px;">
                <h5 style="margin:0 0 10px 0; color:#ef4444;"><?php _e('تأكيد التعديلات', 'control'); ?></h5>
                <p style="font-size:0.8rem; margin-bottom:15px;"><?php _e('أنت على وشك تحديث صلاحيات هذا الدور. سيتم تطبيق التغييرات فوراً على كافة المستخدمين المرتبطين.', 'control'); ?></p>
                <div id="permissions-summary" style="font-size:0.75rem; font-weight:700;"></div>
                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="submit" class="control-btn" style="flex:1; background:#10b981; border:none;"><?php _e('تأكيد وحفظ نهائي', 'control'); ?></button>
                    <button type="button" id="back-to-edit" class="control-btn" style="flex:1; background:#fff; color:var(--control-text-dark) !important; border:1px solid var(--control-border);"><?php _e('عودة للتعديل', 'control'); ?></button>
                </div>
            </div>

            <div id="role-initial-actions" style="display:flex; gap:15px; margin-top:30px; border-top:1px solid var(--control-border); padding-top:20px;">
                <button type="button" id="prepare-save-btn" class="control-btn" style="flex:2; background:var(--control-primary); border:none; font-weight:800;"><?php _e('مراجعة وحفظ', 'control'); ?></button>
                <button type="button" class="control-btn close-role-modal" style="flex:1; background:var(--control-bg); color:var(--control-text) !important; border:none;"><?php _e('إلغاء', 'control'); ?></button>
            </div>
        </form>
    </div>
</div>

<style>
.user-count-badge {
    background: #fdf2f8; /* Pastel Pink */
    color: #9d174d;
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}
</style>

<script>
jQuery(document).ready(function($) {
    const modal = $('#role-modal');

    $('#add-role-btn').on('click', function() {
        $('#role-form')[0].reset();
        $('#role-db-id').val('');
        $('#role-key-input').prop('readonly', false);
        $('#role-modal-title').text('<?php _e("إضافة دور جديد", "control"); ?>');
        modal.css('display', 'flex');
    });

    $(document).on('click', '.edit-role-btn', function() {
        const r = $(this).closest('.role-card').data('role');
        $('#role-db-id').val(r.id);
        $('#role-name-input').val(r.role_name);
        $('#role-key-input').val(r.role_key).prop('readonly', r.is_system == 1);

        $('#role-form .perm-checkbox').prop('checked', false);
        const perms = JSON.parse(r.permissions);
        if (perms) {
            for (const [key, val] of Object.entries(perms)) {
                if (val) $(`.perm-checkbox[name="permissions[${key}]"]`).prop('checked', true);
            }
        }

        $('#role-modal-title').text('<?php _e("تعديل الدور", "control"); ?>');
        modal.css('display', 'flex');
    });

    $('.close-role-modal').on('click', function() {
        modal.hide();
        $('#role-confirmation-step').hide();
        $('#role-initial-actions').show();
        $('.role-wizard-step').show();
    });

    $('#prepare-save-btn').on('click', function() {
        let summary = [];
        $('#role-form .perm-checkbox:checked').each(function() {
            summary.push($(this).parent().text().trim());
        });

        if (summary.length === 0) summary.push('<?php _e("لا توجد صلاحيات مختارة", "control"); ?>');

        $('#permissions-summary').html('<?php _e("الصلاحيات المختارة:", "control"); ?> ' + summary.join('، '));
        $('#role-initial-actions').hide();
        $('.control-form-group, h4, .max-height-300').hide(); // Hide editing parts
        $('#role-confirmation-step').fadeIn();
    });

    $('#back-to-edit').on('click', function() {
        $('#role-confirmation-step').hide();
        $('#role-initial-actions').show();
        $('.control-form-group, h4, .max-height-300').fadeIn();
    });

    $('#role-form').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize() + '&action=control_save_role&nonce=' + control_ajax.nonce;
        $.post(control_ajax.ajax_url, formData, function(res) {
            if (res.success) {
                location.reload();
            } else {
                alert(res.data || '<?php echo esc_js(__("حدث خطأ", "control")); ?>');
            }
        });
    });

    let roleToDelete = null;
    let roleKeyToDelete = null;

    $(document).on('click', '.delete-role-btn', function() {
        const card = $(this).closest('.role-card');
        const role = card.data('role');
        roleToDelete = role.id;
        roleKeyToDelete = role.role_key;

        // Populate replacement options excluding the role being deleted
        $('#replacement-role-select option').show();
        $(`#replacement-role-select option[value="${roleKeyToDelete}"]`).hide();

        // Pick the first visible option as default
        $('#replacement-role-select option:not([style*="display: none"])').first().prop('selected', true);

        $('#role-delete-modal').css('display', 'flex');
    });

    $('#confirm-role-delete-btn').on('click', function() {
        const replacement = $('#replacement-role-select').val();
        const $btn = $(this);
        $btn.prop('disabled', true).text('<?php _e("جاري الحذف...", "control"); ?>');

        $.post(control_ajax.ajax_url, {
            action: 'control_delete_role',
            id: roleToDelete,
            replacement_role_key: replacement,
            nonce: control_ajax.nonce
        }, function(res) {
            if (res.success) {
                location.reload();
            } else {
                alert(res.data || '<?php echo esc_js(__("حدث خطأ", "control")); ?>');
                $btn.prop('disabled', false).text('<?php _e("تأكيد الحذف النهائي", "control"); ?>');
            }
        });
    });
});
</script>
