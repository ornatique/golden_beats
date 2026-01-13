@extends('layouts.admin')

@section('content')
<section class="content">
    <div class="container-fluid">

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Edit Permission</h3>
            </div>

            <form method="POST" action="{{ route('admin.permissions.update',$permission->id) }}">
                @csrf
                @method('PUT')
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Permission Name</label>
                                <input type="text" name="name" value="{{ $permission->name }}" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-right">
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-info">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button class="btn btn-primary">
                        <i class="fas fa-save"></i> Update
                    </button>
                </div>

            </form>
        </div>
    </div>
    </div>
</section>
@endsection