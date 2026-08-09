import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import $ from 'jquery';
import 'select2';
import DataTable from 'datatables.net-bs5';

window.$ = window.jQuery = $;
window.DataTable = DataTable;

Alpine.plugin(collapse);
window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    $('.admin-select2').each(function () {
        $(this).select2({
            width: '100%',
            placeholder: $(this).data('placeholder') || null,
            allowClear: true,
        });
    });
});
