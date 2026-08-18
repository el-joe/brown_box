{{-- Shared address form fields used by the create/edit address modals (account + checkout). --}}
<div class="web-checkout-field sm:col-span-2">
    <label for="addr-label">{{ __('website.address_label') }}</label>
    <input id="addr-label" x-model="form.label" type="text" placeholder="{{ __('website.address_label_placeholder') }}">
</div>
<div class="web-checkout-field">
    <label for="addr-name">{{ __('website.full_name') }}</label>
    <input id="addr-name" x-model="form.name" type="text" required>
    <template x-if="errors.name"><p class="web-field-error"><i class="fa-solid fa-circle-exclamation"></i> <span x-text="errors.name[0]"></span></p></template>
</div>
<div class="web-checkout-field">
    <label for="addr-phone">{{ __('website.phone_number') }}</label>
    <input id="addr-phone" x-model="form.phone" type="tel" required>
    <template x-if="errors.phone"><p class="web-field-error"><i class="fa-solid fa-circle-exclamation"></i> <span x-text="errors.phone[0]"></span></p></template>
</div>
<div class="web-checkout-field">
    <label for="addr-governorate">{{ __('website.governorate') }}</label>
    <select id="addr-governorate" x-model="form.governorate_id" @change="onGovernorateChange()" required>
        <option value="">—</option>
        <template x-for="gov in governorates" :key="gov.id">
            <option :value="gov.id" x-text="gov.name"></option>
        </template>
    </select>
    <template x-if="errors.governorate_id"><p class="web-field-error"><i class="fa-solid fa-circle-exclamation"></i> <span x-text="errors.governorate_id[0]"></span></p></template>
</div>
<div class="web-checkout-field">
    <label for="addr-city">{{ __('website.city') }}</label>
    <select id="addr-city" x-model="form.city_id" required>
        <option value="">—</option>
        <template x-for="city in availableCities" :key="city.id">
            <option :value="city.id" x-text="city.name"></option>
        </template>
    </select>
    <template x-if="errors.city_id"><p class="web-field-error"><i class="fa-solid fa-circle-exclamation"></i> <span x-text="errors.city_id[0]"></span></p></template>
</div>
<div class="web-checkout-field sm:col-span-2">
    <label for="addr-address-line">{{ __('website.street_address') }}</label>
    <input id="addr-address-line" x-model="form.address_line" type="text" required>
    <template x-if="errors.address_line"><p class="web-field-error"><i class="fa-solid fa-circle-exclamation"></i> <span x-text="errors.address_line[0]"></span></p></template>
</div>
<label class="web-checkout-checkbox sm:col-span-2">
    <input type="checkbox" x-model="form.is_default">
    <span>{{ __('website.set_default') }}</span>
</label>
