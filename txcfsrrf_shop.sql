-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 16-07-2026 a las 00:41:15
-- Versión del servidor: 8.0.46-cll-lve
-- Versión de PHP: 8.4.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `txcfsrrf_shop`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `banners`
--

CREATE TABLE `banners` (
  `id` int UNSIGNED NOT NULL,
  `type` enum('home1','home2','category','related') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slot` tinyint UNSIGNED NOT NULL,
  `imagen` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `banners`
--

INSERT INTO `banners` (`id`, `type`, `slot`, `imagen`, `url`, `created_at`) VALUES
(1, 'home1', 1, '1782765246_capellb5_banner_logo_insertado_438x240.png', 'https://www.instagram.com/capellb5?igsh=cXR5aWZqMmlyZ2Zs', '2025-09-12 00:21:37'),
(11, 'home1', 3, NULL, '', '2025-09-24 18:30:14'),
(12, 'home2', 1, '1782766891_mascarilla_banner.png', '', '2025-09-24 18:30:14'),
(13, 'home2', 2, '1782766891_capellb5_tonico_solo_438x220.png', '', '2025-09-24 18:30:14'),
(14, 'category', 1, NULL, '', '2025-09-24 18:30:14'),
(15, 'home1', 2, '1782765246_capellb5_agenda_cita_top_438x240.png', 'https://wa.link/0qk94r', '2025-11-05 14:54:09'),
(16, 'related', 1, NULL, '', '2026-06-29 20:34:06'),
(17, 'related', 2, NULL, '', '2026-06-29 21:01:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `blog_categories`
--

CREATE TABLE `blog_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `deleted` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `blog_categories`
--

INSERT INTO `blog_categories` (`id`, `name`, `slug`, `description`, `status`, `deleted`, `created_at`, `updated_at`) VALUES
(5, 'uso tonico capilar', 'uso-tonico-capilar', '', 'active', 0, '2025-09-25 01:00:18', '2025-09-25 01:00:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Admin',
  `status` enum('draft','published') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `deleted` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `seo_title` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `title`, `slug`, `content`, `image`, `author`, `status`, `deleted`, `created_at`, `updated_at`, `seo_title`, `seo_description`, `seo_keywords`) VALUES
(4, 'Usos y Tips para el Tonico Capilar CapellB5', 'usos-y-tips-para-el-tonico-capilar-capellb5', '<p>Mapa 6-Zonas + Masaje 90s 🕛</p><p><br></p><p>Qué hace: Cobertura uniforme “raíz por raíz”, mejor distribución y sensación de cuero cabelludo activo.</p><p>Cómo se hace (1.5 min):</p><p><span style=\"white-space: normal;\"><span style=\"white-space:pre\">	</span>•<span style=\"white-space:pre\">	</span>Divide la cabeza en 6 zonas: frontal, coronilla, nuca y laterales (izq/der).</span></p><p><span style=\"white-space: normal;\"><span style=\"white-space:pre\">	</span>•<span style=\"white-space:pre\">	</span>Aplica 2–3 sprays por zona a 10–15 cm.</span></p><p><span style=\"white-space: normal;\"><span style=\"white-space:pre\">	</span>•<span style=\"white-space:pre\">	</span>Masajea 15 s con yemas (movimientos circulares + leves presiones).</span></p><p><span style=\"white-space: normal;\"><span style=\"white-space:pre\">	</span>•<span style=\"white-space:pre\">	</span>Finaliza con un peine de púas anchas para llevar micro-bruma a medios y puntas.</span></p><p>Pro tip: Usa un masajeador de silicona 2–3 veces por semana para potenciar la rutina sin maltratar.</p>', 'public/images/blog/1758762324_Imagen_de_WhatsApp_2025-09-24_a_las_20.03.26_ec53f3b8.jpg', 'Luis Arias', 'published', 0, '2025-09-25 01:05:24', '2025-09-26 19:28:48', 'usos del Tónico Capell B5 que sí se sienten', 'Activa raíces, refresca post-gym y arma rutina AM/PM con el Tónico Capell B5 (11+ extractos). Tres hacks efectivos: mapa 6 zonas, reset express y frío/calma', 'tónico capilar, Capell B5, 11 extractos naturales, hacks de cabello, mapa 6 zonas, post gym hair, rutina AM PM, cuero cabelludo, crecimiento saludable, fortalecer raíces, bruma capilar, masaje capilar, romero, ortiga, quina, cola de caballo, ginseng');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `blog_post_category`
--

CREATE TABLE `blog_post_category` (
  `post_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `blog_post_category`
--

INSERT INTO `blog_post_category` (`post_id`, `category_id`) VALUES
(4, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image`, `status`, `deleted`, `created_at`, `updated_at`) VALUES
(6, 'Shampoo Anticaída CapellB5', 'shampoo-anticaida-capellb5', 'Shampoo Anticaída CapellB5 diseñado para una limpieza suave y efectiva, ideal para cabellos que necesitan fortalecimiento, frescura y cuidado diario. Su fórmula sin sal ayuda a limpiar el cuero cabelludo sin sensación pesada, aportando suavidad, brillo y una apariencia de cabello más fuerte, saludable y revitalizado.\r\n\r\nIdeal para complementar rutinas capilares enfocadas en fortalecer la fibra capilar y ayudar a reducir la caída asociada al quiebre.', 'public/images/categories/da5ea3b262ab5586.png', 'active', 0, '2025-09-24 20:56:26', '2026-06-30 14:53:02'),
(7, 'Acondicionador Reparador CapellB5', 'acondicionador-reparador-capellb5', 'Acondicionador Reparador CapellB5, diseñado para complementar la rutina capilar diaria aportando suavidad, nutrición ligera y mejor manejabilidad al cabello. Su fórmula ayuda a mejorar la apariencia de la fibra capilar, dejando una sensación más sedosa, flexible y saludable, ideal para todo tipo de cabello que necesita cuidado, brillo y control del frizz.', 'public/images/categories/2b822a0e8ab11dee.png', 'active', 0, '2025-09-24 22:19:53', '2026-06-30 14:48:39'),
(8, 'Tratamiento Multivitamínico CapellB5', 'tratamiento-multivitaminico-capellb5', 'Tratamiento Multivitamínico CapellB5, desarrollado para complementar la rutina capilar diaria con una experiencia ligera, nutritiva y sensorial. Su fórmula ayuda a mejorar la apariencia de la fibra capilar, aportando suavidad, brillo, manejabilidad y una sensación de cabello más fuerte, fresco y saludable.\r\n\r\nIdeal para todo tipo de cabello que necesita cuidado, nutrición y vitalidad sin sensación pesada.', 'public/images/categories/d7ec2fb2bda49918.png', 'active', 0, '2025-09-24 22:41:23', '2026-06-30 14:43:20'),
(9, 'Multivitaminicos', 'multivitaminicos', '', 'public/images/categories/78babd567276e2fa88f9923b.jpg', 'active', 0, '2025-09-24 23:02:25', '2025-09-24 23:02:25'),
(10, 'Mascarilla Repolarizadora CapellB5', 'mascarilla-repolarizadora-capellb5', 'Mascarilla repolarizadora CapellB5 diseñada para aportar nutrición profunda, suavidad y brillo visible al cabello. Ideal para complementar rutinas de reparación capilar, ayudar a mejorar la apariencia de la fibra capilar y dejar el cabello con sensación más manejable, sedosa y saludable.', 'public/images/categories/98fc8e9d1d933a5e.png', 'active', 0, '2025-09-24 23:38:32', '2026-06-30 00:42:17'),
(11, 'Tónico Capilar CapellB5', 'tonico-capilar-capellb5', 'Tónico botánico fortificante CapellB5, formulado con extractos naturales para aportar frescura, vitalidad y fortalecimiento a la fibra capilar. Ideal para complementar la rutina de cuidado capilar, ayudar a mejorar la sensación de cuero cabelludo saludable y favorecer un cabello con apariencia más fuerte, fresco y revitalizado.', 'public/images/categories/6520b6638c700748.png', 'active', 0, '2025-09-25 00:30:41', '2026-06-30 00:35:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `coupons`
--

CREATE TABLE `coupons` (
  `id` int UNSIGNED NOT NULL,
  `code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('percent','fixed','free_shipping') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `start_at` datetime NOT NULL,
  `end_at` datetime NOT NULL,
  `min_cart_total` decimal(10,2) DEFAULT '0.00',
  `max_discount` decimal(10,2) DEFAULT NULL,
  `include_discounted` tinyint(1) DEFAULT '0',
  `stackable` tinyint(1) DEFAULT '0',
  `usage_limit` int DEFAULT NULL,
  `usage_limit_per_user` int DEFAULT '1',
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `coupon_categories`
--

CREATE TABLE `coupon_categories` (
  `coupon_id` int UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `coupon_products`
--

CREATE TABLE `coupon_products` (
  `coupon_id` int UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `coupon_usages`
--

CREATE TABLE `coupon_usages` (
  `id` int UNSIGNED NOT NULL,
  `coupon_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `order_id` int UNSIGNED DEFAULT NULL,
  `used_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders`
--

CREATE TABLE `orders` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `address_id` int UNSIGNED DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `shipping_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `shipping_label` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_rate_id` int DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `coupon_code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','paid','processing','shipped','delivered','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `transporter_id` bigint UNSIGNED DEFAULT NULL,
  `tracking_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock_deducted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `address_id`, `subtotal`, `discount`, `shipping_cost`, `shipping_label`, `shipping_rate_id`, `total`, `coupon_code`, `status`, `transporter_id`, `tracking_number`, `stock_deducted`, `created_at`) VALUES
(4, 32, 105, 150000.00, 0.00, 0.00, 'Envío Gratis (monto mínimo)', NULL, 150000.00, NULL, 'paid', NULL, NULL, 1, '2025-09-24 21:03:00'),
(5, 32, 105, 150000.00, 0.00, 0.00, 'Envío Gratis (monto mínimo)', NULL, 150000.00, NULL, 'delivered', 1, '1254121', 1, '2025-09-24 21:05:04'),
(10, 156, 109, 2000.00, 0.00, 0.00, 'Envío Gratis (monto mínimo)', NULL, 2000.00, NULL, 'cancelled', 1, '98920958885', 1, '2025-09-24 21:30:44'),
(12, 186, 112, 67500.00, 0.00, 12000.00, 'Eje Cafetero', 8, 79500.00, NULL, 'delivered', NULL, NULL, 0, '2026-05-14 13:43:30'),
(13, 187, 113, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:35:38'),
(14, 187, 114, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:35:38'),
(15, 187, 113, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:35:38'),
(16, 187, 115, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:35:58'),
(17, 187, 116, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:00'),
(18, 187, 117, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:01'),
(19, 187, 113, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:02'),
(20, 187, 118, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:02'),
(21, 187, 114, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:02'),
(22, 187, 119, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:03'),
(23, 187, 120, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:03'),
(24, 187, 121, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:03'),
(25, 187, 113, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:03'),
(26, 187, 122, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:04'),
(27, 187, 123, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:04'),
(28, 187, 124, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:04'),
(29, 187, 125, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:05'),
(30, 187, 126, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:05'),
(31, 187, 127, 118800.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 136800.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:05'),
(32, 187, 128, 118800.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 136800.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:05'),
(33, 187, 129, 118800.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 136800.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:05'),
(34, 187, 130, 118800.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 136800.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:06'),
(35, 187, 131, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:06'),
(36, 187, 132, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:06'),
(37, 187, 133, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:06'),
(38, 187, 134, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:06'),
(39, 187, 135, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:07'),
(40, 187, 136, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:13'),
(41, 187, 136, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:20'),
(42, 187, 136, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:26'),
(43, 187, 137, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:27'),
(44, 187, 138, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:27'),
(45, 187, 139, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:28'),
(46, 187, 140, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:28'),
(47, 187, 141, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:28'),
(48, 187, 142, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:36:50'),
(49, 187, 128, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:25'),
(50, 187, 113, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:25'),
(51, 187, 128, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:25'),
(52, 187, 113, 39600.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 57600.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:25'),
(53, 187, 128, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:25'),
(54, 187, 113, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:26'),
(55, 187, 128, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:26'),
(56, 187, 113, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:26'),
(57, 187, 128, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:27'),
(58, 187, 128, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:27'),
(59, 187, 143, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:27'),
(60, 187, 128, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:27'),
(61, 187, 143, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:28'),
(62, 187, 128, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:28'),
(63, 187, 143, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:28'),
(64, 187, 143, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:28'),
(65, 187, 136, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:29'),
(66, 187, 143, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:29'),
(67, 187, 144, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:29'),
(68, 187, 143, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:29'),
(69, 187, 144, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:30'),
(70, 187, 143, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:30'),
(71, 187, 144, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:30'),
(72, 187, 143, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:30'),
(73, 187, 142, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:30'),
(74, 187, 143, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:31'),
(75, 187, 144, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:31'),
(76, 187, 145, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:31'),
(77, 187, 143, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:31'),
(78, 187, 144, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:31'),
(79, 187, 146, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:32'),
(80, 187, 143, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:32'),
(81, 187, 147, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:32'),
(82, 187, 144, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:32'),
(83, 187, 148, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:32'),
(84, 187, 144, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:33'),
(85, 187, 149, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:33'),
(86, 187, 144, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:33'),
(87, 187, 150, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:33'),
(88, 187, 144, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:33'),
(89, 187, 151, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:34'),
(90, 187, 144, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:34'),
(91, 187, 152, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:34'),
(92, 187, 144, 79200.00, 0.00, 18000.00, 'Envio Estandar Nacional', 7, 97200.00, NULL, 'pending', NULL, NULL, 0, '2026-06-14 11:37:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_items`
--

CREATE TABLE `order_items` (
  `id` int UNSIGNED NOT NULL,
  `order_id` int UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `qty` int NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `price`, `qty`, `subtotal`) VALUES
(4, 4, 10, 150000.00, 1, 150000.00),
(5, 5, 10, 150000.00, 1, 150000.00),
(10, 10, 10, 2000.00, 1, 2000.00),
(12, 12, 15, 67500.00, 1, 67500.00),
(13, 13, 12, 39600.00, 1, 39600.00),
(14, 14, 12, 39600.00, 1, 39600.00),
(15, 15, 12, 39600.00, 1, 39600.00),
(16, 16, 12, 39600.00, 1, 39600.00),
(17, 17, 12, 39600.00, 1, 39600.00),
(18, 18, 12, 39600.00, 1, 39600.00),
(19, 19, 12, 39600.00, 1, 39600.00),
(20, 20, 12, 39600.00, 1, 39600.00),
(21, 21, 12, 39600.00, 1, 39600.00),
(22, 22, 12, 39600.00, 1, 39600.00),
(23, 23, 12, 39600.00, 1, 39600.00),
(24, 24, 12, 39600.00, 1, 39600.00),
(25, 25, 12, 39600.00, 1, 39600.00),
(26, 26, 12, 39600.00, 1, 39600.00),
(27, 27, 12, 39600.00, 1, 39600.00),
(28, 28, 12, 39600.00, 2, 79200.00),
(29, 29, 12, 39600.00, 2, 79200.00),
(30, 30, 12, 39600.00, 2, 79200.00),
(31, 31, 12, 39600.00, 3, 118800.00),
(32, 32, 12, 39600.00, 3, 118800.00),
(33, 33, 12, 39600.00, 3, 118800.00),
(34, 34, 12, 39600.00, 3, 118800.00),
(35, 35, 12, 39600.00, 1, 39600.00),
(36, 36, 12, 39600.00, 1, 39600.00),
(37, 37, 12, 39600.00, 2, 79200.00),
(38, 38, 12, 39600.00, 2, 79200.00),
(39, 39, 12, 39600.00, 2, 79200.00),
(40, 40, 12, 39600.00, 1, 39600.00),
(41, 41, 12, 39600.00, 1, 39600.00),
(42, 42, 12, 39600.00, 1, 39600.00),
(43, 43, 12, 39600.00, 1, 39600.00),
(44, 44, 12, 39600.00, 1, 39600.00),
(45, 45, 12, 39600.00, 1, 39600.00),
(46, 46, 12, 39600.00, 1, 39600.00),
(47, 47, 12, 39600.00, 1, 39600.00),
(48, 48, 12, 39600.00, 2, 79200.00),
(49, 49, 12, 39600.00, 1, 39600.00),
(50, 50, 12, 39600.00, 1, 39600.00),
(51, 51, 12, 39600.00, 1, 39600.00),
(52, 52, 12, 39600.00, 1, 39600.00),
(53, 53, 12, 39600.00, 2, 79200.00),
(54, 54, 12, 39600.00, 2, 79200.00),
(55, 55, 12, 39600.00, 2, 79200.00),
(56, 56, 12, 39600.00, 2, 79200.00),
(57, 57, 12, 39600.00, 2, 79200.00),
(58, 58, 12, 39600.00, 2, 79200.00),
(59, 59, 12, 39600.00, 2, 79200.00),
(60, 60, 12, 39600.00, 2, 79200.00),
(61, 61, 12, 39600.00, 2, 79200.00),
(62, 62, 12, 39600.00, 2, 79200.00),
(63, 63, 12, 39600.00, 2, 79200.00),
(64, 64, 12, 39600.00, 2, 79200.00),
(65, 65, 12, 39600.00, 2, 79200.00),
(66, 66, 12, 39600.00, 2, 79200.00),
(67, 67, 12, 39600.00, 2, 79200.00),
(68, 68, 12, 39600.00, 2, 79200.00),
(69, 69, 12, 39600.00, 2, 79200.00),
(70, 70, 12, 39600.00, 2, 79200.00),
(71, 71, 12, 39600.00, 2, 79200.00),
(72, 72, 12, 39600.00, 2, 79200.00),
(73, 73, 12, 39600.00, 2, 79200.00),
(74, 74, 12, 39600.00, 2, 79200.00),
(75, 75, 12, 39600.00, 2, 79200.00),
(76, 76, 12, 39600.00, 2, 79200.00),
(77, 77, 12, 39600.00, 2, 79200.00),
(78, 78, 12, 39600.00, 2, 79200.00),
(79, 79, 12, 39600.00, 2, 79200.00),
(80, 80, 12, 39600.00, 2, 79200.00),
(81, 81, 12, 39600.00, 2, 79200.00),
(82, 82, 12, 39600.00, 2, 79200.00),
(83, 83, 12, 39600.00, 2, 79200.00),
(84, 84, 12, 39600.00, 2, 79200.00),
(85, 85, 12, 39600.00, 2, 79200.00),
(86, 86, 12, 39600.00, 2, 79200.00),
(87, 87, 12, 39600.00, 2, 79200.00),
(88, 88, 12, 39600.00, 2, 79200.00),
(89, 89, 12, 39600.00, 2, 79200.00),
(90, 90, 12, 39600.00, 2, 79200.00),
(91, 91, 12, 39600.00, 2, 79200.00),
(92, 92, 12, 39600.00, 2, 79200.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_payments`
--

CREATE TABLE `order_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` int UNSIGNED NOT NULL,
  `provider` enum('mercadopago','cod','manual') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mercadopago',
  `preference_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_detail` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'COP',
  `method` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `installments` int DEFAULT NULL,
  `payer_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `order_payments`
--

INSERT INTO `order_payments` (`id`, `order_id`, `provider`, `preference_id`, `payment_id`, `status`, `status_detail`, `amount`, `currency`, `method`, `installments`, `payer_email`, `created_at`, `updated_at`) VALUES
(0, 4, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', '1341346795', 'approved', 'accredited', 150000.00, 'COP', 'pse', 1, 'test_user_80507629@testuser.com', '2025-09-24 21:03:00', '2026-06-14 11:37:34'),
(0, 5, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 150000.00, 'COP', NULL, NULL, 'djedme22@gmail.com', '2025-09-24 21:05:04', '2026-06-14 11:37:34'),
(0, 10, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', '127417475780', 'approved', 'accredited', 2000.00, 'COP', 'master', 1, 'luisarias29@gmail.com', '2025-09-24 21:30:44', '2026-06-14 11:37:34'),
(0, 13, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:35:38', '2026-06-14 11:37:34'),
(0, 14, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:35:38', '2026-06-14 11:37:34'),
(0, 16, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:35:58', '2026-06-14 11:37:34'),
(0, 17, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:00', '2026-06-14 11:37:34'),
(0, 18, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:01', '2026-06-14 11:37:34'),
(0, 19, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:02', '2026-06-14 11:37:34'),
(0, 20, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:02', '2026-06-14 11:37:34'),
(0, 21, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:02', '2026-06-14 11:37:34'),
(0, 22, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:03', '2026-06-14 11:37:34'),
(0, 23, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:03', '2026-06-14 11:37:34'),
(0, 24, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:03', '2026-06-14 11:37:34'),
(0, 26, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:04', '2026-06-14 11:37:34'),
(0, 27, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:04', '2026-06-14 11:37:34'),
(0, 28, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:04', '2026-06-14 11:37:34'),
(0, 29, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:05', '2026-06-14 11:37:34'),
(0, 30, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:05', '2026-06-14 11:37:34'),
(0, 31, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 136800.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:05', '2026-06-14 11:37:34'),
(0, 32, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 136800.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:05', '2026-06-14 11:37:34'),
(0, 33, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 136800.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:05', '2026-06-14 11:37:34'),
(0, 35, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:06', '2026-06-14 11:37:34'),
(0, 36, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:06', '2026-06-14 11:37:34'),
(0, 37, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:06', '2026-06-14 11:37:34'),
(0, 38, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:06', '2026-06-14 11:37:34'),
(0, 40, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:13', '2026-06-14 11:37:34'),
(0, 41, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:20', '2026-06-14 11:37:34'),
(0, 42, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:26', '2026-06-14 11:37:34'),
(0, 43, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:27', '2026-06-14 11:37:34'),
(0, 44, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:27', '2026-06-14 11:37:34'),
(0, 45, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:28', '2026-06-14 11:37:34'),
(0, 46, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:28', '2026-06-14 11:37:34'),
(0, 47, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:28', '2026-06-14 11:37:34'),
(0, 48, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:36:50', '2026-06-14 11:37:34'),
(0, 49, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:25', '2026-06-14 11:37:34'),
(0, 50, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:25', '2026-06-14 11:37:34'),
(0, 51, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:25', '2026-06-14 11:37:34'),
(0, 52, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 57600.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:25', '2026-06-14 11:37:34'),
(0, 53, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:25', '2026-06-14 11:37:34'),
(0, 54, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:26', '2026-06-14 11:37:34'),
(0, 55, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:26', '2026-06-14 11:37:34'),
(0, 56, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:26', '2026-06-14 11:37:34'),
(0, 57, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:27', '2026-06-14 11:37:34'),
(0, 58, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:27', '2026-06-14 11:37:34'),
(0, 59, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:27', '2026-06-14 11:37:34'),
(0, 60, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:27', '2026-06-14 11:37:34'),
(0, 61, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:28', '2026-06-14 11:37:34'),
(0, 62, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:28', '2026-06-14 11:37:34'),
(0, 63, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:28', '2026-06-14 11:37:34'),
(0, 64, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:28', '2026-06-14 11:37:34'),
(0, 65, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:29', '2026-06-14 11:37:34'),
(0, 66, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:29', '2026-06-14 11:37:34'),
(0, 67, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:29', '2026-06-14 11:37:34'),
(0, 68, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:29', '2026-06-14 11:37:34'),
(0, 69, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:30', '2026-06-14 11:37:34'),
(0, 70, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:30', '2026-06-14 11:37:34'),
(0, 71, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:30', '2026-06-14 11:37:34'),
(0, 72, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:30', '2026-06-14 11:37:34'),
(0, 73, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:30', '2026-06-14 11:37:34'),
(0, 74, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:31', '2026-06-14 11:37:34'),
(0, 75, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:31', '2026-06-14 11:37:34'),
(0, 76, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:31', '2026-06-14 11:37:34'),
(0, 77, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:31', '2026-06-14 11:37:34'),
(0, 78, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:31', '2026-06-14 11:37:34'),
(0, 79, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:32', '2026-06-14 11:37:34'),
(0, 80, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:32', '2026-06-14 11:37:34'),
(0, 81, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:32', '2026-06-14 11:37:34'),
(0, 82, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:32', '2026-06-14 11:37:34'),
(0, 83, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:32', '2026-06-14 11:37:34'),
(0, 84, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:33', '2026-06-14 11:37:34'),
(0, 85, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:33', '2026-06-14 11:37:34'),
(0, 86, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:33', '2026-06-14 11:37:34'),
(0, 87, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:33', '2026-06-14 11:37:34'),
(0, 88, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:33', '2026-06-14 11:37:34'),
(0, 89, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:34', '2026-06-14 11:37:34'),
(0, 90, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:34', '2026-06-14 11:37:34'),
(0, 91, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:34', '2026-06-14 11:37:34'),
(0, 92, 'mercadopago', '88490882-90e26d2a-951a-4e2d-a73e-014c51138a2a', NULL, 'pending', NULL, 97200.00, 'COP', NULL, NULL, 'testing@example.com', '2026-06-14 11:37:34', '2026-06-14 11:37:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` int NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `category`, `created_at`, `updated_at`) VALUES
(1, 'Ver y Editar Usuarios', 'Usuarios', '2025-09-03 21:35:09', '2025-09-03 21:35:09'),
(2, 'Gestionar Roles', 'Usuarios', '2025-09-03 21:39:54', '2025-09-03 21:39:54'),
(3, 'Ver Mensajes Pendientes', 'Sistema', '2025-09-16 01:35:16', '2025-09-16 01:35:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `sku` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_desc` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `seo_title` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_price` decimal(10,2) DEFAULT NULL,
  `recommended` tinyint(1) NOT NULL DEFAULT '0',
  `view_before_cart` tinyint(1) NOT NULL DEFAULT '0',
  `video_url` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_button_label` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock` int UNSIGNED NOT NULL DEFAULT '0',
  `status` enum('draft','active','archived') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id`, `sku`, `name`, `slug`, `short_desc`, `description`, `seo_title`, `seo_description`, `seo_keywords`, `price`, `discount_price`, `recommended`, `view_before_cart`, `video_url`, `video_button_label`, `stock`, `status`, `deleted`, `created_at`, `updated_at`) VALUES
(10, '0001', 'Shampoo capell B5 3', 'shampoo-capell-b5-3', 'Exelente Producto para el cabello', '', NULL, NULL, NULL, 2000.00, NULL, 1, 0, NULL, NULL, 7, 'active', 1, '2025-09-24 20:57:25', '2026-06-12 00:52:06'),
(12, 'Cap_0001', 'Acondicionador Reparador Capellb5', 'acondicionador-reparador-capellb5', 'Acondicionador Reparador', '<p>Descripción del producto</p><p><span style=\"font-size: 1rem;\">El Acondicionador Reparador Capell B5 devuelve la suavidad y el brillo a tu melena desde el primer uso. Su fórmula ligera para todo tipo de cabello ayuda a desenredar al instante, reducir el frizz y sellar puntas, dejando el pelo suave, manejable y con movimiento natural.</span></p><p>Libre de sal y parabenos, ideal para uso diario y perfecto para quienes aman un acabado pulido y saludable.</p><p><span style=\"font-size: 1rem;\">Beneficios clave</span></p><p><span style=\"white-space: normal;\">❤️ Desenreda rápido y deja un tacto seda.</span><span style=\"font-size: 1rem;\"><br></span></p><p><span style=\"font-size: 1rem;\">❤️&nbsp;</span><span style=\"font-size: 1rem;\">Controla frizz y aporta brillo visible.</span></p><p>❤️&nbsp;<span style=\"white-space: normal;\">Ayuda a reparar apariencia de puntas y daño por calor/peinado.</span></p><p><span style=\"font-size: 1rem;\">❤️&nbsp;</span><span style=\"white-space: normal;\">Fórmula suave sin sal ni parabenos.</span></p><p><span style=\"font-size: 1rem;\">❤️&nbsp;</span><span style=\"white-space: normal;\">Apto para todo tipo de cabello (liso, ondulado, rizado)</span></p>', 'Acondicionador Reparador Capell B5 | Brillo e Hidratación', 'Acondicionador reparador Capell B5 sin sal ni parabenos. Desenreda, suaviza y reduce el frizz en todo tipo de cabello para un brillo saludable y tacto sedoso', 'condicionador reparador, Capell B5, acondicionador sin sal, sin parabenos, anti frizz, desenredante, brillo e hidratación, cuidado capilar, cabello dañado, todo tipo de cabello, tratamiento capilar, suavidad inmediata, reparación capilar, cosmetica n', 39600.00, NULL, 1, 1, NULL, NULL, 10, 'active', 0, '2025-09-24 22:37:42', '2025-09-24 23:09:11'),
(13, 'Cap_0002', 'Tratamiento Multivitamínico Capell B5', 'tratamiento-multivitaminico-capell-b5', 'Tratamiento Multivitamínico', '<p>El Tratamiento Multivitamínico Capell B5 es un boost de nutrición para tu melena ✨. Su fórmula ligera para todo tipo de cabello aporta vitaminas y humectación para fortalecer, suavizar y dar brillo espejo. Ayuda a reducir frizz, mejorar la apariencia de puntas y dejar el pelo con tacto seda y movimiento natural.</p><p>🌿 Sin sal ni parabenos. Ideal cuando tu cabello necesita un shot de vida rápida y efectiva.</p>', 'Tratamiento Multivitamínico Capell B5 | Brillo y Fuerza ✨', 'Tratamiento multivitamínico Capell B5 🌿 sin sal ni parabenos. Nutre, hidrata y fortalece; reduce frizz y deja brillo sedoso en todo tipo de cabello ✨', 'tratamiento multivitamínico, Capell B5, reparación intensiva, nutrición profunda, fortalecedor capilar, anti frizz, brillo sedoso, hidratación cabello, puntas abiertas, cabello dañado, todo tipo de cabello, sin sal, sin parabenos, cuidado capilar', 39600.00, NULL, 0, 1, NULL, NULL, 10, 'active', 0, '2025-09-24 22:51:03', '2025-09-24 22:51:46'),
(14, 'Cap_0003', 'Colágeno Colgen5', 'colageno-colgen5', 'Colágeno Colgen5', '<p>El Colágeno Colgen5 es tu aliado diario para una belleza que se nota desde adentro ✨. Combina colágeno bovino hidrolizado con biotina y vitaminas C + E, más isoflavonas de soya.</p><p>Sin azúcar añadida, endulzado con stevia 🌿 y de mezcla fácil: agrégalo a agua, jugos o smoothies.</p><p><span style=\"font-size: 1rem;\">¿Qué hace por ti?</span></p><p><span style=\"white-space: normal;\">💆‍♀ Piel con apariencia más firme y luminosa.</span></p><p><span style=\"white-space: normal;\">💁‍♀ Cabello con fuerza y brillo.</span></p><p><span style=\"white-space: normal;\">💅 Uñas más resistentes.</span></p><p><span style=\"white-space: normal;\">🦴 Soporte para articulaciones y movilidad.</span></p><p><span style=\"white-space: normal;\">⚡ Ideal para tu rutina diaria de bienestar.</span></p><div><br></div>', 'Colágeno Colgen5 | Piel, Cabello y Articulaciones ✨', 'Colágeno Colgen5 sin azúcar, endulzado con stevia 🌿. Con biotina y vitaminas C+E para piel, cabello, uñas y articulaciones. Se disuelve fácil; ideal a diario ✨', 'colágeno Colgen5, colágeno hidrolizado, colágeno bovino, suplemento belleza, piel cabello y uñas, articulaciones, biotina, vitamina C, vitamina E, isoflavonas de soya, sin azúcar, stevia, bienestar, cuidado de la piel, cabello fuerte, uñas fuertes', 85000.00, NULL, 1, 1, NULL, NULL, 10, 'archived', 0, '2025-09-24 23:05:28', '2026-06-12 00:53:51'),
(15, 'Cap_0004', 'Mascarilla Repolarizadora Capell B', 'mascarilla-repolarizadora-capell-b', 'Mascarilla Repolarizadora', '<p>La Mascarilla Repolarizadora Capell B5 es un shot de hidratación intensa con ácido hialurónico 💧. Nutre, suaviza y sella la cutícula para un brillo espejo y menos frizz desde el primer uso. Con aceite de argán, manteca de karité y vitamina E, deja el pelo con tacto seda y movimiento natural.</p><p>🌿 Sin sal ni parabenos. Apta para todo tipo de cabello.</p><p><span style=\"font-size: 1rem;\">Beneficios clave</span></p><p><span style=\"white-space: normal;\"><span style=\"white-space:pre\">	</span>•<span style=\"white-space:pre\">	</span>💦 Hidratación profunda y elasticidad.</span></p><p><span style=\"white-space: normal;\"><span style=\"white-space:pre\">	</span>•<span style=\"white-space:pre\">	</span>✨ Brillo inmediato y look pulido.</span></p><p><span style=\"white-space: normal;\"><span style=\"white-space:pre\">	</span>•<span style=\"white-space:pre\">	</span>🚫 Menos frizz, puntas con mejor apariencia.</span></p><p><span style=\"white-space: normal;\"><span style=\"white-space:pre\">	</span>•<span style=\"white-space:pre\">	</span>🧴 Desenreda fácil y deja el cabello suave.</span></p><div><br></div>', 'Mascarilla Repolarizadora Capell B5 | Ácido Hialurónico ✨', 'Mascarilla Repolarizadora Capell B5 con ácido hialurónico 🌿. Hidrata profundo, reduce frizz y sella la cutícula para brillo sedoso y suavidad inmediata en todo', 'mascarilla repolarizadora, Capell B5, ácido hialurónico, hidratación profunda, anti frizz, brillo sedoso, reparación capilar, puntas abiertas, tratamiento capilar, todo tipo de cabello, sin sal, sin parabenos, aceite de argán, manteca de karité', 67500.00, NULL, 1, 1, NULL, NULL, 10, 'active', 0, '2025-09-24 23:45:51', '2025-09-24 23:45:51'),
(16, 'Cap_0005', 'Tónico Capilar Capell B5', 'tonico-capilar-capell-b5', 'Tónico Capilar', '<p>El Tónico Capilar Capell B5 es un boost herbal que activa tu cuero cabelludo y fortalece desde la raíz 🌱. Con 11+ extractos naturales (romero, ortiga, canela, anís, ginseng, maca, cola de caballo, quina, manzanilla, menta, salvia…):</p><p><span style=\"white-space: normal;\"><span style=\"white-space:pre\">	</span>•<span style=\"white-space:pre\">	</span>💪 Fortalece y ayuda a reducir el quiebre.</span></p><p><span style=\"white-space: normal;\"><span style=\"white-space:pre\">	</span>•<span style=\"white-space:pre\">	</span>🌿 Estimula y refresca el cuero cabelludo.</span></p><p><span style=\"white-space: normal;\"><span style=\"white-space:pre\">	</span>•<span style=\"white-space:pre\">	</span>✨ Aporta brillo y suavidad sin dejar residuos.</span></p><p>Sin sal ni parabenos. Ideal para todo tipo de cabello y uso diario.</p>', 'Tónico Capilar Capell B5 | 11+ Extractos Naturales ✨', 'Tónico Capell B5 con 11+ extractos 🌿 (romero, ortiga, quina, cola de caballo, ginseng…). Fortalece, refresca y ayuda a reducir el quiebre para un crecimiento', 'tónico capilar, Capell B5, crecimiento del cabello, fortalecer raíces, caída del cabello, romero, ortiga, quina, cola de caballo, ginseng, maca, canela, anís, menta, manzanilla, salvia, spray capilar, estimula cuero cabelludo, brillo y suavidad', 45000.00, NULL, 1, 1, NULL, NULL, 10, 'active', 0, '2025-09-25 00:39:45', '2025-09-25 00:39:45'),
(17, 'Cap_0006', 'Shampoo Orgánico Sin Sal Anti-Caída Capell B5', 'shampoo-organico-sin-sal-anti-caida-capell-b5', 'Shampoo Orgánico Sin Sal', '<p>El Shampoo Capell B5 sin sal limpia suave y deja el cabello con fuerza, brillo y frescura ✨. Su mezcla de extractos naturales —romero, ortiga, ginseng, maca y manzanilla— ayuda a fortalecer desde la raíz, equilibrar el cuero cabelludo y reducir el quiebre (la causa más común de la caída visible).</p><p>Apto para uso diario y todo tipo de cabello. 🌿</p><p><br></p><p>Beneficios clave</p><p><span style=\"white-space: normal;\"><span style=\"white-space:pre\">	</span>•<span style=\"white-space:pre\">	</span>💪 Fortalece la fibra y ayuda a disminuir la caída por quiebre.</span></p><p><span style=\"white-space: normal;\"><span style=\"white-space:pre\">	</span>•<span style=\"white-space:pre\">	</span>🌱 Sensación de cuero cabelludo fresco y equilibrado.</span></p><p><span style=\"white-space: normal;\"><span style=\"white-space:pre\">	</span>•<span style=\"white-space:pre\">	</span>✨ Brillo ligero y movimiento natural.</span></p><p><span style=\"white-space: normal;\"><span style=\"white-space:pre\">	</span>•<span style=\"white-space:pre\">	</span>✅ Sin sal (y libre de pesadez). Ideal para cabellos tinturados.</span></p><div><br></div>', 'Shampoo Sin Sal Capell B5 | Anti-Caída + Hialurónico ✨', 'Shampoo sin sal Capell B5 con romero, ortiga, ginseng, maca y manzanilla. Limpieza suave que fortalece y ayuda a reducir el quiebre. Brillo ligero y frescura. ✨', 'shampoo sin sal, anti caída natural, Capell B5, romero, ortiga, ginseng, maca, manzanilla, extractos naturales, shampoo fortalecedor, caída por quiebre, cuero cabelludo', 47000.00, NULL, 1, 1, NULL, NULL, 10, 'active', 0, '2025-09-25 01:17:37', '2025-09-25 14:39:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product_attributes`
--

CREATE TABLE `product_attributes` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product_attribute_options`
--

CREATE TABLE `product_attribute_options` (
  `id` bigint UNSIGNED NOT NULL,
  `attribute_id` bigint UNSIGNED NOT NULL,
  `value` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product_category`
--

CREATE TABLE `product_category` (
  `product_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `product_category`
--

INSERT INTO `product_category` (`product_id`, `category_id`) VALUES
(17, 6),
(12, 7),
(13, 8),
(14, 9),
(15, 10),
(16, 11);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product_images`
--

CREATE TABLE `product_images` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `position` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `path`, `is_primary`, `position`, `created_at`) VALUES
(42, 10, 'public/images/products/cd1dd3e1c08667fe349dda8d546e26e7.jpg', 1, 0, '2025-09-24 21:18:03'),
(43, 12, 'public/images/products/3e8d19357fa7f8c25022ded0cf614b5c.jpg', 1, 0, '2025-09-24 22:38:45'),
(44, 12, 'public/images/products/2e260cdb33125f730a1280e1805b1dda.jpg', 0, 0, '2025-09-24 22:38:45'),
(47, 13, 'public/images/products/d97721723fb8ee542fac606bce18ac08.jpg', 0, 0, '2025-09-24 22:51:03'),
(48, 13, 'public/images/products/924019c3400f71d31055e8fb993b3e1b.jpg', 0, 1, '2025-09-24 22:51:03'),
(49, 13, 'public/images/products/fb4342deeeea4440e21e1cab0a278a07.jpg', 1, 0, '2025-09-24 22:52:30'),
(50, 13, 'public/images/products/9bb27e487ff35a2e1b81db93a3cb4847.jpg', 0, 2, '2025-09-24 22:52:30'),
(52, 14, 'public/images/products/4fcea66b384d9fb1411e218d3aefab8e.jpg', 0, 0, '2025-09-24 23:05:28'),
(53, 14, 'public/images/products/4872e5749ce2dcc4d8f6c0846fbe09c4.jpg', 1, 0, '2025-09-24 23:36:57'),
(54, 14, 'public/images/products/ddd11f4efc0a19bc4c2b047da0b7758a.jpg', 0, 1, '2025-09-24 23:37:21'),
(55, 15, 'public/images/products/6c6d62e66180d680e4e32bcac2b22450.jpg', 1, 0, '2025-09-24 23:45:51'),
(56, 15, 'public/images/products/2807bf133fcebe8888e0a6930b55310d.jpg', 0, 0, '2025-09-24 23:45:51'),
(57, 15, 'public/images/products/1f7925000e92095fc0711284896b0e69.jpg', 0, 1, '2025-09-24 23:45:51'),
(58, 16, 'public/images/products/e50a68aa7d0968f6bb3fa3cd5a2153f7.png', 1, 0, '2025-09-25 00:39:45'),
(59, 16, 'public/images/products/06581b3ba01033f9e3038ed1748a3487.jpg', 0, 0, '2025-09-25 00:39:45'),
(60, 16, 'public/images/products/e7dc48f2621cc31b8b71b62339893fc5.png', 0, 1, '2025-09-25 00:39:45'),
(62, 17, 'public/images/products/564afbe4ff6b22009991dca0b53d6e4a.jpg', 0, 0, '2025-09-25 01:17:37'),
(63, 17, 'public/images/products/a8a83a89dd4c53885171f6f480479a79.jpg', 0, 1, '2025-09-25 01:17:37'),
(64, 17, 'public/images/products/b35dc9479b3f697339d0a9cc48298ba4.png', 1, 0, '2026-06-30 14:55:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product_variations`
--

CREATE TABLE `product_variations` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `sku` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `discount_price` decimal(12,2) DEFAULT NULL,
  `stock` int UNSIGNED DEFAULT '0',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attributes` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `borrado` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`, `borrado`) VALUES
(1, 'Administrador', '', '2025-09-03 21:33:26', '2025-09-16 01:35:39', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int NOT NULL,
  `permission_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sent_messages`
--

CREATE TABLE `sent_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` int UNSIGNED NOT NULL,
  `status_sent` enum('pending','paid','processing','shipped','delivered','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `channel` enum('whatsapp','email') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'whatsapp',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sent_messages`
--

INSERT INTO `sent_messages` (`id`, `order_id`, `status_sent`, `channel`, `created_at`) VALUES
(157, 4, 'paid', 'whatsapp', '2025-09-24 21:05:05'),
(158, 4, 'paid', 'email', '2025-09-24 21:05:06'),
(159, 10, 'paid', 'whatsapp', '2025-09-24 21:32:02'),
(160, 10, 'paid', 'email', '2025-09-24 21:32:02'),
(161, 10, 'shipped', 'whatsapp', '2025-09-24 21:34:04'),
(162, 10, 'shipped', 'email', '2025-09-24 21:34:04'),
(163, 12, 'delivered', 'whatsapp', '2026-05-16 02:28:02'),
(164, 12, 'delivered', 'email', '2026-05-16 02:28:03'),
(165, 5, 'delivered', 'whatsapp', '2026-06-12 00:53:01'),
(166, 5, 'delivered', 'email', '2026-06-12 00:53:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `shipping_rates`
--

CREATE TABLE `shipping_rates` (
  `id` int NOT NULL,
  `name` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `type` enum('flat') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'flat',
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `shipping_rates`
--

INSERT INTO `shipping_rates` (`id`, `name`, `amount`, `type`, `status`, `notes`, `deleted`, `created_at`, `updated_at`) VALUES
(7, 'Envio Estandar Nacional', 18000.00, 'flat', 'active', '', 0, '2025-09-24 21:15:06', '2025-09-26 17:10:51'),
(8, 'Eje Cafetero', 12000.00, 'flat', 'active', '', 0, '2025-09-26 17:12:01', NULL),
(9, 'valle del cauca', 15000.00, 'flat', 'active', '', 0, '2025-09-26 17:12:28', NULL),
(10, 'Armenia', 8000.00, 'flat', 'active', '', 0, '2026-05-15 16:00:29', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `shipping_rate_locations`
--

CREATE TABLE `shipping_rate_locations` (
  `id` int NOT NULL,
  `rate_id` int NOT NULL,
  `department` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `shipping_rate_locations`
--

INSERT INTO `shipping_rate_locations` (`id`, `rate_id`, `department`, `city`) VALUES
(25, 7, '*', NULL),
(27, 8, 'Caldas', 'Aguadas'),
(26, 8, 'Caldas', 'Anserma'),
(28, 8, 'Caldas', 'Aranzazu'),
(29, 8, 'Caldas', 'Belalcázar'),
(30, 8, 'Caldas', 'Chinchiná'),
(31, 8, 'Caldas', 'Filadelfia'),
(32, 8, 'Caldas', 'La Dorada'),
(33, 8, 'Caldas', 'La Merced'),
(34, 8, 'Caldas', 'Manizales'),
(35, 8, 'Caldas', 'Manzanares'),
(37, 8, 'Caldas', 'Marmato'),
(36, 8, 'Caldas', 'Marquetalia'),
(38, 8, 'Caldas', 'Marulanda'),
(39, 8, 'Caldas', 'Neira'),
(40, 8, 'Caldas', 'Norcasia'),
(41, 8, 'Caldas', 'Pácora'),
(42, 8, 'Caldas', 'Palestina'),
(43, 8, 'Caldas', 'Pensilvania'),
(44, 8, 'Caldas', 'Riosucio'),
(45, 8, 'Caldas', 'Risaralda'),
(46, 8, 'Caldas', 'Salamina'),
(47, 8, 'Caldas', 'Samaná'),
(48, 8, 'Caldas', 'San José'),
(49, 8, 'Caldas', 'Supía'),
(50, 8, 'Caldas', 'Villamaría'),
(51, 8, 'Caldas', 'Viterbo'),
(52, 8, 'Quindío', 'Armenia'),
(53, 8, 'Quindío', 'Buenavista'),
(54, 8, 'Quindío', 'Calarcá'),
(55, 8, 'Quindío', 'Circasia'),
(56, 8, 'Quindío', 'Córdoba'),
(57, 8, 'Quindío', 'Filandia'),
(58, 8, 'Quindío', 'Génova'),
(59, 8, 'Quindío', 'La Tebaida'),
(60, 8, 'Quindío', 'Montenegro'),
(61, 8, 'Quindío', 'Pijao'),
(62, 8, 'Quindío', 'Quimbaya'),
(63, 8, 'Quindío', 'Salento'),
(64, 8, 'Risaralda', 'Apía'),
(65, 8, 'Risaralda', 'Balboa'),
(66, 8, 'Risaralda', 'Belén de Umbría'),
(67, 8, 'Risaralda', 'Dosquebradas'),
(68, 8, 'Risaralda', 'Guática'),
(69, 8, 'Risaralda', 'La Celia'),
(70, 8, 'Risaralda', 'La Virginia'),
(71, 8, 'Risaralda', 'Marsella'),
(72, 8, 'Risaralda', 'Mistrató'),
(73, 8, 'Risaralda', 'Pereira'),
(74, 8, 'Risaralda', 'Pueblo Rico'),
(75, 8, 'Risaralda', 'Quinchía'),
(76, 8, 'Risaralda', 'Santa Rosa de Cabal'),
(77, 8, 'Risaralda', 'Santuario'),
(78, 9, 'Valle del Cauca', 'Alcalá'),
(79, 9, 'Valle del Cauca', 'Andalucía'),
(80, 9, 'Valle del Cauca', 'Ansermanuevo'),
(81, 9, 'Valle del Cauca', 'Argelia'),
(82, 9, 'Valle del Cauca', 'Bolívar'),
(85, 9, 'Valle del Cauca', 'Buenaventura'),
(84, 9, 'Valle del Cauca', 'Buga'),
(83, 9, 'Valle del Cauca', 'Bugalagrande'),
(87, 9, 'Valle del Cauca', 'Caicedonia'),
(86, 9, 'Valle del Cauca', 'Cali'),
(88, 9, 'Valle del Cauca', 'Candelaria'),
(89, 9, 'Valle del Cauca', 'Cartago'),
(90, 9, 'Valle del Cauca', 'Dagua'),
(91, 9, 'Valle del Cauca', 'Darién'),
(92, 9, 'Valle del Cauca', 'El Águila'),
(93, 9, 'Valle del Cauca', 'El Cairo'),
(94, 9, 'Valle del Cauca', 'El Cerrito'),
(95, 9, 'Valle del Cauca', 'El Dovio'),
(97, 9, 'Valle del Cauca', 'Florida'),
(96, 9, 'Valle del Cauca', 'Floridablanca'),
(98, 9, 'Valle del Cauca', 'Ginebra'),
(99, 9, 'Valle del Cauca', 'Guacarí'),
(100, 9, 'Valle del Cauca', 'Guamo'),
(101, 9, 'Valle del Cauca', 'Jamundí'),
(102, 9, 'Valle del Cauca', 'La Cumbre'),
(103, 9, 'Valle del Cauca', 'La Unión'),
(104, 9, 'Valle del Cauca', 'La Victoria'),
(105, 9, 'Valle del Cauca', 'Obando'),
(106, 9, 'Valle del Cauca', 'Palmira'),
(107, 9, 'Valle del Cauca', 'Pradera'),
(108, 9, 'Valle del Cauca', 'Restrepo'),
(109, 9, 'Valle del Cauca', 'Ríofrío'),
(110, 9, 'Valle del Cauca', 'Roldanillo'),
(111, 9, 'Valle del Cauca', 'San Pedro'),
(112, 9, 'Valle del Cauca', 'Sevilla'),
(113, 9, 'Valle del Cauca', 'Toro'),
(115, 9, 'Valle del Cauca', 'Trujillo'),
(114, 9, 'Valle del Cauca', 'Tuluá'),
(116, 9, 'Valle del Cauca', 'Ullóa'),
(117, 9, 'Valle del Cauca', 'Versalles'),
(118, 9, 'Valle del Cauca', 'Vijes'),
(119, 9, 'Valle del Cauca', 'Yotoco'),
(120, 9, 'Valle del Cauca', 'Yumbo'),
(121, 9, 'Valle del Cauca', 'Zarzal'),
(122, 10, 'Quindío', 'Armenia');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sliders`
--

CREATE TABLE `sliders` (
  `id` int UNSIGNED NOT NULL,
  `titulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `titulo_color` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#000000',
  `subtitulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtitulo_color` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#000000',
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `descripcion_color` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#000000',
  `boton_texto` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Shop Now',
  `boton_color` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffffff',
  `boton_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` tinyint(1) DEFAULT '1',
  `orden` int UNSIGNED DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sliders`
--

INSERT INTO `sliders` (`id`, `titulo`, `titulo_color`, `subtitulo`, `subtitulo_color`, `descripcion`, `descripcion_color`, `boton_texto`, `boton_color`, `boton_url`, `imagen`, `estado`, `orden`, `created_at`) VALUES
(1, 'Promociones Imperdibles de Temporada', '#000000', 'A capell B5 llega lo mejor para ti', '#000000', 'Paorvecha los descuentos que tenemos para ti', '#000000', 'Ver Promociones', '#ffffff', 'https://shop.intermediacolombia.com//template/assets/images/sliders/01.jpg', '1757037128_01.jpg', 0, 0, '2025-09-05 01:52:08'),
(3, '.', '#ffffff', '', '#ffffff', '', '#ffffff', 'Conoce más por WhatsApp', '#ffffff', 'https://wa.me/573105161214?text=Hola%20CapellB5%2C%20quiero%20informaci%C3%B3n%20sobre%20la%20Mascarilla%20Repolarizadora.', '1782780592_bb16d2a3-58bf-49ef-ba3d-153a2095f096.png', 1, 0, '2025-09-05 18:33:41'),
(4, '.', '#ffffff', '', '#dcd5d5', '.', '#e7eaee', 'Conoce más', '#ffffff', 'https://www.youtube.com/@capellb5161', '1782783129_capellb5_rutina_completa_5_productos_1375x520.png', 1, 0, '2025-09-15 17:32:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `system_settings`
--

CREATE TABLE `system_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `setting_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_name`, `value`, `enabled`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'Capell B5', 1, '2025-09-16 19:47:57', '2025-09-16 19:47:57'),
(2, 'site_email', 'capellb5@gmail.com', 1, '2025-09-16 19:47:57', '2025-09-23 05:27:27'),
(3, 'mercadopago_access_token', 'APP_USR-5363174449042275-092414-98dec974e6a2be4b53eaabf5c449a469-88490882', 1, '2025-09-16 19:47:57', '2025-09-24 21:12:17'),
(4, 'mercadopago_public_key', 'APP_USR-c2a921b3-9e2b-415b-8e05-7cfa36665133', 1, '2025-09-16 19:47:57', '2025-09-24 21:12:17'),
(5, 'api_whatsapp', 'H3x0M8nW0aUvbWIhlTIeD4yv9wN7oVWpPS7', 1, '2025-09-16 19:47:57', '2025-09-24 16:16:57'),
(6, 'mail_new_order_message', '<p>Estimado/a {nombre_completo},</p>\r\n\r\n<p>Le informamos que hemos recibido correctamente el pago de su pedido <strong>#{pedido_id}</strong>, con un valor total de <strong>{total}</strong>.</p>\r\n\r\n<p><strong>Resumen del pedido:</strong></p>\r\n<ul>\r\n  <li><strong>Estado:</strong> {estado}</li>\r\n  <li><strong>Fecha:</strong> {fecha}</li>\r\n  <li><strong>Productos:</strong><br>{productos_lista}</li>\r\n</ul>\r\n\r\n<p><strong>Dirección de entrega:</strong><br>\r\n{direccion}, {ciudad}, {departamento}, {codigo_postal}</p>\r\n\r\n<p>En cuanto su pedido sea despachado, recibirá una notificación con la información de envío y el número de seguimiento.</p>\r\n\r\n<p>También puede consultar en cualquier momento el estado de su pedido en nuestra página web.</p>\r\n\r\n<p>Atentamente,<br>\r\n<strong>Equipo Capell B5</strong></p>', 1, '2025-09-16 19:47:57', '2025-09-17 02:10:23'),
(7, 'ws_new_order_message', '🎉 Hola *{nombre}*, ¡gracias por tu compra!  😁\r\n\r\nHemos recibido el pago de tu pedido *#{pedido_id}* por un total de *{total}* 🛍️  \r\n\r\n📍 *Dirección de entrega:*  \r\n{direccion}, {ciudad}  \r\n\r\n📦 *Productos:*  \r\n{productos_lista}  \r\n\r\n✨ Gracias por confiar en nosotros.  \r\nCon cariño,  \r\n*Equipo Capell B5* 💚 \r\n\r\n🚚 Te estaremos informando cuando tu pedido esté en camino.  \r\n🌐 Puedes consultar el estado en cualquier momento en nuestra página web.', 1, '2025-09-16 19:47:57', '2025-09-26 19:01:21'),
(8, 'mail_smtp_host', 'smtp-relay.brevo.com', 1, '2025-09-16 19:47:57', '2025-09-16 19:47:57'),
(9, 'mail_smtp_user', 'intermediacolombia@gmail.com', 1, '2025-09-16 19:47:57', '2025-09-16 19:47:57'),
(10, 'mail_smtp_pass', 'IasgCtERL3vwBUjr', 1, '2025-09-16 19:47:57', '2025-09-16 19:47:57'),
(11, 'mail_smtp_port', '587', 1, '2025-09-16 19:47:57', '2025-09-16 19:47:57'),
(12, 'mail_sender', 'no-reply@activgym.com.co', 1, '2025-09-16 19:47:57', '2025-09-16 19:47:57'),
(25, 'site_logo', '/public/images/logo_1782760480.png', 1, '2025-09-16 19:48:36', '2026-06-29 19:14:40'),
(26, 'site_favicon', '/public/images/favicon_1782760494.png', 1, '2025-09-16 19:48:36', '2026-06-29 19:14:54'),
(358, 'ws_shipped_message', '📦 Hola *{nombre}*, ¡buenas noticias!\r\n\r\nTu pedido *#{pedido_id}* ha sido enviado 🚚  \r\n\r\n📍 *Dirección de entrega:*  \r\n{direccion}, {ciudad}\r\n\r\n🏷️ *Transportadora:* {transporter}  \r\n🔎 *Número de guía:* {tracking}  \r\n\r\nRastrea tu envio aquí {tracking_url}{tracking}\r\n\r\nPuedes consultar el estado en nuestra página web en cualquier momento.  \r\n\r\nGracias por confiar en nosotros.  \r\n*Equipo Capell B5* 💚', 1, '2025-09-17 05:48:05', '2025-09-22 23:42:48'),
(370, 'mail_shipped_message', '<p>Estimado/a {nombre_completo},</p>\r\n\r\n<p>Nos complace informarle que su pedido <strong>#{pedido_id}</strong> ha sido despachado.</p>\r\n\r\n<p><strong>Detalles del envío:</strong></p>\r\n<ul>\r\n  <li><strong>Estado:</strong> {estado}</li>\r\n  <li><strong>Transportadora:</strong> {transporter}</li>\r\n  <li><strong>Número de guía:</strong> {tracking}</li>\r\n  <li><strong>Dirección de entrega:</strong><br>\r\n  {direccion}, {ciudad}, {departamento}, {codigo_postal}</li>\r\n</ul>\r\n\r\n<p>En las próximas horas podrá consultar el movimiento de su paquete a través del sistema de la transportadora. aquí&nbsp;<a href=\"{tracking_url}{tracking}\" target=\"_blank\">{tracking_url}{tracking}</a></p>\r\n\r\n<p>Le recordamos que también puede hacer seguimiento a su pedido desde nuestra página web.</p>\r\n\r\n<p>Atentamente,<br>\r\n<strong>Equipo Capell B5</strong></p>', 1, '2025-09-17 06:04:04', '2025-09-17 07:33:41'),
(371, 'mail_delivered_message', '<p>Estimado/a {nombre},</p>\r\n\r\n<p>Le confirmamos que su pedido <strong>#{pedido_id}</strong> ha sido entregado correctamente en la dirección registrada:</p>\r\n\r\n<p>{direccion}, {ciudad}, {departamento}, {codigo_postal}</p>\r\n\r\n<p><strong>Resumen del pedido:</strong></p>\r\n<ul>\r\n  <li><strong>Estado:</strong> {estado}</li>\r\n  <li><strong>Fecha de entrega:</strong> {fecha}</li>\r\n  <li><strong>Productos:</strong><br>{productos_lista}</li>\r\n</ul>\r\n\r\n<p>Gracias por confiar en nosotros. Esperamos que disfrute de su compra.</p>\r\n\r\n<p>Atentamente,<br>\r\n<strong>Equipo Capell B5</strong></p>', 1, '2025-09-17 06:04:04', '2025-09-17 07:09:02'),
(374, 'ws_delivered_message', '✅ Hola *{nombre}*, tu pedido *#{pedido_id}* ha sido entregado con éxito.  \r\n\r\n📍 *Dirección de entrega:*  \r\n{direccion}, {ciudad}  \r\n\r\n🛍️ *Productos:*  \r\n{productos_lista}  \r\n\r\nEsperamos que disfrutes tu compra.  \r\nGracias por elegirnos 🙏  \r\n*Equipo Capell B5* 💚', 1, '2025-09-17 06:04:04', '2025-09-22 23:42:48'),
(574, 'feature1_icon', 'fa-gift', 1, '2025-09-22 23:58:50', '2025-09-23 01:19:52'),
(575, 'feature1_text', 'Promociones Permanentes', 1, '2025-09-22 23:58:50', '2025-09-24 18:43:06'),
(576, 'feature2_icon', 'fa-whatsapp', 1, '2025-09-22 23:58:50', '2025-09-23 01:19:52'),
(577, 'feature2_text', 'Contacto directo con nosotros', 1, '2025-09-22 23:58:50', '2025-09-23 01:19:52'),
(578, 'feature3_icon', 'fa-truck', 1, '2025-09-22 23:58:50', '2025-09-23 01:19:52'),
(579, 'feature3_text', 'Envios a nivel nacional', 1, '2025-09-22 23:58:50', '2025-09-23 01:19:52'),
(580, 'feature4_icon', 'fa-money', 1, '2025-09-22 23:58:50', '2025-09-24 18:43:06'),
(581, 'feature4_text', 'Compra Protegida', 1, '2025-09-22 23:58:50', '2025-09-23 01:19:52'),
(704, 'free_shipping', '200000', 1, '2025-09-23 04:12:10', '2025-09-24 23:14:09'),
(977, 'special_menu_text', 'Agrega $200.000 o más a tu carrito y disfruta envío gratis', 1, '2025-09-23 04:50:02', '2026-06-30 00:24:31'),
(978, 'special_menu_link', '', 1, '2025-09-23 04:50:02', '2025-09-24 18:44:45'),
(1008, 'business_address', 'calle 50 No. 36-37 segundo piso', 1, '2025-09-23 05:27:27', '2025-09-24 23:12:37'),
(1037, 'business_phone', '+573105161214', 1, '2025-09-23 05:31:48', '2025-09-24 23:12:37'),
(1125, 'facebook', '#', 1, '2025-09-23 05:50:37', '2025-09-23 05:52:22'),
(1126, 'instagram', 'https://www.instagram.com/capellb5?igsh=cXR5aWZqMmlyZ2Zs', 1, '2025-09-23 05:50:37', '2025-09-24 23:18:53'),
(1127, 'youtube', 'https://youtube.com/@capellb5161?si=T9V1eZdxpMIxRR7E', 1, '2025-09-23 05:50:37', '2025-09-24 23:18:53'),
(1128, 'tiktok', 'https://www.tiktok.com/@capellb5?_t=ZS-900TYXNj9nU&_r=1', 1, '2025-09-23 05:50:37', '2025-09-24 23:18:53'),
(1129, 'whatsapp', '+573105161214', 1, '2025-09-23 05:50:37', '2025-09-24 23:12:37'),
(1130, 'twitter', '', 1, '2025-09-23 05:50:37', '2025-09-24 18:36:29'),
(1475, 'about_us', '<h1 data-start=\"239\" data-end=\"256\">Quiénes Somos</h1><p data-start=\"258\" data-end=\"605\">En <strong data-start=\"261\" data-end=\"274\">Capell B5</strong> creemos que el cuidado personal empieza desde lo más esencial: la salud y belleza del cabello. Somos una marca joven, fresca y comprometida que nace con el propósito de ofrecer productos capilares de calidad, inspirados en lo natural y respaldados por fórmulas innovadoras que responden a las necesidades reales de las personas.</p><p data-start=\"607\" data-end=\"895\">Nuestro nombre representa un estilo de vida en el que se combina lo orgánico, lo moderno y lo auténtico. Cada uno de nuestros productos es desarrollado pensando en quienes desean fortalecer, nutrir y revitalizar su cabello sin comprometer el bienestar de su salud ni del medio ambiente.</p><p data-start=\"897\" data-end=\"1257\">Lo que nos diferencia es la pasión por la innovación y el compromiso con nuestros clientes. No solo queremos que el cabello luzca increíble, sino también que cada experiencia con Capell B5 inspire confianza, seguridad y autoestima. Apostamos por ingredientes de alta calidad, procesos responsables y una estética fresca y juvenil que refleja nuestra esencia.</p><p data-start=\"1259\" data-end=\"1459\">En Capell B5 trabajamos día a día para consolidarnos como una marca referente en el mercado de productos capilares, ofreciendo soluciones eficaces y accesibles que contribuyan al bienestar integral.</p><hr data-start=\"1461\" data-end=\"1464\"><h1 data-start=\"1466\" data-end=\"1476\">Misión</h1><p data-start=\"1478\" data-end=\"1689\">Nuestra misión es <strong data-start=\"1496\" data-end=\"1540\">potenciar la belleza natural del cabello</strong> a través de productos capilares innovadores, seguros y de calidad, elaborados con fórmulas que integran lo mejor de la ciencia y de la naturaleza.</p><p data-start=\"1691\" data-end=\"1986\">Nos enfocamos en brindar una experiencia única a cada cliente, acompañándolos en su rutina de cuidado personal y fomentando la confianza en sí mismos. Buscamos ser una alternativa consciente que no solo embellece, sino que también respeta el entorno y promueve hábitos responsables de consumo.</p><p data-start=\"1988\" data-end=\"2167\">En Capell B5 trabajamos con pasión, transparencia y compromiso para generar un impacto positivo en la vida de las personas, aportando salud, bienestar y estilo en cada producto.</p><hr data-start=\"2169\" data-end=\"2172\"><h1 data-start=\"2174\" data-end=\"2184\">Visión</h1><p data-start=\"2186\" data-end=\"2374\">Nuestra visión es convertirnos en una <strong data-start=\"2224\" data-end=\"2260\">marca líder en el sector capilar</strong> a nivel nacional e internacional, reconocida por la calidad, innovación y sostenibilidad de nuestros productos.</p><p data-start=\"2376\" data-end=\"2570\">Queremos ser sinónimo de confianza y autenticidad, destacándonos como pioneros en el uso de ingredientes naturales y fórmulas modernas que se adaptan a las necesidades de cada tipo de cabello.</p><p data-start=\"2572\" data-end=\"2755\">A largo plazo, aspiramos a ser un referente global que inspire a las personas a cuidar de sí mismas y de su entorno, promoviendo una cultura de autocuidado consciente y responsable.</p><p>\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n</p><p data-start=\"2757\" data-end=\"2958\">En Capell B5 soñamos con llegar a cada hogar, demostrando que la belleza y la salud capilar pueden ir de la mano con el respeto por la naturaleza y con un estilo de vida fresco, moderno y sostenible.</p>', 1, '2025-09-23 16:46:52', '2025-09-23 16:46:52'),
(1518, 'hashtag', '#CapellB5', 1, '2025-09-23 19:30:28', '2025-09-23 19:30:28'),
(1549, 'terms-and-conditions', '<p><span style=\"font-size: 2.5rem;\">Términos y Condiciones</span></p><p><strong>Capell B5</strong></p><p>Los presentes <strong>Términos y Condiciones</strong> regulan el acceso, uso y compras realizadas a través del sitio web oficial de <strong>Capell B5</strong> (<a href=\"http://www.capellb5.com/\">www.capellb5.com</a>) y demás canales digitales asociados a la marca. Al navegar en nuestro sitio o adquirir cualquiera de nuestros productos, el usuario acepta expresamente las condiciones aquí descritas.</p><hr><h2>1. Aceptación de los términos</h2><p>El uso de los servicios de Capell B5 implica la aceptación plena y sin reservas de todas las disposiciones incluidas en estos Términos y Condiciones. Si el usuario no está de acuerdo, deberá abstenerse de utilizar el sitio o realizar compras en él.</p><hr><h2>2. Capacidad legal</h2><p>El sitio está disponible únicamente para personas mayores de edad (18 años en adelante) que cuenten con plena capacidad legal para contratar. Los menores de edad solo podrán realizar compras con la supervisión y autorización de sus padres o tutores legales.</p><hr><h2>3. Registro y cuentas de usuario</h2><p>Para realizar compras, el usuario podrá crear una cuenta proporcionando información veraz, completa y actualizada. El usuario es responsable de mantener la confidencialidad de sus credenciales de acceso y de todas las actividades realizadas en su cuenta.</p><hr><h2>4. Información de los productos</h2><p>Capell B5 se esfuerza por mostrar de manera precisa las características, imágenes y descripciones de sus productos. No obstante, pueden existir variaciones mínimas en presentaciones, colores o empaques. Estas diferencias no constituyen incumplimiento contractual.</p><hr><h2>5. Precios y formas de pago</h2><ul>\r\n<li>\r\n<p>Todos los precios están expresados en pesos colombianos (COP) e incluyen los impuestos aplicables.</p>\r\n</li>\r\n<li>\r\n<p>Los métodos de pago habilitados incluyen pasarelas electrónicas, transferencias y pagos contra entrega (según disponibilidad).</p>\r\n</li>\r\n<li>\r\n<p>Capell B5 se reserva el derecho de modificar precios, promociones o descuentos sin previo aviso, respetando siempre las compras ya confirmadas.</p>\r\n</li>\r\n</ul><hr><h2>6. Envíos y entregas</h2><ul>\r\n<li>\r\n<p>Los tiempos de entrega se calculan desde la confirmación del pago y pueden variar según la ciudad o el método de envío seleccionado.</p>\r\n</li>\r\n<li>\r\n<p>El usuario es responsable de proporcionar una dirección de entrega válida y accesible.</p>\r\n</li>\r\n<li>\r\n<p>Capell B5 no se responsabiliza por retrasos ocasionados por causas externas como situaciones climáticas, problemas logísticos o de fuerza mayor.</p>\r\n</li>\r\n</ul><hr><h2>7. Cambios, devoluciones y garantías</h2><ul>\r\n<li>\r\n<p>El usuario podrá solicitar cambios o devoluciones dentro de los plazos legales establecidos, siempre y cuando los productos no hayan sido usados, abiertos ni manipulados indebidamente.</p>\r\n</li>\r\n<li>\r\n<p>Los costos de envío asociados a devoluciones o cambios correrán a cargo del cliente, salvo en casos de productos defectuosos o errores atribuibles a Capell B5.</p>\r\n</li>\r\n<li>\r\n<p>Todos los productos cuentan con garantía legal según la normatividad colombiana vigente.</p>\r\n</li>\r\n</ul><hr><h2>8. Propiedad intelectual</h2><p>Todos los contenidos del sitio web (logos, marcas, textos, fotografías, productos, diseños, códigos, etc.) son propiedad exclusiva de <strong>Capell B5</strong> y están protegidos por las leyes de derechos de autor y propiedad industrial. Queda prohibido su uso sin autorización expresa.</p><hr><h2>9. Responsabilidad del usuario</h2><p>El usuario se compromete a:</p><ul>\r\n<li>\r\n<p>No utilizar el sitio con fines ilegales o fraudulentos.</p>\r\n</li>\r\n<li>\r\n<p>No difundir virus, malware o cualquier software dañino.</p>\r\n</li>\r\n<li>\r\n<p>Proporcionar datos verídicos y actualizados al realizar una compra.</p>\r\n</li>\r\n</ul><hr><h2>10. Limitación de responsabilidad</h2><p>Capell B5 no será responsable por daños directos o indirectos derivados del uso del sitio, interrupciones técnicas, caídas de sistema o errores fuera de nuestro control razonable.</p><hr><h2>11. Promociones y cupones</h2><ul>\r\n<li>\r\n<p>Los descuentos, cupones o campañas promocionales estarán sujetos a condiciones particulares que serán informadas en cada caso.</p>\r\n</li>\r\n<li>\r\n<p>No son acumulables, salvo que se indique expresamente.</p>\r\n</li>\r\n</ul><hr><h2>12. Modificaciones de los términos</h2><p>Capell B5 se reserva el derecho de modificar en cualquier momento estos Términos y Condiciones. La versión vigente estará disponible en el sitio web y será aplicable a las transacciones futuras.</p><hr><h2>13. Legislación aplicable</h2><p>Estos Términos y Condiciones se rigen por las leyes de la República de Colombia. Cualquier controversia será resuelta en los tribunales competentes de acuerdo con la normativa colombiana.</p><hr><h2>14. Contacto</h2><p>Si tienes dudas, reclamos o solicitudes relacionadas con estos Términos y Condiciones, puedes comunicarte con nosotros a través de:</p><ul>\r\n<li>\r\n<p>Página web: <strong><a href=\"http://www.capellb5.com/\">www.capellb5.com</a></strong></p>\r\n</li>\r\n<li>\r\n<p>Correo electrónico: <strong>[correo oficial de Capell B5]</strong></p>\r\n</li>\r\n<li>\r\n<p>Teléfono: <strong>[número de contacto]</strong></p></li></ul>', 1, '2025-09-24 01:51:12', '2025-09-24 01:52:56'),
(1550, 'privacy-policy', '<p><span style=\"font-size: 2.5rem;\">Política de Privacidad</span></p><p><strong>Capell B5</strong></p><p>En <strong>Capell B5</strong> valoramos la confianza que depositas en nosotros y reconocemos la importancia de proteger la información personal que nos compartes. Por ello, esta Política de Privacidad describe cómo recopilamos, utilizamos, almacenamos y protegemos tus datos, garantizando la transparencia y el cumplimiento de la normativa vigente en materia de protección de datos.</p><hr><h2>1. Información que recopilamos</h2><p>Podemos solicitar y recopilar información personal de los usuarios a través de nuestros canales digitales, formularios de contacto, procesos de compra y otros medios relacionados con nuestros servicios. Entre los datos que podemos almacenar se encuentran:</p><ul>\r\n<li>\r\n<p>Nombre y apellidos.</p>\r\n</li>\r\n<li>\r\n<p>Documento de identificación.</p>\r\n</li>\r\n<li>\r\n<p>Dirección de correo electrónico.</p>\r\n</li>\r\n<li>\r\n<p>Número de teléfono o celular.</p>\r\n</li>\r\n<li>\r\n<p>Dirección de envío y facturación.</p>\r\n</li>\r\n<li>\r\n<p>Información relacionada con métodos de pago.</p>\r\n</li>\r\n<li>\r\n<p>Datos de navegación como dirección IP, tipo de dispositivo y cookies.</p>\r\n</li>\r\n</ul><hr><h2>2. Finalidad de los datos recopilados</h2><p>La información proporcionada será utilizada para:</p><ul>\r\n<li>\r\n<p>Procesar pedidos, pagos y entregas de productos.</p>\r\n</li>\r\n<li>\r\n<p>Brindar soporte al cliente y responder consultas.</p>\r\n</li>\r\n<li>\r\n<p>Informar sobre promociones, novedades y campañas relacionadas con Capell B5 (previo consentimiento del usuario).</p>\r\n</li>\r\n<li>\r\n<p>Mejorar la experiencia de navegación y personalizar contenidos.</p>\r\n</li>\r\n<li>\r\n<p>Cumplir obligaciones legales, contractuales o fiscales aplicables.</p>\r\n</li>\r\n</ul><hr><h2>3. Uso responsable de la información</h2><p>En <strong>Capell B5</strong> nos comprometemos a:</p><ul>\r\n<li>\r\n<p>No vender, alquilar ni compartir tu información personal con terceros no autorizados.</p>\r\n</li>\r\n<li>\r\n<p>Compartir datos únicamente con proveedores o aliados estratégicos necesarios para la prestación de nuestros servicios (por ejemplo, transportadoras o pasarelas de pago), bajo estrictos acuerdos de confidencialidad.</p>\r\n</li>\r\n<li>\r\n<p>Tratar tus datos con la máxima seguridad y confidencialidad.</p>\r\n</li>\r\n</ul><hr><h2>4. Derechos de los usuarios</h2><p>Como titular de los datos, tienes derecho a:</p><ul>\r\n<li>\r\n<p>Acceder a la información personal que hemos recopilado sobre ti.</p>\r\n</li>\r\n<li>\r\n<p>Solicitar la corrección de tus datos si son inexactos o están desactualizados.</p>\r\n</li>\r\n<li>\r\n<p>Solicitar la eliminación de tu información cuando ya no sea necesaria para los fines establecidos.</p>\r\n</li>\r\n<li>\r\n<p>Revocar en cualquier momento el consentimiento otorgado para el tratamiento de tus datos.</p>\r\n</li>\r\n</ul><p>Para ejercer estos derechos, puedes escribirnos a:<br>\r\n📩 <strong>[correo oficial de Capell B5]</strong></p><hr><h2>5. Seguridad de la información</h2><p>Implementamos medidas técnicas, administrativas y organizativas para proteger tu información personal contra accesos no autorizados, pérdida, alteración o divulgación indebida. No obstante, debes tener en cuenta que ningún sistema digital es 100% infalible.</p><hr><h2>6. Uso de cookies y tecnologías similares</h2><p>Nuestro sitio web utiliza cookies con el fin de mejorar tu experiencia de navegación, analizar el tráfico del sitio y personalizar contenidos. Puedes configurar tu navegador para rechazar cookies, aunque algunas funciones del sitio podrían verse limitadas.</p><hr><h2>7. Modificaciones de la política</h2><p>Capell B5 se reserva el derecho de modificar en cualquier momento esta Política de Privacidad para adaptarla a cambios legales, tecnológicos o comerciales. La versión más reciente siempre estará disponible en nuestro sitio web oficial.</p><hr><h2>8. Contacto</h2><p>Si tienes preguntas, solicitudes o inquietudes relacionadas con esta Política de Privacidad o con el manejo de tus datos personales, puedes comunicarte con nosotros a través de:</p><ul>\r\n<li>\r\n<p>Página web: <strong><a href=\"http://www.capellb5.com/\">www.capellb5.com</a></strong></p>\r\n</li>\r\n<li>\r\n<p>Correo electrónico: <strong>[correo oficial de Capell B5]</strong></p>\r\n</li>\r\n<li>\r\n<p>Teléfono de atención al cliente: <strong>[número de contacto]</strong></p>\r\n</li>\r\n</ul><hr><p>🔒 En <strong>Capell B5</strong> trabajamos cada día para garantizar que tu información esté protegida y que tu experiencia con nosotros sea segura, confiable y satisfactoria.</p>', 1, '2025-09-24 01:51:12', '2025-09-24 01:51:12'),
(1551, 'return-policy', '<p style=\"text-align: center; \"><span style=\"font-size: 2.5rem;\">Política de Devoluciones</span></p><p><strong>Capell B5</strong></p><p>En <strong>Capell B5</strong> nos esforzamos por garantizar la satisfacción de nuestros clientes con cada compra realizada. Sin embargo, entendemos que pueden presentarse situaciones en las que sea necesario solicitar una devolución, cambio o reembolso. Por ello, hemos diseñado la siguiente política de devoluciones con el fin de establecer procesos claros, justos y transparentes para todas las partes.</p><hr><h2>1. Plazos para solicitar devoluciones</h2><ul>\r\n<li>\r\n<p>El cliente podrá solicitar la devolución de un producto dentro de los <strong>5 días hábiles</strong> siguientes a la recepción del pedido, conforme a lo establecido en el Estatuto del Consumidor en Colombia (Ley 1480 de 2011).</p>\r\n</li>\r\n<li>\r\n<p>Pasado este plazo, las solicitudes serán revisadas únicamente por motivos de garantía o defectos de fábrica.</p>\r\n</li>\r\n</ul><hr><h2>2. Condiciones generales de devolución</h2><p>Para que un producto sea aceptado como devolución, debe cumplir con las siguientes condiciones:</p><ul>\r\n<li>\r\n<p>El producto debe estar <strong>sin uso, en perfecto estado y en su empaque original</strong>.</p>\r\n</li>\r\n<li>\r\n<p>Debe conservar todas sus etiquetas, accesorios, sellos de seguridad y embalaje.</p>\r\n</li>\r\n<li>\r\n<p>No se aceptarán productos abiertos, usados, con signos de manipulación indebida o deterioro causado por el cliente.</p>\r\n</li>\r\n<li>\r\n<p>El producto debe estar acompañado de la <strong>factura de compra</strong> o comprobante de pago correspondiente.</p>\r\n</li>\r\n</ul><hr><h2>3. Productos que no aplican para devolución</h2><p>Por motivos de higiene y cuidado personal, no se aceptan devoluciones de:</p><ul>\r\n<li>\r\n<p>Productos capilares abiertos o usados (shampoo, acondicionadores, mascarillas, tratamientos).</p>\r\n</li>\r\n<li>\r\n<p>Artículos en promoción, liquidación o compras con descuentos especiales, salvo que presenten defectos de fábrica.</p>\r\n</li>\r\n</ul><hr><h2>4. Procedimiento para solicitar una devolución</h2><ol>\r\n<li>\r\n<p>El cliente deberá comunicarse con nuestro equipo de atención al cliente a través de:</p>\r\n<ul>\r\n<li>\r\n<p>Correo electrónico: <strong>[correo oficial de Capell B5]</strong></p>\r\n</li>\r\n<li>\r\n<p>WhatsApp o teléfono: <strong>[número de contacto]</strong></p>\r\n</li>\r\n</ul>\r\n</li>\r\n<li>\r\n<p>Deberá indicar el número de pedido, los datos de la compra y el motivo de la devolución.</p>\r\n</li>\r\n<li>\r\n<p>Nuestro equipo revisará la solicitud y dará respuesta en un plazo máximo de <strong>3 días hábiles</strong>.</p>\r\n</li>\r\n<li>\r\n<p>Si la devolución es aceptada, se informará el procedimiento y la dirección a la cual el cliente debe enviar el producto.</p>\r\n</li>\r\n</ol><hr><h2>5. Costos de envío</h2><ul>\r\n<li>\r\n<p>Cuando la devolución se deba a errores de despacho, productos defectuosos o en mal estado atribuibles a Capell B5, <strong>nos haremos responsables de los costos de envío</strong>.</p>\r\n</li>\r\n<li>\r\n<p>Si la devolución es voluntaria por parte del cliente (ejemplo: arrepentimiento de compra), los gastos de envío correrán por cuenta del comprador.</p>\r\n</li>\r\n</ul><hr><h2>6. Opciones de compensación</h2><p>Una vez recibido el producto y validado que cumple con las condiciones de esta política, el cliente podrá optar por:</p><ul>\r\n<li>\r\n<p><strong>Cambio por otro producto</strong> de igual valor.</p>\r\n</li>\r\n<li>\r\n<p><strong>Bonificación o crédito en tienda</strong> para futuras compras.</p>\r\n</li>\r\n<li>\r\n<p><strong>Reembolso del dinero</strong>, el cual se realizará a través del mismo método de pago utilizado en la compra, dentro de los <strong>15 días hábiles</strong> siguientes a la aceptación de la devolución.</p>\r\n</li>\r\n</ul><hr><h2>7. Garantía de los productos</h2><p>En caso de que el producto presente defectos de fábrica o problemas de calidad, el cliente podrá hacer efectiva la garantía legal dentro del plazo estipulado por la ley. El análisis del producto será realizado por nuestro equipo técnico para determinar si corresponde a un defecto de fabricación o a un mal uso.</p><hr><h2>8. Contacto</h2><p>Para más información sobre nuestra Política de Devoluciones, comunícate con:</p><ul>\r\n<li>\r\n<p>Página web: <strong><a href=\"http://www.capellb5.com/\">www.capellb5.com</a></strong></p>\r\n</li>\r\n<li>\r\n<p>Correo electrónico: <strong>[correo oficial de Capell B5]</strong></p>\r\n</li>\r\n<li>\r\n<p>Teléfono / WhatsApp: <strong>[número de contacto]</strong></p>\r\n</li>\r\n</ul><hr><p>🔄 En <strong>Capell B5</strong> buscamos que cada experiencia de compra sea segura, confiable y satisfactoria, respaldando la calidad de nuestros productos con una política de devoluciones clara y justa.</p>', 1, '2025-09-24 01:51:12', '2025-09-24 01:56:03'),
(1668, 'seo_home_title', 'Capell B5 | Productos capilares naturales para un cabello hermoso.', 1, '2025-09-24 03:30:12', '2025-09-24 03:30:12'),
(1669, 'seo_home_description', 'Descubre en Capell B5 shampoos, mascarillas y tratamientos capilares naturales que cuidan, fortalecen y revitalizan tu cabello cada día.', 1, '2025-09-24 03:30:12', '2025-09-24 03:30:12'),
(1670, 'seo_home_keywords', 'capell b5, shampoo natural, productos capilares, cuidado del cabello, mascarillas capilares, tratamientos para el cabello, shampoo sin sal, cabello sano, nutrición capilar, cosmética natural', 1, '2025-09-24 03:30:12', '2025-09-24 03:30:12'),
(3004, 'business_map', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d836.1477836076108!2d-75.70623347865347!3d4.519627359582317!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e38f5f30628d77f%3A0xc3a0e4bddbf7eea6!2sMAGICNAT%20LAB!5e0!3m2!1ses-419!2sco!4v1758912195915!5m2!1ses-419!2sco\" width=\"100%\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', 1, '2025-09-26 18:47:46', '2025-09-26 18:49:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transporters`
--

CREATE TABLE `transporters` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tracking_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `transporters`
--

INSERT INTO `transporters` (`id`, `name`, `tracking_url`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'Coordinadora', 'https://coordinadora.com/rastreo/rastreo-de-guia/detalle-de-rastreo-de-guia/?guia=', 'active', '', '2025-09-15 18:38:40', '2025-09-15 19:27:48'),
(3, 'Servientrega', 'https://www.servientrega.com/wps/portal/rastreo-envio/detalle?id=', 'active', '', '2025-09-15 19:32:49', '2025-09-15 19:32:49'),
(4, 'Envia', 'https://envia.com/es-CO/rastreo?label=', 'active', '', '2025-09-17 07:39:57', '2025-09-17 17:37:21'),
(5, 'Deprisa', 'https://www.deprisa.com//Tracking/?track=', 'active', '', '2025-09-24 18:19:49', '2025-09-24 18:19:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `email` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cc_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `dial_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `birth_date` date NOT NULL,
  `status` enum('active','inactive','deleted') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `email`, `first_name`, `last_name`, `cc_number`, `dial_code`, `phone`, `birth_date`, `status`, `created_at`, `updated_at`) VALUES
(32, 'djedme22@gmail.com', 'Edisson', 'Medina Bedoya', '1094914578', '+57', '3147165269', '1990-08-22', 'active', '2025-08-29 19:15:35', '2025-09-17 17:14:01'),
(133, 'djedme@hotmail.com', 'John', 'Doe', '123456789', '+57', '3172998776', '1990-08-22', 'active', '2025-09-17 17:48:52', '2025-09-17 17:48:52'),
(156, 'luisarias29@gmail.com', 'luis', 'arias', '10755221', '+57', '3136172171', '1980-12-07', 'active', '2025-09-24 16:30:08', '2025-09-24 21:27:02'),
(185, 'info@jorgesoto.com.co', 'Jorge', 'Soto', '1094904230', '+57', '3137349709', '2000-01-09', 'active', '2025-10-16 15:35:16', '2025-10-16 15:35:16'),
(186, 'leidy599@hotmail.com', 'Leidy', 'Marin', '1094908849', '+57', '3207699534', '1989-05-10', 'active', '2026-05-14 13:43:30', '2026-05-14 13:43:30'),
(187, 'testing@example.com', 'pHqghUme', 'pHqghUme', '4111111111111111', '94102', '555-666-0606', '0000-00-00', 'active', '2026-06-14 11:35:38', '2026-06-14 11:37:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_addresses`
--

CREATE TABLE `user_addresses` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `department` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_line` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `directions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_default` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `user_addresses`
--

INSERT INTO `user_addresses` (`id`, `user_id`, `department`, `city`, `address_line`, `postal_code`, `directions`, `is_default`, `created_at`) VALUES
(91, 32, 'Quindío', 'Calarcá', 'Barrio Villa Yolanda MZB # 9', '630001', 'Enseguida del motel la guaca', 1, '2025-09-10 00:47:59'),
(92, 32, 'Quindío', 'Armenia', 'Barrio Villa Yolanda', '630001', '', 0, '2025-09-10 00:49:53'),
(93, 32, 'Quindío', 'Génova', 'Barrio Villa Yolanda Mz B Casa 9', '630001', '', 0, '2025-09-10 00:55:57'),
(99, 32, 'Quindío', 'Génova', 'Barrio Villa Yolanda', '630001', '', 1, '2025-09-16 00:34:17'),
(100, 32, 'Amazonas', 'Puerto Nariño', 'Barrio Villa Yolanda', '630001', 'Ensegudia', 1, '2025-09-17 17:12:13'),
(101, 32, 'Quindío', 'Génova', 'Barrio Villa Yolanda', '630001', 'Ensegudia', 1, '2025-09-17 17:16:24'),
(102, 133, 'Quindío', 'Génova', 'Barrio Villa Yolanda', '630001', 'Enseguida el motel la guaca', 1, '2025-09-17 18:01:10'),
(103, 32, 'Quindío', 'Pijao', 'Barrio Villa Yolanda Mzb Casa 9', '630001', 'Enseguida del motel la guaca', 1, '2025-09-18 15:04:56'),
(104, 32, 'Quindío', 'Génova', 'Barrio Villa Yolanda Mz B Casa 9', '630001', 'Enseguida del motel la guaca', 1, '2025-09-20 06:15:47'),
(105, 32, 'Quindío', 'Armenia', 'Barrio Villa Yolanda', '630001', 'Ensegudia', 1, '2025-09-23 16:12:56'),
(106, 156, 'Quindío', 'Armenia', 'Calle 50', '630001', 'Frente al ara', 1, '2025-09-24 16:30:08'),
(107, 156, 'Quindío', 'Armenia', 'Calle 50 #36-37', '630001', 'Frente al ARA', 1, '2025-09-24 19:19:59'),
(109, 156, 'Quindío', 'Armenia', 'Calle 50 # 36 - 37', '630001', 'frente al Ara', 1, '2025-09-24 21:13:48'),
(110, 156, 'Quindío', 'Armenia', 'Calle 50', '630001', '', 1, '2025-09-24 21:16:19'),
(111, 185, 'Quindío', 'Calarcá', 'calle', '632001', '', 1, '2025-10-16 15:35:16'),
(112, 186, 'Quindío', 'Armenia', 'Cra 24 A # 1N-56', '630001', 'Barrio Niagara bajo via hojas anchas', 1, '2026-05-14 13:43:30'),
(113, 187, 'Amazonas', 'San Francisco', '3137 Laguna Street', '94102', '555', 1, '2026-06-14 11:35:38'),
(114, 187, 'Antioquia', 'San Francisco', '3137 Laguna Street', '94102', '555', 1, '2026-06-14 11:35:38'),
(115, 187, 'Atlántico', 'San Francisco', '3137 Laguna StreetDzGKh0mw', '94102', '555', 1, '2026-06-14 11:35:58'),
(116, 187, 'Bolívar', 'San Francisco', '3137 Laguna StreetuyWynInq', '94102', '555', 1, '2026-06-14 11:36:00'),
(117, 187, 'Boyacá', 'San Francisco', '3137 Laguna Street0hqRfIFE', '94102', '555', 1, '2026-06-14 11:36:01'),
(118, 187, 'Amazonas', 'San Francisco', '-1 OR 2+391-391-1=0+0+0+1 --', '94102', '555', 1, '2026-06-14 11:36:02'),
(119, 187, 'Amazonas', 'San Francisco', '-1 OR 3+391-391-1=0+0+0+1 --', '94102', '555', 1, '2026-06-14 11:36:03'),
(120, 187, 'Antioquia', 'San Francisco', '-1 OR 2+980-980-1=0+0+0+1 --', '94102', '555', 1, '2026-06-14 11:36:03'),
(121, 187, 'Amazonas', 'San Francisco', '-1 OR 2+811-811-1=0+0+0+1', '94102', '555', 1, '2026-06-14 11:36:03'),
(122, 187, 'Arauca', 'San Francisco', '-1 OR 2+455-455-1=0+0+0+1 --', '94102', '555', 1, '2026-06-14 11:36:04'),
(123, 187, 'Antioquia', 'San Francisco', '-1 OR 2+216-216-1=0+0+0+1', '94102', '555', 1, '2026-06-14 11:36:04'),
(124, 187, 'Amazonas', 'San Francisco', '-1 OR 3*2>(0+5+811-811)', '94102', '555', 1, '2026-06-14 11:36:04'),
(125, 187, 'Antioquia', 'San Francisco', '-1 OR 3+216-216-1=0+0+0+1', '94102', '555', 1, '2026-06-14 11:36:05'),
(126, 187, 'Arauca', 'San Francisco', '-1 OR 3+455-455-1=0+0+0+1 --', '94102', '555', 1, '2026-06-14 11:36:05'),
(127, 187, 'Amazonas', 'San Francisco', '-1 OR 2+1-1-1=1 AND 811=811', '94102', '555', 1, '2026-06-14 11:36:05'),
(128, 187, 'Atlántico', 'San Francisco', '3137 Laguna Street', '94102', '555', 1, '2026-06-14 11:36:05'),
(129, 187, 'Antioquia', 'San Francisco', '-1\' OR 2+628-628-1=0+0+0+1 --', '94102', '555', 1, '2026-06-14 11:36:05'),
(130, 187, 'Amazonas', 'San Francisco', '-1\' OR 2+512-512-1=0+0+0+1 --', '94102', '555', 1, '2026-06-14 11:36:06'),
(131, 187, 'Atlántico', 'San Francisco', '-1 OR 2+174-174-1=0+0+0+1 --', '94102', '555', 1, '2026-06-14 11:36:06'),
(132, 187, 'Amazonas', 'San Francisco', '-1 OR 3*2=5 AND 811=811', '94102', '555', 1, '2026-06-14 11:36:06'),
(133, 187, 'Antioquia', 'San Francisco', '-1\' OR 3+628-628-1=0+0+0+1 --', '94102', '555', 1, '2026-06-14 11:36:06'),
(134, 187, 'Atlántico', 'San Francisco', '-1 OR 3+174-174-1=0+0+0+1 --', '94102', '555', 1, '2026-06-14 11:36:06'),
(135, 187, 'Amazonas', 'San Francisco', '-1\' OR 3+512-512-1=0+0+0+1 --', '94102', '555', 1, '2026-06-14 11:36:07'),
(136, 187, 'Amazonas', '-----', '3137 Laguna Street', '94102', '555', 1, '2026-06-14 11:36:13'),
(137, 187, 'Amazonas', '-----', '-1 OR 2+560-560-1=0+0+0+1 --', '94102', '555', 1, '2026-06-14 11:36:27'),
(138, 187, 'Amazonas', '-----', '-1 OR 2+130-130-1=0+0+0+1', '94102', '555', 1, '2026-06-14 11:36:27'),
(139, 187, 'Amazonas', '-----', '-1\' OR 2+909-909-1=0+0+0+1 --', '94102', '555', 1, '2026-06-14 11:36:28'),
(140, 187, 'Amazonas', '-----', '-1\' OR 2+118-118-1=0+0+0+1 or \'SVeZlUiU\'=\'', '94102', '555', 1, '2026-06-14 11:36:28'),
(141, 187, 'Amazonas', '-----', '-1\" OR 2+22-22-1=0+0+0+1 --', '94102', '555', 1, '2026-06-14 11:36:28'),
(142, 187, 'Caldas', 'San Francisco', '3137 Laguna Street', '94102', '555', 1, '2026-06-14 11:36:50'),
(143, 187, 'Bolívar', 'San Francisco', '3137 Laguna Street', '94102', '555', 1, '2026-06-14 11:37:27'),
(144, 187, 'Boyacá', 'San Francisco', '3137 Laguna Street', '94102', '555', 1, '2026-06-14 11:37:29'),
(145, 187, 'Caldas', 'San Francisco', '-1 OR 2+553-553-1=0+0+0+1 --', '94102', '555', 1, '2026-06-14 11:37:31'),
(146, 187, 'Caldas', 'San Francisco', '-1 OR 3+553-553-1=0+0+0+1 --', '94102', '555', 1, '2026-06-14 11:37:32'),
(147, 187, 'Caldas', 'San Francisco', '-1 OR 2+98-98-1=0+0+0+1', '94102', '555', 1, '2026-06-14 11:37:32'),
(148, 187, 'Caldas', 'San Francisco', '-1 OR 3+98-98-1=0+0+0+1', '94102', '555', 1, '2026-06-14 11:37:32'),
(149, 187, 'Caldas', 'San Francisco', '-1\' OR 2+420-420-1=0+0+0+1 --', '94102', '555', 1, '2026-06-14 11:37:33'),
(150, 187, 'Caldas', 'San Francisco', '-1\' OR 3+420-420-1=0+0+0+1 --', '94102', '555', 1, '2026-06-14 11:37:33'),
(151, 187, 'Caldas', 'San Francisco', '-1\' OR 2+386-386-1=0+0+0+1 or \'sHuaEzwn\'=\'', '94102', '555', 1, '2026-06-14 11:37:34'),
(152, 187, 'Caldas', 'San Francisco', '-1\' OR 3+386-386-1=0+0+0+1 or \'sHuaEzwn\'=\'', '94102', '555', 1, '2026-06-14 11:37:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_tokens`
--

CREATE TABLE `user_tokens` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `user_tokens`
--

INSERT INTO `user_tokens` (`id`, `user_id`, `token`, `expires_at`) VALUES
(61, 5, '33253784ff9a13f2dba7e314183d3951', 1761339027),
(62, 19, 'ebf07b6640704fd7ceaee6d7e99c0975', 1761498569),
(66, 19, 'da7f893473569c78f9007c24dd506b63', 1785421873);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `correo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol_id` int DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` enum('admin','user') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `borrado` tinyint(1) NOT NULL DEFAULT '0',
  `intentos` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `correo`, `username`, `rol_id`, `password`, `rol`, `estado`, `created_at`, `updated_at`, `borrado`, `intentos`) VALUES
(5, 'Intermedia', 'Colombia', 'intermediacolombia@gmail.com', 'intermedia', 1, '$2y$10$iPwibqnFEvK0Kwg26FDmDOz3KhV8rLc8DshH.ubEVo/qMe9yJcvha', 'admin', 0, '2025-02-10 15:04:02', '2025-09-24 15:08:34', 0, 0),
(6, 'Edisson', 'Medina Bedoya', 'djedme22@gmail.com', 'admin', 2, '$2y$10$CetDCEv5NsN2gLn5KOult.WOVCVk7hXWm7R.UdghcVqmnkkSyWM0O', '', 1, '2025-02-10 16:29:30', '2025-08-09 21:19:39', 1, 0),
(11, 'Fran', 'Arias', 'gerencia@activgym.com.co', 'adminactiv', 1, '$2y$10$AIzqtWw6S82hX.XzYN4Zo.J0HeL2qPZ52zCDvMe3jJ8UNZHtf4w/y', 'admin', 0, '2025-03-03 14:11:39', '2025-09-03 21:56:36', 1, 0),
(12, 'Laura', 'Isaza', 'isaza7482@gmail.com', 'lau.isaza', 5, '$2y$10$err5btiC.0vUqZlS/63r6uAliOqvjB2U8lexJv9.RayH5B5Kv3SL2', 'admin', 0, '2025-03-03 15:17:27', '2025-09-03 21:56:51', 1, 0),
(13, 'Dilan', 'Garcia', 'dilangarcia2901@gmail.com', 'dilangarce', 2, '$2y$10$cV7IfMWdo5hMt4SVFHA/DuBxbaX1NOtXijCNtdHLJ1bkI5UM5fEga', 'user', 0, '2025-03-07 20:28:23', '2025-09-03 21:56:44', 1, 0),
(14, 'Juan Camilo', 'Marin Tapasco', 'juancamilomarin9502@gmail.com', 'juanmarino', 2, '$2y$10$t7KcxypFPquytbS8OBg1r.mZqGiMbiNNTqa9TcnXJ2yR6R8Avmw0y', 'user', 0, '2025-03-07 20:31:25', '2025-09-03 21:56:48', 1, 0),
(15, 'Lenin', 'Morales Osorio', 'lenina.moraleso@uqvirtual.edu.co', 'lenin.morales', NULL, '$2y$10$VFqwPOANpZU/VjM2Pv.y/ONh4FhOkU722JSNK/E8HNh710bjV16cS', 'admin', 0, '2025-03-17 23:45:22', '2025-04-26 02:53:35', 1, 0),
(17, 'Jhon Admin', 'Doe', 'jhondoe@gmail.com', 'jhon.doe', 2, '$2y$10$g1eDbQebGvxmCkJe4jRfK.5kiIaCpXAxFiEgTWXmQm4HOu4rZTSDq', 'admin', 0, '2025-04-05 04:41:52', '2025-08-09 21:19:34', 1, 0),
(18, 'Claudia', 'Suarez', 'claudia.suarez2424@gmail.com', 'claudia.suarez', 5, '$2y$10$toRIQuljHq4aUye37M83ZOh0rlP9e04oG8J894OvERSsT7PYfCawC', 'admin', 0, '2025-07-02 18:14:07', '2025-09-03 21:56:40', 1, 0),
(19, 'Luis', 'Arias', 'luisarias29@gmail.com', 'admin_capell', 1, '$2y$10$7kcYidW210QDi/7HodC8h.0jk1qaj0eeyAPZ6xtiScB6CkY/6u9wC', 'admin', 0, '2025-09-03 21:57:43', '2025-09-24 21:20:17', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ws_outbox`
--

CREATE TABLE `ws_outbox` (
  `id` bigint UNSIGNED NOT NULL,
  `phonenumber` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ws_outbox`
--

INSERT INTO `ws_outbox` (`id`, `phonenumber`, `text`, `url`, `created_at`) VALUES
(51, '+573207699534', '✅ Hola *Leidy*, tu pedido *#12* ha sido entregado con éxito.  \r\n\r\n📍 *Dirección de entrega:*  \r\nCra 24 A # 1N-56, Armenia  \r\n\r\n🛍️ *Productos:*  \r\n1x Mascarilla Repolarizadora Capell B - $67.500  \r\n\r\nEsperamos que disfrutes tu compra.  \r\nGracias por elegirnos 🙏  \r\n*Equipo Capell B5* 💚', NULL, '2026-05-16 02:28:03'),
(52, '+573147165269', '✅ Hola *Edisson*, tu pedido *#5* ha sido entregado con éxito.  \r\n\r\n📍 *Dirección de entrega:*  \r\nBarrio Villa Yolanda, Armenia  \r\n\r\n🛍️ *Productos:*  \r\n1x Shampoo capell B5 3 - $150.000  \r\n\r\nEsperamos que disfrutes tu compra.  \r\nGracias por elegirnos 🙏  \r\n*Equipo Capell B5* 💚', NULL, '2026-06-12 00:53:02');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_banner` (`type`,`slot`);

--
-- Indices de la tabla `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indices de la tabla `blog_post_category`
--
ALTER TABLE `blog_post_category`
  ADD PRIMARY KEY (`post_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_coupon_code` (`code`);

--
-- Indices de la tabla `coupon_categories`
--
ALTER TABLE `coupon_categories`
  ADD PRIMARY KEY (`coupon_id`,`category_id`),
  ADD KEY `fk_cc_category` (`category_id`);

--
-- Indices de la tabla `coupon_products`
--
ALTER TABLE `coupon_products`
  ADD PRIMARY KEY (`coupon_id`,`product_id`),
  ADD KEY `fk_cp_product` (`product_id`);

--
-- Indices de la tabla `coupon_usages`
--
ALTER TABLE `coupon_usages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cu_coupon` (`coupon_id`),
  ADD KEY `idx_cu_user` (`user_id`),
  ADD KEY `idx_cu_order` (`order_id`);

--
-- Indices de la tabla `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_orders_user_id` (`user_id`),
  ADD KEY `idx_orders_address_id` (`address_id`),
  ADD KEY `fk_orders_transporter` (`transporter_id`);

--
-- Indices de la tabla `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_items_order` (`order_id`),
  ADD KEY `idx_items_product` (`product_id`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token_unique` (`token`),
  ADD KEY `idx_pr_user` (`user_id`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_perm_name` (`name`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_products_sku` (`sku`),
  ADD UNIQUE KEY `uq_products_slug` (`slug`),
  ADD UNIQUE KEY `uq_products_name` (`name`);

--
-- Indices de la tabla `product_attributes`
--
ALTER TABLE `product_attributes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indices de la tabla `product_attribute_options`
--
ALTER TABLE `product_attribute_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attribute_id` (`attribute_id`);

--
-- Indices de la tabla `product_category`
--
ALTER TABLE `product_category`
  ADD PRIMARY KEY (`product_id`,`category_id`),
  ADD KEY `idx_pc_category` (`category_id`);

--
-- Indices de la tabla `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pi_product` (`product_id`);

--
-- Indices de la tabla `product_variations`
--
ALTER TABLE `product_variations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_roles_name` (`name`);

--
-- Indices de la tabla `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `idx_rp_perm` (`permission_id`);

--
-- Indices de la tabla `sent_messages`
--
ALTER TABLE `sent_messages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_order_status_channel` (`order_id`,`status_sent`,`channel`);

--
-- Indices de la tabla `shipping_rates`
--
ALTER TABLE `shipping_rates`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `shipping_rate_locations`
--
ALTER TABLE `shipping_rate_locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_rate_dept_city` (`rate_id`,`department`,`city`);

--
-- Indices de la tabla `sliders`
--
ALTER TABLE `sliders`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_name` (`setting_name`);

--
-- Indices de la tabla `transporters`
--
ALTER TABLE `transporters`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_email` (`email`),
  ADD UNIQUE KEY `uq_users_cc` (`cc_number`);

--
-- Indices de la tabla `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_addr_user` (`user_id`);

--
-- Indices de la tabla `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token_unique` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `rol_id` (`rol_id`);

--
-- Indices de la tabla `ws_outbox`
--
ALTER TABLE `ws_outbox`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ws_outbox_phone` (`phonenumber`),
  ADD KEY `idx_ws_outbox_created` (`created_at`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `coupon_usages`
--
ALTER TABLE `coupon_usages`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT de la tabla `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `product_attributes`
--
ALTER TABLE `product_attributes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `product_attribute_options`
--
ALTER TABLE `product_attribute_options`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de la tabla `product_variations`
--
ALTER TABLE `product_variations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `sent_messages`
--
ALTER TABLE `sent_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=167;

--
-- AUTO_INCREMENT de la tabla `shipping_rates`
--
ALTER TABLE `shipping_rates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `shipping_rate_locations`
--
ALTER TABLE `shipping_rate_locations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT de la tabla `sliders`
--
ALTER TABLE `sliders`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3355;

--
-- AUTO_INCREMENT de la tabla `transporters`
--
ALTER TABLE `transporters`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=267;

--
-- AUTO_INCREMENT de la tabla `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT de la tabla `user_tokens`
--
ALTER TABLE `user_tokens`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `ws_outbox`
--
ALTER TABLE `ws_outbox`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `blog_post_category`
--
ALTER TABLE `blog_post_category`
  ADD CONSTRAINT `blog_post_category_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blog_post_category_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `coupon_categories`
--
ALTER TABLE `coupon_categories`
  ADD CONSTRAINT `fk_cc_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cc_coupon` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `coupon_products`
--
ALTER TABLE `coupon_products`
  ADD CONSTRAINT `fk_cp_coupon` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cp_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `coupon_usages`
--
ALTER TABLE `coupon_usages`
  ADD CONSTRAINT `fk_cu_coupon` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cu_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_cu_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_address` FOREIGN KEY (`address_id`) REFERENCES `user_addresses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_orders_transporter` FOREIGN KEY (`transporter_id`) REFERENCES `transporters` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT;

--
-- Filtros para la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_pr_usuario` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `product_attributes`
--
ALTER TABLE `product_attributes`
  ADD CONSTRAINT `product_attributes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `product_attribute_options`
--
ALTER TABLE `product_attribute_options`
  ADD CONSTRAINT `product_attribute_options_ibfk_1` FOREIGN KEY (`attribute_id`) REFERENCES `product_attributes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `product_category`
--
ALTER TABLE `product_category`
  ADD CONSTRAINT `fk_pc_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pc_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `fk_product_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `product_variations`
--
ALTER TABLE `product_variations`
  ADD CONSTRAINT `product_variations_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sent_messages`
--
ALTER TABLE `sent_messages`
  ADD CONSTRAINT `fk_sm_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `shipping_rate_locations`
--
ALTER TABLE `shipping_rate_locations`
  ADD CONSTRAINT `fk_srl_rate` FOREIGN KEY (`rate_id`) REFERENCES `shipping_rates` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD CONSTRAINT `fk_addr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
