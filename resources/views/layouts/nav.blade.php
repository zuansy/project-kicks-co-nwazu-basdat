 <div id="nav" class="nav-container d-flex">
     <div class="nav-content d-flex">
         <!-- Logo Start -->
         <div class="logo position-relative">
             <a href="{{ route('barang.index') }}">
                 <!-- Logo can be added directly -->
                 <!-- <img src="img/logo/logo-white.svg" alt="logo" /> -->

                 <!-- Or added via css to provide different ones for different color themes -->
                 <span class="brand-logo"><span class="brand-emoji" aria-hidden="true">👟</span><span>Nwazu Kicks</span></span>
             </a>
         </div>
         <!-- Logo End -->


         <!-- User Menu Start -->
         <div class="user-container d-flex">
             <a href="#" class="d-flex user position-relative" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                 <span class="profile profile-initial">{{ strtoupper(mb_substr(session('username', 'T'), 0, 1)) }}</span>
                 <div class="name">{{ session('username', 'Tamu') }}</div>
             </a>
             <div class="dropdown-menu dropdown-menu-end user-menu wide">
                 <div class="row mb-1 ms-0 me-0">
                     <div class="col-12 p-1 mb-2 pt-2">
                         <div class="text-extra-small text-primary">AKUN</div>
                     </div>
                     <div class="col-12 ps-1 pe-1">
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
                     <a href="{{ route('barang.index', [], false) }}">
                         <i data-acorn-icon="archive" class="icon" data-acorn-size="18"></i>
                         <span class="label">Barang</span>
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
