@extends('layouts.app')
@section('title', 'Users - W Mart')

@section('content')
  <div class="mb-4">
    <div class="breadcrumb">
        <span class="breadcrumb-item">
            <i class="bi bi-house-door"></i>
            <i class="bi bi-arrow-right-short"></i> Users
        </span>
    </div>
    <h2 class="page-title">Users</h2>
  </div>

  <div class="card shadow border-0">
    <div class="card-body">
      <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('user.create') }}" class="btn btn-primary">
          <i class="bi bi-plus-circle"></i> Add User
        </a>
      </div>
      <table class="table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($users as $user)
            <tr>
              <td>{{ $user->name }}</td>
              <td>{{ $user->email }}</td>
              <td>{{ $user->role }}</td>
              <td>
                <!-- Edit button -->
                <a href="{{ route('user.edit', $user->id) }}" class="btn btn-warning">Edit</a>
                
                <!-- Delete button -->
                <form action="{{ route('user.destroy', $user->id) }}" method="POST" style="display: inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection
