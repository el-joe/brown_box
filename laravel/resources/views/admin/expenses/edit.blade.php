@extends('admin.layouts.app')

@section('title', __('Edit Expense'))

@section('breadcrumb')
    <a href="{{ route('admin.expenses.index') }}" class="hover:text-slate-700">{{ __('Expenses') }}</a>
    <span class="mx-1">/</span>
    <span>{{ __('Edit') }}</span>
@endsection

@section('content')
    @include('admin.expenses.form')
@endsection
