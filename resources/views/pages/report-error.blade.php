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
                    <div class="col-md-12 text-center">
                        <span class="display-1 d-block">500 ERROR</span>
                        <a href="/" class="btn btn-link">Failed to fetch data, Back to Home</a>

                    </div>
                </div>
            </div>
        </section>

        <!-- /.content -->
    </div>
@endsection
