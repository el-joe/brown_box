@extends('admin.layouts.app')

@section('title', __('Dashboard'))

@section('breadcrumb')
    <span>{{ __('Dashboard') }}</span>
@endsection

@section('content')
    <x-admin.card :title="__('Welcome')">
        {{ __('Welcome to the Brown Box admin panel.') }}
    </x-admin.card>
@endsection
