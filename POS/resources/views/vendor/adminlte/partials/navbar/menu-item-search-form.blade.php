<li class="nav-item">

    {{-- Botón de la lupa (para abrir la barra) --}}
    <a class="nav-link" data-widget="navbar-search" href="#" role="button">
        <i class="fas fa-search"></i>
    </a>

    {{-- Bloque del buscador desplegable --}}
    <div class="navbar-search-block">

        {{-- CAMBIO 1: Action apunta directo a tu ruta y Method es GET --}}
        <form class="form-inline" action="{{ route('global.search') }}" method="GET">

            <div class="input-group">

                {{-- CAMBIO 2: Input con name="query" para que lo lea el controlador --}}
                <input class="form-control form-control-navbar" type="search"
                    name="query"
                    placeholder="Buscar producto, venta, cliente..."
                    aria-label="Buscar">

                <div class="input-group-append">
                    <button class="btn btn-navbar" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                    <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

            </div>
        </form>
    </div>

</li>