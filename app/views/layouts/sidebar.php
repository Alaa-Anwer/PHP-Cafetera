<?php
$active = $active ?? '';
?>




<html>

<style>
:root{
  --nav-w: 240px;
  --bg: #f4efe9;
  --nav: #2f261f;
  --navText: #e9dfd6;
  --active: #4a362b;
}

body { background: var(--bg); }

.app{ min-height: 100vh; display: flex; }

.sidebar{
  width: var(--nav-w);
  background: var(--nav);
  color: var(--navText);
  padding: 18px 14px;
  position: sticky;
  top: 0;
  height: 100vh;
}

.side-link{
  display:flex;
  align-items:center;
  gap: 10px;
  padding: 10px 12px;
  color: var(--navText);
  text-decoration:none;
  border-radius: 12px;
  margin-bottom: 6px;
}

.side-link:hover{
  background: rgba(255,255,255,.08);
  color: var(--navText);
}

.side-link.active{
  background: var(--active);
}

.content{ flex: 1; padding: 28px; }

</style>


<aside class="sidebar">
  <div class="fw-bold fs-5 mb-3">Admin</div>

  <a class="side-link <?= $active === 'dashboard' ? 'active' : '' ?>"
     href="/PHP-Cafetera/public/index.php">
    Dashboard
  </a>

  <a class="side-link <?= $active === 'products' ? 'active' : '' ?>"
     href="/PHP-Cafetera/app/views/admin/products.php">
    Products
  </a>

  <a class="side-link <?= $active === 'orders' ? 'active' : '' ?>"
     href="/PHP-Cafetera/app/views/admin/orders.php">
    Orders
  </a>

  <a class="side-link <?= $active === 'reports' ? 'active' : '' ?>"
     href="#">
    Checks
  </a>

  <a class="side-link <?= $active === 'logout' ? 'active' : '' ?>"
     href="#">
    Logout
  </a>
</aside>

</html>
