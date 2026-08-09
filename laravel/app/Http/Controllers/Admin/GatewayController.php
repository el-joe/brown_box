<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gateway;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GatewayController extends Controller
{
    private const CONFIG_FIELDS = [
        'paymob' => ['api_key', 'integration_id', 'hmac_secret', 'iframe_id'],
        'bank_transfer' => ['bank_name', 'account_name', 'account_number', 'iban', 'instructions.ar', 'instructions.en'],
        'vodafone_cash' => ['number', 'name', 'instructions.ar', 'instructions.en'],
        'instapay' => ['address', 'instructions.ar', 'instructions.en'],
    ];

    public function index(): View
    {
        $gateways = Gateway::query()->get()->keyBy('code');

        foreach (array_keys(self::CONFIG_FIELDS) as $code) {
            if (! $gateways->has($code)) {
                $gateways->put($code, Gateway::query()->create(['code' => $code]));
            }
        }

        return view('admin.settings.gateways', [
            'gateways' => $gateways,
        ]);
    }

    public function update(Request $request, Gateway $gateway): RedirectResponse
    {
        $fields = self::CONFIG_FIELDS[$gateway->code] ?? [];

        $rules = ['is_active' => ['boolean']];

        foreach ($fields as $field) {
            $rules[$field] = ['nullable', 'string', 'max:1000'];
        }

        $data = $request->validate($rules);

        $config = [];

        foreach ($fields as $field) {
            if (str_contains($field, '.')) {
                [$base, $locale] = explode('.', $field);
                $config[$base][$locale] = $data[$base][$locale] ?? null;
            } else {
                $config[$field] = $data[$field] ?? null;
            }
        }

        $gateway->update([
            'is_active' => $request->boolean('is_active'),
            'config' => $config,
        ]);

        return redirect()->route('admin.gateways.index')->with('success', __('Gateway updated successfully.'));
    }
}
