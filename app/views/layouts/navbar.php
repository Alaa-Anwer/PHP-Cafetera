<style>
.navbar{
    background-color:#4E342E !important;
}

.navbar .navbar-brand{
    color:#FFD54F !important;
    font-weight:bold;
}

.navbar .nav-link{
    color:#FFF8E1 !important;
    font-weight:500;
}

.navbar .nav-link:hover{
    color:#FFD54F !important;
}

.navbar .btn-outline-success{
    border-color:#FFD54F;
    color:#FFD54F;
}

.navbar .btn-outline-success:hover{
    background-color:#FFD54F;
    color:#4E342E;
}

.navbar-coffee{
    background-color: #6f4e37;
}

.navbar-coffee .navbar-brand,
.navbar-coffee .nav-link{
    color: #fff;
}

.navbar-coffee .nav-link:hover{
    color: #ffd700; 
}
</style>

<nav class="navbar navbar-expand-lg navbar-coffee">
  <div class="container-fluid">

    <a class="navbar-brand" href="/">☕ Cafeteria</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">

      <ul class="navbar-nav me-auto">

        <li class="nav-item">
          <a class="nav-link" href="/">Home</a>
        </li>

        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
          <li class="nav-item">
            <a class="nav-link" href="/admin/products">Products</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/admin/orders">Orders</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/admin/checks">Checks</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/admin/users">Users</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="/my-orders">My Orders</a>
          </li>
        <?php endif; ?>

      </ul>

      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item">
            <span class="nav-link"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></span>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/logout">Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="/login">Login</a>
          </li>
        <?php endif; ?>
      </ul>

    </div>
  </div>
</nav>
