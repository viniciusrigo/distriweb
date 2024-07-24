@extends('errors::minimal')

@section('title', __('Erro de segurança'))
@section('code', '429')
@section('message', __('Muitas requisições feita em um curto periodo de tempo.'))
