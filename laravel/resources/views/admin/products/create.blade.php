@extends('admin.layouts.app')

@section('title', __('Create Product'))

@section('breadcrumb')
    <a href="{{ route('admin.products.index') }}" class="hover:text-slate-700">{{ __('Products') }}</a>
    <span class="mx-1">/</span>
    <span>{{ __('Create') }}</span>
@endsection

@section('content')
    @include('admin.products.partials._form')
@endsection
