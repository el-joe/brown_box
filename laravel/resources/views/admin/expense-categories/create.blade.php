@extends('admin.layouts.app')

@section('title', __('Create Expense Category'))

@section('breadcrumb')
    <a href="{{ route('admin.expense-categories.index') }}" class="hover:text-slate-700">{{ __('Expense Categories') }}</a>
    <span class="mx-1">/</span>
    <span>{{ __('Create') }}</span>
@endsection

@section('content')
    @include('admin.expense-categories.form')
@endsection
