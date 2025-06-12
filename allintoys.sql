-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-06-2025 a las 14:40:44
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `allintoys`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
--

CREATE TABLE `carrito` (
  `id_carrito` int(11) NOT NULL,
  `id_usu` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `carrito`
--

INSERT INTO `carrito` (`id_carrito`, `id_usu`, `id_producto`, `cantidad`) VALUES
(6, 9, 3, 7),
(13, 12, 3, 2),
(14, 12, 4, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedido`
--

CREATE TABLE `detalle_pedido` (
  `id_detalle` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marca`
--

CREATE TABLE `marca` (
  `id_marca` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `imagen_marca` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `marca`
--

INSERT INTO `marca` (`id_marca`, `nombre`, `imagen_marca`) VALUES
(1, 'BARBIE', 'tfg/imagenes/marcas/barbie.jpg'),
(2, 'LEGO', 'tfg/imagenes/marcas/lego.jpg'),
(3, 'NANCY', 'tfg/imagenes/marcas/nancy.jpg'),
(4, 'PLAYMOBIL', 'tfg/imagenes/marcas/playmobil.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id_pedido` int(11) NOT NULL,
  `id_usu` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','procesando','enviado','entregado','cancelado') NOT NULL DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id_producto` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen_producto` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `id_marca` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id_producto`, `titulo`, `precio`, `imagen_producto`, `descripcion`, `id_marca`, `cantidad`) VALUES
(1, 'Barbie Dreamhouse', 199.99, 'tfg/imagenes/productos/barbie/dreamhouse.jpg', 'La casa de ensueño de Barbie con 3 pisos, 8 habitaciones y ascensor que funciona.', 1, 10),
(2, 'Barbie Chef de Pasteles', 24.99, 'tfg/imagenes/productos/barbie/chef_de_pasteles.jpg', 'Con la muñeca Barbie panadera y accesorios de repostería para hornear galletas.', 1, 15),
(3, 'Barbie Coche Descapotable Rosa', 39.99, 'tfg/imagenes/productos/barbie/coche_descapotable.jpg', 'Un elegante coche descapotable para la muñeca Barbie con espacio para dos muñecas.', 1, 8),
(4, 'Barbie Fashionista con Silla de Ruedas', 29.99, 'tfg/imagenes/productos/barbie/fashionista_con_silla_ruedas.jpg', 'Barbie Fashionista con silla de ruedas y rampa para un juego inclusivo.', 1, 12),
(5, 'Barbie Paseo de Mascotas', 22.50, 'tfg/imagenes/productos/barbie/paseo_de_mascotas.jpg', 'Barbie y sus mascotas, un cachorro y un gatito, listos para un paseo.', 1, 20),
(6, 'Barbie Sirena Mágica', 18.75, 'tfg/imagenes/productos/barbie/sirena_magica.jpg', 'Muñeca Barbie sirena con cola brillante y accesorios mágicos para el agua.', 1, 18),
(7, 'Barbie Veterinaria con Clínica', 55.00, 'tfg/imagenes/productos/barbie/veterinaria_con_accesorios.jpg', 'Barbie veterinaria con su clínica móvil y adorables pacientes animales.', 1, 7),
(8, 'Barbie Granja de Animales', 65.00, 'tfg/imagenes/productos/barbie/granja.jpg', 'Un set de granja completo con muñeca Barbie y varios animales de granja.', 1, 9),
(9, 'Barbie Piscina de Ensueño', 19.99, 'tfg/imagenes/productos/barbie/piscina.jpg', 'Gran piscina de Barbie con tobogán y accesorios para un día divertido.', 1, 6),
(10, 'LEGO Ciudad: Comisaría de Policía', 99.99, 'tfg/imagenes/productos/lego/comisaria.jpg', 'Set de construcción de una comisaría de policía con vehículos y minifiguras.', 2, 12),
(11, 'LEGO Technic: Bugatti Chiron', 349.99, 'tfg/imagenes/productos/lego/bugatti.jpg', 'Réplica a escala 1:8 del Bugatti Chiron, con motor W16 y caja de cambios de 8 velocidades.', 2, 3),
(12, 'LEGO Creator Expert: Ford Mustang', 149.99, 'tfg/imagenes/productos/lego/ford_mustang.jpg', 'Construye un icónico Ford Mustang de los años 60 con extras personalizables.', 2, 7),
(13, 'LEGO Harry Potter: Hogwarts', 419.99, 'tfg/imagenes/productos/lego/hogwarts.jpg', 'El castillo de Hogwarts a gran escala con microfiguras y detalles emblemáticos.', 2, 2),
(14, 'LEGO Ideas: Barco en una Botella', 69.99, 'tfg/imagenes/productos/lego/barco_en_botella.jpg', 'Crea un barco en miniatura dentro de una botella de LEGO, con soporte de exhibición.', 2, 10),
(15, 'LEGO Star Wars: Halcón Milenario', 799.99, 'tfg/imagenes/productos/lego/halcon_milenario.jpg', 'El set LEGO Star Wars Halcón Milenario más grande y detallado hasta la fecha.', 2, 1),
(16, 'LEGO Minecraft: La Mina', 109.99, 'tfg/imagenes/productos/lego/minecraft.jpg', 'Explora una mina de Minecraft con Steve, un creeper y un golem de hierro.', 2, 8),
(17, 'LEGO Ninjago: Dragón del Fuego', 49.99, 'tfg/imagenes/productos/lego/dragon.jpg', 'Construye un dragón de fuego con alas articuladas y misiles.', 2, 15),
(18, 'LEGO Duplo: Caja de Ladrillos Grande', 35.00, 'tfg/imagenes/productos/lego/duplo.jpg', 'Una caja con una gran variedad de ladrillos LEGO Duplo para los más pequeños.', 2, 25),
(19, 'Nancy: Un Día con el Unicornio', 45.00, 'tfg/imagenes/productos/nancy/unicornio.jpg', 'Muñeca Nancy acompañada de su majestuoso unicornio con crin de colores.', 3, 15),
(20, 'Nancy Colección: Estilo de Moda', 32.99, 'tfg/imagenes/productos/nancy/estilista.jpg', 'Nancy con un conjunto de ropa de moda y accesorios para un look moderno.', 3, 18),
(21, 'Nancy: Fiesta de pijamas', 28.50, 'tfg/imagenes/productos/nancy/fiesta.jpg', 'Set de Nancy con pijama, antifaz y accesorios para una divertida fiesta de pijamas.', 3, 20),
(22, 'Nancy: Viaje a Londres', 39.99, 'tfg/imagenes/productos/nancy/londres.jpg', 'Nancy lista para su viaje a Londres con su maleta, pasaporte y accesorios turísticos.', 3, 10),
(23, 'Nancy: Peluquería de Ensueño', 58.00, 'tfg/imagenes/productos/nancy/peluqueria.jpg', 'Nancy con un set de peluquería, con silla, secador y accesorios para peinar.', 3, 7),
(24, 'Nancy: Maquillaje de Estrellas', 27.99, 'tfg/imagenes/productos/nancy/maquillaje.jpg', 'Muñeca Nancy con kit de maquillaje para crear looks espectaculares.', 3, 14),
(25, 'Nancy: Clase de Ballet', 31.00, 'tfg/imagenes/productos/nancy/ballet.jpg', 'Nancy con su atuendo de ballet y accesorios, lista para su clase.', 3, 16),
(26, 'Nancy: Cocina Creativa', 48.00, 'tfg/imagenes/productos/nancy/cocina.jpg', 'Set de cocina con Nancy, horno, utensilios y alimentos de juguete.', 3, 9),
(27, 'Nancy: Bicicleta de Paseo', 36.50, 'tfg/imagenes/productos/nancy/bici.jpg', 'Nancy con su bicicleta de paseo y casco, perfecta para un día al aire libre.', 3, 11),
(28, 'Playmobil Barco Pirata Clásico', 79.99, 'tfg/imagenes/productos/playmobil/barco_pirata_clasico.jpg', 'El icónico barco pirata de Playmobil con velas, cañones y figuras de piratas.', 4, 10),
(29, 'Playmobil Comisaría de Policía', 89.99, 'tfg/imagenes/productos/playmobil/comisaria_policia.jpg', 'Gran comisaría de policía con helipuerto, celdas y figuras de agentes y ladrones.', 4, 8),
(30, 'Playmobil Casa Moderna', 129.99, 'tfg/imagenes/productos/playmobil/casa_moderna.jpg', 'Espaciosa casa moderna con múltiples habitaciones y accesorios para decorar.', 4, 5),
(31, 'Playmobil Hospital Infantil', 75.00, 'tfg/imagenes/productos/playmobil/hospital_infantil.jpg', 'Hospital completamente equipado con figuras de médicos, pacientes y ambulancia.', 4, 7),
(32, 'Playmobil T-Rex', 110.00, 'tfg/imagenes/productos/playmobil/t-rex.jpg', 'El dinosaurio más peligroso de la Prehistoria, su cuidador y una gran variedad de dinosaurios.', 4, 6),
(33, 'Playmobil Camión Bomberos', 59.99, 'tfg/imagenes/productos/playmobil/camion_bomberos.jpg', 'Camión de bomberos con escalera extensible, manguera de agua y luces y sonido.', 4, 12),
(34, 'Playmobil Zoo', 36.00, 'tfg/imagenes/productos/playmobil/zoo.jpg', 'Un zoo cercado y figuras de niños y animales.', 4, 9),
(35, 'Playmobil Castillo Medieval', 149.99, 'tfg/imagenes/productos/playmobil/castillo_caballeros.jpg', 'Imponente castillo medieval con torreones, trampas y caballeros.', 4, 4),
(36, 'Playmobil Viejo Oeste: Tren', 95.00, 'tfg/imagenes/productos/playmobil/viejo_oeste.jpg', 'Caseta del Viejo Oeste con carcel y figuras.', 4, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usu` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `psw` varchar(255) NOT NULL,
  `tipo_usu` enum('registrado','administrador') NOT NULL DEFAULT 'registrado'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usu`, `nombre`, `apellidos`, `correo`, `psw`, `tipo_usu`) VALUES
(1, 'manu', 'romero', 'manu@gmail.com', '$2y$10$73S0lz/gV67qqKvFU8u5cesTrjFldoWIPHxrHCyJcet7U4cviPzs2', 'registrado'),
(2, 'admin', 'admin', 'admin@gmail.com', '$2y$10$C7y5pKuv3Ti1iqDeyoQBOew41fTfywvsKNsoLNdFS4LZ4zIYbXVgW', 'administrador'),
(3, 'man', 'man', 'man@gmail.com', '$2y$10$25x8JndR0.DD76pIIFH7heaXnfh9QUhZlCQWbgS/ZXKTEmzfMl0nO', 'administrador'),
(4, 'ron', 'ron', 'ron@ron', '$2y$10$0AMSRUHGzLZZHmr1ye.ev.Lfrs5m3ffebx9oHOdshQ1KxopV3tp1a', 'registrado'),
(5, 'pep', 'pep', 'pep@pep', '$2y$10$3Te2.7Sgiw7zQj1osrNNN.Yh4YS4YAkfIq61swZQdMA37PMuKcPwC', 'registrado'),
(7, 'q', 'q', 'q@gmail.com', '$2y$10$3SOsMmL6V3EC1Ht.dnGyn.Dtudc9jvw/maQ5HvZ3ikKRJFBFlhSt.', 'administrador'),
(8, 's', 's', 's@gmail.com', '$2y$10$jYib6s6P4PBgViHZApr09uUcOcyHln3bqBhnAt3UspxKPNl4wOLrW', 'registrado'),
(9, 'w', 'w', 'w@gmail.com', '$2y$10$6EvVMFpQ4k9/WL4gVUWgNuxnifMwjMeG14BWIGiguMcYhiAbEnXs.', 'registrado'),
(10, 'e', 'e', 'e@gmail.com', '$2y$10$LsqorhwKPZwPmwm/7Ue9.OUwuPEKleFhjM6gFbmjXOzse/TkbyWPW', 'registrado'),
(11, 'manu', 'admin', 'manuadmin@gmail.com', '$2y$10$6rboX1VlEI2K7C4GpNP8ie.A7eerWa8LWAGxSZzt15bqqfskL6GPG', 'administrador'),
(12, 'm', 'm', 'm@gmail.com', '$2y$10$26LzKfUSIMcX9gDA54tmZejASuLjFA.gRZUAsi7wKHmy6WbYm3wrO', 'registrado');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`id_carrito`),
  ADD UNIQUE KEY `id_usu` (`id_usu`,`id_producto`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `marca`
--
ALTER TABLE `marca`
  ADD PRIMARY KEY (`id_marca`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `id_usu` (`id_usu`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `id_marca` (`id_marca`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usu`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carrito`
--
ALTER TABLE `carrito`
  MODIFY `id_carrito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `marca`
--
ALTER TABLE `marca`
  MODIFY `id_marca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`id_usu`) REFERENCES `usuarios` (`id_usu`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `carrito_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD CONSTRAINT `detalle_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_pedido_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_usu`) REFERENCES `usuarios` (`id_usu`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`id_marca`) REFERENCES `marca` (`id_marca`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
