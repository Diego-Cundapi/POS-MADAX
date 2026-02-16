<div class="grid md:grid-cols-2 bg-white gap-6 lg:gap-12 place-items my-auto max-w-6xl mx-auto p-6 rounded-md">
  <div class="grid gap-4 md:gap-10 items-start">
    <img
      src="{{ asset($productoMostrar->imagen) }}"
      alt="Product Image"
      width="600"
      height="900"
      class="aspect-w-2 aspect-h-3 object-cover w-full rounded-lg overflow-hidden" />
  </div>

  <div class="grid gap-4 md:gap-10 items-start">
    <div class="grid gap-4">
      <h1 class="font-bold text-3xl lg:text-4xl">{{$productoMostrar->nombre}}</h1>
      <div class="flex items-center gap-4">
        <span class="text-sm text-gray-500 dark:text-gray-400">Modelo: {{$productoMostrar->modelo}}</span>
        <span class="text-sm text-gray-500 dark:text-gray-400">Marca: {{$productoMostrar->marca}}</span>
      </div>
      <div class="flex items-center gap-4">
        <span class="text-4xl font-bold">${{$productoMostrar->precio}}</span>
        <span class="text-sm text-gray-500 dark:text-gray-400">Clave: {{$productoMostrar->clave}}</span>
      </div>
    </div>
    <div class="grid gap-4">
      <p>{{$productoMostrar->descripcion}}</p>
      <div class="flex items-center gap-4">
        <span class="text-sm text-gray-500 dark:text-gray-400">Disponible: {{$productoMostrar->disponible}}</span>
      </div>
    </div>
    <form class="grid gap-4 md:gap-10" action="{{route('carrito.agregarproducto')}}" method="POST">
      @csrf
      <input type="hidden" name="producto_id" value="{{$producto->id}}">
      <input class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600" type="submit" value="Agregar al carrito">
    </form>

  </div>
</div>