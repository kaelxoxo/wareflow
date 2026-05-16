<?php
class CustomFieldController {
    public function index(): void {
        Auth::guard('manage_custom_fields');
        $tid    = Auth::tenantId();
        $fields = CustomField::all($tid, 'item');
        view('custom-fields/index', compact('fields'));
    }

    public function create(): void {
        Auth::guard('manage_custom_fields');
        Auth::verifyCsrf();
        $tid = Auth::tenantId();

        $label = trim($_POST['label'] ?? '');
        $type  = $_POST['field_type'] ?? '';
        $validTypes = ['text','number','date','select','checkbox','textarea','url','email'];

        if (!$label) { flash('error', 'Field label is required.'); redirect('/custom-fields'); }
        if (!in_array($type, $validTypes)) { flash('error', 'Invalid field type.'); redirect('/custom-fields'); }

        $options = [];
        if ($type === 'select' && !empty($_POST['options'])) {
            $options = array_filter(array_map('trim', explode("\n", $_POST['options'])));
        }

        CustomField::create($tid, [
            'label'       => $label,
            'field_type'  => $type,
            'entity_type' => 'item',
            'required'    => !empty($_POST['required']),
            'options'     => $options,
        ]);

        ActivityLog::log($tid, Auth::id(), 'custom_field.created', 'custom_field', null, ['label' => $label]);
        flash('success', "Custom field '{$label}' added.");
        redirect('/custom-fields');
    }

    public function delete(int $id): void {
        Auth::guard('manage_custom_fields');
        Auth::verifyCsrf();
        $tid   = Auth::tenantId();
        $field = CustomField::find($id, $tid);
        if (!$field) { flash('error', 'Field not found.'); redirect('/custom-fields'); }
        CustomField::delete($id, $tid);
        ActivityLog::log($tid, Auth::id(), 'custom_field.deleted', 'custom_field', $id, ['label' => $field['label']]);
        flash('success', "Field '{$field['label']}' deleted.");
        redirect('/custom-fields');
    }
}
