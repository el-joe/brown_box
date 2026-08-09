import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import $ from 'jquery';
import 'select2';
import DataTable from 'datatables.net-bs5';
import flatpickr from 'flatpickr';
import Sortable from 'sortablejs';

window.$ = window.jQuery = $;
window.DataTable = DataTable;
window.flatpickr = flatpickr;
window.Sortable = Sortable;

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

    document.querySelectorAll('.admin-datepicker').forEach((el) => flatpickr(el));

    document.querySelectorAll('.admin-sortable').forEach((el) => {
        Sortable.create(el, {
            handle: '.sortable-handle',
            animation: 150,
        });
    });
});
