 <style id="nav-kicks-fix">
     /* Logo teks lebih lebar dari logo gambar bawaan template -> jangan dipotong/menimpa menu */
     html[data-placement="horizontal"] .nav-container .logo { flex: 0 0 auto !important; width: auto !important; margin-right: 1rem; }
     html[data-placement="horizontal"] .nav-container .logo a { display: inline-block !important; width: auto !important; overflow: visible !important; }
     /* 8 menu: rapatkan jarak antar item supaya avatar tidak terdorong keluar layar */
     html[data-placement="horizontal"] .nav-container .menu-container .menu > li > a { padding: 0 .75rem !important; }
 </style>
 <div id="nav" class="nav-container d-flex">
     <div class="nav-content d-flex">
         <!-- Logo Start -->
         <div class="logo position-relative">
             <a href="{{ route('dashboard') }}">
                 <!-- Logo can be added directly -->
                 <!-- <img src="img/logo/logo-white.svg" alt="logo" /> -->

                 <!-- Or added via css to provide different ones for different color themes -->
                 <span class="brand-logo" style="display:inline-flex;align-items:center;gap:.5rem;color:#fff !important;font-weight:700;font-size:1.4rem;letter-spacing:.3px;white-space:nowrap;text-shadow:0 1px 3px rgba(0,0,0,.35)"><span class="brand-emoji" aria-hidden="true" style="font-size:1.6rem;line-height:1">👟</span><span style="color:#fff !important">Nwazu Kicks</span></span>
             </a>
         </div>
         <!-- Logo End -->


         <!-- User Menu Start -->
         <div class="user-container d-flex">
             <a href="#" class="d-flex user position-relative" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                 <span class="profile profile-initial" style="display:inline-flex !important;align-items:center;justify-content:center;width:40px;height:40px;border-radius:50%;background:#fff;color:#0d9be0 !important;font-weight:700;font-size:1.1rem">{{ strtoupper(mb_substr(session('username', 'T'), 0, 1)) }}</span>
                 <div class="name">{{ session('username', 'Tamu') }}</div>
             </a>
             <div class="dropdown-menu dropdown-menu-end user-menu wide">
                 <div class="row mb-1 ms-0 me-0">
                     <div class="col-12 p-1 mb-2 pt-2">
                         <div class="text-extra-small text-primary">{{ strtoupper(session('nama_role') ?? 'Akun') }}</div>
                     </div>
                     <div class="col-12 ps-1 pe-1">
                         <a href="{{ route('toko') }}" class="d-block mb-2 text-decoration-none">
                             <i data-acorn-icon="shop" class="me-2" data-acorn-size="17"></i>
                             <span class="align-middle">Lihat Toko</span>
                         </a>
                         <form action="{{ route('logout') }}" method="POST">
                             @csrf
                             <button type="submit" class="btn btn-link p-0 text-decoration-none">
                                 <i data-acorn-icon="logout" class="me-2" data-acorn-size="17"></i>
                                 <span class="align-middle">Keluar</span>
                             </button>
                         </form>
                     </div>
                 </div>
             </div>
         </div>


         <!-- Icons Menu Start -->
         <ul class="list-unstyled list-inline text-center menu-icons">
             
             <li class="list-inline-item">
                 <a href="#" id="pinButton" class="pin-button">
                     <i data-acorn-icon="lock-on" class="unpin" data-acorn-size="18"></i>
                     <i data-acorn-icon="lock-off" class="pin" data-acorn-size="18"></i>
                 </a>
             </li>
             <li class="list-inline-item">
                 <a href="#" id="colorButton">
                     <i data-acorn-icon="light-on" class="light" data-acorn-size="18"></i>
                     <i data-acorn-icon="light-off" class="dark" data-acorn-size="18"></i>
                 </a>
             </li>
         </ul>
         <!-- Icons Menu End -->

         <!-- Menu Start -->
         <div class="menu-container flex-grow-1">
             <ul id="menu" class="menu">
                 <li>
                     <a href="{{ route('dashboard', [], false) }}">
                         <i data-acorn-icon="home" class="icon" data-acorn-size="18"></i>
                         <span class="label">Dashboard</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('barang.index', [], false) }}">
                         <i data-acorn-icon="archive" class="icon" data-acorn-size="18"></i>
                         <span class="label">Barang</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('master.hub', [], false) }}">
                         <i data-acorn-icon="database" class="icon" data-acorn-size="18"></i>
                         <span class="label">Master Data</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('pengadaan.index', [], false) }}">
                         <i data-acorn-icon="shipping" class="icon" data-acorn-size="18"></i>
                         <span class="label">Pengadaan</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('penerimaan.index', [], false) }}">
                         <i data-acorn-icon="inbox" class="icon" data-acorn-size="18"></i>
                         <span class="label">Penerimaan</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('retur.index', [], false) }}">
                         <i data-acorn-icon="upload" class="icon" data-acorn-size="18"></i>
                         <span class="label">Retur</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('penjualan.index', [], false) }}">
                         <i data-acorn-icon="cart" class="icon" data-acorn-size="18"></i>
                         <span class="label">Penjualan</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('kartustok.index', [], false) }}">
                         <i data-acorn-icon="clipboard" class="icon" data-acorn-size="18"></i>
                         <span class="label">Kartu Stok</span>
                     </a>
                 </li>
             </ul>
         </div>
         <!-- Menu End -->

         <!-- Mobile Buttons Start -->
         <div class="mobile-buttons-container">
             <!-- Scrollspy Mobile Button Start -->
             <a href="#" id="scrollSpyButton" class="spy-button" data-bs-toggle="dropdown">
                 <i data-acorn-icon="menu-dropdown"></i>
             </a>
             <!-- Scrollspy Mobile Button End -->

             <!-- Scrollspy Mobile Dropdown Start -->
             <div class="dropdown-menu dropdown-menu-end" id="scrollSpyDropdown"></div>
             <!-- Scrollspy Mobile Dropdown End -->

             <!-- Menu Button Start -->
             <a href="#" id="mobileMenuButton" class="menu-button">
                 <i data-acorn-icon="menu"></i>
             </a>
             <!-- Menu Button End -->
         </div>
         <!-- Mobile Buttons End -->
     </div>
     <div class="nav-shadow"></div>
 </div>
