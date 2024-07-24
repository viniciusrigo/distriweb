@extends('errors::minimal')

@section('title', __('Serviço não disponível'))
@section('code', '503')
@section('message', __('Servidor em manutenção ou sobrecarregado.'))
