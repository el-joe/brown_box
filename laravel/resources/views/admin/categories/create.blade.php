@extends('admin.layouts.app')

@section('title', __('Create Category'))

@section('breadcrumb')
    <a href="{{ route('admin.categories.index') }}" class="hover:text-slate-700">{{ __('Categories') }}</a>
    <span class="mx-1">/</span>
    <span>{{ __('Create') }}</span>
@endsection

@section('content')
    @include('admin.categories.form')
@endsection
