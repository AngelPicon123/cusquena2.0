<!-- sidebear_admin.html -->
  <!-- Sidebar -->
  <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
      <div class="nav">
        <div class="sb-sidenav-menu-heading">Gestión</div>
        <a class="nav-link collapsed" href="gestionProducto.php" data-bs-toggle="collapse" data-bs-target="#collapseLayoutsProductos"
          aria-expanded="false" aria-controls="collapseLayouts">
          <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
          Gestionar Productos
          <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
        </a>
        <div class="collapse" id="collapseLayoutsProductos" aria-labelledby="headingOne"
          data-bs-parent="#sidenavAccordion">
          <nav class="sb-sidenav-menu-nested nav">
            <a class="nav-link" href="gestionProducto.php">Gestión de Productos</a>
            <a class="nav-link" href="gestionHistorialIngreso.php">Ingresos de Productos</a>
          </nav>
        </div>
        <a class="nav-link" href="gestionServicio.php">
          <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
          Gestionar Servicios
        </a>
        <a class="nav-link collapsed" href="gestionConductor.php" data-bs-toggle="collapse"
          data-bs-target="#collapseLayoutsConductores" aria-expanded="false" aria-controls="collapseLayouts">
          <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
          Gestionar Conductores
          <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
        </a>
        <div class="collapse" id="collapseLayoutsConductores" aria-labelledby="headingOne"
          data-bs-parent="#sidenavAccordion">
          <nav class="sb-sidenav-menu-nested nav">
            <a class="nav-link" href="gestionSoat.php">Lista de SOAT</a>
          </nav>
        </div>
        <a class="nav-link" href="gestionAlquiler.php">
          <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
          Gestionar Alquileres
        </a>
        <div class="sb-sidenav-menu-heading">Balances</div>
        <a class="nav-link" href="balanceServicio.php">
          <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
          Balance de Servicios
        </a>
        <a class="nav-link" href="balanceProducto.php">
          <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
          Balance de Productos
        </a>
        <a class="nav-link" href="balacenGastos.php">
          <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
          Balance de Gastos
        </a>
      </div>
    </div>
    <div class="sb-sidenav-footer">
      <div class="small">Sesion Inciada como:</div>
      Secretaria
    </div>
  </nav>
 <!-- Script de control -->
<script>
    // Control del toggle
    document.getElementById('sidebarToggle')?.addEventListener('click', function(e) {
      e.preventDefault();
      document.body.classList.toggle('sb-sidenav-toggled');
      localStorage.setItem('sb-sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
    });
    
    // Estado inicial
    if(localStorage.getItem('sb-sidebar-toggle') === 'true') {
      document.body.classList.add('sb-sidenav-toggled');
    }
    
    // Asegurar clases necesarias
    document.body.classList.add('sb-nav-fixed');
    </script>