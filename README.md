# Catálogo de Productos con Filtros

Este proyecto implementa un catálogo de productos con funcionalidad de filtrado por categorías.

## Características

- **Filtrado por categorías**: Permite filtrar productos por categorías como Deportes, Hogar, Ropa y Tecnología
- **Interfaz responsive**: Diseño adaptable a diferentes tamaños de pantalla
- **Indicadores visuales**: Muestra qué categoría está activa actualmente
- **Contador de productos**: Muestra cuántos productos se están visualizando

## Estructura del Proyecto
proyecto/
│
├── index.html # Archivo principal con la implementación completa
├── README.md # Este archivo
└── (posibles futuras extensiones)
├── styles/
│ └── style.css # Para separar estilos en el futuro
└── js/
└── script.js # Para separar JavaScript en el futuro


## Tecnologías Utilizadas

- **HTML5**: Estructura semántica del contenido
- **CSS3**: Estilos y diseño responsive
- **JavaScript Vanilla**: Funcionalidad de filtrado sin dependencias externas

## Cómo usar

1. Abre el archivo `index.html` en tu navegador
2. Haz clic en cualquier categoría del panel de filtros
3. Los productos se filtrarán automáticamente según la categoría seleccionada
4. Haz clic en "Todos" para ver todos los productos nuevamente

## Categorías Disponibles

- **Todos**: Muestra todos los productos sin filtrar
- **Tecnología**: Productos electrónicos y tecnológicos
- **Deportes**: Artículos deportivos y de ejercicio
- **Hogar**: Productos para el hogar y electrodomésticos
- **Ropa**: Prendas de vestir y accesorios

## Personalización

Para agregar nuevos productos:

1. Añade un nuevo elemento con la clase `product-card`
2. Agrega el atributo `data-category` con la categoría correspondiente
3. Las categorías disponibles son: `technology`, `sports`, `home`, `clothing`

Ejemplo:
```html
<div class="product-card" data-category="technology">
    <!-- Contenido del producto -->
</div>

Compatibilidad
Compatible con navegadores modernos (Chrome, Firefox, Safari, Edge)

Diseño responsive para dispositivos móviles y tablets

No requiere conexión a internet para funcionar

Mejoras Futuras
Sistema de búsqueda por nombre de producto

Filtrado por rango de precios

Ordenamiento por precio, nombre, etc.

Paginación de resultados

Sistema de favoritos

Carrito de compras

Autor
Desarrollado como solución para implementar filtros funcionales en un catálogo de productos.
