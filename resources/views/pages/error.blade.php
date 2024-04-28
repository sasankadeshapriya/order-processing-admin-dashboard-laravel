@extends('layouts.app')

@section('title', 'Error')

@section('content')
    <div class="content-wrapper">

        <section class="content-header">
        </section>

        <!-- Main content -->
        <!-- Main content -->
        <section class="content" style="height: 80vh; display: flex; justify-content: center; align-items: center;">
            <div class="container">
                <div class="row justify-content-center">
                    @if (isset($errorCode))
                        <div class="col-md-12 text-center">
                            <span class="display-1 d-block">{{ $errorCode }} ERROR</span>
                            <a href="/" class="btn btn-link">Back to Home</a>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- /.content -->
    </div>
@endsection
