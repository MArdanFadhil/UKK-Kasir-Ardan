@extends('layouts.app')
@section('title', 'Create User')

@section('content')
  <div class="mb-4">
    <div class="breadcrumb">
        <span class="breadcrumb-item">
            <i class="bi bi-house-door"></i>
            <i class="bi bi-arrow-right-short"></i> Users
        </span>
        <i class="bi bi-arrow-right-short"></i> Create User
    </div>
    <h2 class="page-title">Create User</h2>
  </div>

  <div class="card shadow border-0">
    <div class="card-body">
      @if(session('success'))
        <div class="alert alert-success">
          {{ session('success') }}
        </div>
      @endif

      <form method="POST" action="{{ route('user.store') }}">
        @csrf
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
              <div class="text-danger">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
            @error('email')
              <div class="text-danger">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
            @error('password')
              <div class="text-danger">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6">
            <label for="role" class="form-label">Role</label>
            <select class="form-control" id="role" name="role" required>
              <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                <i class="bi bi-person-fill"></i> Admin
              </option>
              <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>
                <i class="bi bi-person-badge"></i> Staff
              </option>
            </select>
            @error('role')
              <div class="text-danger">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="mb-3">
          <label for="password_confirmation" class="form-label">Confirm Password</label>
          <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
        </div>

        <button type="submit" class="btn btn-primary">Create User</button>
      </form>
    </div>
  </div>
@endsection
