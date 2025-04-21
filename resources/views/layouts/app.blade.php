  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'W Mart Dashboard')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
      /* Global Styles */
      * {
        box-sizing: border-box;
      }
      body {
        display: flex;
        margin: 0;
        min-height: 100vh;
        font-family: 'Josefin Sans', 'Rubik', sans-serif;
        background-color: #f8f9fa;
      }

      /* Sidebar Styles */
      .sidebar {
        width: 250px;
        background-color: #fff;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        padding: 20px 0;
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
      }
      .sidebar .sidebar-header {
        text-align: center;
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
      }
      .sidebar nav a {
        color: #6c757d;
        display: block;
        padding: 10px 20px;
        text-decoration: none;
        transition: background-color 0.2s, color 0.2s;
      }
      .sidebar nav a:hover {
        background-color: #f1f1f1;
        color: #007bff;
      }

      /* Content Styles */
      .content {
        flex-grow: 1;
        margin-left: 250px;
        padding: 20px;
      }

      /* Kelas bantu chart */
      .chart-container {
        position: relative;
        margin: 0 auto;
        padding: 1rem;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
      }

      /* Responsive adjustments */
      @media (max-width: 768px) {
        .sidebar {
          width: 200px;
        }
        .content {
          margin-left: 200px;
        }
      }
    </style>

    @yield('head')
  </head>
  
  <body>
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="sidebar-header">
          <img src="{{ asset('images/kasir.png') }}" alt="Logo" style="width:50px; margin-right:10px;">
        W Mart
      </div>
      <nav>
        <a href="{{ url('/dashboard') }}">
          <i class="bi bi-house-door-fill"></i> 
          Dashboard
        </a>
        <a href="{{ url('/product') }}">
          <i class="bi bi-box-seam-fill"></i> Product
        </a>
        <a href="{{ url('/purchase') }}">
          <i class="bi bi-cart-plus-fill"></i> Purchase
        </a>
        @if (Auth::check() && Auth::user()->isAdmin())
          <a href="{{ url('/users') }}">
              <i class="bi bi-person-fill"></i> User
          </a>
        @endif
      </nav>
    </div>

    <!-- Content -->
    <div class="content">
      <!-- Menampilkan pesan sukses jika ada -->
      @if (session('success'))
          <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
              <i class="bi bi-check-circle-fill me-2"></i>
              <div>{{ session('success') }}</div>
              <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
      @endif
  
      @if (session('error'))
          <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
              <i class="bi bi-x-circle-fill me-2"></i>
              <div>{{ session('error') }}</div>
              <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
      @endif

      <div class="d-flex justify-content-between align-items-center mb-4" style="border-bottom: 1px solid #dee2e6; padding-bottom: 10px;">
        <div class="input-group" style="max-width: 500px;">
            <input id="search-input" type="search" class="form-control" placeholder="Search..." />
            <button id="search-button" type="button" class="btn btn-primary">
                <i class="bi bi-search"></i>
            </button>
        </div>

        <div class="d-flex align-items-center">
            <div class="me-3 fw-semibold text-secondary">
                <i class="bi bi-person-circle"></i>  
                @php
                    $roleName = Auth::user()->role_id == 1 ? 'Admin' : '';
                @endphp
                {{ $roleName }} - {{ Auth::user()->name }}
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
    </div>
  
    @yield('content')
</div>  


  <!-- Bootstrap Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Chart.js -->
  <script
    src="https://cdn.jsdelivr.net/npm/chart.js@4.2.1/dist/chart.umd.min.js"
    crossorigin="anonymous"
  ></script>
    
  @yield('scripts')
  <script>
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
          const bsAlert = new bootstrap.Alert(alert);
          bsAlert.close();
        });
      }, 2000);
  </script>  

</body>
</html>
