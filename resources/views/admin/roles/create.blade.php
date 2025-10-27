@extends('accesstree::admin.layouts.resource-form')

@section('title', 'Create Role')

@php
    $action = route('accesstree.admin.roles.store');
    $title = 'Create Role';
@endphp
