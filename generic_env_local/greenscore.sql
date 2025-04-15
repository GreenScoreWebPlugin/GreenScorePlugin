-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql-greenscoreweb.alwaysdata.net
-- Generation Time: Apr 15, 2025 at 09:57 AM
-- Server version: 10.11.9-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `greenscoreweb_database`
--
CREATE DATABASE IF NOT EXISTS `greenscoreweb_database` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `greenscoreweb_database`;

-- --------------------------------------------------------

--
-- Table structure for table `advice`
--

CREATE TABLE `advice` (
  `id` int(11) NOT NULL,
  `is_dev` tinyint(1) NOT NULL,
  `advice` longtext NOT NULL,
  `title` varchar(100) NOT NULL,
  `icon` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `advice`
--

INSERT INTO `advice` (`id`, `is_dev`, `advice`, `title`, `icon`) VALUES
(1, 1, 'Optimisez vos requêtes SQL pour éviter les opérations inutiles.', 'Optimisez vos requêtes', 'fa-solid fa-database'),
(2, 1, 'Minifiez et compressez les fichiers CSS et JavaScript.', 'Minifiez vos fichiers', 'fa-brands fa-css'),
(3, 1, 'Implémentez un système de cache efficace côté serveur.', 'Cache serveur efficace', 'fa-solid fa-server'),
(4, 1, 'Réduisez le nombre de requêtes HTTP en regroupant les fichiers.', 'Regroupez vos fichiers', 'fa-solid fa-file'),
(5, 1, 'Utilisez des images dans des formats modernes comme WebP.', 'Utilisez WebP', 'fa-solid fa-image'),
(6, 1, 'Adoptez le chargement différé (lazy loading) pour les images et les scripts.', 'Activez le lazy loading', 'fa-solid fa-image'),
(7, 1, 'Évitez les boucles infinies ou les calculs inutiles côté serveur.', 'Évitez les boucles', 'fa-solid fa-rotate-left'),
(8, 1, 'Déployez votre application sur des serveurs écologiques.', 'Serveurs écologiques', 'fa-solid fa-server'),
(9, 1, 'Utilisez des CDN pour distribuer vos contenus statiques.', 'CDN pour contenus', 'fa-solid fa-chart-simple'),
(10, 1, 'Surveillez et réduisez l\'utilisation de mémoire et CPU dans vos scripts.', 'Réduisez mémoire & CPU', 'fa-solid fa-microchip'),
(11, 1, 'Concevez des algorithmes plus efficaces pour économiser des ressources.', 'Algorithmes optimisés', 'fa-solid fa-chart-diagram'),
(12, 1, 'Réduisez la taille des cookies et limitez leur utilisation.', 'Cookies allégés', 'fa-solid fa-cookie'),
(13, 1, 'Implémentez des stratégies de mise en cache HTTP (ETag, Cache-Control).', 'Cache HTTP stratégique', 'fa-solid fa-server'),
(14, 1, 'Utilisez des frameworks ou bibliothèques légères.', 'Frameworks légers', 'fa-solid fa-book'),
(15, 1, 'Choisissez des bases de données adaptées à vos besoins pour éviter la surcharge.', 'Base de données adaptée', 'fa-solid fa-database'),
(16, 1, 'Testez régulièrement les performances de votre code.', 'Testez vos performances', 'fa-solid fa-code'),
(17, 1, 'Optez pour des solutions cloud avec une empreinte carbone réduite.', 'Cloud écoresponsable', 'fa-solid fa-cloud-arrow-up'),
(18, 1, 'Désactivez les logs en mode production pour réduire la charge.', 'Désactivez les logs', 'fa-solid fa-file-lines'),
(19, 1, 'Générez les rapports ou analyses hors ligne si possible.', 'Rapports hors ligne', 'fa-solid fa-bug'),
(20, 1, 'Faites des audits réguliers pour détecter les ressources inutilisées.', 'Auditez vos ressources', 'fa-solid fa-network-wired'),
(21, 0, 'Réglez la luminosité de votre écran pour économiser de l\'énergie.', 'Écran moins lumineux', 'fa-solid fa-desktop'),
(22, 0, 'Fermez les onglets inutilisés dans votre navigateur.', 'Fermez les onglets', 'fa-solid fa-window-restore'),
(23, 0, 'Utilisez un bloqueur de publicités pour réduire la charge réseau.', 'Bloquez les pubs', 'fa-solid fa-bullhorn'),
(24, 0, 'Privilégiez les moteurs de recherche écologiques comme Ecosia.', 'Moteurs écolos', 'fa-solid fa-leaf'),
(25, 0, 'Désactivez les vidéos en lecture automatique.', 'Stop aux vidéos auto', 'fa-solid fa-photo-film'),
(26, 0, 'Téléchargez des fichiers uniquement si nécessaire.', 'Téléchargez malin', 'fa-solid fa-file'),
(27, 0, 'Effacez régulièrement les caches et cookies de votre navigateur.', 'Videz caches & cookies', 'fa-solid fa-cookie'),
(28, 0, 'Limitez l\'utilisation des extensions de navigateur gourmandes.', 'Limitez les extensions', 'fa-solid fa-puzzle-piece'),
(29, 0, 'Privilégiez le mode économie de données sur les appareils mobiles.', 'Mode économie activé', 'fa-solid fa-leaf'),
(30, 0, 'Consultez les sites optimisés pour mobile pour réduire la consommation de données.', 'Sites mobiles optimisés', 'fa-solid fa-mobile'),
(31, 0, 'Regardez des vidéos en basse résolution si la haute définition n\'est pas nécessaire.', 'Vidéo en basse qualité', 'fa-solid fa-photo-film'),
(32, 0, 'Déconnectez-vous des comptes inutilisés pendant la navigation.', 'Déconnectez les comptes', 'fa-solid fa-right-from-bracket'),
(33, 0, 'Utilisez un navigateur léger comme Firefox Focus pour les tâches simples.', 'Navigateur ultra léger', 'fa-solid fa-window-maximize'),
(34, 0, 'Téléchargez des contenus en Wi-Fi au lieu d\'utiliser les données mobiles.', 'Wi-Fi avant tout', 'fa-solid fa-wifi'),
(35, 0, 'Évitez les sites web surchargés de publicités.', 'Évitez les pubs lourdes', 'fa-solid fa-bullhorn'),
(36, 0, 'Planifiez vos recherches web pour éviter de multiplier les requêtes.', 'Recherches planifiées', 'fa-solid fa-globe'),
(37, 0, 'Privilégiez les versions texte des articles lorsque c\'est possible.', 'Articles en mode texte', 'fa-solid fa-text-height'),
(38, 0, 'Partagez les ressources (documents, vidéos) via des liens plutôt qu\'en pièce jointe.', 'Partagez via liens', 'fa-solid fa-link'),
(39, 0, 'Désactivez les notifications push non essentielles sur les sites web.', 'Désactivez notifications', 'fa-solid fa-bell'),
(40, 0, 'Fermez les applications ouvertes en arrière-plan pour économiser des ressources.', 'Fermez les apps inutiles', 'fa-solid fa-square-xmark');

-- --------------------------------------------------------

--
-- Table structure for table `equivalent`
--

CREATE TABLE `equivalent` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `equivalent` double NOT NULL,
  `icon_thumbnail` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `equivalent`
--

INSERT INTO `equivalent` (`id`, `name`, `equivalent`, `icon_thumbnail`) VALUES
(1, 'A/R Lille - Nîmes', 0.24, 'car.png'),
(2, 'A/R Paris - Berlin en TGV', 13.8, 'train-paris-berlin.png'),
(3, 'A/R Paris - Marseille en TGV', 22.7, 'train-paris-marseille.png'),
(4, 'A/R Paris - New York en avion', 0.06, 'new-york.png'),
(5, 'intégrales de Friends', 12.6, 'friends.png'),
(6, 'journées au ski', 2.04, 'ski.png'),
(7, 'marathons des films Harry Potter', 159, 'harry-potter.png'),
(8, 'nuits au camping', 52.6, 'camping.png'),
(9, 'nuits dans un hôtel', 14.5, 'hotel.png'),
(10, 'nuits dans une location', 17.2, 'rental.png'),
(11, 'remplissages de piscines', 13.3, 'swimming.png'),
(12, 'tour de la terre en voiture', 0.01, 'earth.png'),
(13, 'emails', 40620, 'email.png'),
(14, 'go de données', 10521, 'data.png'),
(15, 'recherches sur le web', 1959, 'search.png'),
(16, 'spams', 26748, 'spam.png'),
(17, 'stocker un go de données', 424928, 'data-cloud.png'),
(18, 'heures de streaming vidéo', 1562, 'youtube.png'),
(19, 'heures de visioconférence', 1752, 'video-conference.png'),
(20, 'km en autocar thermique', 3399, 'autocar.png'),
(21, 'km en avion court courrier', 387, 'plane.png'),
(22, 'km en avion long courrier', 658, 'plane.png'),
(23, 'km en bus', 883, 'bus.png'),
(24, 'km en avion moyen courrier', 533, 'plane.png'),
(25, 'km en intercités', 11136, 'intercity.png'),
(26, 'km dans le métro', 22523, 'metro.png'),
(27, 'km en moto thermique', 523, 'motorbike.png'),
(28, 'km en RER', 10225, 'rer.png'),
(29, 'km en scooter', 1311, 'scooter.png'),
(30, 'km en TER', 3611, 'ter.png'),
(31, 'km en TGV', 34130, 'tgv.png'),
(32, 'km en tramway', 23364, 'tramway.png'),
(33, 'km en trottinette électrique', 4016, 'trottinette.png'),
(34, 'km en vélo électrique', 9132, 'bike.png'),
(35, 'km en voiture électrique', 967, 'electric-car.png'),
(36, 'km en voiture thermique', 460, 'car.png'),
(37, 'kg d\'abricots', 114, 'apricot.png'),
(38, 'kg d\'ail', 279, 'garlic.png'),
(39, 'kg d\'ananas', 77.4, 'pineapple.png'),
(40, 'kg d\'artichaut', 25.8, 'artichoke.png'),
(41, 'kg d\'asperge', 64.1, 'asparagus.png'),
(42, 'kg d\'aubergine', 219, 'eggplant.png'),
(43, 'kg d\'avocat', 67.6, 'avocado.png'),
(44, 'kg de banane', 114, 'banana.png'),
(45, 'kg de betterave', 274, 'beetroot.png'),
(46, 'kg de blette', 184, 'chard.png'),
(47, 'kg de brocoli', 111, 'broccoli.png'),
(48, 'kg de carambole', 188, 'carom.png'),
(49, 'kg de cassis', 55.7, 'cassis.png'),
(50, 'kg de carotte', 275, 'carrot.png'),
(51, 'kg de céleri', 148, 'celery.png'),
(52, 'kg de cerise', 74.9, 'cherry.png'),
(53, 'kg de champignon', 203, 'mushroom.png'),
(54, 'kg de châtaigne', 53.2, 'chestnut.png'),
(55, 'kg de chou', 116, 'cabbage.png'),
(56, 'kg de chou de Bruxelles', 174, 'brussels-sprouts.png'),
(57, 'kg de chou-fleur', 136, 'cauliflower.png'),
(58, 'kg de citron', 141, 'lemon.png'),
(59, 'kg de clémentine', 81.9, 'lemon.png'),
(60, 'kg de coing', 185, 'quince.png'),
(61, 'kg de concombre', 211, 'cucumber.png'),
(62, 'kg de courgette', 162, 'zucchini.png'),
(63, 'kg d\'endive', 107, 'endive.png'),
(64, 'kg de fraise', 210, 'strawberry.png'),
(65, 'kg de framboise', 67.8, 'raspberry.png'),
(66, 'kg de kiwi', 102, 'kiwi.png'),
(67, 'kg d\'haricot vert', 242, 'green-bean.png'),
(68, 'kg de laitue', 106, 'salad.png'),
(69, 'kg de maïs', 123, 'corn.png'),
(70, 'kg de mangue', 9.4, 'mango.png'),
(71, 'kg de melon', 107, 'melon.png'),
(72, 'kg de noix de coco', 40.1, 'coconut.png'),
(73, 'kg d\'oignon', 257, 'oignon.png'),
(74, 'kg d\'orange', 158, 'orange.png'),
(75, 'kg de pastèque', 156, 'watermelon.png'),
(76, 'kg de pêche', 168, 'peach.png'),
(77, 'kg de poire', 275, 'pear.png'),
(78, 'kg de poivron', 84.5, 'bell-pepper.png'),
(79, 'kg de pomme', 253, 'apple.png'),
(80, 'kg de raisin', 219, 'grapes.png'),
(81, 'kg de tomate', 172, 'tomato.png'),
(82, 'kg de topinambour', 198, 'topinambour.png'),
(83, 'boxs', 1.22, 'internet-box.png'),
(84, 'casque de réalité virtuelle', 1.38, 'vr-headset.png'),
(85, 'clefs usb', 34.5, 'usb-key.png'),
(86, 'disque durs externes', 8.4, 'hdd.png'),
(87, 'écrans d\'ordinateur', 1.09, 'screen.png'),
(88, 'enceintes connectées', 3.77, 'speakers.png'),
(89, 'ordinateur fixe', 0.33, 'computer.png'),
(90, 'ordinateur portable', 0.52, 'laptop.png'),
(91, 'smartphones', 1.16, 'smartphones.png'),
(92, 'télévision', 0.21, 'tv.png'),
(93, 'kg de beurre', 12.9, 'butter.png'),
(94, 'kg de boeuf', 3.81, 'beef.png'),
(95, 'kg de cheeseburger', 5.79, 'cheeseburger.png'),
(96, 'kg de kebab', 8.57, 'kebab.png'),
(97, 'kg d\'oeufs', 31.6, 'eggs.png'),
(98, 'kg de pâtes', 46.6, 'pasta.png'),
(99, 'repas avec du boeuf', 13.8, 'beefsteak.png'),
(100, 'repas avec du poulet', 63.3, 'chicken.png'),
(101, 'chemises en coton', 7.56, 'shirt.png'),
(102, 'jeans', 3.99, 'jeans.png'),
(103, 'manteau', 0.99, 'jacket.png'),
(104, 'chaussures en cuir', 6.69, 'shoes.png'),
(105, 'sweats en coton', 3.08, 'hoddie.png'),
(106, 'armoire', 0.11, 'cupboard.png'),
(107, 'canapé en textile', 0.56, 'sofa.png'),
(108, 'chaises en bois', 5.37, 'wooden-chair.png'),
(109, 'lit', 0.23, 'bed.png'),
(110, 'table en bois', 1.25, 'table.png'),
(111, 'aspirateurs', 2.11, 'vaccum-cleaner.png'),
(112, 'bouilloires', 2.45, 'kettles.png'),
(113, 'cafetière expresso', 0.47, 'coffee-machine.png'),
(114, 'climatiseur', 0.24, 'clim.png'),
(115, 'four électrique', 0.38, 'four électrique.png'),
(116, 'lave-vaisselle', 0.21, 'dishes-washer.png'),
(117, 'réfrégirateur', 0.31, 'fridge.png'),
(118, 'lave-linge', 0.2, 'washing-machine.png'),
(119, 'litres de bière', 89.3, 'beer.png'),
(120, 'litres de café', 168, 'coffee.png'),
(121, 'litres de soda', 196, 'soda.png'),
(122, 'litres d\'eau du robinet', 747576, 'faucet.png'),
(123, 'litres d\'eau en bouteille', 374, 'water-bottle.png'),
(124, 'litres de vin', 84, 'wine.png'),
(125, 'litres de thé', 2554, 'tee.png'),
(126, 'heures d\'utilisation d\'une LED', 200000, 'led.png'),
(127, 'heures d\'utilisation d\'un ventilateur', 83333, 'ventilateur.png'),
(128, 'min de chargement d\'un smartphone', 1666666, 'chargeur.png'),
(129, 'km en vélo', 588235, 'bike.png');

-- --------------------------------------------------------

--
-- Table structure for table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `monitored_website`
--

CREATE TABLE `monitored_website` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `url_domain` varchar(255) DEFAULT NULL,
  `url_full` longtext DEFAULT NULL,
  `queries_quantity` int(11) DEFAULT NULL,
  `carbon_footprint` double DEFAULT NULL,
  `data_transferred` double DEFAULT NULL,
  `resources` longtext DEFAULT NULL,
  `loading_time` double DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `creation_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `monitored_website`
--

INSERT INTO `monitored_website` (`id`, `user_id`, `url_domain`, `url_full`, `queries_quantity`, `carbon_footprint`, `data_transferred`, `resources`, `loading_time`, `country`, `creation_date`) VALUES
(1, 3, 'youtube.com', 'https://www.youtube.com/', 17, 1.61, 1979313, '2324467', 1.978, 'France', '2025-01-21 17:11:08'),
(2, 5, 'amazon.com', 'https://www.amazon.com/', 288, 19.19, 136183, '2492594', 1.741, 'France', '2025-01-21 17:11:16'),
(3, 5, 'amazon.com', 'https://www.amazon.com/b/?_encoding=UTF8&node=23634165011&pd_rd_w=GpurF&content-id=amzn1.sym.9e3167b9-3458-4bd3-a9ee-02b4fc17a8ef&pf_rd_p=9e3167b9-3458-4bd3-a9ee-02b4fc17a8ef&pf_rd_r=2C7HAPA6MT0BNGQK0QFR&pd_rd_wg=3xyBx&pd_rd_r=fbd3236c-1a5c-4bb4-a548-f60496b6af44&ref_=pd_hp_d_hero_unk', 104, 1.06, 113392, '2423335', 1.392, 'France', '2025-01-21 17:11:19'),
(4, 5, 'amazon.com', 'https://www.amazon.com/b/ref=s9_acss_bw_cg_HOL21GFG_1a1_w?node=23634102011&pf_rd_p=9b4a235a-d43f-40cc-972b-378d04bad40f&pf_rd_r=0Y97H5FV8MB6V5K32RPH', 68, 0.72, 908784, '1086969', 1.297, 'France', '2025-01-21 17:11:22'),
(5, 3, 'amazon.fr', 'https://www.amazon.fr/?tag=admarketpla00-21&ref=pd_sl_780d6e6918d3edbf67fd91f28348c5e4bb827a5c095523bc0558ce2d&mfadid=adm', 5, 0.33, 0, '0', -1737475884.256, 'France', '2025-01-21 17:11:25'),
(6, 5, 'amazon.com', 'https://www.amazon.com/ref=nav_logo', 89, 0.9, 141451, '502869', 1.216, 'France', '2025-01-21 17:11:27'),
(7, 5, '127.0.0.1', 'http://127.0.0.1:8000/derniere-page-web-consultee', 10, 0.7, 161788, '261913', 2.773, 'France', '2025-01-21 17:11:32'),
(8, 3, 'adidas.fr', 'https://www.adidas.fr/?utm_source=admarketplace&utm_medium=SEM&utm_campaign=FR%20CPQV&utm_content=e140110976401813504&cm_mmc=AdieSEM_adMarketplace-_-FR%20CPQV-_-Privacy-_-privacy+fr-_-140110976401813504&cm_mmca1=DE&cm_mmca2=Exact&ds_kid=_dstrackerid&mfadid=adm', 29, 2.21, 1022609, '2581968', 0.357, 'France', '2025-01-28 17:11:43'),
(9, 3, 'temu.com', 'https://www.temu.com/?_x_ns_irclickid=3Jm2w6VCrxyKTEoy-6S3vTkgUksxigwMbR9KzA0&_x_ads_account=18350&_x_ads_id=1580294&_x_ns_iradname=Online%20Tracking%20Link&_x_ns_iradsize=&_x_ns_prodsku=&_x_ns_irmptype=mediapartner&_x_ns_sharedid=&_x_ns_ts=1737475914324&_x_ns_randint=6025383&_x_ns_adtype=ONLINE_TRACKING_LINK&_p_rfs=1&irgwc=1&_x_ns_irmpgroupname=%22cx%22&_x_ads_channel=impact&_x_ns_mp_value2=&_x_ns_mp_value3=&_x_ns_irmpname=adMarketplace&_x_ns_irpid=3798038&_bg_fs=1&_p_jump_id=866&_x_vst_scene=adg', 23, 1.6, 212559, '1062689', 0.688, 'France', '2025-01-21 17:11:57'),
(10, 3, 'wikipedia.org', 'https://www.wikipedia.org/', 4, 0.27, 36938, '61490', 0.166, 'France', '2025-01-21 17:12:16'),
(11, 3, 'facebook.com', 'https://www.facebook.com/login/?next=https%3A%2F%2Fwww.facebook.com%2F', 32, 2.28, 632870, '827476', 0.859, 'France', '2025-01-21 17:12:56'),
(12, 3, 'google.com', 'https://www.google.com/search?client=firefox-b-d&q=jeujeujeu+voiture&sei=o8ePZ-3iC-DV7M8Pj4qtgAY', 19, 1.36, 320661, '1001770', 1.869, 'France', '2025-01-21 17:13:28'),
(13, 3, 'poki.com', 'https://poki.com/fr/voiture', 110, 1.11, 105152, '642801', 1.574, 'France', '2025-01-21 17:13:41'),
(14, 3, 'poki.com', 'https://poki.com/fr/gar%C3%A7on', 334, 3.64, 7560543, '9418737', 20.735, 'France', '2025-01-21 17:14:00'),
(15, 3, 'poki.com', 'https://poki.com/fr/gar%C3%A7on', 503, 5.45, 10476961, '12800073', 30.835, 'France', '2024-12-21 17:14:10'),
(16, 3, 'facebook.com', 'https://www.facebook.com/login/?next=https%3A%2F%2Fwww.facebook.com%2F', 60, 0.64, 1114445, '1343606', 77.738, 'France', '2025-01-21 17:14:15'),
(17, 3, 'youtube.com', 'https://www.youtube.com/', 303, 3.12, 2104426, '2534304', 132.901, 'France', '2025-01-21 17:14:20'),
(19, 5, 'amazon.com', 'https://www.amazon.com/dp/B0DTKK9BVR/ref=sr_1_5?_encoding=UTF8&content-id=amzn1.sym.901633d4-d57a-4ad5-8a4f-0d5e9c7c398e&dib=eyJ2IjoiMSJ9.u5m6vPS4NjWqEDeqA2kRi6bg7ZXzMi3ygAuy00yRVmHPHxeoDdHKHvCs0W73qzIbPCiG4lnIXuTCxzgo8InfN9GFP6qz71qqc4ODdFM3Vh3Th2eSvoJDdrpOZKRbHtKpplMAWNlROwsJwcOQ7F5WaPlIKK1ewQhWM-8mqizEvaU11Eg73Iu6x-AwG2hxfbu5wxShHcAcqai_fzqNiLuVx4BwngZ7rhRooyykt6stcnjWx7ByI84dpXGtU-qaMO1JzT3PQvhlmDSPB4e7UD2RLR1hyQOVnnL6E6dQqC1Pkf42XVGwYO9_hqvbLOm0mz0h2HvudkcaZw4y188kmBlGV9DWIqCoRQIR4lo7kLFzThpeRgUxaOpMYUDfT5BTd6hQt6arOBY1VldvjL38xaTxcZinriU8nokPnLqHjHF4w65aQHVHV7cJ0R1rF0vrCqA2.lCTcDV1UiLPbZHx5ZfQ8Yyhn1MfN1moOFY_ga8IrmIg&dib_tag=se&keywords=toys+games&pd_rd_r=83e28977-ed98-4c17-9302-bf9a140d6afa&pd_rd_w=wOcoh&pd_rd_wg=WrpfI&pf_rd_p=901633d4-d57a-4ad5-8a4f-0d5e9c7c398e&pf_rd_r=ANCM7C5FKZ7GFP2ZW98J&qid=1737477459&sr=8-5', 44, 0.47, 774965, '820450', 0.689, 'France', '2025-01-21 17:38:32'),
(20, 5, 'amazon.com', 'https://www.amazon.com/ref=nav_logo', 156, 1.58, 135575, '1224297', 0.72, 'France', '2025-01-21 17:38:37'),
(21, 5, 'amazon.com', 'https://www.amazon.com/b/?_encoding=UTF8&node=23634165011&pd_rd_w=2mpeD&content-id=amzn1.sym.9e3167b9-3458-4bd3-a9ee-02b4fc17a8ef&pf_rd_p=9e3167b9-3458-4bd3-a9ee-02b4fc17a8ef&pf_rd_r=GKTGT8GCA8BQPE7F5AGN&pd_rd_wg=KjMRc&pd_rd_r=eaa8ae2a-5eaf-4f84-affa-aa0a972f05ae&ref_=pd_hp_d_hero_unk', 63, 4.26, 107807, '2425240', 1.086, 'France', '2025-01-21 17:40:41'),
(22, 5, 'amazon.com', 'https://www.amazon.com/b/ref=s9_acss_bw_cg_HOL21GFG_3d1_w?node=23634107011&pf_rd_p=9b4a235a-d43f-40cc-972b-378d04bad40f&pf_rd_r=GERAW1Z9FKGBX68Q8W31', 33, 2.22, 96232, '374397', 0.466, 'France', '2025-01-21 17:42:30'),
(23, 5, 'amazon.com', 'https://www.amazon.com/b/ref=s9_acss_bw_cg_HOL21GFG_3d1_w?node=23634107011&pf_rd_p=9b4a235a-d43f-40cc-972b-378d04bad40f&pf_rd_r=GERAW1Z9FKGBX68Q8W31', 92, 0.93, 105285, '434225', 44.625, 'France', '2025-01-21 17:43:14'),
(24, 5, 'amazon.com', 'https://www.amazon.com/', 409, 1.54, 201059, '1925189', 50.636, 'France', '2025-01-29 15:01:32'),
(25, 5, 'amazon.com', 'https://www.amazon.com/', 428, 1.61, 206405, '1925387', 250.246, 'France', '2025-01-29 15:04:46'),
(26, 5, 'amazon.com', 'https://www.amazon.com/', 13, 0.86, 0, '87358', 0.057, 'France', '2025-01-29 15:04:51'),
(27, 5, 'amazon.com', 'https://www.amazon.com/', 338, 1.27, 146628, '2817655', 6.681, 'France', '2025-01-29 15:04:58'),
(28, 5, 'amazon.com', 'https://www.amazon.com/', 14, 0.05, 4752, '176', 11.634, 'France', '2025-01-29 15:05:11'),
(29, 5, 'amazon.com', 'https://www.amazon.com/', 284, 18.91, 138855, '2077847', 1.045, 'France', '2025-01-29 15:10:50'),
(30, 5, 'amazon.com', 'https://www.amazon.com/s?k=kitchen&rh=p_36%3A-5000&_encoding=UTF8&content-id=amzn1.sym.3addd8c2-c121-4001-827d-961993c7a38d&pd_rd_r=1788c078-3166-426f-9fb4-a960c7020771&pd_rd_w=Pz12T&pd_rd_wg=cKQ42&pf_rd_p=3addd8c2-c121-4001-827d-961993c7a38d&pf_rd_r=RTM1NMY32Z4QX7XC5TM1&ref=pd_hp_d_hero_unk', 14, 0.05, 40329, '195816', 0.643, 'France', '2025-01-29 15:11:09'),
(31, 5, 'amazon.com', 'https://www.amazon.com/s/?_encoding=UTF8&k=kitchen&rh=p_36%3A-5000&pd_rd_w=Pz12T&content-id=amzn1.sym.3addd8c2-c121-4001-827d-961993c7a38d&pf_rd_p=3addd8c2-c121-4001-827d-961993c7a38d&pf_rd_r=RTM1NMY32Z4QX7XC5TM1&pd_rd_wg=cKQ42&pd_rd_r=1788c078-3166-426f-9fb4-a960c7020771&ref_=pd_hp_d_hero_unk', 189, 0.73, 1252562, '3652495', 3.348, 'France', '2025-01-29 15:11:11'),
(32, 5, 'amazon.com', 'https://www.amazon.com/s/?_encoding=UTF8&k=kitchen&rh=p_36%3A-5000&pd_rd_w=Pz12T&content-id=amzn1.sym.3addd8c2-c121-4001-827d-961993c7a38d&pf_rd_p=3addd8c2-c121-4001-827d-961993c7a38d&pf_rd_r=RTM1NMY32Z4QX7XC5TM1&pd_rd_wg=cKQ42&pd_rd_r=1788c078-3166-426f-9fb4-a960c7020771&ref_=pd_hp_d_hero_unk', 234, 0.9, 1420304, '4204835', 18.969, 'France', '2025-01-29 15:11:28'),
(33, 5, 'amazon.com', 'https://www.amazon.com/', 260, 17.34, 138465, '2572987', 0.823, 'France', '2025-01-29 15:16:15'),
(34, 5, 'amazon.com', 'https://www.amazon.com/', 359, 1.35, 145823, '3248857', 4.75, 'France', '2025-01-29 15:16:19'),
(35, 5, 'amazon.com', 'https://www.amazon.com/s/?_encoding=UTF8&k=good%20books&pd_rd_w=BD2s6&content-id=amzn1.sym.47a749d7-720e-40e3-bf0b-d92fb71ae016&pf_rd_p=47a749d7-720e-40e3-bf0b-d92fb71ae016&pf_rd_r=MHBVSN38XY0R5XP184MH&pd_rd_wg=Fk6WN&pd_rd_r=d7564e09-f65b-4b6d-ae4d-3dad2f38ef43&ref_=pd_hp_d_hero_unk', 15, 0.06, 24911, '76615', 0.425, 'France', '2025-01-29 15:16:20'),
(36, 5, 'amazon.com', 'https://www.amazon.com/s/?_encoding=UTF8&k=good%20books&pd_rd_w=BD2s6&content-id=amzn1.sym.47a749d7-720e-40e3-bf0b-d92fb71ae016&pf_rd_p=47a749d7-720e-40e3-bf0b-d92fb71ae016&pf_rd_r=MHBVSN38XY0R5XP184MH&pd_rd_wg=Fk6WN&pd_rd_r=d7564e09-f65b-4b6d-ae4d-3dad2f38ef43&ref_=pd_hp_d_hero_unk', 162, 0.61, 403910, '1565183', 10.114, 'France', '2025-01-29 15:16:30'),
(37, 5, 'x.com', 'https://x.com/', 241, 0.93, 1557442, '2491241', 1.374, 'France', '2025-01-29 15:16:32'),
(38, 5, 'x.com', 'https://x.com/notifications', 501, 2.07, 13650681, '15210787', 7.409, 'France', '2025-01-29 15:16:38'),
(39, 5, 'x.com', 'https://x.com/notifications', 506, 2.09, 13650681, '15218843', 8.314, 'France', '2025-01-29 15:16:45'),
(40, 5, 'x.com', 'https://x.com/notifications', 515, 2.12, 13650681, '15221613', 17.97, 'France', '2025-01-29 15:16:52'),
(41, 5, 'x.com', 'https://x.com/notifications', 578, 2.36, 13650681, '15238193', 345.839, 'France', '2025-01-29 15:22:20'),
(42, 5, 'amazon.com', 'https://www.amazon.com/', 11, 0.73, 0, '68454', 0.154, 'France', '2025-01-29 15:22:24'),
(43, 5, 'amazon.com', 'https://www.amazon.com/', 336, 1.27, 147553, '2947241', 2.892, 'France', '2025-01-29 15:22:27'),
(44, 5, 'amazon.com', 'https://www.amazon.com/s/?_encoding=UTF8&k=good%20books&pd_rd_w=6E96y&content-id=amzn1.sym.47a749d7-720e-40e3-bf0b-d92fb71ae016&pf_rd_p=47a749d7-720e-40e3-bf0b-d92fb71ae016&pf_rd_r=Z5D3WB06FVH37W686TKK&pd_rd_wg=DLHsd&pd_rd_r=242bc16d-2a3e-406f-a404-b99b84f09a48&ref_=pd_hp_d_hero_unk', 72, 0.27, 191194, '1541770', 0.501, 'France', '2025-01-29 15:22:28'),
(45, 5, 'amazon.com', 'https://www.amazon.com/s/?_encoding=UTF8&k=good%20books&pd_rd_w=6E96y&content-id=amzn1.sym.47a749d7-720e-40e3-bf0b-d92fb71ae016&pf_rd_p=47a749d7-720e-40e3-bf0b-d92fb71ae016&pf_rd_r=Z5D3WB06FVH37W686TKK&pd_rd_wg=DLHsd&pd_rd_r=242bc16d-2a3e-406f-a404-b99b84f09a48&ref_=pd_hp_d_hero_unk', 147, 0.56, 242199, '1747390', 4.772, 'France', '2025-01-29 15:22:34'),
(46, 5, 'amazon.com', 'https://www.amazon.com/s/?_encoding=UTF8&k=good%20books&pd_rd_w=6E96y&content-id=amzn1.sym.47a749d7-720e-40e3-bf0b-d92fb71ae016&pf_rd_p=47a749d7-720e-40e3-bf0b-d92fb71ae016&pf_rd_r=Z5D3WB06FVH37W686TKK&pd_rd_wg=DLHsd&pd_rd_r=242bc16d-2a3e-406f-a404-b99b84f09a48&ref_=pd_hp_d_hero_unk', 159, 0.6, 246357, '1747544', 18.811, 'France', '2025-01-29 15:22:47'),
(47, 11, '', 'about:addons', 0, 0, 0, '0', 0, 'France', '2025-01-29 16:18:15'),
(48, 11, 'youtube.com', 'https://www.youtube.com/results?search_query=ibra+tv', 21, 1.39, 0, '5062', 1.523, 'France', '2025-01-29 16:18:15'),
(49, 11, 'facebook.com', 'https://www.facebook.com/login/?next=https%3A%2F%2Fwww.facebook.com%2F', 0, 0.01, 25850, '25850', 1738163927.563, 'France', '2025-01-29 16:18:48'),
(50, 11, '', 'about:newtab', 0, 0, 0, '0', 0, 'France', '2025-01-29 16:18:54'),
(51, 11, 'wikipedia.org', 'https://www.wikipedia.org/', 5, 0.02, 56149, '63556', 0.189, 'France', '2025-01-29 16:18:57'),
(52, 11, 'commons.wikimedia.org', 'https://commons.wikimedia.org/wiki/Main_Page', 45, 0.17, 165801, '272104', 1.601, 'France', '2025-01-29 16:19:02'),
(53, 11, 'commons.wikimedia.org', 'https://commons.wikimedia.org/wiki/Special:UploadWizard', 12, 0.05, 48016, '106836', 0.511, 'France', '2025-01-29 16:20:31'),
(54, 11, 'commons.wikimedia.org', 'https://commons.wikimedia.org/wiki/Special:NewFiles', 37, 0.15, 478641, '1052611', 5.476, 'France', '2025-01-29 16:20:59'),
(55, 3, 'amazon.fr', 'https://www.amazon.fr/', 321, 21.44, 354267, '3201407', 1.037, 'France', '2025-03-27 09:04:36'),
(56, 3, 'amazon.fr', 'https://www.amazon.fr/', 485, 3.16, 664891, '5108599', 4.759, 'France', '2025-03-27 09:04:40'),
(57, 3, 'amazon.fr', 'https://www.amazon.fr/Energizer-Pack-Piles-AA-Alkaline-Power/dp/B07L47TF81/?_encoding=UTF8&pd_rd_w=nPQEb&content-id=amzn1.sym.4dd835b2-1cef-4f67-9aa6-a889456f9338&pf_rd_p=4dd835b2-1cef-4f67-9aa6-a889456f9338&pf_rd_r=51XX475K6E8P9W3SFTNJ&pd_rd_wg=8bgZ8&pd_rd_r=d59e6730-5fcc-42aa-8a9f-13c33dcb066c&ref_=pd_hp_d_atf_dealz_tdc', 0, 0, 0, '0', 0, 'France', '2025-03-27 09:04:41'),
(58, 3, 'amazon.fr', 'https://www.amazon.fr/Energizer-Pack-Piles-AA-Alkaline-Power/dp/B07L47TF81/?_encoding=UTF8&pd_rd_w=nPQEb&content-id=amzn1.sym.4dd835b2-1cef-4f67-9aa6-a889456f9338&pf_rd_p=4dd835b2-1cef-4f67-9aa6-a889456f9338&pf_rd_r=51XX475K6E8P9W3SFTNJ&pd_rd_wg=8bgZ8&pd_rd_r=d59e6730-5fcc-42aa-8a9f-13c33dcb066c&ref_=pd_hp_d_atf_dealz_tdc&th=1', 91, 0.6, 534620, '759377', 1.65, 'France', '2025-03-27 09:04:43'),
(59, 3, 'amazon.fr', 'https://www.amazon.fr/dp/B07WXVSXLS/ref=twister_B0D2YBPSXG?_encoding=UTF8&psc=1', 0, 0, 0, '0', 0, 'France', '2025-03-27 09:04:43'),
(60, 3, 'amazon.fr', 'https://www.amazon.fr/dp/B07WXVSXLS/ref=twister_B0D2YBPSXG?_encoding=UTF8&th=1', 191, 1.27, 1437080, '2807206', 2.072, 'France', '2025-03-27 09:04:46'),
(61, 3, 'amazon.fr', 'https://www.amazon.fr/dp/B07WXVSXLS/ref=twister_B0D2YBPSXG?_encoding=UTF8&th=1', 230, 1.54, 2407329, '3846016', 3.491, 'France', '2025-03-27 09:04:47'),
(62, 3, 'amazon.fr', 'https://www.amazon.fr/ap/signin?_encoding=UTF8&openid.assoc_handle=amazon_checkout_fr&openid.claimed_id=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0%2Fidentifier_select&openid.identity=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0%2Fidentifier_select&openid.mode=checkid_setup&openid.ns=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0&openid.ns.pape=http%3A%2F%2Fspecs.openid.net%2Fextensions%2Fpape%2F1.0&openid.pape.max_auth_age=0&openid.return_to=https%3A%2F%2Fwww.amazon.fr%2Fgp%2Fcheckoutportal%2Fenter-checkout.html%3Fie%3DUTF8%26asin%3DB07WXVSXLS%26buyNow%3D1%26cartCustomerID%3D0%26fromSignIn%3D1%26isGift%3D0%26offeringID%3DsdFbGjO%25252F1ProzqLHnju6IUCmOFgE852olNaNi7Nj7YstaZBXt6jkVvAelJFf91MVz98Q%25252FgR6ukVx6vkxMK59An0xhSUoQYTUuIASBH81JRyAk4TvV%25252BPynZ4gqcaO%25252FadDPVGQEKLzRBo%25253D%26purchaseInputs%3DHASH%25280xf5ddd9c0%2529%26quantity%3D1%26sessionID%3D262-1054591-1711722&pageId=amazon_checkout_fr&showRmrMe=0&siteState=IMBMsgs.&suppressSignInRadioButtons=0', 15, 0.12, 692035, '2424228', 0.225, 'France', '2025-03-27 09:04:48'),
(63, 3, 'amazon.fr', 'https://www.amazon.fr/ap/signin?_encoding=UTF8&openid.assoc_handle=amazon_checkout_fr&openid.claimed_id=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0%2Fidentifier_select&openid.identity=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0%2Fidentifier_select&openid.mode=checkid_setup&openid.ns=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0&openid.ns.pape=http%3A%2F%2Fspecs.openid.net%2Fextensions%2Fpape%2F1.0&openid.pape.max_auth_age=0&openid.return_to=https%3A%2F%2Fwww.amazon.fr%2Fgp%2Fcheckoutportal%2Fenter-checkout.html%3Fie%3DUTF8%26asin%3DB07WXVSXLS%26buyNow%3D1%26cartCustomerID%3D0%26fromSignIn%3D1%26isGift%3D0%26offeringID%3DsdFbGjO%25252F1ProzqLHnju6IUCmOFgE852olNaNi7Nj7YstaZBXt6jkVvAelJFf91MVz98Q%25252FgR6ukVx6vkxMK59An0xhSUoQYTUuIASBH81JRyAk4TvV%25252BPynZ4gqcaO%25252FadDPVGQEKLzRBo%25253D%26purchaseInputs%3DHASH%25280xf5ddd9c0%2529%26quantity%3D1%26sessionID%3D262-1054591-1711722&pageId=amazon_checkout_fr&showRmrMe=0&siteState=IMBMsgs.&suppressSignInRadioButtons=0', 51, 0.35, 696186, '2462497', 1.799, 'France', '2025-03-27 09:04:50'),
(64, 3, 'amazon.fr', 'https://www.amazon.fr/dp/B07WXVSXLS/ref=twister_B0D2YBPSXG?_encoding=UTF8&th=1', 139, 0.91, 541267, '2112517', 2.273, 'France', '2025-03-27 09:04:53'),
(65, 3, 'google.com', 'https://www.google.com/search?client=firefox-b-d&q=cdiscount', 15, 0.1, 172295, '1414736', 0.377, 'France', '2025-03-27 09:04:56'),
(66, 3, 'google.com', 'https://www.cdiscount.com/', 76, 0.49, 0, '82591', 0.035, 'France', '2025-03-27 09:04:56'),
(67, 3, 'cdiscount.com', 'https://www.cdiscount.com/', 437, 2.83, 479522, '1486058', 2.784, 'France', '2025-03-27 09:05:00'),
(68, 3, 'cdiscount.com', 'https://www.cdiscount.com/telephonie/telephone-mobile/apple-iphone-13-128-go-midnight-2021-reconditi/f-1440402-rcdapp0032098exe.html?idOffre=3723244159#cm_sp=PA:12587582:NH:CAR', 7, 0.05, 148343, '739866', 0.347, 'France', '2025-03-27 09:05:01'),
(69, 3, 'cdiscount.com', 'https://www.cdiscount.com/telephonie/telephone-mobile/apple-iphone-13-128-go-midnight-2021-reconditi/f-1440402-rcdapp0032098exe.html?idOffre=3723244159#cm_sp=PA:12587582:NH:CAR', 76, 0.5, 273429, '960314', 1.915, 'France', '2025-03-27 09:05:02'),
(70, 3, 'cdiscount.com', 'https://www.cdiscount.com/telephonie/telephone-mobile/f-1440402-rcdapp0036170exe.html', 158, 1.02, 63631, '332967', 0.283, 'France', '2025-03-27 09:05:03'),
(71, 3, 'cdiscount.com', 'https://www.cdiscount.com/telephonie/telephone-mobile/f-1440402-rcdapp0036170exe.html', 384, 2.49, 575217, '2146190', 2.26, 'France', '2025-03-27 09:05:05'),
(72, 3, 'cdiscount.com', 'https://www.cdiscount.com/telephonie/telephone-mobile/apple-iphone-13-128-go-bleu-2021-reconditionne/r2-1440402-rcdapp0036170exe-4001963605.html', 34, 0.22, 0, '105757', 0.367, 'France', '2025-03-27 09:05:06'),
(73, 3, 'cdiscount.com', 'https://www.cdiscount.com/telephonie/telephone-mobile/apple-iphone-13-128-go-bleu-2021-reconditionne/r2-1440402-rcdapp0036170exe-4001963605.html', 233, 1.5, 23466, '246418', 1.927, 'France', '2025-03-27 09:05:07'),
(74, 3, 'amazon.fr', 'https://www.amazon.fr/', 297, 1.93, 365588, '2223460', 0.759, 'France', '2025-03-27 09:05:17'),
(75, 3, 'amazon.fr', 'https://www.amazon.fr/', 464, 3.02, 642718, '5049441', 3.608, 'France', '2025-03-27 09:05:20'),
(76, 3, '', 'about:addons', 0, 0, 0, '0', 0, 'France', '2025-03-27 09:07:34'),
(77, 3, 'amazon.fr', 'https://www.amazon.fr/', 76, 5.05, 0, '483071', 0.339, 'France', '2025-03-27 09:08:06'),
(78, 3, 'amazon.fr', 'https://www.amazon.fr/', 430, 2.79, 345777, '5193666', 3.402, 'France', '2025-03-27 09:08:11'),
(79, 3, 'google.com', 'https://www.google.com/search?client=firefox-b-d&q=cddiscount', 47, 3.2, 163233, '1698533', 0.446, 'France', '2025-03-27 09:09:06'),
(80, 3, 'cdiscount.com', 'https://www.cdiscount.com/', 439, 2.83, 1240, '1023995', 0.396, 'France', '2025-03-27 09:09:07'),
(81, 3, 'cdiscount.com', 'https://www.cdiscount.com/', 368, 2.38, 112132, '399721', 5.818, 'France', '2025-03-27 09:09:13'),
(82, 3, 'cdiscount.com', 'https://www.cdiscount.com/maison/canape-canapes/l-11701.html', 46, 0.3, 0, '240134', 0.433, 'France', '2025-03-27 09:09:14'),
(83, 3, 'cdiscount.com', 'https://www.cdiscount.com/maison/canape-canapes/l-11701.html', 456, 2.94, 89864, '543794', 14.491, 'France', '2025-03-27 09:09:28'),
(84, 3, 'cdiscount.com', 'https://www.cdiscount.com/maison/canape-canapes/canape-d-angle-convertible-reversible-4-places-t/f-11701-hamiltonancvbl.html#mpos=0|cd', 47, 0.33, 1047917, '2069805', 0.421, 'France', '2025-03-27 09:09:29'),
(85, 3, 'cdiscount.com', 'https://www.cdiscount.com/maison/canape-canapes/canape-d-angle-convertible-reversible-4-places-t/f-11701-hamiltonancvbl.html#mpos=0|cd', 149, 0.99, 1189207, '2304742', 2.029, 'France', '2025-03-27 09:09:30'),
(86, 3, 'cdiscount.com', 'https://www.cdiscount.com/maison/canape-canapes/canape-d-angle-convertible-reversible-4-places-t/r2-11701-hamiltonancvbl-1117696696.html', 132, 0.85, 16738, '116937', 0.583, 'France', '2025-03-27 09:09:31'),
(87, 3, 'youtube.com', 'https://www.youtube.com/', 16, 0.1, 0, '0', 0.432, 'France', '2025-03-27 09:09:34'),
(88, 3, 'youtube.com', 'https://www.youtube.com/shorts/jjfBWDM2_F4', 144, 0.94, 459889, '526911', 4.621, 'France', '2025-03-27 09:09:38'),
(89, 3, 'youtube.com', 'https://www.youtube.com/shorts/jjfBWDM2_F4', 246, 1.77, 7682810, '7832526', 12.086, 'France', '2025-03-27 09:09:46'),
(90, 3, 'amazon.fr', 'https://www.amazon.fr/', 510, 3.31, 403521, '5271327', 100.793, 'France', '2025-03-27 09:09:47'),
(91, 3, 'amazon.fr', 'https://www.amazon.fr/Capsules-Original-Incrust%C3%A9es-Impeccable-Fabriqu%C3%A9/dp/B0CMJGPDKZ/?_encoding=UTF8&pd_rd_w=6ReZX&content-id=amzn1.sym.afad9d33-5abe-46a4-9e35-a98528b29ef1&pf_rd_p=afad9d33-5abe-46a4-9e35-a98528b29ef1&pf_rd_r=WZV5D7DAPMK9C3APMVK2&pd_rd_wg=QzfbZ&pd_rd_r=75a8378f-7ae0-4b72-9550-e19e9e0e9b54&ref_=pd_hp_d_atf_dealz_cs', 0, 0, 0, '0', 0, 'France', '2025-03-27 09:09:48'),
(92, 3, 'amazon.fr', 'https://www.amazon.fr/Capsules-Original-Incrust%C3%A9es-Impeccable-Fabriqu%C3%A9/dp/B0CMJGPDKZ/?_encoding=UTF8&pd_rd_w=6ReZX&content-id=amzn1.sym.afad9d33-5abe-46a4-9e35-a98528b29ef1&pf_rd_p=afad9d33-5abe-46a4-9e35-a98528b29ef1&pf_rd_r=WZV5D7DAPMK9C3APMVK2&pd_rd_wg=QzfbZ&pd_rd_r=75a8378f-7ae0-4b72-9550-e19e9e0e9b54&ref_=pd_hp_d_atf_dealz_cs&th=1', 42, 0.27, 32170, '267350', 0.495, 'France', '2025-03-27 09:09:49'),
(93, 3, 'amazon.fr', 'https://www.amazon.fr/Capsules-Original-Incrust%C3%A9es-Impeccable-Fabriqu%C3%A9/dp/B0CMJGPDKZ/?_encoding=UTF8&pd_rd_w=6ReZX&content-id=amzn1.sym.afad9d33-5abe-46a4-9e35-a98528b29ef1&pf_rd_p=afad9d33-5abe-46a4-9e35-a98528b29ef1&pf_rd_r=WZV5D7DAPMK9C3APMVK2&pd_rd_wg=QzfbZ&pd_rd_r=75a8378f-7ae0-4b72-9550-e19e9e0e9b54&ref_=pd_hp_d_atf_dealz_cs&th=1', 127, 0.86, 1537143, '2474298', 2.136, 'France', '2025-03-27 09:09:50'),
(94, 3, 'amazon.fr', 'https://www.amazon.fr/Capsules-Original-Incrust%C3%A9es-Impeccable-Fabriqu%C3%A9/dp/B0CMJGPDKZ/?_encoding=UTF8&pd_rd_w=6ReZX&content-id=amzn1.sym.afad9d33-5abe-46a4-9e35-a98528b29ef1&pf_rd_p=afad9d33-5abe-46a4-9e35-a98528b29ef1&pf_rd_r=WZV5D7DAPMK9C3APMVK2&pd_rd_wg=QzfbZ&pd_rd_r=75a8378f-7ae0-4b72-9550-e19e9e0e9b54&ref_=pd_hp_d_atf_dealz_cs&th=1', 191, 1.27, 1554811, '2561510', 6.447, 'France', '2025-03-27 09:09:55'),
(95, 3, 'amazon.fr', 'https://www.amazon.fr/', 13, 0.08, 0, '106272', 0.022, 'France', '2025-03-27 09:10:05'),
(96, 3, 'amazon.fr', 'https://www.amazon.fr/', 416, 2.7, 431460, '4737360', 3.619, 'France', '2025-03-27 09:10:09'),
(97, 3, 'amazon.fr', 'https://www.amazon.fr/', 459, 2.98, 460059, '4970205', 139.81, 'France', '2025-03-27 09:12:25'),
(98, 3, 'amazon.fr', 'https://www.amazon.fr/', 52, 0.34, 0, '337211', 0.333, 'France', '2025-03-27 09:12:27'),
(99, 3, 'amazon.fr', 'https://www.amazon.fr/', 420, 2.73, 510706, '4917184', 19.525, 'France', '2025-03-27 09:12:50'),
(100, 209, 'amazon.fr', 'https://www.amazon.fr/', 11, 0.07, 1186, '44', 0.201, 'France', '2025-03-27 09:37:27'),
(101, 209, 'amazon.fr', 'https://www.amazon.fr/', 250, 1.63, 521993, '1946340', 3.914, 'France', '2025-03-27 09:37:30'),
(102, 209, 'google.com', 'https://www.google.com/search?client=firefox-b-d&q=cdiscount', 42, 2.9, 338329, '1714633', 0.698, 'France', '2025-03-27 09:40:38'),
(103, 209, 'cdiscount.com', 'https://www.cdiscount.com/', 853, 5.52, 489053, '3595126', 8.611, 'France', '2025-03-27 09:40:47'),
(104, 209, 'cdiscount.com', 'https://www.cdiscount.com/high-tech/televiseurs/mini-projecteur-portable-bluetooth-auto-videopr/f-1062603-tra1698341958008.html', 15, 0.1, 148299, '810448', 0.365, 'France', '2025-03-27 09:40:47'),
(105, 209, 'cdiscount.com', 'https://www.cdiscount.com/high-tech/televiseurs/mini-projecteur-portable-bluetooth-auto-videopr/f-1062603-tra1698341958008.html', 101, 0.66, 182236, '1185259', 1.618, 'France', '2025-03-27 09:40:49'),
(106, 209, '', 'about:addons', 0, 0, 0, '0', 0, 'France', '2025-03-27 10:16:04'),
(107, 209, '', 'about:addons', 0, 0, 0, '0', 0, 'France', '2025-03-27 10:16:06'),
(108, 209, 'amazon.fr', 'https://www.amazon.fr/', 242, 1.46, 235106, '1464402', 10.347, 'France', '2025-03-27 10:16:13'),
(109, 209, 'amazon.fr', 'https://www.amazon.fr/', 247, 1.49, 236292, '1464446', 12.348, 'France', '2025-03-27 10:16:15');

-- --------------------------------------------------------

--
-- Table structure for table `organisation`
--

CREATE TABLE `organisation` (
  `id` int(11) NOT NULL,
  `organisation_name` varchar(255) NOT NULL,
  `organisation_code` varchar(20) NOT NULL,
  `city` varchar(255) DEFAULT NULL,
  `siret` varchar(14) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `organisation`
--

INSERT INTO `organisation` (`id`, `organisation_name`, `organisation_code`, `city`, `siret`) VALUES
(2, 'Adidas', 'W43KBHO4', 'France', '08548006901136'),
(11, 'Youtube', '8L710TBP', 'France', NULL),
(12, 'Nike', '9KGUFB08', 'France', NULL),
(13, 'leclerc', 'DKIZ2F10', 'France', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `organisation_id` int(11) DEFAULT NULL,
  `is_admin_of_id` int(11) DEFAULT NULL,
  `email` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '(DC2Type:json)' CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `total_carbon_footprint` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `organisation_id`, `is_admin_of_id`, `email`, `roles`, `password`, `first_name`, `last_name`, `total_carbon_footprint`) VALUES
(3, 2, NULL, 'lucie@gmail.com', '[\"ROLE_USER\"]', '$2y$13$dv/NxQ4Xy9xd6nYvJ1hvDOwSjSEiHjiUzGY.IraYAoyA7YZrqhVQK', 'Lucie', 'Dubos', 106.30999999999997),
(5, NULL, NULL, 'neo@gmail.com', '[\"ROLE_USER\"]', '$2y$13$xq2wxoZH2nocdZwkUzJ4FuYymBq4Ez16k63.lg3Qpbs.Jy/2nO7ra', 'Neo', 'Elduayen', 90.30999999999999),
(7, NULL, 2, 'adidas@gmail.com', '[\"ROLE_ORGANISATION\"]', '$2y$13$XaJBpq56r/z4FueglNFtXeoUxg30VHX5qnjI2Ohb0fscNmiquvc.y', NULL, NULL, NULL),
(10, NULL, NULL, 'alex@gmail.com', '[\"ROLE_USER\"]', '$2y$13$MUfxUZtnggVcxAwO7BkcJu7TIwQxuWt1DH55AotXZdpM6ZzuDOpg.', 'Alex', 'Moreno', NULL),
(11, NULL, NULL, 'souleymen@gmail.com', '[\"ROLE_USER\"]', '$2y$13$S/QxS8nWD1swyhmexBkAQOhkJdeyL.yuW6pKe3pk8RqdRnZ8pFyNC', 'souleymen', 'zaza', 1.7899999999999998),
(20, NULL, 11, 'youtube@gmail.com', '[\"ROLE_ORGANISATION\"]', '$2y$13$5NoboLauejRNHRw.thmRW.Uul9nEVCGyx51E0/Yp6Zd98sOJWJOaS', NULL, NULL, NULL),
(21, NULL, 12, 'nike@gmail.com', '[\"ROLE_ORGANISATION\"]', '$2y$13$FntrhCeAX4aS.UwhX.97WedW061oSWYLodEqQLjZnC316Tbke7IlS', NULL, NULL, NULL),
(22, NULL, 13, 'leclerc@gmail.com', '[\"ROLE_ORGANISATION\"]', '$2y$13$6e4O0elGukgcTN55JXUHAe6cvauzc9XXm6CNVwIuB4lbLOQqFkry.', NULL, NULL, NULL),
(184, NULL, NULL, 'loic@gmail.com', '[\"ROLE_USER\"]', '$2y$13$3IrFh334/bnNCwfbHm.87e6ZPiVXa2ROnf.UHlh7XlTwbNXiHTbBy', 'loic', 'q', NULL),
(207, NULL, NULL, 'zdzdzdz@gmail.com', '[\"ROLE_USER\"]', '$2y$13$rVYw7m3EJ1iUsjFVQUSIvuSIT/PzkjhejDUzLZkFbP1lzUI3eBTTC', 'dzdz', 'zdzdzd', NULL),
(208, NULL, NULL, 'yon.dourisboure@gmail.com', '[\"ROLE_USER\"]', '$2y$13$66ND4TJgY5z81/e9HI4/m.U/OhsB5xJDepeLLo3vwIUFlC80JuI7.', 'Yon', 'Dourisboure', NULL),
(209, 2, NULL, 'yon.dourisboure@iutbayonne.univ-pau.fr', '[\"ROLE_USER\"]', '$2y$13$INhtmI7nHz6ILecozeHo4eP8/zzpe11tRAvO.N9gciXO/2mSxJKeq', 'Yon', 'Dourisboure', 13.83);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `advice`
--
ALTER TABLE `advice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `equivalent`
--
ALTER TABLE `equivalent`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Indexes for table `monitored_website`
--
ALTER TABLE `monitored_website`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_7458B0D5A76ED395` (`user_id`);

--
-- Indexes for table `organisation`
--
ALTER TABLE `organisation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D6492B4135F3` (`is_admin_of_id`),
  ADD KEY `IDX_8D93D6499E6B1585` (`organisation_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `advice`
--
ALTER TABLE `advice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `equivalent`
--
ALTER TABLE `equivalent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT for table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `monitored_website`
--
ALTER TABLE `monitored_website`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `organisation`
--
ALTER TABLE `organisation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=210;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `monitored_website`
--
ALTER TABLE `monitored_website`
  ADD CONSTRAINT `FK_7458B0D5A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_8D93D6492B4135F3` FOREIGN KEY (`is_admin_of_id`) REFERENCES `organisation` (`id`),
  ADD CONSTRAINT `FK_8D93D6499E6B1585` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
